<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const ROLES = ['admin', 'kepala_sekolah', 'guru'];

    public function index(string $role)
    {
        $this->guardRole($role);

        $users = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', $role))
            ->with('teacher')
            ->orderBy('name')
            ->paginate(20);

        return view('users.index', ['users' => $users, 'role' => $role]);
    }

    public function create(string $role)
    {
        $this->guardRole($role);

        $teachers = $role === 'guru'
            ? Teacher::whereDoesntHave('user')->orderBy('name')->get()
            : collect();

        return view('users.create', ['role' => $role, 'teachers' => $teachers]);
    }

    public function store(Request $request, string $role)
    {
        $this->guardRole($role);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'teacher_id' => $data['teacher_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $user->assignRole($role);

        if ($role === 'guru' && ! $user->teacher_id) {
            $teacher = Teacher::create([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '-',
                'is_active' => true,
            ]);

            $user->update(['teacher_id' => $teacher->id]);
        }

        ActivityLogService::log('create_user_account', $user, 'Membuat akun '.$role.': '.$user->email);

        return redirect()->route('users.index', $role)->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(string $role, User $user)
    {
        $this->guardRole($role);

        abort_unless($user->hasRole($role), 404);

        return view('users.edit', ['user' => $user, 'role' => $role]);
    }

    public function update(Request $request, string $role, User $user)
    {
        $this->guardRole($role);

        abort_unless($user->hasRole($role), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_active = $request->boolean('is_active');

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        if ($user->teacher) {
            $user->teacher->update([
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
            ]);
        }

        ActivityLogService::log('update_user_account', $user, 'Memperbarui akun '.$role.': '.$user->email);

        return redirect()->route('users.index', $role)->with('success', 'Akun diperbarui.');
    }

    public function destroy(string $role, User $user)
    {
        $this->guardRole($role);

        abort_unless($user->hasRole($role), 404);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($role === 'super_admin' && User::whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu akun Super Admin.');
        }

        $user->delete();

        ActivityLogService::log('delete_user_account', $user, 'Menghapus akun '.$role.': '.$user->email);

        return back()->with('success', 'Akun dihapus.');
    }

    public function toggleActive(string $role, User $user)
    {
        $this->guardRole($role);

        abort_unless($user->hasRole($role), 404);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        if ($user->teacher) {
            $user->teacher->update(['is_active' => $user->is_active]);
        }

        ActivityLogService::log(
            $user->is_active ? 'activate_user_account' : 'deactivate_user_account',
            $user,
            ($user->is_active ? 'Mengaktifkan' : 'Menonaktifkan').' akun '.$role.': '.$user->email,
        );

        return back()->with('success', 'Status akun diperbarui.');
    }

    private function guardRole(string $role): void
    {
        abort_unless(in_array($role, self::ROLES), 404);
    }
}
