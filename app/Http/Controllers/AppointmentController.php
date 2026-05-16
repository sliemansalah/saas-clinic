<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Tenant;
use App\Services\Notifications\NotificationSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        // جلب المواعيد مع العيادات والأطباء والملاحظات الخاصة بكل منها بكفاءة صاروخية تمنع الـ N+1
        $appointments = Appointment::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
                                    ->with([
                                        'tenant.comments',    // جلب ملاحظات المستشفى المرتبطة
                                        'doctors.comments',   // جلب ملاحظات الطبيب المرتبطة
                                        'comments'            // جلب ملاحظات الموعد نفسه
                                    ]) 
                                    ->latest()
                                    ->get(); 

        // كاش العيادات والأطباء لسرعة فائقة في الأداء
        $tenants = Cache::remember('all_tenants', 3600, function () {
            return DB::table('tenants')->get();
        });

        $doctors = Cache::remember('all_doctors', 3600, function () {
            return Doctor::all();
        });
        
        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'tenants' => $tenants,
            'doctors' => $doctors 
        ]);
    }

    public function store(Request $request, NotificationSender $sender)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id', 
            'doctor_id' => 'required|exists:doctors,id', 
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string',
            'appointment_date' => 'required|date',
            'notification_preference' => 'required|string|in:sms,whatsapp,in_app', 
            
            // 🚀 الـ 3 حقول الديناميكية الجديدة للملاحظات الثلاث
            'appointment_note' => 'required|string|max:1000',
            'doctor_note' => 'required|string|max:1000',
            'hospital_note' => 'required|string|max:1000',
        ]);

        session(['tenant_id' => $data['tenant_id']]);
        session(['tenant_preference' => $data['notification_preference']]);
        
        // 1. حفظ الموعد الأساسي
        $appointment = Appointment::create([
            'patient_name' => $data['patient_name'],
            'patient_phone' => $data['patient_phone'],
            'appointment_date' => $data['appointment_date'],
        ]);

        // 2. ربط الطبيب بالموعد (Many-to-Many)
        $appointment->doctors()->attach($data['doctor_id']);

        // ========================================================
        // 🔥 3. السحر المعماري الأقوى: حقن 3 ملاحظات في 3 موديلات مختلفة بنفس الجدول!
        // ========================================================
        
        // أ. حفظ ملاحظة الموعد
        $appointment->comments()->create(['body' => '📌 ملاحظة الموعد: ' . $data['appointment_note']]);

        // ب. حفظ ملاحظة تخص الطبيب المختار
        $doctor = Doctor::find($data['doctor_id']);
        $doctor->comments()->create(['body' => '👨‍⚕️ ملاحظة للطبيب: ' . $data['doctor_note']]);

        // ج. حفظ ملاحظة تخص المستشفى/العيادة المختارة
        $tenant = Tenant::find($data['tenant_id']);
        $tenant->comments()->create(['body' => '🏢 ملاحظة للمستشفى: ' . $data['hospital_note']]);

        // ========================================================

        // 4. إطلاق الـ Job في طابور الخلفية الآمن (Queue)
        $preference = $data['notification_preference'];
        $message = "مرحبًا {$appointment->patient_name}، تم تأكيد موعدك بنجاح.";

        \App\Jobs\SendAppointmentNotificationJob::dispatch(
            $appointment->patient_phone, 
            $message, 
            $preference
        );

        return redirect()->back()->with('success', 'تم حفظ الموعد وتوزيع الملاحظات الثلاث وتنبيه المريض!');
    }
}
