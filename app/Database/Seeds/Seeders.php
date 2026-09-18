<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Seeders extends Seeder
{
    public function run()
    {
        // $this->call(SeedRoleGroup::class);
        // $this->call(SeedRole::class);
        // $this->call(SeedKelompokOpd::class);
        // $this->call(SeedOpd::class);
        // $this->call(SeedBidang::class);
        // $this->call(SeedKegiatanKategori::class);
        // $this->call(SeedUser::class);
        // $this->call(SeedUserBidang::class);
        $this->call(BarangSeeder::class);
        $this->call(DimensiSeeder::class);
        $this->call(KelompokOpdSeeder::class);
        $this->call(OpdSeeder::class);
        $this->call(VariabelSeeder::class);
        $this->call(SubVariabelSeeder::class);
    }
}
