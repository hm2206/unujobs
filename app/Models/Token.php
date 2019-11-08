<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    
    protected $fillable = [
        "type", "is_revoked", "user_agent", "address_ip",
        "token", "expires_in", "user_id"
    ];

}
