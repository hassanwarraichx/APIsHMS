<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\Specialization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $doctorRole = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'api']);
        $patientRole = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'api']);


        // Create Specializations
        $cardio = Specialization::firstOrCreate(['name' => 'Cardiology']);
        $neuro  = Specialization::firstOrCreate(['name' => 'Neurology']);
        $pedia  = Specialization::firstOrCreate(['name' => 'Pediatrics']);

        // Create Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole($adminRole);

        // Create Doctors
        $doctors = [
            ['name' => 'Dr. Ayesha', 'email' => 'ayesha@hospital.com', 'spec' => $cardio->id],
            ['name' => 'Dr. Bilal',  'email' => 'bilal@hospital.com',  'spec' => $neuro->id],
            ['name' => 'Dr. Zara',   'email' => 'zara@hospital.com',   'spec' => $pedia->id],
        ];

        foreach ($doctors as $doc) {
            $user = User::create([
                'name' => $doc['name'],
                'email' => $doc['email'],
                'password' => Hash::make('password'),
                'profile_picture' => null,
                'is_active' => true,
            ]);
            $user->assignRole($doctorRole);

            DoctorProfile::create([
                'user_id' => $user->id,
                'specialization_id' => $doc['spec'],
                'availability' => [
                    'monday' => ['09:00–12:00'],
                    'wednesday' => ['10:00–13:00'],
                    'friday' => ['15:00–17:00'],
                ],
            ]);
        }

        // Create Patients
        $patients = [
            ['name' => 'Ahmed Raza',  'email' => 'ahmed@hospital.com',  'gender' => 'male'],
            ['name' => 'Fatima Khan', 'email' => 'fatima@hospital.com', 'gender' => 'female'],
            ['name' => 'Ali Noor',    'email' => 'ali@hospital.com',    'gender' => 'male'],
        ];

        foreach ($patients as $pat) {
            $user = User::create([
                'name' => $pat['name'],
                'email' => $pat['email'],
                'password' => Hash::make('password'),
                'profile_picture' => null,
                'is_active' => true,
            ]);
            $user->assignRole($patientRole);

            PatientProfile::create([
                'user_id' => $user->id,
                'dob' => now()->subYears(rand(20, 50))->format('Y-m-d'),
                'gender' => $pat['gender'],
                'address' => '123 Sample Street, City',
                'phone' => '03' . rand(100000000, 999999999),
            ]);
        }
    }
}
