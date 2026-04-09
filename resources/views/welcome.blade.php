<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Himalaya Trading – Empowering People, Creating Opportunities</title>

    <!-- Google Font (safe fallback to system fonts) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    :root {
        --bg: #f9f9f9;
        --card: #f8f8f8;
        --muted: #393939;
        --text: #2e2e2f;
        --text-dim: #575757;
        --accent: #3f5193;
        /* neon lime */
        --accent-2: #293b8f;
        /* teal-green */
        --accent-3: #12d1ff;
        /* cyan for subtle touches */
        --border: #e6e6e6;
        --ring: 0 0 0 8px #3f51b51a,, 0 0 60px 10px #3f51b51a inset;
        --radius: 14px;
        --shadow: 0 10px 30px rgba(0, 0, 0, .45), 0 4px 14px rgba(0, 0, 0, .35);
    }

    * {
        box-sizing: border-box
    }

    @media (max-width: 520px) {
        .img-main img {
            height: 310px !important;
            object-fit: cover;
        }
    }

    html,
    body {
        height: 100%
    }

    body {
        margin: 0;
        font-family: "Inter", -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        background: #fff;
        color: var(--text);
        line-height: 1.6;
        overflow-x: hidden;
    }

    a {
        color: inherit;
        text-decoration: none
    }

    nav a {
        color: #515252 !important;
        font-weight: 600;
    }

    img {
        max-width: 100%;
        display: block
    }

    svg {
        display: block
    }

    /* ---------- Layout ---------- */
    .container {
        width: min(1200px, 92vw);
        margin: 0 auto
    }


    .nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 72px;
        gap: 20px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px
    }

    header {
        background: #fff;
        padding: 6px 0px;

    }

    .logo img {
        height: 59px;
        width: 100%;
        object-fit: contain;
        max-width: 100%;
    }

    /* .logo {
        width: 60px;
        height: 60px;


        display: grid;
        place-items: center;
        color: #06170a;
        font-weight: 900
    } */

    .brand h1 {
        font-size: 18px;
        margin: 0;
        letter-spacing: .5px
    }

    nav ul {
        display: flex;
        gap: 28px;
        align-items: center;
        margin: 0;
        padding: 0;
        list-style: none
    }

    nav a {
        color: var(--text-dim);
        font-weight: 600
    }

    nav a:hover {
        color: #000000
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 16px;
        border-radius: 10px;
        font-weight: 700;
        transition: .22s;
        border: 1px solid transparent
    }

    .btn-primary {
        background: linear-gradient(90deg, var(--accent), #3f5193);
        color: #ffffff;
        box-shadow: var(--ring);
        position: relative;
        z-index: 9;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 0 0 10px #a7ff1e1f, var(--ring)
    }

    .btn-ghost {
        border-color: #2a323b;
        color: #dce7f0;
        background: #131922
    }

    .btn-ghost:hover {
        border-color: #3a4450;
        background: #171f2a
    }

    .btn-outline {
        border-color: #93ff1e33;
        color: #ffffffff;
        background: #10171e;
    }

    .btn-outline:hover {
        background: #131e26;
        border-color: #3f5193;
    }

    .nav-cta {
        display: flex;
        gap: 10px
    }

    /* Mobile nav */
    .burger {
        display: none;
        flex-direction: column;
        gap: 4px;
        width: 28px;
        cursor: pointer
    }

  .burger span {
    height: 3px;
    background: #000000;
    border-radius: 99px;
}

   .mobile-menu {
    display: none;
    position: absolute;
    left: 0;
    top: 72px;
    width: 100%;
    background: #fffffffa;
    border-top: 1px solid #121a22;
    z-index: 99999;
}

    .mobile-menu.open {
        display: block
    }

    .mobile-menu ul {
        list-style: none;
        margin: 0;
        padding: 12px
    }
.mobile-menu li {
    border: 1px solid #919191;
    border-radius: 10px;
    padding: 12px;
    margin: 8px 0;
    background: #ffffff;
}

    .mobile-menu .nav-cta {
        padding: 12px;
        gap: 8px
    }

    /* ---------- Hero ---------- */
    .hero {
        position: relative;
        padding: 80px 0 60px;
        overflow: hidden
    }

    .hero .container {
        display: grid;
        grid-template-columns: 1.05fr .95fr;
        gap: 36px;
        align-items: center
    }

    .eyebrow {
        display: inline-flex;
        gap: 8px;
        align-items: center;
        color: #a6ffc4;
        background: #0d1913;
        border: 1px solid #1a3326;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 13px
    }

    .eyebrow:before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 30%, #a7ff1e, #2ee6a6)
    }

    .hero h2 {
        font-size: 40px;
        line-height: 1.18;
        margin: 14px 0 10px;
        font-weight: 800;
        letter-spacing: .2px
    }

    .hero h2 .focus {
        color: var(--accent);
        text-shadow: 0 0 24px #bfff2d60
    }

    .lead {
        color: var(--text-dim);
        max-width: 600px;
        margin: 0 0 22px
    }

    .cta {
        display: flex;
        gap: 12px;
        flex-wrap: wrap
    }

    .illustration {
        position: relative;
        background: radial-gradient(120% 100% at 60% 40%, #1b2330 0%, #131a22 55%, #0e141c 100%);
        border: 1px solid #1e2834;
        border-radius: 22px;
        padding: 26px;
        box-shadow: var(--shadow)
    }

    .glow {
        position: absolute;
        inset: -30px;
        border-radius: 26px;
        background: radial-gradient(600px 300px at 70% 20%, #bfff2d1c, transparent 60%),
            radial-gradient(420px 260px at 20% 90%, #12d1ff18, transparent 60%);
        pointer-events: none;
        filter: blur(6px)
    }

    /* Dummy hero art (replace with your image later) */
    .hero-art {
        width: 100%;
        aspect-ratio: 16/11;
        border-radius: 16px;
        border: 1px solid #2a3542;
        background:
            radial-gradient(120px 60px at 25% 30%, #1e2b39 0%, transparent 70%),
            radial-gradient(90px 50px at 42% 58%, #1b2937 0%, transparent 70%),
            linear-gradient(180deg, #0f1720, #0a0f16);
        position: relative;
        overflow: hidden
    }

    .hero-art::after {
        content: "DUMMY CHART";
        position: absolute;
        inset: auto 14px 14px auto;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid #2a3340;
        color: #8da3b5;
        font-size: 12px;
        background: #0f1620
    }

    .chart-line {
        position: absolute;
        left: 14px;
        right: 14px;
        top: 18px;
        height: 58%;
        background:
            radial-gradient(circle at 30% 70%, #a7ff1e 3px, transparent 4px) 0 18px/48px 22px repeat,
            linear-gradient(120deg, #2ee6a6, #a7ff1e 50%, #12d1ff);
        -webkit-mask:
            linear-gradient(#000 0 0) top/100% 2px no-repeat,
            linear-gradient(#000 0 0) bottom/100% 2px no-repeat,
            repeating-linear-gradient(transparent 0 18px, #000 18px 20px);
        mask:
            linear-gradient(#000 0 0) top/100% 2px no-repeat,
            linear-gradient(#000 0 0) bottom/100% 2px no-repeat,
            repeating-linear-gradient(transparent 0 18px, #000 18px 20px);
        border-radius: 8px;
        opacity: .85;
        filter: drop-shadow(0 0 12px #bfff2d55)
    }

    /* Decorative stars */
    .star {
        position: absolute;
        width: 12px;
        height: 12px;
        rotate: 45deg;
        background: radial-gradient(#a7ff1e, #76ff00 45%, transparent 46%);
        filter: drop-shadow(0 0 12px #a7ff1e88)
    }

    .star.s1 {
        top: 30px;
        left: 20px
    }

    .star.s2 {
        top: 160px;
        left: 120px;
        scale: .8
    }

    .star.s3 {
        top: 56%;
        left: 8%;
        scale: .9
    }

    /* ---------- Section Head ---------- */
    .section {
        padding: 72px 0
    }

   .section .title-eyebrow {
    display: inline-flex;
    gap: 6px;
    align-items: center;
    margin-bottom: 14px;
    color: #485998;
    font-weight: 700;
    font-size: 12px;
    opacity: .9;
}

    .section h3 {
        margin: 0 0 14px;
        font-size: 28px
    }

    /* ---------- About ---------- */
    .about {
        background: radial-gradient(900px 380px at 20% 20%, #0f1d19 0%, transparent 60%),
            radial-gradient(900px 380px at 90% 0%, #0f1626 0%, transparent 60%);
        border-top: 1px solid #0e151d;
        border-bottom: 1px solid #0e151d;
        display: none;
    }

    .about-grid {
        display: grid;
        grid-template-columns: 1.1fr .9fr;
        gap: 38px;
        align-items: center
    }

    .about-card {
        border: 1px solid #1d2632;
        background: linear-gradient(180deg, #101823 0%, #0b1118 100%);
        border-radius: 20px;
        padding: 24px;
        box-shadow: var(--shadow)
    }

    .about p {
        color: var(--text-dim)
    }

    /* Dummy image block */
    .about-illus {
        border: 1px solid #243142;
        border-radius: 18px;
        padding: 16px;
        background: #0f1620
    }

    .about-illus .screen {
        height: 240px;
        border-radius: 12px;
        border: 1px dashed #2a3a4d;
        display: grid;
        place-items: center;
        color: #7fa0b9
    }

    /* ---------- How It Works ---------- */
    .how {
        position: relative
    }

    .steps {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-top: 20px
    }

    .step {
        text-align: center;
        border: 1px solid #5a5a5a;
        border-radius: 16px;
        background: #ffffff;
        padding: 20px 16px;
        box-shadow: 0 10px 24px #0000002b;
    }

 .icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 10px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: radial-gradient(circle at 30% 30%, #14201a0a, #0f181200);
    border: 1px solid #bdbdbd;
    box-shadow: 0 0 0 8px #6b6c6810;
}

    .icon svg {
        width: 32px;
        height: 32px;
        stroke: var(--accent);
        fill: none;
        stroke-width: 2
    }

    .step h4 {
        margin: 6px 0 6px
    }

.step p {
    margin: 0;
    color: #272828;
    font-size: 14px;
}

    .dotted {
        position: absolute;
        left: 6%;
        right: 6%;
        top: 116px;
        height: 2px;
        border-top: 2px dotted #39e5a6;
        opacity: .45
    }

    /* ---------- CTA Banner ---------- */
    .cta-band {
        margin-top: 40px;
        border: 1px solid #b0b0b0;
        border-radius: 18px;
        position: relative;
        overflow: hidden;
        background: #fff;
    }

    .cta-band .inner {
        padding: 26px 22px;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 18px;
        align-items: center
    }

    .cta-band .note {
        color: #070808
    }

   .cta-band .inner:before {
    content: "";
    position: absolute;
    inset: -40px -10px auto auto;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: radial-gradient(circle, #bfff2d22, transparent 60%);
    filter: blur(12px);
}

    /* ---------- Footer ---------- */
   footer {
    border-top: 1px solid #e7e7e73e;
    padding: 60px 0 20px;
    background: #fff;
}

    .footer-top {
        display: grid;
        grid-template-columns: 1.2fr 1fr 1fr 1fr;
        gap: 30px;
        margin-bottom: 26px
    }

    .f-brand .mini-logo {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: conic-gradient(from 140deg, #2ee6a6, #a7ff1e, #12d1ff, #2ee6a6)
    }

    .f-brand p {
        color: #97a8b4
    }

    .f-col h5 {
        margin: 0 0 10px
    }

    .f-col ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 10px
    }
.f-col a {
    color: #343434;
}

    .f-col a:hover {
        color: #000000
    }

    .social {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        padding-bottom:10px;
    }

    .social a {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        border: 1px solid #273342;
        background: #0f1620;
        color: #b7c5d1
    }

    .copy {
        border-top: 1px solid #131a24;
        margin-top: 24px;
        padding-top: 14px;
        color: #8ea0ae;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap
    }

    .brand-green {
        color: #263789;
    }

    /* ---------- Floating Chat/Top Button ---------- */
    .float-btn {
        position: fixed;
        right: 16px;
        bottom: 16px;
        width: 35px;
        height: 35px;
        border-radius: 999px;
        border: 1px solid #2a3543;
        display: grid;
        place-items: center;
        background: #ffffff;
        box-shadow: 0 12px 28px #00000050;
        cursor: pointer;
        z-index: 40;
    }

    .float-btn:hover {
        border-color: #3a4758;
        transform: translateY(-2px)
    }

    .float-btn svg {
        width: 22px;
        height: 22px;
        stroke: #c7d7e3
    }

    /* ---------- Animations ---------- */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(14px)
        }

        to {
            opacity: 1;
            transform: none
        }
    }

    .reveal {
        opacity: 0;
        transform: translateY(14px)
    }

    .reveal.show {
        animation: fadeUp .7s cubic-bezier(.2, .7, .2, 1) forwards
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 1024px) {
        .hero .container {
            grid-template-columns: 1fr;
            gap: 26px
        }

        .about-grid {
            grid-template-columns: 1fr;
            gap: 22px
        }

        .footer-top {
            grid-template-columns: 1.2fr 1fr 1fr
        }

        .dotted {
            display: none
        }
    }

    @media (max-width: 780px) {
        nav ul {
            display: none
        }

        .burger {
            display: flex
        }

        .nav-cta {
            display: none
        }

        .hero h2 {
            font-size: 34px
        }

        .footer-top {
            grid-template-columns: 1fr 1fr
        }

        .steps {
            grid-template-columns: 1fr 1fr
        }

        .cta-band .inner {
            grid-template-columns: 1fr
        }
    }

    @media (max-width: 520px) {
        .img-main img {
            height: 310px !important;
            object-fit: cover;
        }

        .footer-top {
            grid-template-columns: 1fr !important;
        }

        .steps {
            grid-template-columns: 1fr
        }

        .hero {
            padding-top: 56px
        }

        .hero h2 {
            font-size: 30px
        }

        .btn {
            width: 100%;
            justify-content: center
        }
    }



    /* ============================
   WHY CHOOSE US – DARK THEME
============================ */
    .why-choose-dark {
        padding-bottom: 50px;
        background: var(--bg);
    }

    .why-eyebrow {
        display: block;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1.6px;
        color: var(--accent-2);
        margin-bottom: 0px;
    }

    .why-title {
        text-align: center;
        font-size: 34px;
        font-weight: 800;
        margin-bottom: 14px;
        color: var(--text);
        margin-top: 0px;
    }

    .why-title span {
        color: var(--accent);
    }

    .why-subtitle {
        max-width: 760px;
        margin: 0 auto 56px;
        text-align: center;
        font-size: 16px;
        color: var(--text-dim);
        line-height: 1.7;
    }

    /* Grid */
    .why-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 26px;
    }

    /* Card */
 .why-card {
    background: linear-gradient(180deg, var(--card), #ffffff);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 34px 28px;
    position: relative;
    box-shadow: var(--shadow);
    transition: transform .25s ease, box-shadow .25s ease;
}

    .why-card:hover {
        transform: translateY(-8px);
        box-shadow:
            0 20px 45px rgba(0, 0, 0, .55),
            0 0 0 1px var(--accent);
    }

    /* Number */
 .why-num {
    position: absolute;
    top: 3px;
    right: 24px;
    font-size: 36px;
    font-weight: 800;
    color: #2196F3;
}

    /* Content */
    .why-card h4 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--text);
    }

    .why-card p {
        font-size: 15px;
        color: var(--text-dim);
        line-height: 1.65;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .why-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .why-grid {
            grid-template-columns: 1fr;
        }

        .why-title {
            font-size: 28px;
        }
    }



    /* ==============================
   MLM GROWTH SECTION
============================== */
    .img-main {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .mlm-growth-section {
        padding-top: 40px;
        background: var(--bg);
    }

    .mlm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }

    /* LEFT IMAGE STACK */
    .mlm-images {
        position: relative;
    }

    .img-main {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .img-main img {
        height: 600px;
        object-fit: cover;
    }

    .img-small {
        position: absolute;
        width: 260px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow);
        border: 3px solid var(--bg);
    }

    .img-2 img {
        height: 281px;
        object-fit: cover;
        width: 400px;
        max-width: 400px !important;
    }

    .img-1 {
        top: -30px;
        right: -30px;
    }

    .img-2 {
        bottom: -30px;
        left: -30px;
    }

    .experience-badge {
        position: absolute;
        bottom: -31px;
        right: 6px;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: #ffffff;
        padding: 16px 24px;
        border-radius: 16px;
        text-align: center;
        box-shadow: var(--shadow);
    }

    .experience-badge strong {
        display: block;
        font-size: 22px;
        font-weight: 800;
    }

    .experience-badge span {
        font-size: 18px;
        font-weight: 600;
    }

    /* RIGHT CONTENT */
    .mlm-eyebrow {
        font-size: 12px;
        letter-spacing: 1.6px;
        font-weight: 700;
        color: var(--accent-2);
    }

    .mlm-content h2 {
        font-size: 36px;
        margin: 12px 0 18px;
        color: var(--text);
    }

    .mlm-content h2 span {
        color: var(--accent);
    }

    .mlm-content p {
        color: var(--text-dim);
        line-height: 1.7;
        margin-bottom: 16px;
    }

    .mlm-points {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }

    .mlm-points li {
        margin-bottom: 10px;
        color: var(--text);
        font-weight: 500;
    }

    .mlm-note {
        font-size: 15px;
        margin-top: 10px;
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .mlm-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .img-1,
        .img-2 {
            display: none;
        }
    }



    /* ================================
   FAQ SECTION – MLM THEME
================================ */
    .faq-section {
        padding: 50px 0;
        background: var(--bg);
    }

    .faq-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 70px;
        align-items: flex-start;
    }

    /* LEFT */
    .faq-left h2 {
        font-size: 38px;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
        margin-bottom: 16px;
    }

    .faq-left h2 span {
        color: var(--accent);
    }

    .faq-left p {
        font-size: 16px;
        color: var(--text-dim);
        line-height: 1.7;
        max-width: 420px;
    }

    /* RIGHT */
    .faq-item {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        margin-bottom: 16px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .faq-question {
        width: 100%;
        background: none;
        border: none;
        padding: 20px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--text);
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }

    .faq-icon {
        width: 26px;
        height: 26px;
        background: var(--accent);
        color: #ffffff;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 18px;
        transition: transform .25s ease;
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height .35s ease;
    }

    .faq-answer p {
        padding: 0 22px 20px;
        color: var(--text-dim);
        font-size: 15px;
        line-height: 1.7;
    }

    /* ACTIVE STATE */
    .faq-item.active .faq-answer {
        max-height: 300px;
    }

    .faq-item.active .faq-icon {
        transform: rotate(45deg);
        background: var(--accent-2);
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .faq-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .faq-left p {
            max-width: 100%;
        }
    }

    /* ===============================
   TESTIMONIAL SECTION
=============================== */
    /* ============================
   TESTIMONIAL SECTION
============================ */
    .testimonial-section {
padding-top: 50px;
        /* background: var(--bg); */
        text-align: center;
    }

    .testi-eyebrow {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: var(--accent-2);
    }

    .testi-title {
        font-size: 38px;
        font-weight: 800;
        margin: 0px 0px 20px;
        color: var(--text);
    }

    /* WRAPPER */
    .testimonial-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    /* VIEWPORT */
    .testimonial-viewport {
        overflow: hidden;
        width: 100%;
    }

    /* TRACK */
    .testimonial-track {
        display: flex;
        gap: 24px;
        transition: transform .45s ease;
        will-change: transform;
    }

    .testimonial-track.no-transition {
        transition: none;
    }

    /* CARD */
    .testimonial-card {
        flex: 0 0 calc(33.333% - 16px);
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 30px 24px;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
    }

    .testimonial-card img {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        margin-bottom: 10px;
        margin: 0px auto;
    }

    .testimonial-card h4 {
        margin: 6px 0 2px;
        font-size: 18px;
        color: var(--text);
    }

    .testimonial-card span {
        font-size: 13px;
        color: var(--accent);
        font-weight: 600;
    }

    .testimonial-card p {
        margin-top: 14px;
        font-size: 15px;
        color: var(--text-dim);
        line-height: 1.7;
    }

    /* NAV */
    .nav-btn {
        position: absolute;
        top: 49%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--text);
        font-size: 28px;
        cursor: pointer;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .nav-btn.prev {
        left: -22px;
    }

    .nav-btn.next {
        right: -22px;
    }

    .nav-btn:hover {
        background: var(--accent);
        color: #fff;
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .testimonial-card {
            flex: 0 0 calc(50% - 12px);
        }
    }

    @media (max-width: 640px) {
          .testimonial-section {
padding-top: 20px;
        /* background: var(--bg); */
        text-align: center;
    }
        .testimonial-card {
            flex: 0 0 100%;
        }

        .nav-btn {
            display: none;
        }
    }

    /* =========================
   HERO BANNER
========================= */
.hero-banner {
    padding: 120px 0 0;
    background: radial-gradient(800px 300px at 10% 20%, rgb(3 169 244 / 13%), transparent 60%), var(--bg);
}

    /* GRID */
    .hero-grid {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 60px;
        align-items: center;
    }

    /* LEFT CONTENT */
    .hero-eyebrow {
        font-size: 13px;
        font-weight: 600;
        color: var(--accent-2);
    }

    .hero-content h1 {
        font-size: 44px;
        font-weight: 800;
        line-height: 1.15;
        margin: 14px 0 18px;
        color: var(--text);
    }

    .hero-content h1 span {
        color: var(--accent);
    }

    .hero-content p {
        max-width: 520px;
        color: #000;
        font-size: 16px;
        line-height: 1.7;
    }

    .hero-actions {
        display: flex;
        gap: 14px;
        margin-top: 28px;
    }

    /* RIGHT IMAGES */
    .hero-images {
        position: relative;
    }

    .hero-images img {
        border-radius: 20px;
        box-shadow: var(--shadow);
    }

    .img-main {
        width: 100%;
    }

    .img-top {
        position: absolute;
        width: 160px;
        top: -30px;
        right: -30px;
        border: 4px solid var(--bg);
    }

    .img-bottom {
        position: absolute;
        width: 180px;
        bottom: -30px;
        left: -30px;
        border: 4px solid var(--bg);
    }

    /* BADGE */
    .hero-badge {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: #fff;
        color: #06201d;
        padding: 12px 16px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: var(--shadow);
    }

    /* =========================
   MARQUEE
========================= */
    .hero-marquee {
        margin-top: 70px;
        background: #fff;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        overflow: hidden;
    }

    .marquee-track {
        display: flex;
        gap: 40px;
        padding: 18px 0;
        white-space: nowrap;
        animation: marquee 20s linear infinite;
    }

    .marquee-track span {
        color: var(--text);
        font-weight: 600;
        font-size: 14px;
        position: relative;
        padding-left: 22px;
    }

    .marquee-track span::before {
        content: "✳";
        position: absolute;
        left: 0;
        color: var(--accent);
    }

    /* ANIMATION */
    @keyframes marquee {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-50%);
        }
    }

    /* =========================
   RESPONSIVE
========================= */
    @media (max-width: 1024px) {
        .hero-grid {
            grid-template-columns: 1fr;
        }

        .hero-content h1 {
            font-size: 36px;
        }

        .img-top,
        .img-bottom {
            display: none;
        }
    }

    @media (max-width: 640px) {
        .why-subtitle {
            margin: 0 auto 20px;

        }

        .faq-icon {
            width: 23px;
            height: 23px;
            background: var(--accent);
            color: #ffffff;
            border-radius: 50%;

            font-size: 15px;
            transition: transform .25s ease;
        }

        .faq-question {
            padding: 20px 10px;
            font-size: 15px;
            font-weight: 600;

            text-align: start;
        }

        .hero-banner {
            padding-top: 70px;
        }

        .hero-content h1 {
            font-size: 30px;
        }

        .hero-actions {
            flex-direction: column;
        }

        .container.nav-cta.destop-view {
            display: flex;
            padding: 0px;
            gap: 22px;

        }

    }

    .f-brand .logo img {
        width: auto;
        height: 70px;
    }

/* Container */
.team-section {
    padding: 50px 20px;
    background: #f8f9fc;
    text-align: center;
    font-family: 'Inter', sans-serif;
    
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Header */
.team-eyebrow {
    font-size: 12px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #4c6ef5;
    font-weight: 600;
}

.team-title {
    font-size: 40px;
    font-weight: 700;
    margin-top: 10px;
    margin-bottom: 60px;
    color: #1a1f36;
}

.team-title span {
    color: #4c6ef5;
}

/* Row Layout */
.team-row {
    display: flex;
    justify-content: center;
    gap: 60px;
    flex-wrap: wrap;
}

/* Card */
.team-card {
    width: 300px;
    transition: 0.3s ease;
}

/* Image */
.team-img {
    width: 200px;
    height: 200px;
    margin: 0 auto 25px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transition: 0.3s ease;
}

.team-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Name */
.team-name {
    font-size: 20px;
    font-weight: 600;
    color: #1d3c88;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

/* Role */
.team-role {
    font-size: 13px;
    text-transform: uppercase;
    color: #777;
    letter-spacing: 1px;
}

/* Hover Effect */
.team-card:hover {
    transform: translateY(-8px);
}

.team-card:hover .team-img {
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

/* ======================
   RESPONSIVE DESIGN
====================== */

/* Tablet */
@media (max-width: 992px) {
    .team-row {
        gap: 40px;
    }
}

/* Mobile */
@media (max-width: 768px) {
    .team-title {
        font-size: 28px;
    }

    .team-card {
        width: 100%;
        max-width: 300px;
    }

    .team-img {
        width: 160px;
        height: 160px;
    }
    .team-section{
        padding-bottom:0px;
    }
    .faq-section{
        padding-top:0px;
    }
}
.desktop-pdf {
    display: block;
}

.mobile-pdf {
    display: none;
    text-align: center;
    padding-top:20px; 
}

.pdf-btn {
    display: inline-block;
    padding: 14px 30px;
    background: #1d3c88;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
}

/* Mobile */
@media (max-width: 768px) {
    .desktop-pdf {
        display: none;
    }

    .mobile-pdf {
        display: block;
    }
}

.pdf-container {
    padding-top:20px;
}
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <div class="container nav">
            <div class="brand">
                <div class="logo">
                   <a href="/#home" ><img src="{{ asset('assets/images/logo.png') }}" alt="Himalaya Trading"></a>
                </div>
                <!-- <h1>Himalaya <span class="brand-green">Trading</span></h1> -->
            </div>

            <nav>
                <ul>
                    <li><a href="/#home">Home</a></li>
                    <li><a href="/#about">About Us</a></li>
                    <li><a href="/#plan">Business Plan</a></li>
                                   <li><a href="/#teams">Team</a></li>
                    <li><a href="/#products">Products / Services</a></li>
                </ul>
            </nav>

            <div class="nav-cta">
                <a class="btn btn-ghost" href="/login">Log in</a>
                <a class="btn btn-primary" href="/#contact">Contact Us</a>
            </div>

            <div class="burger" id="burger" aria-label="Open Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="mobile-menu" id="mobileMenu">
            <ul class="container">
                <li><a href="/#home" class="m-link">Home</a></li>
                <li><a href="/#about" class="m-link">About Us</a></li>
                <li><a href="/#plan" class="m-link">Business Plan</a></li>
                <li><a href="/#products" class="m-link">Products / Services</a></li>
                <div class="container nav-cta mobile-sec" style="padding-bottom:18px">
                    <a class="btn btn-ghost" href="/login">Log in</a>
                    <a class="btn btn-primary" href="/#contact">Contact Us</a>
                </div>
            </ul>
            <div class="container nav-cta destop-view" style="padding-bottom:18px">
                <a class="btn btn-ghost" href="/login">Log in</a>
                <a class="btn btn-primary" href="/#contact">Contact Us</a>
            </div>
        </div>
    </header>




    <section class="hero-banner">
        <div class="container hero-grid">

            <!-- LEFT CONTENT -->
            <div class="hero-content">
                <span class="hero-eyebrow">
                    ✔ Power Your Business With Us
                </span>

                <h1>
                    Empowering People, <br /> Creating <span class="focus">Opportunities</span>
                </h1>

                <p>
                    Secure your future with Himalaya Trading — a trusted network marketing
                    company that leads you on the path to financial freedom and growth.
                </p>

                <div class="hero-actions">
                    <a href="/#plan" class="btn btn-primary">Explore Now</a>
                    <a href="/#products" class="btn btn-outline">View All Services</a>
                </div>
            </div>

            <!-- RIGHT IMAGE COLLAGE -->
            <div class="hero-images">
                <img class="img-main"
                    {{-- src="/storage/fb.avif" --}}
                    src="{{ asset('assets/images/fb.avif') }}"
                    alt="Business team">

                <img class="img-top"
                    src="https://img.freepik.com/free-photo/business-people-working-team-office_23-2148817065.jpg"
                    alt="Entrepreneur working">

                <img class="img-bottom"
                    src="https://img.freepik.com/free-photo/young-businesswoman-working-office_23-2148888852.jpg"
                    alt="Professional training">

                <div class="hero-badge">
                    <span>Trusted Growth</span>
                </div>
            </div>

        </div>

        <!-- MARQUEE -->
        <div class="hero-marquee">
            <div class="marquee-track">
                <span>Network Marketing</span>
                <span>Entrepreneurship</span>
                <span>Skill Development</span>
                <span>Leadership Training</span>
                <span>Financial Growth</span>
                <span>Ethical MLM</span>

                <!-- duplicate for loop -->
                <span>Network Marketing</span>
                <span>Entrepreneurship</span>
                <span>Skill Development</span>
                <span>Leadership Training</span>
                <span>Financial Growth</span>
                <span>Ethical MLM</span>
            </div>
        </div>
    </section>

    <!--  -->
    <section class="mlm-growth-section" id="about">
        <div class="container mlm-grid">

            <!-- LEFT IMAGE AREA -->
            <div class="mlm-images">
                <div class="img-main">
                    <img src="https://img.freepik.com/free-photo/group-people-working-team_23-2147656711.jpg"
                        alt="Network marketing team collaboration">
                </div>

                <div class="img-small img-1">
                    <img src="https://img.freepik.com/free-photo/creative-monitor-tech-digitally-generated-desk_1134-719.jpg?t=st=1767614112~exp=1767617712~hmac=137262e85ab3e594dfe02f9dc25feabc99d50f762b097c9e456f2e6b74157275&w=1480"
                        alt="Entrepreneur learning online">
                </div>

                <div class="img-small img-2">
                    <img src="https://img.freepik.com/free-photo/happy-business-team-celebrating-success_23-2148985510.jpg"
                        alt="MLM team success">
                </div>

                <div class="experience-badge">
                    <strong>25+</strong>
                    <span>Years of Combined Experience</span>
                </div>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="mlm-content">
                <span class="mlm-eyebrow">WHO WE ARE</span>
                <h2>
                    We Are a <span>Growth-Driven Network Marketing</span> Company
                </h2>

                <p>
                    We are a growth-driven network marketing company focused on
                    <strong>financial inclusion</strong> and <strong>entrepreneurship</strong>.
                    Our platform empowers individuals to build a stable and scalable
                    MLM business through education, transparency, and ethical practices.
                </p>

                <p>
                    Our MLM ecosystem provides <strong>structured training</strong>,
                    <strong>digital tools</strong>, and a <strong>clear compensation plan</strong>
                    so that anyone—regardless of background—can learn, participate,
                    and grow step by step.
                </p>

                <ul class="mlm-points">
                    <li>✔ Ethical & transparent MLM business model</li>
                    <li>✔ Multiple income streams with long-term scalability</li>
                    <li>✔ Training, mentorship & leadership development</li>
                    <li>✔ Technology-driven MLM dashboard & analytics</li>
                </ul>

                <p class="mlm-note">
                    Join our mission to build <strong>sustainable income streams</strong>
                    through smart investing, ethical networking, and consistent upskilling—
                    while being part of a trusted and growing MLM community.
                </p>

                <a href="#contact" class="btn btn-primary">Join Our Network</a>
            </div>

        </div>
    </section>
    <!--  -->
<section id="teams" class="team-section">
    <div class="container">

        <span class="team-eyebrow">Teams</span>

        <h2 class="team-title">
            BOARD OF DIRECTORS <span>Himalaya Trading</span>
        </h2>

        <div class="team-row">

            <!-- Member 1 -->
            <div class="team-card">
                <div class="team-img">
                    <img src="{{ asset('assets/images/kapil.png') }}" alt="Kapil Bhardwaj">
                </div>
                <h4 class="team-name">KAPIL BHARDWAJ</h4>
                <p class="team-role">DIRECTOR</p>
            </div>

            <!-- Member 2 -->
            <div class="team-card">
                <div class="team-img">
                    <img src="{{ asset('assets/images/isro.png') }}" alt="Isro Thakur">
                </div>
                <h4 class="team-name">ISRO THAKUR</h4>
                <p class="team-role">CEO</p>
            </div>

            <!-- Member 3 -->
            <div class="team-card">
                <div class="team-img">
                    <img src="{{ asset('assets/images/sonu.png') }}" alt="Sonu Kapoor">
                </div>
                <h4 class="team-name">SONU KAPOOR</h4>
                <p class="team-role">DIRECTOR</p>
            </div>

        </div>
    </div>
</section>
<!--  -->



 
    <!-- About -->
    <section class="section about d-none">
        <div class="container">
            <div class="title-eyebrow">Who We Are ✳︎</div>
            <h3>About Himalaya Trading</h3>

            <div class="about-grid">
                <div class="about-card reveal">
                    <p>
                        We are a growth-driven network marketing company focused on financial
                        inclusion and entrepreneurship. Our platform provides tools, training,
                        and a transparent plan so that anyone can learn, participate, and
                        succeed — step by step.
                    </p>
                    <p>
                        Join our mission to build sustainable income streams through smart
                        investing, ethical networking, and consistent upskilling.
                    </p>
                </div>

                <div class="about-illus reveal">
                    <img src="https://himjantrading.com/HIMJAN/images/trade/trade.png" alt="">
                    <!-- Dummy art block; replace with your illustration -->
                    <!-- <div class="screen">DUMMY ILLUSTRATION</div> -->
                </div>

            </div>
        </div>
    </section>
    <!--  -->

    <section class="faq-section">
        <div class="container faq-grid">

            <!-- LEFT CONTENT -->
            <div class="faq-left">
                <h2>
                    Frequently Asked <br>
                    <span>Questions</span>
                </h2>
                <p>
                    Find answers to common questions about our MLM business model,
                    income structure, training system, and platform transparency.
                </p>
            </div>

            <!-- RIGHT FAQ ACCORDION -->
            <div class="faq-right">

                <div class="faq-item active">
                    <button class="faq-question">
                        What is Himalaya Trading?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>
                            Himalaya Trading is a growth-driven network marketing platform
                            focused on financial inclusion and entrepreneurship. It enables
                            individuals to build sustainable income through ethical networking,
                            structured training, and a transparent compensation plan.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        How does the MLM business model work?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>
                            Members earn income by building a network, promoting approved
                            products or services, and supporting team growth. Earnings are
                            based on performance, consistency, and leadership development.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Is this MLM platform legal and transparent?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>
                            Yes. Our MLM structure follows ethical practices with clear
                            documentation, defined income plans, and transparent policies
                            designed for long-term sustainability.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Do I need prior experience to join?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>
                            No prior MLM or business experience is required. We provide
                            step-by-step training, mentorship, and digital tools to help
                            beginners learn and grow confidently.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        How do I track my income and team growth?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>
                            Members can monitor earnings, network performance, and progress
                            through a secure and user-friendly MLM dashboard.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!--  -->
    <section class="why-choose-dark" id="products">
        <div class="container">
            <span class="why-eyebrow">WHY CHOOSE US</span>
            <h2 class="why-title">
                Why Choose <span>Himalaya Trading </span>
            </h2>
            <p class="why-subtitle">
                A transparent, growth-driven MLM platform designed to help individuals
                build sustainable income through ethical networking and smart leadership.
            </p>

            <div class="why-grid">
                <div class="why-card">
                    <span class="why-num">01</span>
                    <h4>Transparent & Ethical MLM Model</h4>
                    <p>
                        Our business follows a clear, compliant, and ethical MLM structure
                        that builds long-term trust and sustainable growth.
                    </p>
                </div>

                <div class="why-card">
                    <span class="why-num">02</span>
                    <h4>Multiple Income Streams</h4>
                    <p>
                        Earn through direct income, team income, leadership bonuses,
                        and performance-based incentives.
                    </p>
                </div>

                <div class="why-card">
                    <span class="why-num">03</span>
                    <h4>Scalable Business Plan</h4>
                    <p>
                        Our MLM plan is designed for long-term scalability, helping members
                        grow from entry level to leadership positions.
                    </p>
                </div>

                <div class="why-card">
                    <span class="why-num">04</span>
                    <h4>Training & Mentorship</h4>
                    <p>
                        Get access to step-by-step training, mentorship, and business tools
                        to succeed in network marketing.
                    </p>
                </div>

                <div class="why-card">
                    <span class="why-num">05</span>
                    <h4>Secure Digital Platform</h4>
                    <p>
                        Track your network, earnings, and performance through a secure,
                        user-friendly MLM dashboard.
                    </p>
                </div>

                <div class="why-card">
                    <span class="why-num">06</span>
                    <h4>Trusted Community & Leadership</h4>
                    <p>
                        Join a fast-growing MLM community backed by experienced leadership
                        and a strong long-term vision.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonial-section">
        <div class="container">

            <span class="testi-eyebrow">What our members say</span>
            <h2 class="testi-title">Testimonials</h2>

            <div class="testimonial-wrapper">
                <button class="nav-btn prev">‹</button>

                <div class="testimonial-viewport">
                    <div class="testimonial-track">

                        <div class="testimonial-card">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg">
                            <h4>Rajesh Kumar</h4>
                            <span>Independent Associate</span>
                            <p>
                                Himalaya Trading helped me understand network marketing
                                ethically with proper guidance and training.
                            </p>
                        </div>

                        <div class="testimonial-card">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg">
                            <h4>Neha Sharma</h4>
                            <span>Entrepreneur</span>
                            <p>
                                The transparency and structured MLM plan give confidence
                                to grow step by step.
                            </p>
                        </div>

                        <div class="testimonial-card">
                            <img src="https://randomuser.me/api/portraits/men/56.jpg">
                            <h4>Amit Verma</h4>
                            <span>Team Leader</span>
                            <p>
                                Digital tools and performance tracking make building
                                a team simple and reliable.
                            </p>
                        </div>

                        <div class="testimonial-card">
                            <img src="https://randomuser.me/api/portraits/women/65.jpg">
                            <h4>Pooja Mehta</h4>
                            <span>Business Associate</span>
                            <p>
                                Training and mentorship helped me gain confidence even
                                without prior MLM experience.
                            </p>
                        </div>

                        <div class="testimonial-card">
                            <img src="https://randomuser.me/api/portraits/men/78.jpg">
                            <h4>Sandeep Singh</h4>
                            <span>Network Builder</span>
                            <p>
                                Ethical business values and long-term vision make
                                Himalaya Trading trustworthy.
                            </p>
                        </div>

                        <div class="testimonial-card">
                            <img src="https://randomuser.me/api/portraits/women/81.jpg">
                            <h4>Anjali Gupta</h4>
                            <span>Associate Partner</span>
                            <p>
                                Focus on leadership skills and sustainable income
                                growth is what I like most.
                            </p>
                        </div>

                    </div>
                </div>

                <button class="nav-btn next">›</button>
            </div>

        </div>
    </section>



    <!-- How It Works -->
    <section class="section how" >
        <div class="container">
            <div class="title-eyebrow">Steps of Procedure ✳︎</div>
            <h3>How It Works</h3>

            <!-- <div class="dotted" aria-hidden="true"></div> -->

            <div class="steps">
                <div class="step reveal">
                    <div class="icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 7h16M4 12h10M4 17h7" />
                            <circle cx="18" cy="17" r="3" />
                        </svg>
                    </div>
                    <h4>Register</h4>
                    <p>Sign up and get started.</p>
                </div>

                <div class="step reveal">
                    <div class="icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 12l4-4 4 4 4-4 4 4" />
                            <path d="M4 16l4-4 4 4 4-4 4 4" />
                        </svg>
                    </div>
                    <h4>Build Network</h4>
                    <p>Invite and grow your team.</p>
                </div>

                <div class="step reveal">
                    <div class="icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 11h18M5 7h14M7 3h10" />
                            <path d="M7 21h10v-5H7z" />
                        </svg>
                    </div>
                    <h4>Earn</h4>
                    <p>Sell and earn commissions.</p>
                </div>

                <div class="step reveal">
                    <div class="icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 20V4m0 12c3 0 5-8 8-8s5 8 8 8" />
                            <path d="M20 20V8" />
                        </svg>
                    </div>
                    <h4>Grow</h4>
                    <p>Scale up and become leader.</p>
                </div>
            </div>
<div class="pdf-container  container"  id="plan">

    <!-- Desktop View -->
    <div class="desktop-pdf">
    <iframe 
        src="{{ asset('uploads/HGNLPAY.pdf') }}"
        width="100%"
        height="800"
        style="border:none;border-radius:10px; background-color:#fff;">
    </iframe>
</div>

<div class="mobile-pdf">
    <a href="{{ asset('uploads/HGNLPAY.pdf') }}" target="_blank" class="pdf-btn">
        Open PDF
    </a>
</div>

</div>
            <!-- CTA band -->
            <div class="cta-band reveal" style="margin-top:34px">
                <div class="inner">
                    <div class="note">
                        Join <b>Himalaya Trading</b> and build a successful business and a secure future.
                    </div>
                    <div style="text-align:right">
                        <a href="/login" class="btn btn-primary">Contact Us</a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer id="products">
        <div class="container footer-top">
            <div class="f-brand">
                <div class="logo">
                   <a href="/#home" ><img src="{{ asset('assets/images/logo.png') }}" alt="Himalaya Trading"></a>
                </div>
                <!-- <h4 style="margin:12px 0 8px">Himalaya <span class="brand-green">Trading</span></h4> -->
                <p>
                    Trusted network marketing company providing opportunities for growth and income.
                </p>
                <div class="social">
                    <a href="#" aria-label="Facebook">f</a>
                    <a href="#" aria-label="Twitter">t</a>
                    <a href="#" aria-label="LinkedIn">in</a>
                    <a href="#" aria-label="YouTube">▶</a>
                </div>
            </div>

            <div class="f-col">
                <h5>Quick Link</h5>
                <ul>
                    <li><a href="/privacy-policy">Privacy Policy</a></li>
                    <li><a href="/terms-conditions">Terms & Conditions</a></li>
                    <li><a href="/#plan">Contact Us</a></li>
                </ul>
            </div>

            <div class="f-col">
                <h5>Company</h5>
                <ul>
                    <li><a href="/#about">About Us</a></li>
                    <li><a href="/#plan">Business Plan</a></li>
                    <li><a href="/#products">Products</a></li>
                </ul>
            </div>

            <div class="f-col" id="contact">
                <h5>Contact Us</h5>
                <ul>
                    
                    <li><a href="mailto:info@hgnl.co.in">info@hgnl.co.in</a></li>
                    <li><a href="tel:+91 62307 14902">+91 62307 14902</a></li>
                </ul>
            </div>
        </div>

        <div class="container copy">
            <div>Himalaya <span class="brand-green">Trading</span> © 2026. All Rights Reserved.</div>
            <div><a href="#home">Back to top ↑</a></div>
        </div>
    </footer>

    <!-- Floating button (scroll to top) -->
    <button class="float-btn" id="toTop" aria-label="Back to top">
        <svg viewBox="0 0 24 24" fill="none">
            <path d="M12 5l-7 7m7-7l7 7m-7-7v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    </button>

    <script>
    // Mobile menu toggle
    const burger = document.getElementById('burger');
    const mobileMenu = document.getElementById('mobileMenu');
    const links = document.querySelectorAll('.m-link');

    burger.addEventListener('click', () => {
        const open = mobileMenu.classList.toggle('open');
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    links.forEach(l => l.addEventListener('click', () => mobileMenu.classList.remove('open')));

    // Scroll to top
    const toTop = document.getElementById('toTop');
    toTop.addEventListener('click', () => window.scrollTo({
        top: 0,
        behavior: 'smooth'
    }));
    window.addEventListener('scroll', () => {
        toTop.style.opacity = window.scrollY > 400 ? '1' : '0.0';
        toTop.style.pointerEvents = window.scrollY > 400 ? 'auto' : 'none';
    });
    toTop.style.transition = 'opacity .25s ease';

    // Reveal-on-scroll animations
    const IO = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('show');
                IO.unobserve(e.target);
            }
        });
    }, {
        threshold: .2
    });
    document.querySelectorAll('.reveal').forEach(el => IO.observe(el));
    </script>

    <script>
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.faq-item');

            document.querySelectorAll('.faq-item').forEach(i => {
                if (i !== item) i.classList.remove('active');
            });

            item.classList.toggle('active');
        });
    });
    </script>
    <script>
    const track = document.querySelector('.testimonial-track');
    const wrapper = document.querySelector('.testimonial-wrapper');

    let cards = Array.from(track.children);
    let index = 1;
    let cardWidth = 0;
    let auto = null;

    /* Visible cards */
    function visibleCards() {
        if (window.innerWidth <= 640) return 1;
        if (window.innerWidth <= 1024) return 2;
        return 3;
    }

    /* Setup infinite clones */
    function setupLoop() {
        track.innerHTML = '';
        cards.forEach(c => track.appendChild(c));

        const visible = visibleCards();

        const firstClones = cards.slice(0, visible).map(c => c.cloneNode(true));
        const lastClones = cards.slice(-visible).map(c => c.cloneNode(true));

        lastClones.reverse().forEach(c => track.prepend(c));
        firstClones.forEach(c => track.appendChild(c));

        cards = Array.from(track.children);
        index = visible;
    }

    /* Update size */
    function updatePosition() {
        cardWidth = cards[0].offsetWidth + 24;
        track.style.transform = `translateX(-${index * cardWidth}px)`;
    }

    /* Move slide */
    function move(dir = 1) {
        index += dir;
        track.style.transition = 'transform .45s ease';
        track.style.transform = `translateX(-${index * cardWidth}px)`;
    }

    /* Loop fix */
    track.addEventListener('transitionend', () => {
        const visible = visibleCards();

        if (index >= cards.length - visible) {
            track.classList.add('no-transition');
            index = visible;
            track.style.transform = `translateX(-${index * cardWidth}px)`;
            track.offsetHeight;
            track.classList.remove('no-transition');
        }

        if (index <= 0) {
            track.classList.add('no-transition');
            index = cards.length - visible * 2;
            track.style.transform = `translateX(-${index * cardWidth}px)`;
            track.offsetHeight;
            track.classList.remove('no-transition');
        }
    });

    /* Controls */
    document.querySelector('.next').onclick = () => move(1);
    document.querySelector('.prev').onclick = () => move(-1);

    /* AUTOPLAY */
    function startAuto() {
        auto = setInterval(() => move(1), 5000);
    }

    function stopAuto() {
        clearInterval(auto);
    }

    /* Pause on hover */
    wrapper.addEventListener('mouseenter', stopAuto);
    wrapper.addEventListener('mouseleave', startAuto);

    /* Resize */
    window.addEventListener('resize', () => {
        stopAuto();
        cards = Array.from(document.querySelectorAll('.testimonial-card')).slice(0, 6);
        setupLoop();
        updatePosition();
        startAuto();
    });

    /* Init */
    setupLoop();
    updatePosition();
    startAuto();
    </script>



</body>

</html>