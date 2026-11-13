<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:30', 'unique:teachers,nip'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:teachers,email'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $result = DB::transaction(function () use ($validated) {
            $teacher = Teacher::create([
                'nip' => $validated['nip'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'is_active' => false,
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'teacher_id' => $teacher->id,
                'is_active' => false,
            ]);

            $user->assignRole('guru');

            return ['teacher' => $teacher, 'user' => $user];
        });

        ActivityLogService::log(
            'register_guru',
            $result['teacher'],
            'Guru mendaftar menunggu persetujuan: '.$result['user']->email,
        );

        return redirect()->route('login')
            ->with('status', 'Pendaftaran berhasil. Akun Anda menunggu persetujuan Admin.');
    }
}
