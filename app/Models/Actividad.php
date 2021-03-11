<?php
/**
 * Models/Actividad.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla actividads
 * 
 * @category Models
 */
class Actividad extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "descripcion", "fecha_inicio", "fecha_final", "responsable", "convocatoria_id"
    ];

}
