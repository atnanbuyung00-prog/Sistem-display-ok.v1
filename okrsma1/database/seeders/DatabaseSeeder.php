<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Public source extraction: patient/staff/clinical sample data omitted.
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin'),
                'permissions' => [
                    'dashboard.view', 'schedule.view', 'schedule.manage',
                    'schedule.export', 'master.manage', 'shift.manage',
                    'shift.export', 'user.manage', 'display.view',
                ],
            ]
        );
    }
}
