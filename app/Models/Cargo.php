<?php
/**
 * Models/Banco.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla cargos
 * 
 * @category Models
 */
class Cargo extends Model
{
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = ["descripcion", "planilla_id", "tag", "ext_pptto"];


    /**
     * Slug para proteger los id en las urls
     *
     * @return string
     */
    public function slug()
    {
        return \base64_encode($this->id);
    }

    /**
     * Relacion de muchos a muchos con la tabla categorias
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'cargo_categoria');
    }


    /**
     * Relación de uno a muchos con la tabla type_categorias
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typeCategoria()
    {
        return $this->belongsTo(TypeCategoria::class);
    }


    /**
     * RElación de muchos a muchos con la tabla type_remuneraciones
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function typeRemuneracions()
    {
        return $this->belongsToMany(TypeRemuneracion::class, 'cargo_type_remuneracion');
    }


    /**
     * Relación de uno a muchos con la tabla planillas
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function planilla()
    {
        return $this->belongsTo(Planilla::class);
    }


    public function type_remuneraciones()
    {
        return $this->belongsToMany(TypeRemuneracion::class, 'cargo_type_remuneracion');
    }

}
