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
    // 💡 ميزة احترافية: نقوم بتعطيل الـ Global Scope هنا مؤقتًا لرؤية جميع مواعيد كل العيادات في صفحة الإدارة
    $appointments = Appointment::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
                                ->with('tenant')
                                ->latest()
                                ->get(); 

    // جلب قائمة بكل العيادات ليختار منها المستخدم في الفرونت إند
    $tenants = \DB::table('tenants')->get();
    
    return Inertia::render('Appointments/Index', [
        'appointments' => $appointments,
        'tenants' => $tenants // 👈 تمرير العيادات للـ Vue
    ]);
}


    public function store(Request $request, NotificationSender $sender)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id', // 👈 التأكد من أن العيادة المختارة موجودة فعليًا
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string',
            'appointment_date' => 'required|date',
            'notification_preference' => 'required|string|in:sms,whatsapp,in_app', // 👈 التحقق من القيمة الجديدة
        ]);

          // 💡 حيلة احترافية: نضع معرف العيادة المختارة في الـ session لكي يقرأها الـ Trait والـ Job تلقائيًا
    session(['tenant_id' => $data['tenant_id']]);
    session(['tenant_preference' => $data['notification_preference']]);

        
        // حفظ الموعد
  $appointment = Appointment::create([
        'patient_name' => $data['patient_name'],
        'patient_phone' => $data['patient_phone'],
        'appointment_date' => $data['appointment_date'],
    ]);
        // إرسال الإشعار باستخدام الستراتيجية المفضلة المخزنة في الجلسة للعيادة الحالية
        $preference = $data['notification_preference'];
        $message = "مرحبًا {$appointment->patient_name}، تم تأكيد موعدك بنجاح بتاريخ {$appointment->appointment_date}.";

        // $sender->setStrategy($preference)->send($appointment->patient_phone, $message);
        // 🚀 الكود الجديد الاحترافي: إطلاق المهمة في الخلفية دون جعل المستخدم ينتظر ثانية واحدة!
        \App\Jobs\SendAppointmentNotificationJob::dispatch(
            $appointment->patient_phone, 
            $message, 
            $preference
        );

        return redirect()->back()->with('success', 'تم حجز الموعد بنجاح وتنبيه المريض!');
    }
}
