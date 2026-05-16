<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, \Closure $next)
    {
        // سنفترض أن العيادة ترسل معرفها وتفضيلها في الـ Headers لتبسيط التجربة
        if ($request->hasHeader('X-Tenant-Id')) {
            session(['tenant_id' => $request->header('X-Tenant-Id')]);
            session(['tenant_preference' => $request->header('X-Tenant-Preference', 'sms')]);
        } else {
            // قيم افتراضية للتجربة المحلية في حال عدم إرسال الترويسات
            session(['tenant_id' => 1]);
            session(['tenant_preference' => 'whatsapp']);
        }

        return $next($request);
    }

}
