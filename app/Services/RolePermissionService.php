<?php

namespace App\Services;

class RolePermissionService
{
    /**
     * Definición de permisos por rol
     */
    public static function getPermissions()
    {
        return [
            'maestro' => [
                'usuarios',
                'facturacion', 
                'facturas',
                'reportes',
                'inventario',
                'clientes'
            ],
            'ceo' => [
                'usuarios',
                'facturacion', 
                'facturas',
                'reportes',
                'inventario',
                'clientes'
            ],
            'admin' => [
                'usuarios',
                'facturacion', 
                'facturas',
                'reportes',
                'inventario',
                'clientes'
            ],
            'administrativo' => [
                'facturacion', 
                'facturas',
                'reportes',
                'inventario',
                'clientes'
            ],
            'cajero' => [
                'facturacion',
                'clientes'
            ],
            'vendedor' => [
                'facturacion',
                'clientes',
                'inventario'
            ]
        ];
    }

    /**
     * Verificar si un usuario tiene permiso para un módulo
     */
    public static function hasPermission($userRole, $module)
    {
        $permissions = self::getPermissions();
        $userRole = strtolower($userRole);
        
        return isset($permissions[$userRole]) && in_array($module, $permissions[$userRole]);
    }

    /**
     * Obtener módulos disponibles para un rol
     */
    public static function getModulesForRole($role)
    {
        $permissions = self::getPermissions();
        $role = strtolower($role);
        
        return $permissions[$role] ?? [];
    }

    /**
     * Obtener todos los roles disponibles
     */
    public static function getAllRoles()
    {
        return [
            'maestro' => 'Usuario Maestro',
            'ceo' => 'CEO/Director',
            'admin' => 'Administrador',
            'administrativo' => 'Personal Administrativo',
            'cajero' => 'Cajero',
            'vendedor' => 'Vendedor'
        ];
    }

    /**
     * Obtener roles que el usuario actual puede asignar
     */
    public static function getAssignableRoles($currentUserRole)
    {
        $currentUserRole = strtolower($currentUserRole);
        
        // Maestro puede asignar todos los roles EXCEPTO maestro (para evitar duplicados)
        if ($currentUserRole === 'maestro') {
            $roles = self::getAllRoles();
            unset($roles['maestro']); // Maestro no puede crear otros maestros
            return $roles;
        }
        
        // CEO y Admin pueden asignar todos excepto maestro
        if (in_array($currentUserRole, ['ceo', 'admin'])) {
            $roles = self::getAllRoles();
            unset($roles['maestro']);
            return $roles;
        }
        
        // Administrativo solo puede asignar cajero y vendedor
        if ($currentUserRole === 'administrativo') {
            return [
                'cajero' => 'Cajero',
                'vendedor' => 'Vendedor'
            ];
        }
        
        return [];
    }

    /**
     * Obtener descripción de módulos
     */
    public static function getModulesDescription()
    {
        return [
            'usuarios' => 'Gestión de Usuarios',
            'facturacion' => 'Módulo de Facturación',
            'facturas' => 'Listado de Facturas',
            'reportes' => 'Reportes y Estadísticas',
            'inventario' => 'Gestión de Inventario',
            'clientes' => 'Gestión de Clientes'
        ];
    }
}