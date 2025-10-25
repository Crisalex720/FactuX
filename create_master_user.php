<?php
/**
 * Script para crear el usuario maestro en la base de datos
 * Ejecutar después de hacer pull del repositorio
 * 
 * Uso: php create_master_user.php
 */

// Cargar configuración de Laravel
require_once __DIR__ . '/vendor/autoload.php';

// Inicializar la aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Trabajador;

echo "🔧 Creando usuario maestro...\n";
echo "================================\n";

try {
    // Datos del usuario maestro
    $masterData = [
        'cedula' => 999999999,
        'nombre' => 'Usuario',
        'apellido' => 'Maestro',
        'cargo' => 'maestro',
        'contraseña' => Hash::make('master123'),
        'id_pais' => 1,      // Asumiendo que existe un país con ID 1
        'id_depart' => 1,    // Asumiendo que existe un departamento con ID 1
        'id_ciudad' => 1     // Asumiendo que existe una ciudad con ID 1
    ];

    // Verificar si el usuario maestro ya existe
    $existingUser = Trabajador::where('cedula', $masterData['cedula'])->first();
    
    if ($existingUser) {
        echo "⚠️  El usuario maestro ya existe (Cédula: {$masterData['cedula']})\n";
        echo "¿Desea actualizar la contraseña? (s/n): ";
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($response) === 's' || strtolower($response) === 'si') {
            $existingUser->contraseña = $masterData['contraseña'];
            $existingUser->cargo = $masterData['cargo'];
            $existingUser->save();
            echo "✅ Usuario maestro actualizado exitosamente!\n";
        } else {
            echo "❌ Operación cancelada.\n";
        }
        
        return;
    }

    // Verificar que existan los registros de país, departamento y ciudad
    $paisExists = DB::table('pais')->where('id_pais', 1)->exists();
    $departExists = DB::table('departamento')->where('id_depart', 1)->exists();
    $ciudadExists = DB::table('ciudad')->where('id_ciudad', 1)->exists();

    if (!$paisExists || !$departExists || !$ciudadExists) {
        echo "⚠️  Advertencia: No se encontraron registros básicos de ubicación.\n";
        echo "Creando registros básicos...\n";
        
        // Crear país básico si no existe
        if (!$paisExists) {
            DB::table('pais')->insert([
                'id_pais' => 1,
                'nombre_pais' => 'Colombia'
            ]);
            echo "✅ País creado: Colombia\n";
        }
        
        // Crear departamento básico si no existe
        if (!$departExists) {
            DB::table('departamento')->insert([
                'id_depart' => 1,
                'nombre_depart' => 'Cundinamarca',
                'id_pais' => 1
            ]);
            echo "✅ Departamento creado: Cundinamarca\n";
        }
        
        // Crear ciudad básica si no existe
        if (!$ciudadExists) {
            DB::table('ciudad')->insert([
                'id_ciudad' => 1,
                'nombre_ciudad' => 'Bogotá',
                'id_depart' => 1
            ]);
            echo "✅ Ciudad creada: Bogotá\n";
        }
    }

    // Obtener el próximo ID disponible
    $maxId = DB::table('trabajadores')->max('id_trab') ?? 0;
    $masterData['id_trab'] = $maxId + 1;

    // Crear el usuario maestro
    $trabajador = new Trabajador();
    $trabajador->id_trab = $masterData['id_trab'];
    $trabajador->cedula = $masterData['cedula'];
    $trabajador->nombre = $masterData['nombre'];
    $trabajador->apellido = $masterData['apellido'];
    $trabajador->cargo = $masterData['cargo'];
    $trabajador->contraseña = $masterData['contraseña'];
    $trabajador->id_pais = $masterData['id_pais'];
    $trabajador->id_depart = $masterData['id_depart'];
    $trabajador->id_ciudad = $masterData['id_ciudad'];
    
    $trabajador->save();

    echo "\n✅ Usuario maestro creado exitosamente!\n";
    echo "================================\n";
    echo "👤 Cédula: {$masterData['cedula']}\n";
    echo "🔑 Contraseña: master123\n";
    echo "🎯 Cargo: maestro\n";
    echo "🆔 ID: {$masterData['id_trab']}\n";
    echo "================================\n";
    echo "🚀 ¡Ya puedes iniciar sesión en la aplicación!\n";

} catch (Exception $e) {
    echo "❌ Error al crear el usuario maestro:\n";
    echo $e->getMessage() . "\n";
    echo "\n🔍 Detalles técnicos:\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    
    if ($e->getPrevious()) {
        echo "Error anterior: " . $e->getPrevious()->getMessage() . "\n";
    }
}