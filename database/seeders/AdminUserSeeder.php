<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin',  'email' => 'admin@blog.it',  'role' => 'admin'],
            ['name' => 'Editor', 'email' => 'editor@blog.it', 'role' => 'editor'],
            ['name' => 'Author', 'email' => 'author@blog.it', 'role' => 'author'],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password')])
            );
        }

        $this->command->info('Utenti creati (password: "password"):');
        foreach ($users as $u) {
            $this->command->line("  {$u['email']}  [{$u['role']}]");
        }
    }
}
