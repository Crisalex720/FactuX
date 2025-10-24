<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trabajador;
use Illuminate\Support\Facades\Hash;

class HashPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuarios:hash-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convierte todas las contraseñas de texto plano a hash';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trabajadores = Trabajador::all();
        $updated = 0;

        foreach ($trabajadores as $trabajador) {
            // Verificar si la contraseña ya está hasheada
            if (strlen($trabajador->contraseña) < 60) {
                $trabajador->contraseña = Hash::make($trabajador->contraseña);
                $trabajador->save();
                $updated++;
                $this->info("Contraseña actualizada para: {$trabajador->nombre} {$trabajador->apellido}");
            }
        }

        $this->info("Se actualizaron {$updated} contraseñas a formato hash.");
        return 0;
    }
}
