<?php
/**
 * Models/Sede.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla sedes
 * 
 * @category Models
 */
class Sede extends Model
{
    
    public function dependencias() 
    {
        return $this->belongsToMany(Dependencia::class, 'oficinas');
    }


    public function oficinas()
    {
        return $this->hasMany(Oficina::class);
    }

}
