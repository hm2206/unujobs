<?php
/**
 * Models/TypeDescuento.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla type_descuentos
 * 
 * @category Models
 */
class TypeDescuento extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "descripcion", "key", "config_afp", "obligatorio", "base", "activo", "edit", "retencion"
    ];


    /**
     * Relacion de muchos a muchos con la tabla sindicatos
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sindicatos()
    {
        return $this->belongsToMany(Sindicato::class, 'descuento_sindicato');
    }


    /**
     * Relacion de muchos a muchos con la tabla afps
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function seguros()
    {
        return $this->belongsToMany(Afp::class, 'descuento_afp');
    }


    public function config()
    {
        return $this->hasOne(ConfigDescuento::class);
    }


    public function descuentos()
    {
        return $this->hasMany(TypeDescuento::class);
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
