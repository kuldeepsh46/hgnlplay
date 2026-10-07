@extends('common.layout')
@section('title', 'Sync Settings')
@section('main')
<style>
.ssw .card { background: var(--card); border: 1px solid #e5f2e6; border-radius: var(--radius); padding: 20px; margin-bottom: 24px; }
.ssw h2 { margin: 0 0 6px; font-size: 18px; }
.ssw .muted { color: #586d63; font-size: 13px; margin: 0 0 12px; }
.ssw .banner { padding: 12px 16px; border-radius: 10px; margin-bottom: 18px; font-weight: 600; }
.ssw .banner.ok { background: rgba(53, 224, 140, .1); border: 1px solid rgba(53, 224, 140, .35); color: #7ee2b0; }
.ssw .banner.warn { background: rgba(255, 92, 92, .1); border: 1px solid rgba(255, 92, 92, .4); color: #ff9a9a; }
.ssw .flash { padding: 10px 14px; border-radius: 8px; background: #ffffff; border: 1px solid #e5f2e6; margin-bottom: 16px; }
.ssw .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(330px, 1fr)); gap: 16px; }
.ssw .sw { background: #ffffff; border: 1px solid #e5f2e6; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 10px; }
.ssw .sw.on { border-color: rgba(255, 92, 92, .6); box-shadow: 0 0 0 1px rgba(255, 92, 92, .25) inset; }
.ssw .sw.big { grid-column: 1 / -1; }
.ssw .sw-head { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
.ssw .sw-head b { font-size: 15px; color: #203c30; }
.ssw .pill { font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; letter-spacing: .5px; }
.ssw .pill.on { background: #c23b3b; color: #203c30; }
.ssw .pill.off { background: #e5f2e6; color: #203c30; }
.ssw .sw p { margin: 0; font-size: 13px; color: #203c30; line-height: 1.5; }
.ssw .sw small { color: #586d63; font-size: 12px; }
.ssw .sw form { display: flex; gap: 8px; flex-wrap: wrap; }
.ssw .sw input[type=text] { flex: 1; min-width: 160px; padding: 8px; border-radius: 6px; background: #ffffff; border: 1px solid #e5f2e6; color: #203c30; }
.ssw .btn { padding: 9px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 13px; }
.ssw .btn-on { background: #c23b3b; color: #fff; }
.ssw .btn-off { background: #2f8f5b; color: #fff; }
.ssw table { width: 100%; border-collapse: collapse; }
.ssw th, .ssw td { border: 1px solid #e5f2e6; padding: 9px; font-size: 13px; text-align: left; }
.ssw th { background: #e5f2e6; color: #203c30; }
</style>

@php $activeCount = collect($states)->where('is_on', true)->count(); @endphp

<div class="ssw">
    <div class="header">
        <h1>Sync Settings</h1>
        <div class="user-info">👑 {{ Auth::user()->username ?? Auth::user()->name }}</div>
    </div>

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <div class="banner {{ $activeCount ? 'warn' : 'ok' }}">
        {{ $activeCount ? "⚠️ {$activeCount} switch" . ($activeCount > 1 ? 'es are' : ' is') . ' ON. Parts of the site are paused for everyone except superadmins.' : '✅ All switches are off. The site is running normally.' }}
    </div>

    <div class="card">
        <p class="muted">Turning a switch ON pauses that part of the site straight away, for members and admins alike. Superadmins are never blocked, so you can always come back here and turn it off. The optional message is what people see instead.</p>

        <div class="grid">
            @foreach (\App\Services\SystemSwitch::SWITCHES as $key => [$label, $help])
                @php
                    $row = $states[$key] ?? null;
                    $isOn = (bool) ($row->is_on ?? false);
                @endphp
                <div class="sw {{ $isOn ? 'on' : '' }} {{ in_array($key, ['site_offline', 'freeze_all']) ? 'big' : '' }}">
                    <div class="sw-head">
                        <b>{{ $key === 'site_offline' ? '🛑 ' : ($key === 'freeze_all' ? '🧊 ' : '') }}{{ $label }}</b>
                        <span class="pill {{ $isOn ? 'on' : 'off' }}">{{ $isOn ? 'ON' : 'OFF' }}</span>
                    </div>
                    <p>{{ $help }}</p>
                    @if ($row && $row->updated_at)
                        <small>Last changed {{ \Carbon\Carbon::parse($row->updated_at)->format('d M Y, h:i A') }}{{ isset($names[$row->updated_by]) ? ' by ' . $names[$row->updated_by] : '' }}</small>
                    @endif
                    <form method="POST" action="{{ route('account.sync.update', $key) }}"
                          onsubmit="return confirm('{{ $isOn ? 'Turn OFF' : 'Turn ON' }}: {{ addslashes($label) }}?');">
                        @csrf
                        <input type="hidden" name="on" value="{{ $isOn ? 0 : 1 }}">
                        @unless ($isOn)
                            <input type="text" name="message" maxlength="500" placeholder="Message to show (optional)">
                        @endunless
                        <button type="submit" class="btn {{ $isOn ? 'btn-off' : 'btn-on' }}">{{ $isOn ? 'Turn OFF' : 'Turn ON' }}</button>
                    </form>
                    @if ($isOn && $row->message)
                        <small>Showing: “{{ $row->message }}”</small>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h2>Recent changes</h2>
        <div style="overflow-x:auto;">
            <table>
                <thead><tr><th>When</th><th>Switch</th><th>Turned</th><th class="long-text">Message</th></tr></thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}</td>
                            <td>{{ \App\Services\SystemSwitch::SWITCHES[$log->key][0] ?? $log->key }}</td>
                            <td>{{ $log->is_on ? 'ON' : 'OFF' }}</td>
                            <td class="long-text">{{ $log->message ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">No switch has been changed yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
