<?php
/**
 * Models/Concepto.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Modelo de la tabla conceptos
 * 
 * @category Models
 */
class Concepto extends Model
{
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = ["key", "descripcion"];


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
