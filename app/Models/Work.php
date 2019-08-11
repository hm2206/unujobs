<?php
/**
 * Models/Work.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo de la tabla works
 * 
 * @category Models
 */
class Work extends Model
{

    use SoftDeletes;
    

    protected $dates = ['deleted_at'];

    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "ape_paterno", "ape_materno", "nombres","numero_de_documento",
        "fecha_de_nacimiento", "profesion", "phone","fecha_de_ingreso",
        "sexo", "numero_de_essalud", "banco_id", "numero_de_cuenta", "descanso",
        "afp_id", "type_afp", "fecha_de_afiliacion", "numero_de_cussp", "accidentes",
        "sindicato_id", "nombre_completo", "direccion", "total", "profesion", "email"
    ];


    /**
     * Relacion de uno a muchos con la tabla bancos
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }


    /**
     * Relacion de muchos a muchos con la tabla sindicatos
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sindicatos()
    {
        return $this->belongsToMany(Sindicato::class, 'sindicato_work');
    }


    /**
     * Relacion de uno a muchos con la tabla afps
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afp()
    {
        return $this->belongsTo(Afp::class);
    }


    /**
     * Relacion de muchos a uno con la tabla infos
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function infos()
    {
        return $this->hasMany(Info::class);
    }


    /**
     * Relacion de muchos a uno con la tabla obligacions
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function obligaciones()
    {
        return $this->hasMany(Obligacion::class);
    }


    /**
     * Slug para proteger los id en las urls
     *
     * @return string
     */
    public function slug()
    {
        return base64_encode($this->id);
    }


    public function remuneraciones()
    {
        return $this->hasMany(Remuneracion::class);
    }

    public function cronogramas()
    {
        return $this->belongsToMany(Work::class, "work_cronograma");
    }

}
