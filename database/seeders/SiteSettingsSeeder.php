<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\SiteConfigService;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        SiteConfigService::initializeDefaults();
    }
}
