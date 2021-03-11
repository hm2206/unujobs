<?php
/**
 * Models/TypeEtapa.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla type_etapas
 * 
 * @category Models
 */
class TypeEtapa extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = ['descripcion', 'icono', 'fin'];


    /**
     * Relacion de muchos a muchos con la tabla postulantes
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function postulantes()
    {
        return $this->belongsToMany(Postulante::class, 'etapas');
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
