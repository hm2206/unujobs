<?php

use Illuminate\Database\Seeder;
use App\Models\Modulo;

class TableModuleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Modulo::truncate();

        $modulos = [
            ["name" => "Recursos Humanos", "ruta" => "", "token" => bcrypt(date('Y-m-d') . rand(1, 100)), "icono" => "fas fa-fw fa-users"],
            ["name" => "Planillas", "ruta" => "", "token" => bcrypt(date('Y-m-d') . rand(1, 100)), "icono" => "fas fa-fw fa-file"],
            ["name" => "Accesos", "ruta" => "", "token" => bcrypt(date('Y-m-d') . rand(1, 100)), "icono" => "fas fa-fw fa-lock"],
            ["name" => "Usuarios", "ruta" => "/accesos/user", "token" => bcrypt(date('Y-m-d') . rand(1, 100)), "modulo_id" => 3],
            ["name" => "Rol", "ruta" => "/accesos/role", "token" => bcrypt(date('Y-m-d') . rand(1, 100)), "modulo_id" => 3],
            ["name" => "Modulos", "ruta" => "/accesos/modulo", "token" => bcrypt(date('Y-m-d') . rand(1, 100)), "modulo_id" => 3],
        ];

        foreach ($modulos as $modulo) {
            Modulo::create($modulo);
        }
        
    }

}
