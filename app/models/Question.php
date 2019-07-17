<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    
    protected $fillable = [
        "requisito", "body", "personal_id"
    ];

}
