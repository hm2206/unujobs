<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(ConfigSeed::class);
        $this->call(TableModuleSeed::class);
        $this->call(ConfigAportes::class);
        $this->call(GenerarBoletas::class);
    }
}
