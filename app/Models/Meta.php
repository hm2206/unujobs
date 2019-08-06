<?php
/**
 * Models/Meta.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla metas
 * 
 * @category Models
 */
class Meta extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "id", "metaID", "meta", "sectorID", "sector",
        "pliegoID", "pliego", "unidadID", "unidad_ejecutora",
        "programa", "programaID", "funcionID", "funcion",
        "subProgramaID", "sub_programa", "actividadID",
        "actividad"
    ];


    /**
     * Relacion de mucho a uno con la tabla works
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function works()
    {
        return $this->hasMany(Work::class);
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
