<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Models\Healthcare\Patient;
use App\Models\Healthcare\Doctor;
use App\Models\Healthcare\MedicalService;
use App\Models\Healthcare\Appointment;
use Carbon\Carbon;

class HealthcareSeeder extends Seeder
{
    public function run()
    {
        $companyId = session('active_company_id') ?? 1;
        $branchId = session('active_branch_id') ?? 1;

        // 1. Create COA Accounts for Healthcare
        $incomeType = AccountType::where('name_en', 'Revenue')->first();
        $assetType = AccountType::where('name_en', 'Asset')->first();

        // Healthcare Revenue Account
        $revenueAccount = ChartOfAccount::firstOrCreate(
        ['code' => '4005'],
        [
            'name_en' => 'Healthcare Revenue',
            'name_ar' => 'إيرادات الخدمات الصحية',
            'type' => 'revenue',
            'account_type_id' => $incomeType->id ?? 4,
            'is_posting_allowed' => true,
            'company_id' => $companyId,
        ]
        );

        // Patient Receivables
        $receivableAccount = ChartOfAccount::firstOrCreate(
        ['code' => '1105'],
        [
            'name_en' => 'Patient Receivables',
            'name_ar' => 'ذمم المرضى',
            'type' => 'asset',
            'account_type_id' => $assetType->id ?? 1,
            'is_posting_allowed' => true,
            'company_id' => $companyId,
        ]
        );

        // 2. Seed Doctors
        $doctors = [
            ['code' => 'DOC001', 'name' => 'Dr. James Smith', 'spec' => 'General Practitioner'],
            ['code' => 'DOC002', 'name' => 'Dr. Sarah Johnson', 'spec' => 'Cardiologist'],
            ['code' => 'DOC003', 'name' => 'Dr. Mohammed Al-Farsi', 'spec' => 'Pediatrician'],
        ];

        foreach ($doctors as $d) {
            Doctor::firstOrCreate(
            ['code' => $d['code']],
            [
                'company_id' => $companyId,
                'name_en' => $d['name'],
                'specialization' => $d['spec'],
                'is_active' => true,
            ]
            );
        }

        // 3. Seed Medical Services
        $services = [
            ['code' => 'SRV001', 'name' => 'General Consultation', 'cost' => 150.00],
            ['code' => 'SRV002', 'name' => 'Specialist Consultation', 'cost' => 300.00],
            ['code' => 'SRV003', 'name' => 'X-Ray Chest', 'cost' => 250.00],
            ['code' => 'SRV004', 'name' => 'Blood Test (CBC)', 'cost' => 100.00],
        ];

        foreach ($services as $s) {
            MedicalService::firstOrCreate(
            ['code' => $s['code']],
            [
                'company_id' => $companyId,
                'name_en' => $s['name'],
                'cost' => $s['cost'],
                'revenue_account_id' => $revenueAccount->id,
                'is_active' => true,
            ]
            );
        }

        // 4. Seed Patients
        $patients = [
            ['code' => 'PAT001', 'name' => 'John Doe', 'gender' => 'male'],
            ['code' => 'PAT002', 'name' => 'Jane Smith', 'gender' => 'female'],
            ['code' => 'PAT003', 'name' => 'Ahmed Hassan', 'gender' => 'male'],
        ];

        foreach ($patients as $p) {
            Patient::firstOrCreate(
            ['code' => $p['code']],
            [
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'name_en' => $p['name'],
                'gender' => $p['gender'],
                'date_of_birth' => Carbon::now()->subYears(rand(20, 60)),
                'is_active' => true,
            ]
            );
        }

        // 5. Seed Appointments
        $patientIds = Patient::pluck('id')->toArray();
        $doctorIds = Doctor::pluck('id')->toArray();
        $serviceIds = MedicalService::pluck('id')->toArray();

        for ($i = 0; $i < 5; $i++) {
            $service = MedicalService::find($serviceIds[array_rand($serviceIds)]);
            Appointment::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'patient_id' => $patientIds[array_rand($patientIds)],
                'doctor_id' => $doctorIds[array_rand($doctorIds)],
                'service_id' => $service->id,
                'appointment_date' => Carbon::now()->addDays(rand(-5, 5)),
                'status' => 'scheduled',
                'billing_status' => 'unbilled',
                'total_amount' => $service->cost,
            ]);
        }
    }
}
