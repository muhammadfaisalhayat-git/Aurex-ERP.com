<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveBelongsToTenantTrait extends Command
{
    protected $signature = 'fix:belongs-to-tenant';
    protected $description = 'Remove BelongsToTenant trait from models that do not have branch_id column';

    public function handle()
    {
        // Models that should KEEP BelongsToTenant (tables with branch_id)
        $keep = ['Product', 'ProductCategory', 'SystemSetting', 'TaxSetting', 'User'];

        // Get all models
        $modelPath = app_path('Models');
        $files = glob($modelPath . '/*.php');

        $fixed = 0;

        foreach ($files as $file) {
            $modelName = basename($file, '.php');

            // Skip if this model should keep the trait
            if (in_array($modelName, $keep)) {
                continue;
            }

            $content = file_get_contents($file);

            // Check if it has BelongsToTenant
            if (strpos($content, 'BelongsToTenant') === false) {
                continue;
            }

            // Remove the trait from use statement
            $content = preg_replace('/,\s*BelongsToTenant/', '', $content);
            $content = preg_replace('/BelongsToTenant\s*,\s*/', '', $content);
            $content = preg_replace('/use\s+BelongsToTenant;/', '', $content);

            // Remove the import
            $content = preg_replace('/use\s+App\\\\Traits\\\\BelongsToTenant;\s*\n/', '', $content);

            file_put_contents($file, $content);
            $this->info("Fixed: $modelName");
            $fixed++;
        }

        $this->info("\nTotal models fixed: $fixed");
        return 0;
    }
}
