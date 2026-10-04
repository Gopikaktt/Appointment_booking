<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@medicare.com',
            'phone' => '+1 (555) 019-2834',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // 2. Create Sample Patients
        $patient1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1 (555) 234-5678',
            'role' => 'patient',
            'password' => bcrypt('password'),
        ]);

        $patient2 = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'phone' => '+1 (555) 876-5432',
            'role' => 'patient',
            'password' => bcrypt('password'),
        ]);

        $patient3 = User::create([
            'name' => 'Patient Demo',
            'email' => 'patient@medicare.com',
            'phone' => '+1 (555) 432-1098',
            'role' => 'patient',
            'password' => bcrypt('password'),
        ]);

        // 3. Create Doctors
        $doctorsData = [
            [
                'name' => 'Dr. Robert Chen',
                'specialization' => 'Cardiology',
                'email' => 'robert.chen@medicare.com',
                'phone' => '+1 (555) 301-4422',
                'bio' => 'Senior Cardiologist with over 15 years of experience specializing in preventive cardiac care, hypertension management, and heart disease prevention.',
                'consultation_fee' => 120.00,
                'room_number' => 'Building A - Room 302',
                'available_days' => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                'start_time' => '09:00',
                'end_time' => '17:00',
                'slot_duration_minutes' => 30,
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Emily Taylor',
                'specialization' => 'Neurology',
                'email' => 'emily.taylor@medicare.com',
                'phone' => '+1 (555) 301-5533',
                'bio' => 'Board-certified Neurologist focusing on migraine therapy, memory disorders, and comprehensive neurological evaluations.',
                'consultation_fee' => 140.00,
                'room_number' => 'Building B - Room 108',
                'available_days' => ["Monday", "Tuesday", "Wednesday", "Friday"],
                'start_time' => '09:30',
                'end_time' => '16:30',
                'slot_duration_minutes' => 30,
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Marcus Vance',
                'specialization' => 'Orthopedics',
                'email' => 'marcus.vance@medicare.com',
                'phone' => '+1 (555) 301-6644',
                'bio' => 'Orthopedic Specialist focusing on joint replacements, sports injury rehabilitation, and modern arthroscopic techniques.',
                'consultation_fee' => 110.00,
                'room_number' => 'Building A - Room 105',
                'available_days' => ["Tuesday", "Wednesday", "Thursday", "Saturday"],
                'start_time' => '10:00',
                'end_time' => '18:00',
                'slot_duration_minutes' => 30,
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Sophia Martinez',
                'specialization' => 'Pediatrics',
                'email' => 'sophia.martinez@medicare.com',
                'phone' => '+1 (555) 301-7755',
                'bio' => 'Compassionate Pediatrician providing comprehensive child healthcare, routine vaccinations, and developmental milestones tracking.',
                'consultation_fee' => 95.00,
                'room_number' => 'Building C - Room 201',
                'available_days' => ["Monday", "Wednesday", "Thursday", "Friday"],
                'start_time' => '08:30',
                'end_time' => '15:30',
                'slot_duration_minutes' => 30,
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Alexander Wright',
                'specialization' => 'Dermatology',
                'email' => 'alex.wright@medicare.com',
                'phone' => '+1 (555) 301-8866',
                'bio' => 'Expert Dermatologist delivering medical and aesthetic skin treatments, mole analysis, and chronic eczema care.',
                'consultation_fee' => 130.00,
                'room_number' => 'Building B - Room 404',
                'available_days' => ["Monday", "Tuesday", "Thursday", "Friday"],
                'start_time' => '09:00',
                'end_time' => '16:00',
                'slot_duration_minutes' => 30,
                'status' => 'active',
            ],
        ];

        $doctors = [];
        foreach ($doctorsData as $docData) {
            // Create user account for doctor
            $doctorUser = User::create([
                'name' => $docData['name'],
                'email' => $docData['email'],
                'phone' => $docData['phone'],
                'role' => 'doctor',
                'password' => bcrypt('password'),
            ]);

            $docData['user_id'] = $doctorUser->id;
            $doctors[] = \App\Models\Doctor::create($docData);
        }

        // 4. Sample Appointments
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $tomorrow = \Carbon\Carbon::tomorrow()->format('Y-m-d');
        $nextWeek = \Carbon\Carbon::today()->addDays(3)->format('Y-m-d');
        $pastDate = \Carbon\Carbon::today()->subDays(2)->format('Y-m-d');

        \App\Models\Appointment::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $doctors[0]->id,
            'appointment_date' => $tomorrow,
            'appointment_time' => '10:00 AM',
            'status' => 'Confirmed',
            'symptoms_notes' => 'Routine cardiac checkup and blood pressure assessment.',
            'notes' => 'Patient advised to bring recent ECG report.',
        ]);

        \App\Models\Appointment::create([
            'patient_id' => $patient2->id,
            'doctor_id' => $doctors[1]->id,
            'appointment_date' => $nextWeek,
            'appointment_time' => '11:00 AM',
            'status' => 'Pending',
            'symptoms_notes' => 'Persistent migraines for past 2 weeks.',
        ]);

        \App\Models\Appointment::create([
            'patient_id' => $patient3->id,
            'doctor_id' => $doctors[2]->id,
            'appointment_date' => $pastDate,
            'appointment_time' => '02:00 PM',
            'status' => 'Completed',
            'symptoms_notes' => 'Right knee pain after playing tennis.',
            'notes' => 'Prescribed anti-inflammatory gel and knee support brace.',
        ]);

        \App\Models\Appointment::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $doctors[4]->id,
            'appointment_date' => $tomorrow,
            'appointment_time' => '02:30 PM',
            'status' => 'Pending',
            'symptoms_notes' => 'Skin rash evaluation.',
        ]);
    }
}
