<?php
/**
 * Models/TypeCategoria.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla type_categorias
 * 
 * @category Models
 */
class TypeCategoria extends Model
{

    /**
     * Relacion de mucho a uno con la tabla conceptos
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }

}
