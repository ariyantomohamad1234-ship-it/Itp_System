<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (session('login')) {
            $user = session('user');
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            return redirect('/dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $user = DB::table('users')
            ->where('username', $request->username)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'login' => true,
                'user' => $user
            ]);

            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            return redirect('/dashboard');
        }

        return back()->with('error', 'Username atau Password salah');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/');
    }
}