<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function auth(Request $request)
    {
        $data = $request->only([
            'username',
            'password',
        ]);

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($data)) {
            return redirect('/dashboard')->with('success', 'Login berhasil!');
        }

        return redirect()->back()->with('error', 'Username atau password salah!');
    }
}
