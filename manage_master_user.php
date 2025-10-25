<?php
/**
 * Script avanzado para gestionar el usuario maestro
 * Permite crear, actualizar o eliminar el usuario maestro
 * 
 * Uso: php manage_master_user.php [create|update|delete|reset]
 */

// Cargar configuración de Laravel
require_once __DIR__ . '/vendor/autoload.php';

// Inicializar la aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Trabajador;

// Obtener el comando desde argumentos de línea de comandos
$command = $argv[1] ?? 'help';

echo "🔧 Gestión de Usuario Maestro - FactuX\n";
echo "=====================================\n\n";

switch (strtolower($command)) {
    case 'create':
        createMasterUser();
        break;
    case 'update':
        updateMasterUser();
        break;
    case 'delete':
        deleteMasterUser();
        break;
    case 'reset':
        resetMasterUser();
        break;
    case 'info':
        showMasterUserInfo();
        break;
    default:
        showHelp();
        break;
}

function createMasterUser() {
    echo "📝 Creando usuario maestro...\n";
    
    $masterData = [
        'cedula' => 999999999,
        'nombre' => 'Usuario',
        'apellido' => 'Maestro',
        'cargo' => 'maestro',
        'contraseña' => Hash::make('master123'),
        'id_pais' => 1,
        'id_depart' => 1,
        'id_ciudad' => 1
    ];

    try {
        $existingUser = Trabajador::where('cedula', $masterData['cedula'])->first();
        
        if ($existingUser) {
            echo "❌ Error: El usuario maestro ya existe (ID: {$existingUser->id_trab})\n";
            echo "💡 Usa 'php manage_master_user.php update' para actualizarlo\n";
            return;
        }

        // Verificar ubicaciones básicas
        ensureBasicLocations();

        // Obtener el próximo ID
        $maxId = DB::table('trabajadores')->max('id_trab') ?? 0;
        $masterData['id_trab'] = $maxId + 1;

        $trabajador = new Trabajador();
        $trabajador->fill($masterData);
        $trabajador->id_trab = $masterData['id_trab'];
        $trabajador->save();

        echo "✅ Usuario maestro creado exitosamente!\n";
        showUserDetails($trabajador);
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

function updateMasterUser() {
    echo "🔄 Actualizando usuario maestro...\n";
    
    try {
        $user = Trabajador::where('cedula', 999999999)->first();
        
        if (!$user) {
            echo "❌ Error: Usuario maestro no encontrado\n";
            echo "💡 Usa 'php manage_master_user.php create' para crearlo\n";
            return;
        }

        echo "📋 Usuario actual encontrado:\n";
        showUserDetails($user);
        
        echo "\n¿Qué deseas actualizar?\n";
        echo "1. Solo contraseña\n";
        echo "2. Contraseña y datos personales\n";
        echo "3. Cancelar\n";
        echo "Selecciona una opción (1-3): ";
        
        $handle = fopen("php://stdin", "r");
        $option = trim(fgets($handle));
        fclose($handle);
        
        switch ($option) {
            case '1':
                $user->contraseña = Hash::make('master123');
                $user->save();
                echo "✅ Contraseña actualizada exitosamente!\n";
                break;
                
            case '2':
                $user->contraseña = Hash::make('master123');
                $user->nombre = 'Usuario';
                $user->apellido = 'Maestro';
                $user->cargo = 'maestro';
                $user->save();
                echo "✅ Usuario actualizado exitosamente!\n";
                break;
                
            case '3':
                echo "❌ Operación cancelada\n";
                return;
                
            default:
                echo "❌ Opción inválida\n";
                return;
        }
        
        showUserDetails($user);
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

function deleteMasterUser() {
    echo "🗑️  Eliminando usuario maestro...\n";
    
    try {
        $user = Trabajador::where('cedula', 999999999)->first();
        
        if (!$user) {
            echo "❌ Error: Usuario maestro no encontrado\n";
            return;
        }

        echo "⚠️  ADVERTENCIA: Estás a punto de eliminar el usuario maestro:\n";
        showUserDetails($user);
        
        echo "\n❓ ¿Estás seguro? Esta acción no se puede deshacer (s/n): ";
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($response) === 's' || strtolower($response) === 'si') {
            $user->delete();
            echo "✅ Usuario maestro eliminado exitosamente\n";
        } else {
            echo "❌ Operación cancelada\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

function resetMasterUser() {
    echo "🔄 Reseteando usuario maestro...\n";
    echo "Esta operación eliminará y recreará el usuario maestro\n\n";
    
    deleteMasterUser();
    echo "\n";
    createMasterUser();
}

function showMasterUserInfo() {
    echo "ℹ️  Información del usuario maestro:\n";
    
    try {
        $user = Trabajador::where('cedula', 999999999)->first();
        
        if (!$user) {
            echo "❌ Usuario maestro no encontrado\n";
            echo "💡 Usa 'php manage_master_user.php create' para crearlo\n";
            return;
        }

        showUserDetails($user);
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

function showUserDetails($user) {
    echo "================================\n";
    echo "🆔 ID: {$user->id_trab}\n";
    echo "👤 Cédula: {$user->cedula}\n";
    echo "📝 Nombre: {$user->nombre} {$user->apellido}\n";
    echo "🎯 Cargo: {$user->cargo}\n";
    echo "🔑 Contraseña: master123 (hasheada)\n";
    echo "🌍 País ID: {$user->id_pais}\n";
    echo "🏢 Departamento ID: {$user->id_depart}\n";
    echo "🏙️  Ciudad ID: {$user->id_ciudad}\n";
    echo "================================\n";
}

function ensureBasicLocations() {
    // Crear registros básicos si no existen
    if (!DB::table('pais')->where('id_pais', 1)->exists()) {
        DB::table('pais')->insert(['id_pais' => 1, 'nombre_pais' => 'Colombia']);
        echo "✅ País creado: Colombia\n";
    }
    
    if (!DB::table('departamento')->where('id_depart', 1)->exists()) {
        DB::table('departamento')->insert([
            'id_depart' => 1, 
            'nombre_depart' => 'Cundinamarca', 
            'id_pais' => 1
        ]);
        echo "✅ Departamento creado: Cundinamarca\n";
    }
    
    if (!DB::table('ciudad')->where('id_ciudad', 1)->exists()) {
        DB::table('ciudad')->insert([
            'id_ciudad' => 1, 
            'nombre_ciudad' => 'Bogotá', 
            'id_depart' => 1
        ]);
        echo "✅ Ciudad creada: Bogotá\n";
    }
}

function showHelp() {
    echo "📖 Uso: php manage_master_user.php [comando]\n\n";
    echo "Comandos disponibles:\n";
    echo "  create  - Crear el usuario maestro\n";
    echo "  update  - Actualizar el usuario maestro existente\n";
    echo "  delete  - Eliminar el usuario maestro\n";
    echo "  reset   - Eliminar y recrear el usuario maestro\n";
    echo "  info    - Mostrar información del usuario maestro\n";
    echo "  help    - Mostrar esta ayuda\n\n";
    echo "Ejemplos:\n";
    echo "  php manage_master_user.php create\n";
    echo "  php manage_master_user.php info\n";
    echo "  php manage_master_user.php update\n\n";
    echo "👤 Datos del usuario maestro:\n";
    echo "  Cédula: 999999999\n";
    echo "  Contraseña: master123\n";
    echo "  Cargo: maestro\n";
}