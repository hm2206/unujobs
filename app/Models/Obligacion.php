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
        "work_id", "info_id", "historial_id", "cronograma_id", "type_descuento_id",
        "beneficiario", "numero_de_documento", "numero_de_cuenta", "monto",
    ];

}
