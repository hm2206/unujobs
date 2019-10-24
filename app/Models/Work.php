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
        "ape_paterno", "ape_materno", "nombres", "nombre_completo",
        "direccion", "tipo_de_documento", "numero_de_documento", 
        "fecha_de_nacimiento", "profesion", "email", "phone", "sexo", 
        "activo"
    ];


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
     * Relacion de muchos a uno con la tabla infos
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function historial()
    {
        return $this->hasMany(Historial::class);
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

}
