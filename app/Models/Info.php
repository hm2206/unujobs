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
        "work_id", "planilla_id", "cargo_id", "categoria_id",
        "meta_id", "fuente_id", "sindicato_id", "afp_id",
        "type_afp_id", "numero_de_cussp", "fecha_de_afiliacion",
        "banco_id", "numero_de_cuenta", "numero_de_essalud",
        "fuente", "plaza", "perfil", "escuela", "ruc", "pap",
        "fecha_de_ingreso", "fecha_de_cese", "afecto", "active"
    ];

    /**
     * Relacion de uno a mucho con la tabla works
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function work()
    {
        return $this->belongsTo(Work::class);
    }


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
     * Relacion de muchos a mucho con la tabla cronogramas
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cronogramas()
    {
        return $this->belongsToMany(Cronograma::class, 'info_cronograma');
    }

    /**
     * Relacion de muchos a mucho con la tabla remuneracions
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function remuneraciones()
    {
        return $this->hasMany(Remuneracion::class);
    }


    /**
     * Relacion de muchos a mucho con la tabla descuentos
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function descuentos()
    {
        return $this->hasMany(Descuento::class);
    }

    /**
     * Relacion de uno a mucho con la tabla sindicatos
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sindicato()
    {
        return $this->belongsTo(Sindicato::class);
    }


    /* Relacion de uno a mucho con la tabla sindicatos
    *
    * @return Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function obligaciones()
    {
        return $this->hasMany(Obligacion::class);
    }

    
    /**
     * Relacion de muchos a muchos con la tabla type_aportacions
     *
     * @return void
     */
    public function type_aportaciones()
    {
        return $this->belongsToMany(TypeAportacion::class);
    }


    public function historial()
    {
        return $this->hasMany(Historial::class);
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
