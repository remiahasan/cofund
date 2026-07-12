<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'=>'Administrator',
            'email'=>'admin@cofund.test',
            'password'=>Hash::make('password123'),
            'role'=>'admin',
            'balance'=>0,
            'email_verified_at'=>now()
        ]);

        User::create([
            'name'=>'Budi Creator',
            'email'=>'creator@cofund.test',
            'password'=>Hash::make('password123'),
            'role'=>'creator',
            'balance'=>15000000,
            'email_verified_at'=>now()
        ]);

        User::create([
            'name'=>'Andi Backer',
            'email'=>'backer@cofund.test',
            'password'=>Hash::make('password123'),
            'role'=>'backer',
            'balance'=>3000000,
            'email_verified_at'=>now()
        ]);
    }
}