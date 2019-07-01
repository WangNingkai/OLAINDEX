<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email'    => 'gaara1759@gmail.com',
            'name'     => 'user',
            'password' => '12345678',
        ]);
    }
}
