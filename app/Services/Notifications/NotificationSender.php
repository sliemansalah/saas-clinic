<?php

namespace App\Services\Notifications;

use App\Services\Notifications\Strategies\NotificationStrategy;
use App\Services\Notifications\Strategies\SmsStrategy;
use App\Services\Notifications\Strategies\WhatsappStrategy;

class NotificationSender
{
    protected NotificationStrategy $strategy;

    // تحديد الاستراتيجية ديناميكيًا وقت التشغيل
    public function setStrategy(string $preference): self
    {
        $this->strategy = match ($preference) {
            'sms' => new SmsStrategy(),
            'whatsapp' => new WhatsappStrategy(),
            default => new SmsStrategy(),
        };

        return $this;
    }

    public function send(string $phone, string $message): bool
    {
        return $this->strategy->send($phone, $message);
    }
}
