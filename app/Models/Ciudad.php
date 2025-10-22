<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ciudad
 * 
 * @property int $id_ciudad
 * @property string|null $nombre_ciudad
 * @property int $id_depart
 * 
 * @property Departamento $departamento
 * @property Collection|Cliente[] $clientes
 * @property Collection|Trabajadore[] $trabajadores
 *
 * @package App\Models
 */
class Ciudad extends Model
{
	use HasFactory;

	protected $table = 'ciudad';
	protected $primaryKey = 'id_ciudad';
	public $timestamps = false;

	protected $casts = [
		'id_depart' => 'int'
	];

	protected $fillable = [
		'nombre_ciudad',
		'id_depart'
	];

	public function departamento()
	{
		return $this->belongsTo(Departamento::class, 'id_depart', 'id_depart');
	}

	public function clientes()
	{
		return $this->hasMany(Cliente::class, 'id_ciudad');
	}

	public function trabajadores()
	{
		return $this->hasMany(Trabajadore::class, 'id_ciudad');
	}
}
