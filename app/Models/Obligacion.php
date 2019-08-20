<?php
/**
 * Models/Obligacion.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla obligaciones
 * 
 * @category Models
 */
class Obligacion extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "beneficiario", "numero_de_documento", "numero_de_cuenta", 
        "monto", "cronograma_id", "work_id", "categoria_id"
    ];

}
