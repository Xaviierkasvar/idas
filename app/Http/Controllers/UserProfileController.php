<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;

class UserProfileController extends Controller
{
    public function edit()
    {
        // Obtiene el usuario autenticado
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        // Validación de los campos, incluyendo la confirmación de la contraseña
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'password' => 'nullable|string|min:8|confirmed', // Validación de la contraseña opcional
            'password_confirmation' => 'nullable|string|min:8', // Validación de confirmación de contraseña
        ]);

        // Obtiene el usuario autenticado
        $user = Auth::user();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;

        // Si la contraseña fue proporcionada, actualizarla
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        // Guarda los cambios
        $user->save();

        // Redirige con mensaje de éxito
        return redirect()->route('profile.edit')->with('success', 'Información actualizada correctamente.');
    }
}
