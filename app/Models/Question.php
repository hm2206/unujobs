<?php
/**
 * Models/Question.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la tabla questions
 * 
 * @category Models
 */
class Question extends Model
{
    
    /**
     * Los campos que solo serán alterados en la base de datos
     *
     * @var array
     */
    protected $fillable = [
        "requisito", "body", "personal_id"
    ];

}
