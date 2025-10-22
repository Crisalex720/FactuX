<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Devolucione
 * 
 * @property int $id_devol
 * @property int $id_fact
 * @property int $id_lista
 * @property int $id_prod
 * @property string $motivo
 * 
 * @property Factura $factura
 * @property ListaProd $lista_prod
 * @property Producto $producto
 *
 * @package App\Models
 */
class Devolucione extends Model
{
	protected $table = 'devoluciones';
	protected $primaryKey = 'id_devol';
	public $timestamps = false;

	protected $casts = [
		'id_fact' => 'int',
		'id_lista' => 'int',
		'id_prod' => 'int'
	];

	protected $fillable = [
		'id_fact',
		'id_lista',
		'id_prod',
		'motivo'
	];

	public function factura()
	{
		return $this->belongsTo(Factura::class, 'id_fact');
	}

	public function lista_prod()
	{
		return $this->belongsTo(ListaProd::class, 'id_lista');
	}

	public function producto()
	{
		return $this->belongsTo(Producto::class, 'id_prod');
	}
}
