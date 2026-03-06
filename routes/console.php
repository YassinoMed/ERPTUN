<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('education:seed', function () {
    $this->call('db:seed', ['--class' => 'Database\\Seeders\\EducationSeeder']);
})->purpose('Seed education sample data');
