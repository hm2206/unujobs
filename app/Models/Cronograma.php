<?php
/**
 * Models/Cronograma.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla cronogramas
 * 
 * @category Models
 */
class Cronograma extends Model
{

    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "mes", "año", "planilla_id", "adicional", "sede_id", "numero", "dias",
        "pdf", "pendiente", "observacion"
    ];
    

    /**
     * Relacion de uno a mucho con la tabla sedes
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }


    /**
     * Relacion de uno a mucho con la tabla planillas
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function planilla()
    {
        return $this->belongsTo(Planilla::class);
    }


    /**
     * Slug para proteger los id en las urls
     *
     * @return string
     */
    public function slug()
    {
        return \base64_encode($this->id);
    }

}
