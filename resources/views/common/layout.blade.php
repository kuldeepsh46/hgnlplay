<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title') - HGNL Pay</title>

    <meta name="theme-color" content="#0f141b" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --bg:#f0f8ef;
    --card:#ffffff;
    --sidebar:#ffffff;
    --accent:#287b62;
    --accent2:#287b62;
    --accent-strong:#08734f;
    --accent-text:#08734f;   /* accent tuned for text on light surfaces (AA contrast) */
    --border:#cddfd3;
    --text:#203c30;
    --muted:#52695f;
    --focus:#218e65;
    --radius:12px;
}

/* ===== RESET ===== */
*{box-sizing:border-box}
html{-webkit-text-size-adjust:100%}

body{
    margin:0;
    font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;
    background:var(--bg);
    color:var(--text);
    min-height:100vh;
    display:flex;
    line-height:1.5;
    -webkit-font-smoothing:antialiased;
}

a{color:var(--accent-text)}
img{max-width:100%}

h2{
    margin-top:0;
    font-size:22px;
}

/* ===== ACCESSIBILITY ===== */
.skip-link{
    position:absolute;
    left:12px;
    top:-60px;
    z-index:2000;
    background:#08734f;
    color:#fff;
    padding:10px 16px;
    border-radius:8px;
    font-weight:600;
    text-decoration:none;
    transition:top .15s;
}
.skip-link:focus{top:12px}

:focus-visible{
    outline:2px solid var(--focus);
    outline-offset:2px;
}
input:focus-visible,select:focus-visible,textarea:focus-visible{outline-offset:0}

.sr-only{
    position:absolute;width:1px;height:1px;padding:0;margin:-1px;
    overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;
}

@media (prefers-reduced-motion: reduce){
    *,*::before,*::after{
        animation-duration:.01ms !important;
        animation-iteration-count:1 !important;
        transition-duration:.01ms !important;
        scroll-behavior:auto !important;
    }
}

