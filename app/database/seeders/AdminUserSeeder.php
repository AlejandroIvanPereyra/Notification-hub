<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');

        DB::table('users')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role_id'  => $adminRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
