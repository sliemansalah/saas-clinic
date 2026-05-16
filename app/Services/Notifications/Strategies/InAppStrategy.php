<?php

namespace App\Services\Notifications\Strategies;

class InAppStrategy implements NotificationStrategy
{
    public function send(string $phone, string $message): bool
    {
        // محاكاة حفظ الإشعار في قاعدة البيانات ليظهر داخل حساب المريض (نظام الجرس 🔔)
        \Log::info("🔔 [In-App] تم حفظ الإشعار في قاعدة البيانات ليظهر داخل الموقع: {$message}");
        return true;
    }
}
