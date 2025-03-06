<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ApplicationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('application_settings')->insert([
            'api_key' => Str::uuid(),
            'secret_key' => Str::uuid7(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
