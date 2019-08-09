<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Modulo;

class AccesoController extends Controller
{

    public function __construct()
    {
        $this->middleware("origen");
    }
    
    public function user()
    {
        $users = User::paginate(20);
        return view("users.index", compact('users'));
    }

    public function role()
    {
        $roles = Role::paginate(20);
        return view("roles.index", compact('roles'));
    }

    public function modulo()
    {
        $modulos = Modulo::with("modulos")
            ->where("modulo_id", null)
            ->paginate(20);

        return view("modulos.index", compact('modulos'));
    }

}
