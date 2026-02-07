<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupplierRegistration;
use App\Models\User;
use Carbon\Carbon;

class SupplierRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $suppliers = [
            [
                'company_name' => 'Al-Faisal Industrial Solutions',
                'contact_person' => 'Mohammed Al-Faisal',
                'email' => 'mohammed@alfaisal.com',
                'phone' => '011-4567890',
                'mobile' => '050-1234567',
                'address' => 'Industrial City, Zone 3',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '11383',
                'tax_number' => '300123456700003',
                'registration_number' => 'CR-1010234567',
                'business_type' => 'Manufacturing',
                'website' => 'https://www.alfaisal.com',
                'bank_name' => 'National Commercial Bank',
                'bank_account' => '1234567890',
                'iban' => 'SA03 1000 0001 2345 6789 0123',
                'products_services' => 'Industrial equipment, machinery parts, maintenance services',
                'notes' => 'Potential supplier for industrial equipment',
            ],
            [
                'company_name' => 'Gulf Tech Supplies',
                'contact_person' => 'Ahmed Hassan',
                'email' => 'ahmed@gulftech.com',
                'phone' => '012-3456789',
                'mobile' => '050-2345678',
                'address' => 'Tech Valley, Building 45',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'postal_code' => '21442',
                'tax_number' => '300234567800003',
                'registration_number' => 'CR-1010345678',
                'business_type' => 'Trading',
                'website' => 'https://www.gulftech.com',
                'bank_name' => 'Al Rajhi Bank',
                'bank_account' => '2345678901',
                'iban' => 'SA04 2000 0002 3456 7890 1234',
                'products_services' => 'IT equipment, software licenses, networking solutions',
                'notes' => 'Specialized in IT infrastructure',
            ],
            [
                'company_name' => 'Desert Logistics Co.',
                'contact_person' => 'Khalid Al-Rashid',
                'email' => 'khalid@desertlogistics.com',
                'phone' => '013-4567890',
                'mobile' => '050-3456789',
                'address' => 'Logistics Hub, Port Area',
                'city' => 'Dammam',
                'country' => 'Saudi Arabia',
                'postal_code' => '32241',
                'tax_number' => '300345678900003',
                'registration_number' => 'CR-1010456789',
                'business_type' => 'Logistics',
                'website' => 'https://www.desertlogistics.com',
                'bank_name' => 'Riyad Bank',
                'bank_account' => '3456789012',
                'iban' => 'SA05 3000 0003 4567 8901 2345',
                'products_services' => 'Transportation, warehousing, supply chain management',
                'notes' => 'For logistics and transportation services',
            ],
            [
                'company_name' => 'Modern Office Furniture',
                'contact_person' => 'Sara Abdullah',
                'email' => 'sara@modernoffice.com',
                'phone' => '011-5678901',
                'mobile' => '050-4567890',
                'address' => 'Furniture District, Showroom 12',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '12211',
                'tax_number' => '300456789000003',
                'registration_number' => 'CR-1010567890',
                'business_type' => 'Retail',
                'website' => 'https://www.modernoffice.com',
                'bank_name' => 'Arab National Bank',
                'bank_account' => '4567890123',
                'iban' => 'SA06 4000 0004 5678 9012 3456',
                'products_services' => 'Office furniture, ergonomic chairs, workstations',
                'notes' => 'Office furniture supplier',
            ],
            [
                'company_name' => 'Saudi Safety Equipment',
                'contact_person' => 'Fahad Al-Otaibi',
                'email' => 'fahad@saudisafety.com',
                'phone' => '012-5678901',
                'mobile' => '050-5678901',
                'address' => 'Safety Zone, Industrial Area',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'postal_code' => '21453',
                'tax_number' => '300567890100003',
                'registration_number' => 'CR-1010678901',
                'business_type' => 'Manufacturing',
                'website' => 'https://www.saudisafety.com',
                'bank_name' => 'Saudi British Bank',
                'bank_account' => '5678901234',
                'iban' => 'SA07 5000 0005 6789 0123 4567',
                'products_services' => 'Safety equipment, PPE, fire safety systems',
                'notes' => 'Safety equipment supplier for warehouse and staff',
            ],
            [
                'company_name' => 'Premium Packaging Solutions',
                'contact_person' => 'Noura Saeed',
                'email' => 'noura@premiumpack.com',
                'phone' => '013-5678901',
                'mobile' => '050-6789012',
                'address' => 'Packaging Park, Unit 78',
                'city' => 'Dammam',
                'country' => 'Saudi Arabia',
                'postal_code' => '32252',
                'tax_number' => '300678901200003',
                'registration_number' => 'CR-1010789012',
                'business_type' => 'Manufacturing',
                'website' => 'https://www.premiumpack.com',
                'bank_name' => 'Banque Saudi Fransi',
                'bank_account' => '6789012345',
                'iban' => 'SA08 6000 0006 7890 1234 5678',
                'products_services' => 'Custom packaging, boxes, labels, shipping materials',
                'notes' => 'Packaging materials supplier',
            ],
            [
                'company_name' => 'Al-Jazeera Electrical',
                'contact_person' => 'Omar Ibrahim',
                'email' => 'omar@aljazeeraelec.com',
                'phone' => '011-6789012',
                'mobile' => '050-7890123',
                'address' => 'Electrical Market, Shop 34',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '11222',
                'tax_number' => '300789012300003',
                'registration_number' => 'CR-1010890123',
                'business_type' => 'Trading',
                'website' => 'https://www.aljazeeraelec.com',
                'bank_name' => 'Samba Financial Group',
                'bank_account' => '7890123456',
                'iban' => 'SA09 7000 0007 8901 2345 6789',
                'products_services' => 'Electrical supplies, cables, switches, lighting',
                'notes' => 'Electrical supplies for maintenance',
            ],
            [
                'company_name' => 'Green Energy Solutions',
                'contact_person' => 'Yasser Mohammed',
                'email' => 'yasser@greenenergy.com',
                'phone' => '012-6789012',
                'mobile' => '050-8901234',
                'address' => 'Renewable Energy Park, Block C',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'postal_code' => '21464',
                'tax_number' => '300890123400003',
                'registration_number' => 'CR-1010901234',
                'business_type' => 'Services',
                'website' => 'https://www.greenenergy.com',
                'bank_name' => 'Alinma Bank',
                'bank_account' => '8901234567',
                'iban' => 'SA10 8000 0008 9012 3456 7890',
                'products_services' => 'Solar panels, energy audits, power solutions',
                'notes' => 'Renewable energy solutions provider',
            ],
        ];

        $statuses = ['pending', 'under_review', 'approved', 'rejected'];
        $statusWeights = [30, 20, 40, 10]; // 30% pending, 20% under_review, 40% approved, 10% rejected

        foreach ($suppliers as $index => $supplierData) {
            $status = $this->weightedRandom($statuses, $statusWeights);
            $createdBy = $users->random();
            $createdAt = Carbon::now()->subDays(rand(1, 60));
            
            $registrationData = array_merge($supplierData, [
                'registration_code' => 'SUP-REG-' . date('Y') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'status' => $status,
                'created_by' => $createdBy->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            if ($status === 'approved' || $status === 'rejected') {
                $registrationData['approved_by'] = $users->where('id', '!=', $createdBy->id)->random()->id;
                $registrationData['approved_at'] = $createdAt->copy()->addDays(rand(1, 7));
                
                if ($status === 'rejected') {
                    $registrationData['rejection_reason'] = 'Documentation incomplete. Please provide additional business references.';
                } else {
                    $registrationData['approval_notes'] = 'All documents verified. Approved for vendor registration.';
                }
            }

            SupplierRegistration::create($registrationData);
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
