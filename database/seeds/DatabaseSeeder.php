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
        DB::table('users')->insert([
            'name'         => 'Administrator',
            'email'         => 'super@admin.com',
            'role'            => 'admin',
            'api_token'   => bcrypt('super@admin.com'),
            'password'    => bcrypt('password'),
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

    }
}
