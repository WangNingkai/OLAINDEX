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
        $data = [
            'name' => 'admin',
            'email' => 'i@ningkai.wang',
            'password' => bcrypt('123456'),
            'status' => 1,
        ];
        if (\Schema::hasColumn('users', 'remember_token')) {
            $data['remember_token'] = str_random(10);
        }
        \DB::table('users')->insertOrIgnore($data);
    }
}
