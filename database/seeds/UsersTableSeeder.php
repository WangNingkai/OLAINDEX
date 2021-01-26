<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'i@ningkai.wang',
            'password' => bcrypt('123456'),
            'status' => 1,
            'remember_token' => str_random(10),
        ]);
    }
}
