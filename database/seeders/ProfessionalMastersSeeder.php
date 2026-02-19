<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessionalMastersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = 1; // Default company

        $costCenters = [
            ['code' => 'CC-ADMIN', 'name_en' => 'Administration', 'name_ar' => 'الإدارة'],
            ['code' => 'CC-PROD', 'name_en' => 'Production', 'name_ar' => 'الإنتاج'],
            ['code' => 'CC-SALE', 'name_en' => 'Sales & Marketing', 'name_ar' => 'المبيعات والتسويق'],
            ['code' => 'CC-IT', 'name_en' => 'IT Services', 'name_ar' => 'خدمات تقنية المعلومات'],
        ];

        foreach ($costCenters as $cc) {
            \App\Models\CostCenter::updateOrCreate(['code' => $cc['code']], array_merge($cc, ['company_id' => $companyId]));
        }

        $activities = [
            ['code' => 'ACT-CONS', 'name_en' => 'Consulting', 'name_ar' => 'استشارات'],
            ['code' => 'ACT-MAINT', 'name_en' => 'Maintenance', 'name_ar' => 'صيانة'],
            ['code' => 'ACT-TRAINING', 'name_en' => 'Training', 'name_ar' => 'تدريب'],
        ];

        foreach ($activities as $act) {
            \App\Models\Activity::updateOrCreate(['code' => $act['code']], array_merge($act, ['company_id' => $companyId]));
        }

        $lcs = [
            ['code' => 'LC-2024-001', 'name_en' => 'LC - HSBC Export', 'name_ar' => 'اعتماد بنكي - بنك HSBC'],
            ['code' => 'LC-2024-002', 'name_en' => 'LC - Al Rajhi Import', 'name_ar' => 'اعتماد بنكي - مصرف الراجحي'],
        ];

        foreach ($lcs as $lc) {
            \App\Models\LetterOfCredit::updateOrCreate(['code' => $lc['code']], array_merge($lc, ['company_id' => $companyId]));
        }

        $promoters = [
            ['code' => 'PROM-001', 'name_en' => 'External Promoter A', 'name_ar' => 'مروج خارجي أ'],
            ['code' => 'PROM-WEB', 'name_en' => 'Web Ad Campaigns', 'name_ar' => 'حملات إعلانية عبر الويب'],
        ];

        foreach ($promoters as $p) {
            \App\Models\Promoter::updateOrCreate(['code' => $p['code']], array_merge($p, ['company_id' => $companyId]));
        }
    }
}
