<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Sede;
use App\Models\Dependencia;

class ConfigSeed extends Seeder
{
  
    
    public function run()
    {
        
        self::users();
        self::roles();
        self::sedes();
        self::dependencias();

    }

    public function users()
    {
        User::truncate();

        $payload = [
            "ape_paterno" => "medina",
            "ape_materno" => "gonzales",
            "nombres" => "hans",
            "nombre_completo" => "medina gonzales hans",
            "email" => "twd2206@gmail.com",
            "password" => bcrypt(123456789)
        ];

        User::create($payload);
    }

    
    public function roles()
    {
        Role::truncate();

        $roles = [
            ["key" => "admin", "name" => "Administrador"],
            ["key" => "job", "name" => "Personal"],
            ["key" => "postulate", "name" => "Postulante"]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }


    public function sedes() 
    {
        Sede::truncate();
        $sedes = [
            ["descripcion" => "pucallpa"]
        ];

        foreach($sedes as $sede) {
            Sede::create($sede);
        }
    }


    public function dependencias()
    {
        Dependencia::truncate();
        $dependencias = [
            ["descripcion" => "Recursos Humanos"]
        ];

        foreach($dependencias as $dependencia) {
            Dependencia::create($dependencia);
        }
    }

}
