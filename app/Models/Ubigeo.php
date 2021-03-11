<?php
/**
 * Models/Ubigeo.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla type_remuneracions
 * 
 * @category Models
 */
class Ubigeo extends Model
{

    /**
     * Relacion de muchos a uno con la tabla ubigeos
     *
     * Donde la llave foranéa es departamento_id
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function departamentos()
    {
        return $this->hasMany(Ubigeo::class, 'departamento_id');
    }
    
    /**
     * Relacion de muchos a uno con la tabla ubigeos
     *
     * Donde la llave foranéa es provincia_id
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function provincias() 
    {
        return $this->hasMany(Ubigeo::class, 'provincia_id');
    }

}
