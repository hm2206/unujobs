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
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sindicato()
    {
        return $this->hasOne(Sindicato::class);
    }


    public function config()
    {
        return $this->hasOne(ConfigDescuento::class);
    }


    public function descuentos()
    {
        return $this->hasMany(TypeDescuento::class);
    }


    public function afp_primas()
    {
        return $this->hasMany(Afp::class, 'prima_descuento_id');
    }


    public function afp_aportes()
    {
        return $this->hasMany(Afp::class, 'aporte_descuento_id');
    }


    public function type_afp()
    {
        return $this->hasOne(TypeAfp::class);
    }

    
    public function type_aportacion()
    {
        return $this->hasOne(TypeAportacion::class);
    }


    public function type_educacionales() 
    {
        return $this->hasMany(TypeEducacional::class);
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
