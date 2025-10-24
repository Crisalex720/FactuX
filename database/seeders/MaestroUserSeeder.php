<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trabajador;
use Illuminate\Support\Facades\Hash;

class MaestroUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Verificar si ya existe un usuario maestro
        $maestroExists = Trabajador::where('cargo', 'maestro')->exists();
        
        if (!$maestroExists) {
            Trabajador::create([
                'cedula' => 999999999,
                'nombre' => 'Usuario',
                'apellido' => 'Maestro',
                'id_pais' => 1,
                'id_depart' => 50,
                'id_ciudad' => 44,
                'cargo' => 'maestro',
                'contraseña' => Hash::make('master123')
            ]);
            
            $this->command->info('Usuario maestro creado exitosamente');
            $this->command->info('Cédula: 999999999');
            $this->command->info('Contraseña: master123');
        } else {
            $this->command->info('Ya existe un usuario maestro');
        }
    }
}
