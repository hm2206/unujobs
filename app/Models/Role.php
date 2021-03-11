<?php
/**
 * Models/Role.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla roles
 * 
 * @category Models
 */
class Role extends Model
{

    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = ["key", "name"];


    public function users()
    {
        return $this->belongsToMany(User::class, "role_user");
    }

}
