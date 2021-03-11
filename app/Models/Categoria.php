<?php
/**
 * Models/Categoria.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla categorias
 * 
 * @category Models
 */
class Categoria extends Model
{
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = ["nombre", "key"];


    /**
     * Relacion de muchos a muchos con la tabla categorias
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cargos()
    {
        return $this->belongsToMany(Cargo::class);
    }


    /**
     * Relación de muchos a muchos con la tabla conceptos
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function conceptos()
    {
        return $this->belongsToMany(Concepto::class, 'categoria_concepto')->withPivot('monto');
    }


    public function type_remuneraciones()
    {
        return $this->belongsToMany(TypeRemuneracion::class, 'categoria_type_remuneracion');
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
