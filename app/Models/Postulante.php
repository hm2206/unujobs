<?php
/**
 * Models/Postulante.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla postulante
 * 
 * @category Models
 */
class Postulante extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "ape_paterno", "ape_materno", "nombres", "numero_de_documento",
        "departamento_id", "provincia_id", "distrito_id", "fecha_de_nacimiento",
        "phone", "email", 'cv', 'nombre_completo'
    ];


    /**
     * Relacion de uno a mucho con la tabla etapas
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function etapas()
    {
        return $this->hasMany(Etapa::class);
    }

}
