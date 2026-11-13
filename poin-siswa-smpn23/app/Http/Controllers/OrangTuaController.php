<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    public function index(Request $request)
    {
        $parents = OrangTua::query()
            ->withCount('students')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->input('search').'%')
                        ->orWhere('phone', 'like', '%'.$request->input('search').'%');
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('parents.index', compact('parents'));
    }

    public function create()
    {
        return view('parents.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $parent = OrangTua::create($data);

        ActivityLogService::log('create_parent', $parent, 'Menambahkan orang tua: '.$parent->name);

        return redirect()->route('parents.index')->with('success', 'Data orang tua berhasil disimpan.');
    }

    public function edit(OrangTua $parent)
    {
        return view('parents.edit', ['parent' => $parent->load('students.class')]);
    }

    public function update(Request $request, OrangTua $parent)
    {
        $data = $this->validateData($request, $parent);

        $parent->update($data);

        ActivityLogService::log('update_parent', $parent, 'Memperbarui data orang tua: '.$parent->name);

        return redirect()->route('parents.index')->with('success', 'Data orang tua berhasil diperbarui.');
    }

    public function destroy(OrangTua $parent)
    {
        $parent->delete();

        ActivityLogService::log('delete_parent', $parent, 'Menghapus data orang tua: '.$parent->name);

        return back()->with('success', 'Data orang tua berhasil dihapus.');
    }

    private function validateData(Request $request, ?OrangTua $parent = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);
    }
}
