<?php
/**
 * Models/Etapa.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Modelo de la tabla etapas
 * 
 * @category Models
 */
class Etapa extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "postulante_id", "type_etapa_id", "convocatoria_id",
        "personal_id", "current", "next", 'puntaje'
    ];

}
