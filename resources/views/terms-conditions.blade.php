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
        }}
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
   text-decoration: none !important;
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
        border-color: #93ff1e66
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
 .f-brand .logo img {
        width: auto;
        height: 70px;
    }
    .logo img {
    height: 61px;
    width: 100%;
    object-fit: contain;
    max-width: 100%;
}
@media (max-width: 640px) {
    .container.nav-cta.destop-view {
        display: flex
;
        padding: 0px;
        gap: 22px;
    }
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
        border: 1px solid #1a2230;
        border-radius: 10px;
        padding: 12px;
        margin: 8px 0;
        background: #0f151d
    }

    .mobile-menu .nav-cta {
        padding: 12px;
        gap: 8px
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
        background: #dbe7f1;
        border-radius: 99px
    }

    .mobile-menu {
        display: none;
        position: absolute;
        left: 0;
        top: 72px;
        width: 100%;
        background: #0c1117;
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


    /* ---------- Footer ---------- */
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
        margin-top: 10px
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
   h1, h2, h3 {
      color: #000000;
      margin-bottom: 10px;
    }
    h1 {
      font-size: 34px;
      margin-bottom: 20px;
    }
    h2 {
      font-size: 22px;
      margin-top: 40px;
      margin-bottom: 10px;
    }
    p, li {
      color: #b9c3cb;
      font-size: 16px;
    }
        p, li {
    color: #000000;
    font-size: 16px;
}
      ul {
      padding-left: 20px;
    }
  a {
    color: #37478f;
    text-decoration: none;
}
    a:hover {
      text-decoration: underline;
    }
    .updated {
      font-size: 14px;
      color: #8b9aa5;
      margin-bottom: 30px;
    }
    .info-box  {
    background: #ffffff;
    border: 1px solid #1b222b;
    padding: 20px;
    border-radius: 12px;
    margin-top: 30px;
    margin-bottom: 30px;
}
   
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <div class="container nav">
            <div class="brand">
                <div class="logo">
                  <a href="/#home" ><img src="/storage/logo.png" alt="Himalaya Trading"></a>
                </div>
               
            </div>

            <nav>
                <ul>
                    <li><a href="/#home">Home</a></li>
                    <li><a href="/#about">About Us</a></li>
                    <li><a href="/#plan">Business Plan</a></li>
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

  
   

 <div class="container">

    <h1>Terms & Conditions</h1>
    <div class="updated">Last Updated: 2026</div>

    <p>
      Welcome to <strong>Himalaya Trading</strong>. By accessing or using our website,
      you agree to comply with and be bound by the following Terms & Conditions.
      Please read them carefully before using our services.
    </p>

    <h2>1. Acceptance of Terms</h2>
    <p>
      By accessing this website, you acknowledge that you have read, understood,
      and agree to be bound by these Terms & Conditions. If you do not agree,
      please discontinue use of the website.
    </p>

    <h2>2. Use of Website</h2>
    <ul>
      <li>You must be at least 18 years old to use this website.</li>
      <li>You agree to use the website only for lawful purposes.</li>
      <li>You must not misuse, hack, or attempt to disrupt website functionality.</li>
    </ul>

    <h2>3. Business & Income Disclaimer</h2>
    <p>
      Himalaya Trading provides business opportunities, training, and information.
      Any income or success examples shared are not guaranteed and depend on individual
      effort, skills, and market conditions.
    </p>

    <h2>4. Intellectual Property</h2>
    <p>
      All content on this website, including text, graphics, logos, images, and
      design elements, is the property of Himalaya Trading and protected by
      applicable copyright and intellectual property laws.
    </p>

    <h2>5. User Responsibilities</h2>
    <ul>
      <li>Provide accurate and complete information when submitting forms</li>
      <li>Maintain confidentiality of any login credentials</li>
      <li>Accept responsibility for activities performed under your account</li>
    </ul>

    <h2>6. Limitation of Liability</h2>
    <p>
      Himalaya Trading shall not be liable for any direct, indirect, incidental,
      or consequential damages arising from the use or inability to use this website.
    </p>

    <h2>7. Third-Party Links</h2>
    <p>
      This website may contain links to third-party websites. Himalaya Trading
      does not control or endorse the content of these websites and is not
      responsible for their practices.
    </p>

    <h2>8. Termination</h2>
    <p>
      We reserve the right to suspend or terminate access to the website at any time,
      without notice, for conduct that violates these Terms & Conditions.
    </p>

    <h2>9. Governing Law</h2>
    <p>
      These Terms & Conditions shall be governed and interpreted in accordance
      with the laws of India, without regard to conflict of law principles.
    </p>

    <h2>10. Changes to Terms</h2>
    <p>
      Himalaya Trading reserves the right to modify these Terms & Conditions at
      any time. Updates will be posted on this page with a revised date.
    </p>

    <h2>11. Contact Information</h2>
    <div class="info-box">
      <p><strong>Himalaya Trading</strong></p>
    
      <p>Email: <a href="mailto:info@hgnl.co.in">info@hgnl.co.in</a></p> 
      <p>Phone: <a href="tel:+91 62307 14902">+91 62307 14902</a></p>
    </div>

  </div>






    <!-- Footer -->
    <footer id="products">
        <div class="container footer-top">
            <div class="f-brand">
                <div class="logo">
                    <a href="/#home" ><img src="/storage/logo.png" alt="Himalaya Trading"></a>
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