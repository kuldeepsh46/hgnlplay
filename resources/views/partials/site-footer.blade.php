{{-- Slim footer for app pages (member, admin, auth): copyright + developer credit --}}
<footer class="site-footer" role="contentinfo">
    <div class="site-footer__copy">© {{ date('Y') }} Himalaya Pay. All rights reserved.</div>
    @include('partials.developer-credit')
</footer>

<style>
    .site-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 6px 20px;
        margin-top: 32px;
        padding: 16px 4px 4px;
        border-top: 1px solid #d3e6da;
        font-family: Inter, system-ui, sans-serif;
        font-size: 13px;
        color: #586d63;
    }
    .site-footer .dev-credit { font-size: 13px; }
    @media (max-width: 600px) {
        .site-footer { flex-direction: column; align-items: flex-start; }
    }
</style>
