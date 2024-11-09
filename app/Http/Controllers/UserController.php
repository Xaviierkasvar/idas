<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $users = collect();

        if ($user->role_id == 1) {

            $users = User::all();

        } elseif ($user->role_id == 2) {
            
            $leaders = User::where('associated_id', $user->id)
                            ->get();

            $sellers = collect();

            foreach ($leaders as $leader) {
                $leaderSellers = User::where('associated_id', $leader->id)
                                    ->get();
                $sellers = $sellers->merge($leaderSellers);
            }
            
            $users = $leaders->merge($sellers);
        } elseif ($user->role_id == 3) {
            
            $sellers = User::where('associated_id', $user->id)
                            ->get();

            
            $users = $sellers;
        }

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $user = Auth::user();
        $roles = collect();

        if ($user->role_id == 1) {
            $roles = Role::whereIn('id', [1, 2, 3, 4])->get();
        } elseif ($user->role_id == 2) {
            $roles = Role::whereIn('id', [3, 4])->get();
        } elseif ($user->role_id == 3) {
            $roles = Role::where('id', 4)->get();
        }
        
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        try {
            $user = User::create(array_merge($validated, [
                'associated_id' => auth()->id(),
                'created_by' => auth()->id(),
            ]));

            Log::info('Usuario creado exitosamente', ['user_id' => $user->id]);

            return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear el usuario', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            
            return back()->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()]);
        }
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $newStatus = !$user->is_active;

        $user->is_active = $newStatus;
        $user->updated_by = auth()->id();
        $user->save();

        if ($user->role_id == 2) {
            $leaders = User::where('associated_id', $user->id)->where('role_id', 3)->get();
            foreach ($leaders as $leader) {
                $leader->is_active = $newStatus;
                $leader->updated_by = auth()->id();
                $leader->save();
                
                // Desactivar/activar todos los vendedores asociados a este líder
                $sellers = User::where('associated_id', $leader->id)->where('role_id', 4)->get();
                foreach ($sellers as $seller) {
                    $seller->is_active = $newStatus;
                    $seller->updated_by = auth()->id();
                    $seller->save();
                }
            }
        } elseif ($user->role_id == 3) {
            $sellers = User::where('associated_id', $user->id)->where('role_id', 4)->get();
            foreach ($sellers as $seller) {
                $seller->is_active = $newStatus;
                $seller->updated_by = auth()->id();
                $seller->save();
            }
        }

        return redirect()->route('users.index')->with('success', 'User status updated successfully.');
    }


    public function show(User $user)
    {
        return view('users.show', compact('user')); // Vista para mostrar detalles de un usuario
    }

    public function edit($id)
    {
        $user = User::findOrFail($id); // Obtiene el usuario por su ID
        $roles = Role::all(); // Obtiene todos los roles para llenar el dropdown
        $userRole = Auth::user();

        if ($userRole->role_id == 1) {
            $roles = Role::whereIn('id', [1, 2, 3, 4])->get();
        } elseif ($userRole->role_id == 2) {
            $roles = Role::whereIn('id', [3, 4])->get();
        } elseif ($userRole->role_id == 3) {
            $roles = Role::where('id', [4])->get();
        }

        return view('users.edit', compact('user', 'roles', 'userRole')); // Pasa las variables a la vista
    }


    public function update(Request $request, User $user)
    {
        // Validar y actualizar el usuario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $user->update($validated);
        
        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

}
