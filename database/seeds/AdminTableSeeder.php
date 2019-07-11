<?php

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'email'    => 'gaara1759@gmail.com',
            'name'     => 'root',
            'password' => '12345678',
        ]);
    }
}
