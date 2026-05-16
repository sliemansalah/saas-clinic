<!-- resources/js/Pages/Appointments/Index.vue -->
<template>
  <div class="min-h-screen bg-gray-100 p-8" dir="rtl">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
      
      <!-- أولاً: نموذج الإدخال (ثلاث خانات للملاحظات) -->
      <div class="bg-white p-6 rounded-xl shadow-sm h-fit">
        <h2 class="text-lg font-bold mb-4 text-gray-800">حجز موعد متقدم</h2>

        <form @submit.prevent="submit" class="space-y-4">
          
          <!-- 1. حقل اختيار العيادة -->
          <div>
            <label class="block text-sm font-medium text-gray-600">اختر العيادة المستهدفة</label>
            <select v-model="form.tenant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:ring-blue-500" required>
              <option value="" disabled>-- اضغط لاختيار العيادة --</option>
              <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">🏢 {{ tenant.name }}</option>
            </select>
          </div>

          <!-- 2. حقل اختيار الطبيب المعالج -->
          <div>
            <label class="block text-sm font-medium text-gray-600">اختر الطبيب المعالج</label>
            <select v-model="form.doctor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:ring-blue-500" required>
              <option value="" disabled>-- اضغط لاختيار الطبيب --</option>
              <option v-for="doc in doctors" :key="doc.id" :value="doc.id">👨‍⚕️ {{ doc.name }}</option>
            </select>
          </div>

          <!-- 3. حقل اسم المريض -->
          <div>
            <label class="block text-sm font-medium text-gray-600">اسم المريض</label>
            <input v-model="form.patient_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50" required />
          </div>

          <!-- 4. حقل رقم الهاتف -->
          <div>
            <label class="block text-sm font-medium text-gray-600">رقم الهاتف</label>
            <input v-model="form.patient_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50" required />
          </div>

          <!-- 5. حقل التاريخ والوقت -->
          <div>
            <label class="block text-sm font-medium text-gray-600">التاريخ والوقت</label>
            <input v-model="form.appointment_date" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50" required />
          </div>

          <!-- 🚀 قسم الملاحظات الثلاث الديناميكية والمحمية برميًا -->
          <div class="border-t pt-2 space-y-3">
            <h3 class="text-xs font-bold text-blue-600 uppercase">قسم الملاحظات المتعددة الأشكال</h3>
            
            <!-- أ. ملاحظة الموعد -->
            <div>
              <label class="block text-xs font-medium text-gray-500">ملاحظة الموعد (خاصة بالزيارة الحالية)</label>
              <input v-model="form.appointment_note" type="text" placeholder="مثال: مراجعة دورية لفك الأسلاك" class="mt-1 block w-full rounded-md border-gray-300 text-xs p-2 bg-gray-50 focus:ring-blue-500" required />
            </div>

            <!-- ب. ملاحظة الطبيب (مصححة بروابط الـ v-model) -->
            <div>
              <label class="block text-xs font-medium text-gray-500">ملاحظة الطبيب (تعليمات تذهب لملف الطبيب)</label>
              <input 
                v-model="form.doctor_note" 
                :disabled="!form.doctor_id" 
                type="text" 
                :placeholder="form.doctor_id ? 'مثال: يرجى تجهيز أدوات التعقيم قبل دخول الطبيب' : '⚠️ الرجاء اختيار طبيب أولاً لتفعيل الكتابة'" 
                class="mt-1 block w-full rounded-md border-gray-300 text-xs p-2 bg-gray-50 focus:ring-blue-500 disabled:opacity-50" 
                required 
              />
            </div>

            <!-- ج. ملاحظة المستشفى (مصححة بروابط الـ v-model) -->
            <div>
              <label class="block text-xs font-medium text-gray-500">ملاحظة المستشفى (توصية إدارية للمبنى)</label>
              <input 
                v-model="form.hospital_note" 
                :disabled="!form.tenant_id" 
                type="text" 
                :placeholder="form.tenant_id ? 'مثال: المريض يحتاج كرسي متحرك عند الاستقبال' : '⚠️ الرجاء اختيار عيادة أولاً لتفعيل الكتابة'" 
                class="mt-1 block w-full rounded-md border-gray-300 text-xs p-2 bg-gray-50 focus:ring-blue-500 disabled:opacity-50" 
                required 
              />
            </div>
          </div>

          <!-- حقل وسيلة التذكير -->
          <div>
            <label class="block text-sm font-medium text-gray-600">وسيلة التذكير</label>
            <select v-model="form.notification_preference" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50">
              <option value="sms">📨 SMS</option>
              <option value="whatsapp">🟢 WhatsApp</option>
              <option value="in_app">🔔 In-App</option>
            </select>
          </div>

          <button type="submit" :disabled="form.processing" class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 font-bold disabled:opacity-50">
            {{ form.processing ? 'جاري الحفظ وتوزيع البيانات...' : 'تأكيد الحجز وتوزيع الملاحظات' }}
          </button>
        </form>
      </div>

      <!-- ثانياً: جدول العرض المتكامل للملاحظات الثلاث -->
      <div class="bg-white p-6 rounded-xl shadow-sm md:col-span-2">
        <h2 class="text-lg font-bold mb-4 text-gray-800">السجلات واللوحة المعمارية العامة</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b text-gray-400 text-sm">
                <th class="pb-2">المريض والعيادة</th>
                <th class="pb-2">الطبيب المعالج</th> 
                <th class="pb-2">شبكة الملاحظات متعددة الأشكال (Polymorphic Output)</th>
                <th class="pb-2">تاريخ الموعد</th>
              </tr>
            </thead>
            <tbody class="divide-y text-gray-700 text-sm">
              <tr v-for="app in appointments" :key="app.id" class="hover:bg-gray-50">
                <td class="py-3">
                  <div class="font-bold text-gray-800">{{ app.patient_name }}</div>
                  <div class="text-blue-600 text-xs font-semibold">🏢 {{ app.tenant ? app.tenant.name : 'عيادة عامة' }}</div>
                </td>
                
                <td class="py-3 text-gray-600">
                  <span v-for="doc in app.doctors" :key="doc.id" class="inline-block bg-blue-50 text-blue-800 text-xs px-2 py-1 rounded font-medium">👨‍⚕️ {{ doc.name }}</span>
                </td>
                
                <!-- عرض الملاحظات الثلاث المستدعاة بعلاقة البوليمورفيزم من نفس الجدول المشترك -->
                <td class="py-3 max-w-sm space-y-1">
                  <!-- 1. ملاحظات الموعد نفسه -->
                  <div v-for="comment in app.comments" :key="comment.id" class="text-xs text-purple-800 bg-purple-50 p-1.5 rounded border border-purple-200">
                    {{ comment.body }}
                  </div>
                  <!-- 2. ملاحظات الأطباء المشرفين على الموعد -->
                  <div v-for="doc in app.doctors" :key="doc.id">
                    <div v-for="docComment in doc.comments" :key="docComment.id" class="text-xs text-emerald-800 bg-emerald-50 p-1.5 rounded border border-emerald-200">
                      {{ docComment.body }}
                    </div>
                  </div>
                  <!-- 3. ملاحظات المستشفى التابع له الموعد -->
                  <div v-if="app.tenant" v-for="tenantComment in app.tenant.comments" :key="tenantComment.id" class="text-xs text-indigo-800 bg-indigo-50 p-1.5 rounded border border-indigo-200">
                    {{ tenantComment.body }}
                  </div>
                </td>

                <td class="py-3 text-left text-xs" dir="ltr">{{ new Date(app.appointment_date).toLocaleString('ar-EG') }}</td>
              </tr>
              <tr v-if="appointments.length === 0">
                <td colspan="4" class="py-8 text-center text-gray-400">لا توجد سجلات حالياً في النظام.</td>
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

defineProps({
  appointments: Array,
  tenants: Array,
  doctors: Array 
})

const form = useForm({
  tenant_id: '', 
  doctor_id: '', 
  patient_name: '',
  patient_phone: '',
  appointment_date: '',
  notification_preference: 'sms',
  
  // تهيئة حقول الملاحظات الثلاث بنظافة تامة
  appointment_note: '',
  doctor_note: '',
  hospital_note: ''
})

const submit = () => {
  form.post(route('appointments.store'), {
    onSuccess: () => form.reset('patient_name', 'patient_phone', 'appointment_date', 'appointment_note', 'doctor_note', 'hospital_note'), 
  })
}
</script>
