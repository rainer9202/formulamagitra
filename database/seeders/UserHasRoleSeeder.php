<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserHasRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Example provider',
            'email' => 'provider@example.com',
            'password' => Hash::make('password'),
            'active' => 1
        ]);

        User::create([
            'name' => 'Example client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'active' => 1
        ]);

        Client::create([
            'user_id' => 3,
            'nif' => '920201227722',
            'description' => 'Un super cliente',
            'city' => 'Coruna',
            'province' => 'Galicia',
            'address' => 'Carr Temple Cambre 122',
            'postal_code' => '10400',
            'phone' => '34613762528',
        ]);

        Provider::create([
            'user_id' => 2,
            'nif' => '920201227722',
            'description' => 'Un super provider',
            'logo' => '',
            'city' => 'Coruna',
            'province' => 'Galicia',
            'address' => 'Carr Temple Cambre 122',
            'postal_code' => '10400',
            'phone' => '34613762528',
        ]);

        DB::table('roles')->insert([
            'name' => 'proveedor',
            'guard_name' => 'web',
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ]);

        DB::table('roles')->insert([
            'name' => 'cliente',
            'guard_name' => 'web',
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => 2,
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => 3,
            'model_type' => 'App\Models\User',
            'model_id' => 3,
        ]);
    }
}
