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
        "work_id", "info_id", "historial_id", "planilla_id", "categoria_id", 
        "cargo_id", "cronograma_id", "dias", "sede_id", "meta_id", "mes", "año",
        "monto", "adicional", "horas", "type_descuento_id", "base", "edit", "orden"
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


    public function historial()
    {
        return $this->belongsTo(Historial::class);
    }


    public function work()
    {
        return $this->belongsTo(Work::class);
    }

}
