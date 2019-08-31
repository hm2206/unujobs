<?php
/**
 * Models/Descuento.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla descuentos
 * 
 * @category Models
 */
class Descuento extends Model
{
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "work_id", "info_id", "categoria_id", "dias", "cronograma_id",
        "observaciones", "sede_id", "mes", "año", "monto", "adicional",
        "horas", "type_descuento_id", "cargo_id", "planilla_id", "base",
        "meta_id", "edit"
    ];


    /**
     * Relacion de uno a mucho con la tabla type_remuneracions
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typeDescuento()
    {
        return $this->belongsTo(TypeDescuento::class, 'type_descuento_id');
    }

}
