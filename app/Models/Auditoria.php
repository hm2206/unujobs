<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    
    protected $fillable = [
        "ip", "body", "user_id", "client_agent"
    ];

}
