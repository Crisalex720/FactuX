<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'pais';
    protected $primaryKey = 'id_pais';
    public $timestamps = false;

    protected $fillable = [
        'nombre_pais'
    ];

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'id_pais', 'id_pais');
    }

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class, 'id_pais', 'id_pais');
    }
}
