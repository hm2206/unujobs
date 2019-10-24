<?php
/**
 * Models/Remuneracion.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla remuneracions
 * 
 * @category Models
 */
class Remuneracion extends Model
{

    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "work_id", "info_id", "historial_id", "categoria_id", "dias", "cronograma_id",
        "sede_id", "mes", "año", "monto", "adicional", "type_remuneracion_id", 
        'cargo_id', 'base', 'planilla_id', "meta_id", "show", "edit"
    ];
    

    /**
     * Relacion de uno a mucho con la tabla conceptos
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function concepto()
    {
        return $this->belongsTo(Concepto::class);
    }


    /**
     * Relacion de uno a mucho con la tabla type_remuneracions
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typeRemuneracion()
    {
        return $this->belongsTo(TypeRemuneracion::class);
    }

}
