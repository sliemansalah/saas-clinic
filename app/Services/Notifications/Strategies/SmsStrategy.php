<?php

namespace App\Services\Notifications\Strategies;

class SmsStrategy implements NotificationStrategy
{
    public function send(string $phone, string $message): bool
    {
        // هنا يوضع كود الـ API الفعلي للـ SMS
        \Log::info("📨 [SMS] تم الإرسال إلى {$phone}: {$message}");
        return true;
    }
}
