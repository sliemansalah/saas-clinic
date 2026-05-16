<!-- resources/js/Pages/Appointments/Index.vue -->
<template>
  <div class="min-h-screen bg-gray-100 p-8" dir="rtl">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
      
      <!-- أولاً: نموذج إضافة موعد جديد (Form) -->
      <div class="bg-white p-6 rounded-xl shadow-sm h-fit">
        <h2 class="text-lg font-bold mb-4 text-gray-800">حجز موعد جديد</h2>

        <form @submit.prevent="submit" class="space-y-4">
          
          <!-- 1. حقل اختيار العيادة -->
          <div>
            <label class="block text-sm font-medium text-gray-600">اختر العيادة المستهدفة</label>
            <select v-model="form.tenant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:ring-blue-500" required>
              <option value="" disabled>-- اضغط لاختيار العيادة --</option>
              <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                🏢 {{ tenant.name }}
              </option>
            </select>
          </div>

          <!-- 2. 👈 حقل اختيار الطبيب المعالج (Many-to-Many Connection) -->
          <div>
            <label class="block text-sm font-medium text-gray-600">اختر الطبيب المعالج</label>
            <select v-model="form.doctor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:ring-blue-500" required>
              <option value="" disabled>-- اضغط لاختيار الطبيب --</option>
              <option v-for="doc in doctors" :key="doc.id" :value="doc.id">
                👨‍⚕️ {{ doc.name }} ({{ doc.specialty }})
              </option>
            </select>
          </div>

          <!-- 3. حقل اسم المريض -->
          <div>
            <label class="block text-sm font-medium text-gray-600">اسم المريض</label>
            <input v-model="form.patient_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:ring-blue-500" required />
          </div>

          <!-- 4. حقل رقم الهاتف -->
          <div>
            <label class="block text-sm font-medium text-gray-600">رقم الهاتف</label>
            <input v-model="form.patient_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:ring-blue-500" required />
          </div>

          <!-- 5. حقل التاريخ والوقت -->
          <div>
            <label class="block text-sm font-medium text-gray-600">التاريخ والوقت</label>
            <input v-model="form.appointment_date" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:ring-blue-500" required />
          </div>

          <!-- 6. حقل وسيلة التذكير المفضلة (تم نقله داخل الـ form بنجاح) -->
          <div>
            <label class="block text-sm font-medium text-gray-600">وسيلة التذكير المفضلة</label>
            <select v-model="form.notification_preference" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:ring-blue-500">
              <option value="sms">📨 رسالة نصية (SMS)</option>
              <option value="whatsapp">🟢 واتساب (WhatsApp)</option>
              <option value="in_app">🔔 داخل التطبيق (In-App)</option>
            </select>
          </div>

          <!-- زر التأكيد وإطلاق المهمة في الخلفية -->
          <button type="submit" :disabled="form.processing" class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 transition disabled:opacity-50 font-bold">
            {{ form.processing ? 'جاري الحجز والإرسال...' : 'تأكيد وحفظ الموعد' }}
          </button>
        </form>
      </div>

      <!-- ثانياً: جدول عرض المواعيد لجميع العيادات والأطباء -->
      <div class="bg-white p-6 rounded-xl shadow-sm md:col-span-2">
        <h2 class="text-lg font-bold mb-4 text-gray-800">مواعيد العيادات واللوحة العامة</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b text-gray-400 text-sm">
                <th class="pb-2">اسم المريض</th>
                <th class="pb-2">رقم الهاتف</th>
                <th class="pb-2">اسم العيادة</th>
                <th class="pb-2">الطبيب المعالج</th> <!-- 👈 عمود جديد -->
                <th class="pb-2">تاريخ الموعد</th>
              </tr>
            </thead>
            <tbody class="divide-y text-gray-700 text-sm">
              <tr v-for="app in appointments" :key="app.id" class="hover:bg-gray-50">
                <td class="py-3 font-medium">{{ app.patient_name }}</td>
                <td class="py-3">{{ app.patient_phone }}</td>
                <td class="py-3 text-blue-600 font-semibold">{{ app.tenant ? app.tenant.name : 'عيادة عامة' }}</td>
                
                <!-- 👈 عرض قائمة الأطباء المرتبطين بالموعد تلقائياً -->
                <td class="py-3 text-gray-600">
                  <span v-for="doc in app.doctors" :key="doc.id" class="inline-block bg-blue-50 text-blue-800 text-xs px-2 py-1 rounded ml-1 font-medium">
                    👨‍⚕️ {{ doc.name }}
                  </span>
                  <span v-if="!app.doctors || app.doctors.length === 0" class="text-gray-400 text-xs">غير محدد</span>
                </td>

                <td class="py-3 text-left" dir="ltr">{{ new Date(app.appointment_date).toLocaleString('ar-EG') }}</td>
              </tr>
              <tr v-if="appointments.length === 0">
                <td colspan="5" class="py-8 text-center text-gray-400">لا توجد مواعيد محجوزة حالياً.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

// استقبال البيانات والروابط الشبكية القادمة من السيرفر لارافيل
defineProps({
  appointments: Array,
  tenants: Array,
  doctors: Array // 👈 تم تفعيل استقبال مصفوفة الأطباء بنجاح
})

// تهيئة حقول النموذج التفاعلي بالكامل
const form = useForm({
  tenant_id: '', 
  doctor_id: '', // 👈 إضافة حقل الطبيب المختار للنموذج ليرسله الـ Vue للـ Controller
  patient_name: '',
  patient_phone: '',
  appointment_date: '',
  notification_preference: 'sms'
})

const submit = () => {
  form.post(route('appointments.store'), {
    onSuccess: () => form.reset('patient_name', 'patient_phone', 'appointment_date'), 
  })
}
</script>
