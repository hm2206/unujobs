<?php
/**
 * Models/TypeRemuneracion.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla type_remuneracions
 * 
 * @category Models
 */
class TypeRemuneracion extends Model
{
    /**
    * Los campos que solo serán alterados en la base de datos
    *
    * @var array
    */
    protected $fillable = [
        "key", "descripcion", "monto", "base", "bonificacion", "activo"
    ];


    public function conceptos()
    {
        return $this->belongsToMany(Concepto::class, 'concepto_type_remuneracion');
    }

    public function remuneraciones()
    {
        return $this->hasMany(Remuneracion::class);
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
