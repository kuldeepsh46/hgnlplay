{{-- Developer credit: footer line that opens a small contact card --}}
@php
    $dev = [
        'name' => 'Kuldeep Sharma',
        'role' => 'Full-Stack Web & App Developer',
        'email' => 'sh.kuldeep46@gmail.com',
        'phone_display' => '+91 98882 73036',
        'phone' => '+919888273036',
    ];
    $waText = rawurlencode('Hi Kuldeep, I saw your work on the Himalaya Pay website and would like to discuss a website / app project.');
    $services = ['Business websites', 'Web apps & portals', 'Mobile apps', 'E-commerce', 'Dashboards & admin panels', 'Payments & wallets'];
@endphp

<div class="dev-credit">
    Designed &amp; developed by
    <button type="button" class="dev-credit__btn" aria-haspopup="dialog" aria-controls="devCard">{{ $dev['name'] }}</button>
    <span class="dev-credit__tease">· Need a website or app?</span>
</div>

<dialog id="devCard" class="dev-card" aria-labelledby="devCardTitle">
    <button type="button" class="dev-card__close" aria-label="Close" data-dev-close>&times;</button>
    <div class="dev-card__head">
        <span class="dev-card__avatar" aria-hidden="true">KS</span>
        <div>
            <h2 id="devCardTitle" class="dev-card__name">{{ $dev['name'] }}</h2>
            <p class="dev-card__role">{{ $dev['role'] }}</p>
        </div>
    </div>
    <p class="dev-card__pitch">
        <strong>Liked this platform? I built it</strong> — from the member dashboard to wallets, payouts and team trees.
        Whatever you have in mind, I can design and build it for you.
    </p>
    <ul class="dev-card__services" aria-label="Services">
        @foreach ($services as $service)
            <li>{{ $service }}</li>
        @endforeach
    </ul>
    <p class="dev-card__cta">Tell me about your idea — let's build it together.</p>
    <div class="dev-card__actions">
        <a class="dev-card__link dev-card__link--wa" href="https://wa.me/919888273036?text={{ $waText }}" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.5l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.2-.2-.5-.3Z"/></svg>
            WhatsApp
        </a>
        <a class="dev-card__link" href="tel:{{ $dev['phone'] }}">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/></svg>
            {{ $dev['phone_display'] }}
        </a>
        <a class="dev-card__link" href="mailto:{{ $dev['email'] }}?subject={{ rawurlencode('Website / app project enquiry') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm18 2-10 7L2 6"/></svg>
            {{ $dev['email'] }}
        </a>
    </div>
</dialog>

<style>
    .dev-credit { font-size: 14px; color: #586d63; }
    .dev-credit__btn {
        padding: 0; border: 0; background: none; cursor: pointer;
        font: inherit; font-weight: 700; color: #08734f;
        text-decoration: underline; text-decoration-thickness: 1px; text-underline-offset: 3px;
    }
    .dev-credit__btn:hover { color: #056344; text-decoration-thickness: 2px; }

    .dev-card {
        width: min(400px, calc(100vw - 32px));
        padding: 26px 24px 22px;
        border: 0;
        border-radius: 20px;
        background: #fff;
        color: #142e25;
        box-shadow: 0 30px 80px rgba(13, 77, 51, .28);
        font-family: Inter, system-ui, sans-serif;
    }
    .dev-card::backdrop { background: rgba(20, 46, 37, .45); backdrop-filter: blur(2px); }
    .dev-card__close {
        position: absolute; top: 10px; right: 10px;
        width: 36px; height: 36px; border: 0; border-radius: 50%;
        background: transparent; color: #496359; font-size: 24px; line-height: 1; cursor: pointer;
    }
    .dev-card__close:hover { background: #eef5f0; color: #142e25; }
    .dev-card__head { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
    .dev-card__avatar {
        display: grid; place-items: center; flex-shrink: 0;
        width: 54px; height: 54px; border-radius: 16px;
        background: linear-gradient(135deg, #0f8a5f, #5f48aa); color: #fff;
        font-weight: 800; font-size: 19px; letter-spacing: .5px;
    }
    .dev-card__name { margin: 0; font-size: 20px; font-weight: 800; color: #123c2a; }
    .dev-card__role { margin: 2px 0 0; font-size: 13px; color: #496359; }
    .dev-card__pitch { margin: 0 0 14px; font-size: 14px; line-height: 1.6; color: #33584a; }
    .dev-card__pitch strong { color: #123c2a; }
    .dev-card__services { display: flex; flex-wrap: wrap; gap: 6px; margin: 0 0 14px; padding: 0; list-style: none; }
    .dev-card__services li {
        padding: 5px 10px; border-radius: 99px; font-size: 12px; font-weight: 600;
        background: #e3f3e9; color: #0b5a3c;
    }
    .dev-card__cta { margin: 0 0 14px; font-size: 14px; font-weight: 700; color: #123c2a; }
    .dev-credit__tease { color: #586d63; }
    @media (max-width: 420px) { .dev-credit__tease { display: none; } }
    .dev-card__actions { display: flex; flex-direction: column; gap: 10px; }
    .dev-card__link {
        display: flex; align-items: center; gap: 12px;
        min-height: 46px; padding: 10px 14px; border-radius: 12px;
        border: 1px solid #cfe3d7; background: #f6fbf8;
        color: #142e25 !important; font-size: 15px; font-weight: 600; text-decoration: none;
        overflow-wrap: anywhere;
    }
    .dev-card__link svg { width: 20px; height: 20px; flex-shrink: 0; color: #08734f; }
    .dev-card__link:hover { border-color: #08734f; background: #eaf6ef; }
    .dev-card__link--wa { background: #1c8f4c; border-color: #1c8f4c; color: #fff !important; }
    .dev-card__link--wa svg { color: #fff; }
    .dev-card__link--wa:hover { background: #157a3f; border-color: #157a3f; }
</style>

<script>
    (function () {
        var dlg = document.getElementById('devCard');
        var btn = document.querySelector('.dev-credit__btn');
        if (!dlg || !btn || typeof dlg.showModal !== 'function') {
            // Very old browsers: fall back to email
            if (btn) btn.addEventListener('click', function () { location.href = 'mailto:{{ $dev['email'] }}'; });
            return;
        }
        btn.addEventListener('click', function () { dlg.showModal(); });
        dlg.querySelector('[data-dev-close]').addEventListener('click', function () { dlg.close(); });
        dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
        dlg.addEventListener('close', function () { btn.focus(); });
    })();
</script>
