<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cliente
 * 
 * @property int $id_cliente
 * @property int $cedula
 * @property string $nombre_cl
 * @property int|null $celular
 * @property string|null $correo
 * @property int $id_pais
 * @property int $id_depart
 * @property int $id_ciudad
 * 
 * @property Pai $pai
 * @property Departamento $departamento
 * @property Ciudad $ciudad
 * @property Collection|Factura[] $facturas
 *
 * @package App\Models
 */
class Cliente extends Model
{
	use HasFactory;

	protected $table = 'cliente';
	protected $primaryKey = 'id_cliente';
	public $timestamps = false;

	protected $casts = [
		'cedula' => 'int',
		'celular' => 'int',
		'id_pais' => 'int',
		'id_depart' => 'int',
		'id_ciudad' => 'int'
	];

	protected $fillable = [
		'cedula',
		'nombre_cl',
		'celular',
		'correo',
		'id_pais',
		'id_depart',
		'id_ciudad'
	];

	public function pais()
	{
		return $this->belongsTo(Pais::class, 'id_pais');
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
		return $this->hasMany(Factura::class, 'cliente', 'id_cliente');
	}
}
