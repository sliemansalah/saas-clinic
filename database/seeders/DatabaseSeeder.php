<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إدخال العيادة رقم 1 بنصوص محمية وعلامات تنصيص برمجية صحيحة
        DB::table('tenants')->insert([
            'id' => 1,
            'name' => 'عيادة الشفاء التجريبية',
            'domain' => 'shifa.saas.com',
            'notification_preference' => 'whatsapp'
        ]);
    }
}
