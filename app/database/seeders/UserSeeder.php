<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $userRoleId = DB::table('roles')->where('name', 'User')->value('id');

        DB::table('users')->insert([
        [
            'username' => 'user1',
            'email'    => 'user1@example.com',
            'password' => Hash::make('user1234'),
            'role_id'  => $userRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'username' => 'user2',
            'email'    => 'user2@example.com',
            'password' => Hash::make('user1234'),
            'role_id'  => $userRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'username' => 'user3',
            'email'    => 'user3@example.com',
            'password' => Hash::make('user1234'),
            'role_id'  => $userRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ],

        ]);
    }
}
