<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'superadmin',
            'email' => 'superadmin@petany.com',
            'password' => Hash::make('qazwsxedc'),
            'phone_number' => '0895350785800',
            'petshop_id' => null,
        ]);
        $user = User::where('email', 'superadmin@petany.com')->firstOrFail();
        $user->assignRole('admin'); 
    }
}
