<?php

use Illuminate\Database\Seeder;
use App\Models\Info;

class ConfigAportes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $infos = Info::where('planilla_id', '<>', 4)->get();

        foreach ($infos as $info) {
            $info->type_aportaciones()->sync([1]);
        }
        
    }
}
