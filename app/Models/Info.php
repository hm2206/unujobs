<?php
/**
 * Models/Info.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Modelo de la tabla infos
 * 
 * @category Models
 */
class Info extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "categoria_id", "cargo_id", "work_id", "active", "observacion", 
        'meta_id', 'total', 'perfil', 'planilla_id', 'fuente_id', 'plaza',
        'escuela', 'observacion', 'ruc'
    ];


    /**
     * Relacion de uno a mucho con la tabla cargos
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }


    /**
     * Relacion de uno a mucho con la tabla categorias
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
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
