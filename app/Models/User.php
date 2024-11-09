<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Si utilizas la tabla por defecto "users", no es necesario especificar el nombre
    protected $table = 'users';

    // Definición de los atributos que se pueden llenar
    protected $fillable = [
        'username', // Agregado el campo 'username'
        'name',
        'last_name',
        'phone',
        'password',
        'role_id',
        'associated_id', // Agregado el campo 'associated_id'
        'is_active', // Agregado el campo 'is_active'
        'remember_token',
        'created_by', // Agregado el campo 'created_by'
        'updated_by', // Agregado el campo 'updated_by'
    ];

    // Campos que no deberían ser visibles en las respuestas JSON
    protected $hidden = [
        'password', // Contraseña oculta
        'remember_token', // Recuerda ocultar el token en la respuesta JSON
    ];

    // Campos que se deben cast a tipos específicos
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean', // Cast para is_active
    ];

    // Definición de la relación con Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Definición de la relación con el usuario asociado (si aplica)
    public function associated()
    {
        return $this->belongsTo(User::class, 'associated_id');
    }

    // Puedes agregar métodos para auditoría aquí si es necesario
}
