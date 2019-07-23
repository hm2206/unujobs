<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Sede;
use App\Models\Dependencia;
use App\Models\Ubigeo;
use App\Models\TypeEtapa;

class ConfigSeed extends Seeder
{
  
    
    public function run()
    {
        
        self::users();
        self::roles();
        self::sedes();
        self::dependencias();
        self::ubigeos();
        self::typeEtapas();

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
            ["key" => "mod", "name" => "Moderador"],
            ["key" => "basic", "name" => "Consultor"]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }


    public function sedes() 
    {
        Sede::truncate();
        $sedes = [
            ["descripcion" => "Pucallpa"]
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

    public function ubigeos()
    {
        Ubigeo::truncate();

        $ubigeos = [
            ["descripcion" => "Ucayali - Pucallpa"],
            ["descripcion" => "Coronel Portillo", "departamento_id" => "1"],
            ["descripcion" => "Calleria", "provincia_id" => "2"],
            ["descripcion" => "Yarina", "provincia_id" => "2"],
            ["descripcion" => "Manantay", "provincia_id" => "2"]
        ];

        foreach ($ubigeos as $ubigeo) {
            Ubigeo::create($ubigeo);
        }
    }

    public function typeEtapas()
    {
        TypeEtapa::truncate();

        $types = [
            ["descripcion" => "Acta Curricular", "icono" => "fas fa-file-pdf"],
            ["descripcion" => "Acta de Conocimiento", "icono" => "fas fa-file"],
            ["descripcion" => "Entrevista Personal", "icono" => "fas fa-user"]
        ];

        foreach ($types as $type) {
            TypeEtapa::create($type);
        }
    }

}
