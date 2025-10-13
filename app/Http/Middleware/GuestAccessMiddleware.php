<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isGuest()) {
            // Guest hanya bisa akses route yang diizinkan
            $allowedRoutes = [
                'dashboard',
                'mikrobiologi-forms.index',
                'mikrobiologi-forms.show',
                'mikrobiologi-forms.export',
                'mikrobiologi-forms.export-pdf',
                'mikrobiologi-forms.export-all',
                'kimia.index',
                'kimia.show',
                'kimia.export',
                'kimia.export-pdf',
                'kimia.export-all',
                'kimia.print',
                'profile.edit',
                'profile.update',
                'general-note.show',
                'general-note.update',
                'dashboard.note',
            ];

            $routeName = $request->route()?->getName();
            
            if (!in_array($routeName, $allowedRoutes)) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk melakukan aksi ini.');
            }
        }

        return $next($request);
    }
}
