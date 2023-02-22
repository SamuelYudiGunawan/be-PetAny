<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('roles')->insert([
        //     [
        //         'name' => 'admin',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'name' => 'petshop_staff',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'name' => 'petshop_owner',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'name' => 'dokter',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'name' => 'customer',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'name' => 'cashier',
        //         'guard_name' => 'web',
        //     ],
        // ]);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'cashier']);
        Role::create(['name' => 'product_manager']);
        Role::create(['name' => 'dokter']);
        Role::create(['name' => 'petshop_staff']);
        Role::create(['name' => 'customer']);
    }
}
