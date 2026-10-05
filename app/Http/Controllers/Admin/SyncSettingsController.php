<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemSwitch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Superadmin only (everyone else gets a 404): the site's pause switches
|--------------------------------------------------------------------------
*/
class SyncSettingsController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()?->hasRole('superadmin'), 404);

        $states = SystemSwitch::states(true);
        $names = DB::table('users')
            ->whereIn('id', collect($states)->pluck('updated_by')->filter()->all())
            ->pluck('name', 'id');

        $logs = DB::table('system_switch_logs as l')
            ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
            ->select('l.*', 'u.name', 'u.member_id')
            ->orderByDesc('l.id')
            ->limit(30)
            ->get();

        return view('admin.sync-settings', compact('states', 'names', 'logs'));
    }

    public function update(Request $request, string $key)
    {
        abort_unless(Auth::user()?->hasRole('superadmin'), 404);
        abort_unless(isset(SystemSwitch::SWITCHES[$key]), 404);

        $data = $request->validate([
            'on' => ['required', 'boolean'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        SystemSwitch::set($key, (bool) $data['on'], $data['message'] ?? null, Auth::id(), $request->ip());

        return redirect()->route('account.sync')
            ->with('success', SystemSwitch::SWITCHES[$key][0] . ((bool) $data['on'] ? ' is now ON.' : ' is now OFF.'));
    }
}
