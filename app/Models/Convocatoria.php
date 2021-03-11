<?php
/**
 * Models/Convocatoria.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla convocatorias
 * 
 * @category Models
 */
class Convocatoria extends Model
{
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "numero_de_convocatoria", "observacion", "fecha_inicio", "fecha_final",
        "aceptado"
    ];


    /**
     * Relacion de muchos a uno con la tabla personals
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function personals()
    {
        return $this->hasMany(Personal::class);
    }


    /**
     * Relacion de muchos a uno con la tabla actividads
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actividades()
    {
        return $this->hasMany(Actividad::class);
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
