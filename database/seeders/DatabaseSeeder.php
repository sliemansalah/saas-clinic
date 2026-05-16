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
    // 1. إيقاف فحص العلاقات مؤقتًا
    \Schema::disableForeignKeyConstraints();

    // 2. تفريغ الجداول بنظافة
    \DB::table('appointments')->truncate();
    \DB::table('tenants')->truncate();

    // 3. إعادة تفعيل فحص العلاقات لحماية النظام
    \Schema::enableForeignKeyConstraints();

    // 4. إدخال العيادات بروابط فريدة ومختلفة تمامًا لعدم حدوث تكرار
    \DB::table('tenants')->insert([
        [
            'id' => 1, 
            'name' => 'عيادة الشفاء والقلب', 
            'domain' => 'shifa.saas.com', // 👈 رابط فريد للعيادة 1
            'notification_preference' => 'whatsapp'
        ],
        [
            'id' => 2, 
            'name' => 'عيادة الأمل للأسنان', 
            'domain' => 'amal.saas.com', // 👈 رابط فريد للعيادة 2
            'notification_preference' => 'sms'
        ],
        [
            'id' => 3, 
            'name' => 'مستشفى الحياة التخصصي', 
            'domain' => 'hayat.saas.com', // 👈 رابط فريد للعيادة 3
            'notification_preference' => 'in_app'
        ],
    ]);

    }
}
