<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seeds the single admin account used to log into /admin.
     * Idempotent — safe to re-run without creating duplicates.
     *
     * NOTE: the default password below MUST be changed after first login.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bagusbatra.dev'],
            [
                'name' => 'Bagus Batra Admin',
                'password' => Hash::make('Admin#12345'),
                'email_verified_at' => now(),
            ]
        );
    }
}
