<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       // 1. جدول العيادات (Tenants)
    Schema::create('tenants', function (Blueprint $table) {
        $table->id();
        $table->string('name'); 
        $table->string('domain')->unique(); 
        $table->string('notification_preference')->default('sms'); // sms أو whatsapp
        $table->timestamps();
    });

    // 2. جدول المواعيد (Appointments)
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tenant_id')->constrained()->onDelete('cascade'); // أساس عزل البيانات
        $table->string('patient_name');
        $table->string('patient_phone');
        $table->dateTime('appointment_date');
        $table->timestamps();
    });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('appointments');
    }
};
