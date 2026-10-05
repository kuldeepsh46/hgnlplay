<?php

namespace App\Http\Middleware;

use App\Services\SystemSwitch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| Enforces the superadmin kill switches on every web and API request.
| Superadmins always pass, and the login / logout routes stay open so a
| superadmin can still sign in to turn switches off.
|--------------------------------------------------------------------------
*/
class SystemSwitches
{
    public function handle(Request $request, Closure $next): Response
    {
        $active = SystemSwitch::active();
        if (!$active) {
            return $next($request);
        }

        $isApi = $request->is('api/*');
        $user = $isApi ? null : $request->user();
        if ($user && $user->hasRole('superadmin')) {
            return $next($request);
        }

        // Logging out is always allowed, so nobody gets stuck signed in
        if ($request->is('logout')) {
            return $next($request);
        }

        $on = array_flip($active);
        $isAuthRoute = $request->is('login', 'logout');
        $isApiLogin = $request->is('api/login');
        $isWrite = !in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true);

        // 1. Whole site offline: sign out anyone signed in, so the browser is
        //    free for a superadmin to log in again
        if (isset($on['site_offline']) && ($user || !$isAuthRoute)) {
            $this->signOut($request, $user);
            return $this->block($request, 'site_offline');
        }

        // 2. Logins blocked: sign out anyone already signed in
        if (isset($on['member_logins'])) {
            if ($user) {
                $this->signOut($request, $user);
                return $this->block($request, 'member_logins');
            }
            if ($isApiLogin || ($isApi && $request->bearerToken())) {
                return $this->block($request, 'member_logins');
            }
        }

        // 3. Everything that saves or changes data
        if (isset($on['freeze_all']) && $isWrite && !$isAuthRoute && !$isApiLogin) {
            return $this->block($request, 'freeze_all');
        }

        // 4. Individual features
        if ($isWrite) {
            $routeName = $request->route()?->getName();
            foreach (SystemSwitch::ROUTES as $key => $names) {
                if (!isset($on[$key])) {
                    continue;
                }
                if (($routeName && in_array($routeName, $names, true))
                    || (isset(SystemSwitch::PATHS[$key]) && $request->is(...SystemSwitch::PATHS[$key]))) {
                    return $this->block($request, $key);
                }
            }
        }

        return $next($request);
    }

    private function signOut(Request $request, $user): void
    {
        if (!$user) {
            return;
        }
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    private function block(Request $request, string $key): Response
    {
        $message = SystemSwitch::message($key) ?: ($key === 'site_offline'
            ? 'The website is temporarily unavailable. Please check back soon.'
            : 'This action is temporarily paused. Please try again later.');

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['status' => false, 'paused' => $key, 'message' => $message], 503);
        }

        return response()->view('system.paused', [
            'offline' => $key === 'site_offline',
            'message' => $message,
            'showLogout' => in_array($key, ['site_offline', 'member_logins'], true),
        ], 503);
    }
}
