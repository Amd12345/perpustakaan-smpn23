<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function edit()
    {
        return view('account.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'username'         => 'required|string|max:255|unique:users,username,' . $user->id,
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $user->id,
            'password'         => 'nullable|min:8|confirmed',
            'current_password' => 'required_with:password',
        ]);

        if (! empty($validated['password'])) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                return redirect()
                    ->route('account.edit')
                    ->withInput()
                    ->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
            }
        }

        $user->username = $validated['username'];
        $user->name     = $validated['name'];
        $user->email    = $validated['email'];

        if (! empty($validated['password'])) {
            // Cast 'hashed' pada model User otomatis meng-hash nilai ini.
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('account.edit')->with('success', 'Akun berhasil diperbarui.');
    }
}
