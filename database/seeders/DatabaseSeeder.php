<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إيقاف فحص العلاقات مؤقتًا لتمكين تفريغ الجداول المشتركة
        Schema::disableForeignKeyConstraints();

        // 2. تفريغ الجداول بنظافة (أضفنا جدول الأطباء والجدول الوسيط)
        DB::table('appointment_doctor')->truncate();
        DB::table('appointments')->truncate();
        DB::table('tenants')->truncate();
        DB::table('doctors')->truncate(); // 👈 تفريغ جدول الأطباء الجديد

        // 3. إعادة تفعيل فحص العلاقات لحماية النظام
        Schema::enableForeignKeyConstraints();

        // 4. إدخال العيادات بروابط فريدة
        DB::table('tenants')->insert([
            [
                'id' => 1, 
                'name' => 'عيادة الشفاء والقلب', 
                'domain' => 'shifa.saas.com', 
                'notification_preference' => 'whatsapp'
            ],
            [
                'id' => 2, 
                'name' => 'عيادة الأمل للأسنان', 
                'domain' => 'amal.saas.com', 
                'notification_preference' => 'sms'
            ],
            [
                'id' => 3, 
                'name' => 'مستشفى الحياة التخصصي', 
                'domain' => 'hayat.saas.com', 
                'notification_preference' => 'in_app'
            ],
        ]);

        // 5. 🚀 إدخال الأطباء التجريبيين لربط علاقة الـ Many-to-Many بنجاح
        DB::table('doctors')->insert([
            ['id' => 1, 'name' => 'د. أحمد سليمان', 'specialty' => 'طب وجراحة القلب'],
            ['id' => 2, 'name' => 'د. سارة عمر', 'specialty' => 'طب الأسنان'],
            ['id' => 3, 'name' => 'د. محمد علي', 'specialty' => 'الباطنية والأطفال'],
        ]);
    }
}
