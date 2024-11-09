<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function doLogin(Request $request)
    {
        // Validación de los campos de entrada
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Verifica si el usuario existe en la base de datos
        $user = \App\Models\User::where('username', $credentials['username'])->first();

        // Verificar si el usuario existe y si está inactivo
        if ($user && !$user->is_active) {
            return back()->withErrors([
                'username' => 'This account is inactive. Please contact support.',
            ]);
        }

        if (!$user) {
            return back()->withErrors([
                'username' => 'The username does not exist.',
            ]);
        }

        // Verificar las credenciales del usuario
        if (!Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->filled('remember'))) {
            return back()->withErrors([
                'password' => 'The password is incorrect.',
            ]);
        }

        // Regenerar la sesión para evitar fijación de sesión
        $request->session()->regenerate();

        // Guardar información adicional en la sesión si es necesario
        $request->session()->put('user_id', $user->id);
        $request->session()->put('name', $user->name);
        $request->session()->put('last_name', $user->last_name);
        $request->session()->put('role_id', $user->role_id);
        $request->session()->put('is_active', $user->is_active);

        // Redirigir según el rol del usuario
        if ($user->role_id == 1) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role_id == 2) {
            return redirect()->route('chief.dashboard');
        } elseif ($user->role_id == 3) {
            return redirect()->route('leader.dashboard');
        } elseif ($user->role_id == 4) {
            return redirect()->route('seller.dashboard');
        }

        return redirect()->route('home');
    }
}
