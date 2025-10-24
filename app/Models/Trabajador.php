<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Trabajador extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'trabajadores';
    protected $primaryKey = 'id_trab';
    public $timestamps = false;

    protected $fillable = [
        'cedula',
        'nombre',
        'apellido',
        'cargo',
        'contraseña',
        'id_pais',
        'id_depart',
        'id_ciudad'
    ];

    protected $hidden = [
        'contraseña',
        'remember_token',
    ];

    // Especificar que el campo de usuario es 'cedula' en lugar de 'email'
    public function getAuthIdentifierName()
    {
        return 'cedula';
    }

    // Especificar que el campo de contraseña es 'contraseña'
    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id_pais');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_depart', 'id_depart');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad', 'id_ciudad');
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'id_trab', 'id_trab');
    }

    /**
     * Verificar si el usuario tiene permiso para un módulo
     */
    public function hasPermission($module)
    {
        return \App\Services\RolePermissionService::hasPermission($this->cargo, $module);
    }

    /**
     * Verificar si es usuario maestro
     */
    public function isMaster()
    {
        return strtolower($this->cargo) === 'maestro';
    }

    /**
     * Obtener módulos disponibles para este usuario
     */
    public function getAvailableModules()
    {
        return \App\Services\RolePermissionService::getModulesForRole($this->cargo);
    }
}
