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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isGuest') && $user->isGuest()) {

            $allowedRoutes = [

                'dashboard',
                'dashboard.note',

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
            ];

            $routeName = $request->route()?->getName();

            if (!$routeName || !in_array($routeName, $allowedRoutes, true)) {

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Anda tidak memiliki akses untuk melakukan aksi ini.'
                    ], 403);
                }

                return redirect()
                    ->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk melakukan aksi ini.');
            }
        }

        return $next($request);
    }
}
