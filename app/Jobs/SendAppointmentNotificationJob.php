<?php

namespace App\Jobs;

use App\Services\Notifications\NotificationSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 💡 ميزة احترافية للمقابلات: تحديد عدد مرات إعادة المحاولة في حال فشل السيرفر الخارجي
    public $tries = 3;

    // 💡 ميزة احترافية: عدد الثواني للانتظار قبل إعادة المحاولة (مثلاً لو السيرفر معطل مؤقتًا)
    public $backoff = 10; 

    protected $phone;
    protected $message;
    protected $preference;

    /**
     * استقبال البيانات اللازمة للوظيفة عند إطلاقها
     */
    public function __construct($phone, $message, $preference)
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->preference = $preference;
    }

    /**
     * الدالة التي يتم تنفيذها في الخلفية تلقائيًا بواسطة الـ Queue Worker
     */
    public function handle(NotificationSender $sender): void
    {
        // استخدام الـ Strategy Pattern الذي بنيناه سابقًا بكل مرونة في الخلفية
        $sender->setStrategy($this->preference)
               ->send($this->phone, $this->message);
    }
}
