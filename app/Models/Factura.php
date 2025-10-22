<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Factura
 * 
 * @property int $id_fact
 * @property int $num_fact
 * @property string $prefijo_fact
 * @property int $id_trab
 * @property int $cliente
 * @property string $estado
 * 
 * @property Trabajadore $trabajadore
 * @property Collection|Devolucione[] $devoluciones
 * @property Collection|ListaProd[] $lista_prods
 *
 * @package App\Models
 */
class Factura extends Model
{
	use HasFactory;

	protected $table = 'factura';
	protected $primaryKey = 'id_fact';
	public $timestamps = false;

	protected $fillable = [
		'cliente',
		'id_trab',
		'estado',
		'num_fact',
		'prefijo_fact'
	];

	public function cliente()
	{
		return $this->belongsTo(Cliente::class, 'cliente', 'id_cliente');
	}

	public function trabajador()
	{
		return $this->belongsTo(Trabajadore::class, 'id_trab', 'id_trab');
	}

	public function devoluciones()
	{
		return $this->hasMany(Devolucione::class, 'id_fact');
	}

	public function productos()
	{
		return $this->belongsToMany(Producto::class, 'lista_prod', 'id_fact', 'id_producto')
				   ->withPivot('cantidad');
	}

	public function calcularTotal()
	{
		return $this->productos->sum(function ($producto) {
			return $producto->precio_ventap * $producto->pivot->cantidad;
		});
	}
}
