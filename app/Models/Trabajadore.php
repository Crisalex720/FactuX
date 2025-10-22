<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Trabajadore
 * 
 * @property int $id_trab
 * @property int $cedula
 * @property string $nombre
 * @property string $apellido
 * @property int $id_pais
 * @property int $id_depart
 * @property int $id_ciudad
 * @property string $cargo
 * @property string $contraseña
 * 
 * @property Pai $pai
 * @property Departamento $departamento
 * @property Ciudad $ciudad
 * @property Collection|Factura[] $facturas
 *
 * @package App\Models
 */
class Trabajadore extends Model
{
	protected $table = 'trabajadores';
	protected $primaryKey = 'id_trab';
	public $timestamps = false;

	protected $casts = [
		'cedula' => 'int',
		'id_pais' => 'int',
		'id_depart' => 'int',
		'id_ciudad' => 'int'
	];

	protected $fillable = [
		'cedula',
		'nombre',
		'apellido',
		'id_pais',
		'id_depart',
		'id_ciudad',
		'cargo',
		'contraseña'
	];

	public function pai()
	{
		return $this->belongsTo(Pai::class, 'id_pais');
	}

	public function departamento()
	{
		return $this->belongsTo(Departamento::class, 'id_depart');
	}

	public function ciudad()
	{
		return $this->belongsTo(Ciudad::class, 'id_ciudad');
	}

	public function facturas()
	{
		return $this->hasMany(Factura::class, 'id_trab');
	}
}
