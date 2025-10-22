<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Producto
 * 
 * @property int $id_producto
 * @property int $barcode
 * @property string $nombre_prod
 * @property float $cantidad_prod
 * @property float $precio_costop
 * @property float $precio_ventap
 * @property string|null $imagen_url
 * 
 * @property Collection|Devolucione[] $devoluciones
 * @property Collection|ListaProd[] $lista_prods
 *
 * @package App\Models
 */
class Producto extends Model
{
	protected $table = 'producto';
	protected $primaryKey = 'id_producto';
	public $timestamps = false;

	protected $casts = [
		'barcode' => 'int',
		'cantidad_prod' => 'float',
		'precio_costop' => 'float',
		'precio_ventap' => 'float'
	];

	protected $fillable = [
		'barcode',
		'nombre_prod',
		'cantidad_prod',
		'precio_costop',
		'precio_ventap',
		'imagen_url'
	];

	public function devoluciones()
	{
		return $this->hasMany(Devolucione::class, 'id_prod');
	}

	public function lista_prods()
	{
		return $this->hasMany(ListaProd::class, 'id_producto');
	}
}
