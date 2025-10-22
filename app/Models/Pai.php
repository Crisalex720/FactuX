<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Pai
 * 
 * @property int $id_pais
 * @property string $nombre_pais
 * 
 * @property Collection|Cliente[] $clientes
 * @property Collection|Departamento[] $departamentos
 * @property Collection|Trabajadore[] $trabajadores
 *
 * @package App\Models
 */
class Pai extends Model
{
	protected $table = 'pais';
	protected $primaryKey = 'id_pais';
	public $timestamps = false;

	protected $fillable = [
		'nombre_pais'
	];

	public function clientes()
	{
		return $this->hasMany(Cliente::class, 'id_pais');
	}

	public function departamentos()
	{
		return $this->hasMany(Departamento::class, 'id_pais');
	}

	public function trabajadores()
	{
		return $this->hasMany(Trabajadore::class, 'id_pais');
	}
}
