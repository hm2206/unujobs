<?php
/**
 * Models/Personal.php
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
class Personal extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "numero_de_requerimiento", "sede_id", "dependencia_txt",
        "cargo_txt", "cantidad", "honorarios", "meta_id", "deberes",
        "fecha_inicio", "fecha_final", "lugar_txt", "bases", 
        'supervisora_txt', 'aceptado', 'convocatoria_id', 'slug'
    ];


    /**
     * Relacion de uno a mucho con la tabla sedes
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function sede()
    {
        return $this->belongsTo(Sede::class);
    }


    /**
     * Relacion de uno a mucho con la tabla metas
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }


    /**
     * Relacion de mucho a uno con la tabla questions
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Relacion de mucho a muchos con la tabla postulantes
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function postulantes()
    {
        return $this->belongsToMany(Postulante::class, 'etapas');
    }


    /**
     * Sirve para cambiar de slug y proteger el id
     *
     * @return string
     */
    public function changeSlug($replace, $id)
    {
        return \str_replace(" ", "-", $replace) . "-" . date('Y') . "-" . base64_encode($id);
    }

}
