<?php

namespace App\Services\Notifications\Strategies;

class WhatsappStrategy implements NotificationStrategy
{
    public function send(string $phone, string $message): bool
    {
        // هنا يوضع كود الـ API الفعلي للواتساب
        \Log::info("🟢 [WhatsApp] تم الإرسال إلى {$phone}: {$message}");
        return true;
    }
}
