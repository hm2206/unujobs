<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Sede;
use App\Models\Dependencia;
use App\Models\Ubigeo;
use App\Models\TypeEtapa;
use App\Models\Planilla;
use App\Models\Afp;
use App\Models\Banco;
use App\Models\Sindicato;

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
        self::planillas();
        self::afps();
        self::bancos();
        self::sindicatos();

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
            [
                "descripcion" => "Acta Curricular", 
                "icono" => "fas fa-file-pdf", 
                "fin" => 0,
            ],
            [
                "descripcion" => "Acta de Conocimiento", 
                "icono" => "fas fa-file", 
                "fin" => 0
            ],
            [
                "descripcion" => "Entrevista Personal", 
                "icono" => "fas fa-user", 
                "fin" => 1
            ]
        ];

        foreach ($types as $type) {
            TypeEtapa::create($type);
        }
    }


    public function planillas()
    {
        Planilla::truncate();

        $planillas = [
            ["key" => "01", "descripcion" => "Planilla Activos"],
            ["key" => "05", "descripcion" => "Planilla Cas"],
            ["key" => "06", "descripcion" => "Planilla Adicional"]
        ];

        foreach ($planillas as $planilla) {
            Planilla::create($planilla);
        }
    }

    public function afps()
    {
        Afp::truncate();

        $afps = [
            ["nombre" => "HABITAT", "descripcion" => "", "flujo" => "1.47", "mixta" => "0.38", "aporte" => "10.00", "prima" => "1.35"],
            ["nombre" => "INTEGRA", "descripcion" => "", "flujo" => "1.55", "mixta" => "0.00", "aporte" => "10.00", "prima" => "1.35"],
            ["nombre" => "PRIMA", "descripcion" => "", "flujo" => "1.60", "mixta" => "0.18", "aporte" => "10.00", "prima" => "1.35"],
            ["nombre" => "PROFUTURO", "descripcion" => "", "flujo" => "1.69", "mixta" => "0.67", "aporte" => "10.00", "prima" => "1.35"]
        ];

        foreach ($afps as $afp) {
            Afp::create($afp);
        }
    }

    
    public function bancos()
    {

        Banco::truncate();

        $bancos = [
            ["nombre" => "Banco de la Nación"],
            ["nombre" => "Banco Continental"],
            ["nombre" => "Banco de Credito"],
            ["nombre" => "Otros"]
        ];

        foreach ($bancos as $key => $banco) {
            Banco::create($banco);
        }

    }

    public function sindicatos()
    {

        Sindicato::truncate();

        $sindicatos = [
            ["nombre" => "SUTUNU", "porcentaje" => 1.0],
            ["nombre" => "SITUNU", "porcentaje" => 1.0]
        ];

        foreach ($sindicatos as $sindicato) {
            Sindicato::create($sindicato);
        }

    }

}
