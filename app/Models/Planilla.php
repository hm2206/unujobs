<?php
/**
 * Models/Planilla.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Modelo de la tabla planillas
 * 
 * @category Models
 */
class Planilla extends Model
{
    
    /**
     * Relacion de mucho a uno con la tabla cargos
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }

}
