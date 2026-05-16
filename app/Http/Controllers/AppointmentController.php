<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\Notifications\NotificationSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        // 💡 جلب المواعيد مع العيادات والأطباء معاً في استعلام واحد سريع ومنع الـ N+1
        $appointments = Appointment::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
                                    ->with(['tenant', 'doctors']) // 👈 أضفنا جلب الأطباء كعلاقة هنا
                                    ->latest()
                                    ->get(); 

        // جلب قائمة بكل العيادات ليختار منها المستخدم في الفرونت إند
        $tenants = DB::table('tenants')->get();

        // 👈 جلب قائمة بكل الأطباء لتمريرها لشاشة الـ Vue
        $doctors = Doctor::all();
        
        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'tenants' => $tenants,
            'doctors' => $doctors // 👈 تمرير قائمة الأطباء للـ Vue
        ]);
    }

    public function store(Request $request, NotificationSender $sender)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id', 
            'doctor_id' => 'required|exists:doctors,id', // 👈 التحقق من أن الطبيب المختار موجود فعليًا
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string',
            'appointment_date' => 'required|date',
            'notification_preference' => 'required|string|in:sms,whatsapp,in_app', 
        ]);

        // حيلة احترافية لتخزين البيانات في الجلسة لتقرأها المكونات التلقائية
        session(['tenant_id' => $data['tenant_id']]);
        session(['tenant_preference' => $data['notification_preference']]);
        
        // حفظ الموعد في جدول المواعيد الأساسي
        $appointment = Appointment::create([
            'patient_name' => $data['patient_name'],
            'patient_phone' => $data['patient_phone'],
            'appointment_date' => $data['appointment_date'],
        ]);

        // 🚀 السحر البرمجي لعلاقة الـ Many-to-Many:
        // نقوم بربط الموعد الجديد بالطبيب المختار عبر وضع سجل تلقائي في الجدول الوسيط
        $appointment->doctors()->attach($data['doctor_id']);

        // تجهيز نص الرسالة وإطلاق الوظيفة في الخلفية (Queue)
        $preference = $data['notification_preference'];
        $message = "مرحبًا {$appointment->patient_name}، تم تأكيد موعدك بنجاح بتاريخ {$appointment->appointment_date}.";

        \App\Jobs\SendAppointmentNotificationJob::dispatch(
            $appointment->patient_phone, 
            $message, 
            $preference
        );

        return redirect()->back()->with('success', 'تم حجز الموعد بنجاح وتنبيه المريض وربطه بالطبيب المعالج!');
    }
}
