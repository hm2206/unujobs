<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    
    public function unread()
    {   
        $user = auth()->user();
        return $user->unreadNotifications->take(10);
    }

    public function markAsRead($id) 
    {
        $user = auth()->user();
        foreach ($user->notifications as $notify) {
            if ($notify->id == $id) {
                $notify->markAsRead();
                break;
            }
        }

        return $user->unreadNotifications;
    }

}
