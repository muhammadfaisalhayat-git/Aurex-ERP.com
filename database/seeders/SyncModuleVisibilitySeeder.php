<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class SyncModuleVisibilitySeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $baseSettings = DB::table('system_settings')
            ->where('group', 'module_visibility')
            ->whereNull('company_id')
            ->get();

        if ($baseSettings->isEmpty()) {
            $this->command->error('No base visibility settings found (where company_id is NULL). Please run the migration first.');
            return;
        }

        foreach ($companies as $company) {
            $this->command->info("Syncing visibility settings for company: {$company->name}");
            foreach ($baseSettings as $s) {
                DB::table('system_settings')->insertOrIgnore([
                    'company_id' => $company->id,
                    'key' => $s->key,
                    'value' => $s->value,
                    'type' => $s->type,
                    'group' => $s->group,
                    'display_name_en' => $s->display_name_en,
                    'display_name_ar' => $s->display_name_ar,
                    'is_editable' => $s->is_editable,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}