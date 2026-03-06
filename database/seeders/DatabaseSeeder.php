<?php

namespace Database\Seeders;

use App\Models\Utility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Modules\LandingPage\Database\Seeders\LandingPageDatabaseSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(NotificationSeeder::class);
        Artisan::call('migrate', [
            '--path' => module_path('LandingPage', 'Database/Migrations'),
            '--realpath' => true,
        ]);
        $this->call(LandingPageDatabaseSeeder::class);

        if(!file_exists(storage_path() . "/installed"))
        {
            $this->call(PlansTableSeeder::class);
            $this->call(UsersTableSeeder::class);
            $this->call(EducationSeeder::class);
            $this->call(AiTemplateSeeder::class);

        }else{
            Utility::languagecreate();

        }
    }
}
