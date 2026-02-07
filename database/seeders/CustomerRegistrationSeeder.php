<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerRegistration;
use App\Models\User;
use Carbon\Carbon;

class CustomerRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $customers = [
            [
                'company_name' => 'Saudi Construction Group',
                'contact_person' => 'Abdullah Al-Saud',
                'email' => 'abdullah@saudiconstruct.com',
                'phone' => '011-4567891',
                'mobile' => '050-1234568',
                'address' => 'Business Tower, Floor 15',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '11384',
                'tax_number' => '301123456700003',
                'registration_number' => 'CR-2010234567',
                'business_type' => 'Construction',
                'website' => 'https://www.saudiconstruct.com',
                'credit_limit' => 500000,
                'payment_terms' => 'Net 30 days',
                'notes' => 'Major construction company with multiple projects',
            ],
            [
                'company_name' => 'Al-Rajhi Holdings',
                'contact_person' => 'Sulaiman Al-Rajhi',
                'email' => 'sulaiman@alrajhi-holdings.com',
                'phone' => '012-3456790',
                'mobile' => '050-2345679',
                'address' => 'Financial District, Tower A',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'postal_code' => '21443',
                'tax_number' => '301234567800003',
                'registration_number' => 'CR-2010345678',
                'business_type' => 'Investment',
                'website' => 'https://www.alrajhi-holdings.com',
                'credit_limit' => 1000000,
                'payment_terms' => 'Net 45 days',
                'notes' => 'Investment holding company',
            ],
            [
                'company_name' => 'Eastern Petrochemical',
                'contact_person' => 'Faisal Al-Qahtani',
                'email' => 'faisal@easternpetro.com',
                'phone' => '013-4567891',
                'mobile' => '050-3456790',
                'address' => 'Petrochemical Zone, Plant 7',
                'city' => 'Jubail',
                'country' => 'Saudi Arabia',
                'postal_code' => '31951',
                'tax_number' => '301345678900003',
                'registration_number' => 'CR-2010456789',
                'business_type' => 'Manufacturing',
                'website' => 'https://www.easternpetro.com',
                'credit_limit' => 750000,
                'payment_terms' => 'Net 30 days',
                'notes' => 'Petrochemical manufacturing plant',
            ],
            [
                'company_name' => 'Red Sea Trading Co.',
                'contact_person' => 'Hassan Reda',
                'email' => 'hassan@redseatrading.com',
                'phone' => '012-5678902',
                'mobile' => '050-4567891',
                'address' => 'Port Area, Warehouse 23',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'postal_code' => '21454',
                'tax_number' => '301456789000003',
                'registration_number' => 'CR-2010567890',
                'business_type' => 'Trading',
                'website' => 'https://www.redseatrading.com',
                'credit_limit' => 300000,
                'payment_terms' => 'Net 15 days',
                'notes' => 'Import/export trading company',
            ],
            [
                'company_name' => 'Kingdom Healthcare',
                'contact_person' => 'Dr. Latifa Ahmed',
                'email' => 'latifa@kingdomhealth.com',
                'phone' => '011-5678902',
                'mobile' => '050-5678902',
                'address' => 'Medical City, Building 8',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '12212',
                'tax_number' => '301567890100003',
                'registration_number' => 'CR-2010678901',
                'business_type' => 'Healthcare',
                'website' => 'https://www.kingdomhealth.com',
                'credit_limit' => 400000,
                'payment_terms' => 'Net 30 days',
                'notes' => 'Healthcare services provider',
            ],
            [
                'company_name' => 'Desert Star Hotels',
                'contact_person' => 'Kareem Fayed',
                'email' => 'kareem@desertstar.com',
                'phone' => '014-5678902',
                'mobile' => '050-6789013',
                'address' => 'Resort Boulevard, Hotel Complex',
                'city' => 'Taif',
                'country' => 'Saudi Arabia',
                'postal_code' => '26513',
                'tax_number' => '301678901200003',
                'registration_number' => 'CR-2010789012',
                'business_type' => 'Hospitality',
                'website' => 'https://www.desertstar.com',
                'credit_limit' => 250000,
                'payment_terms' => 'Net 15 days',
                'notes' => 'Hotel and hospitality chain',
            ],
            [
                'company_name' => 'Arabian Tech Solutions',
                'contact_person' => 'Tariq Mahmoud',
                'email' => 'tariq@arabiantech.com',
                'phone' => '011-6789013',
                'mobile' => '050-7890124',
                'address' => 'Digital Park, Office 501',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '11223',
                'tax_number' => '301789012300003',
                'registration_number' => 'CR-2010890123',
                'business_type' => 'Technology',
                'website' => 'https://www.arabiantech.com',
                'credit_limit' => 350000,
                'payment_terms' => 'Net 30 days',
                'notes' => 'IT solutions and consulting',
            ],
            [
                'company_name' => 'Gulf Marine Services',
                'contact_person' => 'Jamal Al-Harbi',
                'email' => 'jamal@gulfmarine.com',
                'phone' => '013-6789013',
                'mobile' => '050-8901235',
                'address' => 'Marina Complex, Dock 12',
                'city' => 'Dammam',
                'country' => 'Saudi Arabia',
                'postal_code' => '32253',
                'tax_number' => '301890123400003',
                'registration_number' => 'CR-2010901234',
                'business_type' => 'Marine Services',
                'website' => 'https://www.gulfmarine.com',
                'credit_limit' => 600000,
                'payment_terms' => 'Net 45 days',
                'notes' => 'Marine equipment and services',
            ],
            [
                'company_name' => 'Riyadh Retail Group',
                'contact_person' => 'Muna Al-Otaibi',
                'email' => 'muna@riyadhretail.com',
                'phone' => '011-7890124',
                'mobile' => '050-9012346',
                'address' => 'Shopping District, Mall 5',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '11333',
                'tax_number' => '301901234500003',
                'registration_number' => 'CR-2010012345',
                'business_type' => 'Retail',
                'website' => 'https://www.riyadhretail.com',
                'credit_limit' => 200000,
                'payment_terms' => 'Net 15 days',
                'notes' => 'Retail chain operator',
            ],
            [
                'company_name' => 'Saudi Educational Services',
                'contact_person' => 'Dr. Nadia Hassan',
                'email' => 'nadia@saudiedu.com',
                'phone' => '012-7890124',
                'mobile' => '050-0123457',
                'address' => 'Education City, Campus 3',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'postal_code' => '21465',
                'tax_number' => '301012345600003',
                'registration_number' => 'CR-2010123456',
                'business_type' => 'Education',
                'website' => 'https://www.saudiedu.com',
                'credit_limit' => 450000,
                'payment_terms' => 'Net 30 days',
                'notes' => 'Educational institutions management',
            ],
        ];

        $statuses = ['pending', 'under_review', 'approved', 'rejected'];
        $statusWeights = [25, 25, 40, 10]; // 25% pending, 25% under_review, 40% approved, 10% rejected

        foreach ($customers as $index => $customerData) {
            $status = $this->weightedRandom($statuses, $statusWeights);
            $createdBy = $users->random();
            $createdAt = Carbon::now()->subDays(rand(1, 60));
            
            $registrationData = array_merge($customerData, [
                'registration_code' => 'CUST-REG-' . date('Y') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'status' => $status,
                'created_by' => $createdBy->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            if ($status === 'approved' || $status === 'rejected') {
                $registrationData['approved_by'] = $users->where('id', '!=', $createdBy->id)->random()->id;
                $registrationData['approved_at'] = $createdAt->copy()->addDays(rand(1, 7));
                
                if ($status === 'rejected') {
                    $registrationData['rejection_reason'] = 'Credit check failed. Please provide bank guarantees.';
                } else {
                    $registrationData['approval_notes'] = 'Credit approved. Welcome to our customer network.';
                }
            }

            CustomerRegistration::create($registrationData);
        }
    }

    private function weightedRandom($values, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($values as $index => $value) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $value;
            }
        }
        
        return $values[0];
    }
}
