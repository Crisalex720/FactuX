<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

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
        'contraseña'
    ];

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
}
