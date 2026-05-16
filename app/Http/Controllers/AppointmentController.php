<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\Notifications\NotificationSender;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        // جلب مواعيد العيادة الحالية فقط بفضل الـ Global Scope التلقائي
        $appointments = Appointment::latest()->get();
        
        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments
        ]);
    }

    public function store(Request $request, NotificationSender $sender)
    {
        $data = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string',
            'appointment_date' => 'required|date',
        ]);

        // حفظ الموعد
        $appointment = Appointment::create($data);

        // إرسال الإشعار باستخدام الستراتيجية المفضلة المخزنة في الجلسة للعيادة الحالية
        $preference = session('tenant_preference', 'sms');
        $message = "مرحبًا {$appointment->patient_name}، تم تأكيد موعدك بنجاح بتاريخ {$appointment->appointment_date}.";
        
        $sender->setStrategy($preference)->send($appointment->patient_phone, $message);

        return redirect()->back()->with('success', 'تم حجز الموعد بنجاح وتنبيه المريض!');
    }
}
