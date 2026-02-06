<?php

namespace Database\Seeders;

use App\Services\SiteConfigService;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        SiteConfigService::initializeDefaults();
    }
}
