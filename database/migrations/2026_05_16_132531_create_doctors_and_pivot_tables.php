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
    // 1. جدول الأطباء
    Schema::create('doctors', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('specialty'); // التخصص
        $table->timestamps();
    });

    // 2. جدول المواعيد يعمل كجدول للمرضى حالياً، ولكن لتبسيط الفكرة المعمارية 
    // سننشئ الجدول الوسيط الذي يربط الطبيب بالموعد/المريض الحالي
    Schema::create('appointment_doctor', function (Blueprint $table) {
        $table->id();
        $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
        $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('appointment_doctor');
    }
};
