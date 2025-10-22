<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Departamento
 * 
 * @property int $id_depart
 * @property string|null $nombre_depart
 * @property int $id_pais
 * 
 * @property Pai $pai
 * @property Collection|Cliente[] $clientes
 * @property Collection|Ciudad[] $ciudads
 * @property Collection|Trabajadore[] $trabajadores
 *
 * @package App\Models
 */
class Departamento extends Model
{
	use HasFactory;

	protected $table = 'departamento';
	protected $primaryKey = 'id_depart';
	public $timestamps = false;

	protected $casts = [
		'id_pais' => 'int'
	];

	protected $fillable = [
		'nombre_depart',
		'id_pais'
	];

	public function pai()
	{
		return $this->belongsTo(Pai::class, 'id_pais');
	}

	public function clientes()
	{
		return $this->hasMany(Cliente::class, 'id_depart');
	}

	public function ciudads()
	{
		return $this->hasMany(Ciudad::class, 'id_depart');
	}

	public function trabajadores()
	{
		return $this->hasMany(Trabajadore::class, 'id_depart');
	}
}
