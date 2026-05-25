<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Professor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        if (! User::where('email', 'admin@earist.local')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@earist.local',
                'password' => 'admin123',
                'role' => 'admin',
            ]);
        }

        // Professor account
        if (! User::where('email', 'professor@earist.local')->exists()) {
            $professorUser = User::create([
                'name' => 'Dr. John Smith',
                'email' => 'professor@earist.local',
                'password' => 'professor123',
                'role' => 'professor',
                'employee_id' => 'PROF001',
            ]);

            Professor::create([
                'user_id' => $professorUser->id,
                'name' => 'Dr. John Smith',
                'subject_code' => 'CS101',
                'year_section' => '1st Year - A',
            ]);
        }
    }
}