/* Thin, theme-matched scrollbars */
*{scrollbar-width:thin;scrollbar-color:#a9cbbb transparent}
::-webkit-scrollbar{width:8px;height:8px}
::-webkit-scrollbar-thumb{background:#a9cbbb;border-radius:8px}
::-webkit-scrollbar-track{background:transparent}

/* ===== SHARED BITS ===== */
div#passwordModal h2{color:var(--text)}
nav[aria-label="Pagination Navigation"] > div:first-child{padding-top:20px}
.modal-content p{line-height:1.6;text-align:center}

th, td{white-space:nowrap}
/* Long text columns (remarks, subject ...) read left-aligned and wrap */
th.long-text, td.long-text{
    text-align:left !important;
    white-space:normal;
    min-width:260px;
    max-width:520px;
    overflow-wrap:anywhere;
    line-height:1.45;
}
.table-res{overflow-x:auto;-webkit-overflow-scrolling:touch}

.btn, .btn-primary{color:#fff !important}
button{font-family:inherit}

/* ===== SIDEBAR ===== */
.sidebar{
    width:250px;
    flex-shrink:0;
    background:var(--sidebar);
    border-right:1px solid var(--border);
    display:flex;
    flex-direction:column;
    position:relative;
    transition:left .3s ease, box-shadow .3s ease;
    z-index:1200;
}
.sidebar ul{list-style:none;margin:0;padding:0}

.sidebar-nav{
    flex:1;
    min-height:0;
    overflow-y:auto;
    padding:6px 0 16px;
}

/* Every navigable row — links, accordion toggles and the logout button — shares one look */
.sidebar-nav > ul > li > a,
.sidebar-nav .team-menu,
.sidebar-nav .logout-item button{
    display:flex;
    align-items:center;
    gap:10px;
    width:100%;
    padding:12px 18px;
    color:var(--muted);
    text-decoration:none;
    font-size:15px;
    font-weight:500;
    background:none;
    border:0;
    border-left:4px solid transparent;
    text-align:left;
    cursor:pointer;
    transition:background .2s, color .2s, border-color .2s;
}
/* "body" prefix keeps these ahead of the generic li rules in light-green-theme.css */
body .sidebar-nav > ul > li > a:hover,
body .sidebar-nav .team-menu:hover,
body .sidebar-nav .logout-item button:hover,
body .sidebar-nav > ul > li.team-item.active > .team-menu{
    background:#e3f3e9;
    color:#123c2a;
    border-left-color:var(--accent-strong);
}
body .sidebar-nav > ul > li.active > a{
    background:#08734f;
    color:#fff;
    border-left-color:#63dba0;
}
body .sidebar ul li:hover,
body .sidebar ul li.active{background:transparent}
.sidebar-nav :focus-visible{outline-offset:-2px}

.nav-icon{
    width:22px;
    text-align:center;
    flex-shrink:0;
}

.logout-item{
    margin-top:8px;
    padding-top:8px;
    border-top:1px solid var(--border);
}
.logout-item form{margin:0}
body .sidebar-nav .logout-item button:hover{color:#b9304b;background:#fbe9ed;border-left-color:#b9304b}

/* Accordion groups */
.team-item{list-style:none}
.sidebar-nav .team-menu{justify-content:space-between}
.team-title{display:flex;align-items:center;gap:10px}
.team-menu .arrow{transition:transform .3s ease;font-size:14px}
.team-item.open > .team-menu .arrow{transform:rotate(180deg)}

.submenu{
    max-height:0;
    overflow:hidden;
    visibility:hidden;
    transition:max-height .35s ease, visibility .35s;
    padding-left:22px !important;
}
.team-item.open > .submenu{
    max-height:500px;
    visibility:visible;
}
.submenu li a{
    display:block;
    padding:10px 14px;
    margin:2px 10px 2px 0;
    color:var(--muted);
    text-decoration:none;
    font-size:14px;
    border-radius:8px;
    border-left:3px solid transparent;
    transition:background .2s, color .2s;
}
.submenu li a:hover,
.submenu li a.active{
    color:#123c2a;
    background:#e3f3e9;
    border-left-color:var(--accent-strong);
}

/* ===== LOGO ===== */
.logo{
    display:flex;
    align-items:center;
    justify-content:center;
    padding:12px 14px;
}
.logo img{
    display:block;
    width:auto;
    max-width:100%;
    height:64px;
    object-fit:contain;
    border-radius:8px;
}
.logo h1{font-size:20px}
.logo h1 span{color:#b64f70}

/* ===== MAIN CONTENT ===== */
main.main{
    flex:1;
    min-width:0;
    padding:20px;
    overflow-x:hidden;
}
main.main:focus{outline:none}

/* ===== OVERLAY ===== */
#sidebarOverlay{
    position:fixed;
    inset:0;
    background:rgba(20,46,37,0.45);
    display:none;
    z-index:1150;
}

.mobile-header{display:none}

.toggle-btn{
    background:#08734f;
    color:#fff;
    width:44px;
    height:44px;
    border:0;
    border-radius:50%;
    display:grid;
    place-items:center;
    font-size:20px;
    cursor:pointer;
    box-shadow:0 0 0 4px rgba(8,115,79,.18);
    transition:background .2s;
}
.toggle-btn:hover{background:var(--accent-strong)}

/* ===== MOBILE ===== */
@media (max-width:768px){
    body{display:block !important}

    .mobile-header{
        position:fixed;
        top:0;
        left:0;
        right:0;
        z-index:1300;
        height:72px;
        background:var(--sidebar);
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:8px 14px;
        border-bottom:1px solid var(--border);
        box-shadow:0 6px 20px -12px rgba(13,77,51,.35);
    }
    .mobile-header .logo{padding:0;max-width:60%}
    .mobile-header .logo img{height:54px;width:auto}

    /* The drawer opens below the top bar so the toggle stays reachable as a close button */
    .sidebar{
        position:fixed;
        top:72px;
        bottom:0;
        left:-280px;
        width:260px;
    }
    .sidebar > .logo{display:none}
    #sidebarOverlay{top:72px}
    .sidebar.open{
        left:0;
        box-shadow:12px 0 40px rgba(13,77,51,.25);
    }
    .sidebar.open ~ #sidebarOverlay{display:block}
    body.sidebar-open{overflow:hidden}

    main.main{padding:92px 14px 24px}

    .profile-card{padding-bottom:20px !important}
}

/* ===== DESKTOP ===== */
@media (min-width:769px){
    main.main{padding:24px 32px 32px 40px}
    /* Keep the sidebar in view while the page scrolls; long menus scroll inside it */
    .sidebar{
        position:sticky;
        top:0;
        height:100vh;
        align-self:flex-start;
    }
}

/* ===== FORMS ===== */
input[type="date"]::-webkit-calendar-picker-indicator{
    filter:none;
    cursor:pointer;
}

/* ===== PAGE HEADER ===== */
.header{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
.header h1{margin:0;font-size:22px}

.user-info{
    background:#ffffff;
    padding:8px 14px;
    border-radius:999px;
    display:flex;
    align-items:center;
    gap:8px;
    color:#b64f70 !important;
    font-weight:600;
}

.btn-view{
    background:var(--accent);
    color:#fff;
    border:none;
    padding:10px 12px;
    border-radius:6px;
    cursor:pointer;
    font-weight:600;
    transition:background .2s;
}
.btn-view:hover{background:var(--accent-strong)}

/* ===== PAYMENT DUE ALERT ===== */
.pay-alert{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:16px 20px;
    padding:16px 20px;
    margin-bottom:20px;
    border-radius:14px;
    border:1px solid #f0c77a;
    border-left:6px solid #d99211;
    background:#fff8e8;
    color:#3d2a05;
    box-shadow:0 8px 24px rgba(13,77,51,.06);
}
.pay-alert--overdue{border-color:#efb9c5;border-left-color:#b9304b;background:#fff4f6;color:#4d1020}
.pay-alert--upcoming{border-color:#b3c8ec;border-left-color:#3759a3;background:#f3f7ff;color:#1b2c52}
.pay-alert__icon{font-size:28px;line-height:1}
.pay-alert__body{flex:1 1 320px;min-width:0}
.pay-alert__title{margin:0 0 8px;font-size:17px;color:inherit}
.pay-alert__facts{display:flex;flex-wrap:wrap;gap:6px 24px;margin:0}
.pay-alert__facts div{display:flex;flex-direction:column}
.pay-alert__facts dt{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;opacity:.75}
.pay-alert__facts dd{margin:0;font-weight:700;font-size:15px}
.pay-alert__note{margin:8px 0 0;font-size:13px;opacity:.85}
.pay-alert__actions{display:flex;flex-wrap:wrap;align-items:center;gap:10px}
.pay-alert__btn{
    display:inline-flex;align-items:center;justify-content:center;
    min-height:42px;padding:9px 16px;border-radius:10px;
    border:2px solid currentColor;background:#fff;color:inherit;
    font-weight:700;font-size:14px;text-decoration:none;white-space:nowrap;
}
.pay-alert__btn--primary{background:#08734f;border-color:#08734f;color:#fff}
.pay-alert--overdue .pay-alert__btn--primary{background:#b9304b;border-color:#b9304b}
.pay-alert__btn:hover{filter:brightness(.95)}
.pay-alert__close{
    width:36px;height:36px;border-radius:50%;border:0;
    background:transparent;color:inherit;font-size:16px;cursor:pointer;
}
.pay-alert__close:hover{background:rgba(0,0,0,.06)}
@media (max-width:768px){
    .pay-alert__actions{width:100%}
    .pay-alert__btn{flex:1}
}

.nav-badge{
    margin-left:auto;
    min-width:22px;
    padding:2px 7px;
    border-radius:99px;
    background:#b9304b;
    color:#fff;
    font-size:12px;
    font-weight:700;
    text-align:center;
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="{{ asset('css/light-green-theme.css') }}">
</head>

<body>

<a class="skip-link" href="#main-content">Skip to main content</a>

{{-- HEADER / SIDEBAR --}}
@include('common.header')

{{-- OVERLAY (must come after the sidebar) --}}
<div id="sidebarOverlay" aria-hidden="true"></div>

{{-- MAIN CONTENT --}}
<main class="main" id="main-content" tabindex="-1">
    @unless (request()->routeIs('payments.due'))
        @include('partials.payment-due-alert')
    @endunless
    @yield('main')
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('toggleBtn');
    const overlay = document.getElementById('sidebarOverlay');
    const isMobile = () => window.matchMedia('(max-width: 768px)').matches;

    function setOpen(open) {
        sidebar.classList.toggle('open', open);
        document.body.classList.toggle('sidebar-open', open);
        if (toggle) {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
            toggle.firstElementChild.textContent = open ? '✕' : '☰';
        }
        if (open) {
            const first = sidebar.querySelector('a, button');
            if (first) first.focus();
        }
    }

    if (toggle) {
        toggle.addEventListener('click', () => setOpen(!sidebar.classList.contains('open')));
    }
    overlay.addEventListener('click', () => setOpen(false));

    // Navigating from the drawer closes it on small screens
    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => { if (isMobile()) setOpen(false); });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            setOpen(false);
            if (toggle) toggle.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (!isMobile() && sidebar.classList.contains('open')) setOpen(false);
    });

    // Accordion groups (Profile, Team Detail, Settings)
    document.querySelectorAll('.team-menu').forEach(menu => {
        menu.addEventListener('click', () => {
            const parent = menu.closest('.team-item');
            const willOpen = !parent.classList.contains('open');

            document.querySelectorAll('.team-item.open').forEach(item => {
                if (item !== parent) {
                    item.classList.remove('open');
                    item.querySelector('.team-menu').setAttribute('aria-expanded', 'false');
                }
            });

            parent.classList.toggle('open', willOpen);
            menu.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    });
});
</script>

</body>
</html>
