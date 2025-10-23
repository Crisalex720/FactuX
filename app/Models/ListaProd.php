<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ListaProd
 * 
 * @property int $id_lista
 * @property int $id_fact
 * @property int $id_producto
 * @property float $cantidad
 * @property string $estado
 * 
 * @property Factura $factura
 * @property Producto $producto
 * @property Collection|Devolucione[] $devoluciones
 *
 * @package App\Models
 */
class ListaProd extends Model
{
	protected $table = 'lista_prod';
	protected $primaryKey = 'id_lista';
	public $timestamps = true;

	protected $casts = [
		'id_fact' => 'int',
		'id_producto' => 'int',
		'cantidad' => 'float',
		'fecha_producto' => 'datetime',
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
	];

	protected $fillable = [
		'id_fact',
		'id_producto',
		'cantidad',
		'estado',
		'fecha_producto'
	];

	public function factura()
	{
		return $this->belongsTo(Factura::class, 'id_fact');
	}

	public function producto()
	{
		return $this->belongsTo(Producto::class, 'id_producto');
	}

	public function devoluciones()
	{
		return $this->hasMany(Devolucione::class, 'id_lista');
	}
}
