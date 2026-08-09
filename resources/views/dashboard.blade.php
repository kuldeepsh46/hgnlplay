@extends('common.layout')
@section('title', 'Dashboard')
@section('main')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap');

        /* =========================================
           TOKENS — "Live Network" concept.
           Deep space base, gold = money, teal = growth,
           coral = attention. Everything glows a little,
           because this business is literally a live network.
        ========================================= */
        :root {
            --ink: #060811;
            --ink-2: #0a0d17;
            --glass: rgba(255, 255, 255, .035);
            --glass-strong: rgba(255, 255, 255, .06);
            --line: rgba(255, 255, 255, .09);
            --line-soft: rgba(255, 255, 255, .05);
            --text: #f6f7fb;
            --text-muted: #9298ab;
            --text-dim: #575e72;
            --gold: #f0bd5a;
            --gold-2: #ffd98a;
            --teal: #35e0c9;
            --teal-2: #7cf2e2;
            --coral: #ff6f6f;
            --radius: 22px;
            --radius-sm: 12px;
            --font-display: 'Manrope', 'Inter', sans-serif;
            --font-body: 'Inter', sans-serif;
            --font-mono: 'JetBrains Mono', ui-monospace, monospace;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: var(--font-body);
            background: var(--ink);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .main {
            flex: 1;
            padding: 28px 32px 60px;
            overflow-x: hidden;
            position: relative;
            z-index: 1;
        }

        /* ambient dot-grid texture, sits above canvas, below content */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: -2;
            background-image: radial-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px);
            background-size: 26px 26px;
            mask-image: radial-gradient(circle at 50% 0%, black, transparent 75%);
            pointer-events: none;
        }

        #netCanvas {
            position: fixed;
            inset: 0;
            z-index: -3;
            pointer-events: none;
            opacity: .55;
        }

        a,
        button,
        input {
            font-family: inherit;
        }

        :focus-visible {
            outline: 2px solid var(--teal);
            outline-offset: 2px;
            border-radius: 4px;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
        }

        .mono {
            font-family: var(--font-mono);
            font-variant-numeric: tabular-nums;
        }

        .grad-gold {
            background: linear-gradient(120deg, var(--gold-2), var(--gold) 60%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .grad-teal {
            background: linear-gradient(120deg, var(--teal-2), var(--teal) 60%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* =========================================
           HEADER
        ========================================= */
        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .page-head .eyebrow {
            font-family: var(--font-mono);
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--teal);
            margin: 0 0 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-head .eyebrow .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--teal);
            box-shadow: 0 0 0 0 rgba(53, 224, 201, .6);
            animation: pulseRing 2s ease-out infinite;
        }

        .page-head h1 {
            font-family: var(--font-display);
            font-size: clamp(26px, 3vw, 34px);
            font-weight: 800;
            margin: 0 0 4px;
            letter-spacing: -.5px;
        }

        .page-head .sub {
            color: var(--text-muted);
            font-size: 14px;
            margin: 0;
        }

        @keyframes pulseRing {
            0% {
                box-shadow: 0 0 0 0 rgba(53, 224, 201, .55);
            }

            70% {
                box-shadow: 0 0 0 9px rgba(53, 224, 201, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(53, 224, 201, 0);
            }
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--glass);
            backdrop-filter: blur(16px);
            border: 1px solid var(--line);
            padding: 8px 16px 8px 8px;
            border-radius: 999px;
        }

        .user-chip .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--teal));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #0a0d17;
            font-size: 13px;
        }

        .user-chip .name {
            font-weight: 600;
            font-size: 14px;
        }

        .user-chip .role {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* =========================================
           BENTO GRID SYSTEM
        ========================================= */
        .bento {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .b-3 {
            grid-column: span 3;
        }

        .b-4 {
            grid-column: span 4;
        }

        .b-5 {
            grid-column: span 5;
        }

        .b-6 {
            grid-column: span 6;
        }

        .b-7 {
            grid-column: span 7;
        }

        .b-8 {
            grid-column: span 8;
        }

        .b-9 {
            grid-column: span 9;
        }

        .b-12 {
            grid-column: span 12;
        }

        @media (max-width: 1200px) {

            .b-3,
            .b-4,
            .b-5 {
                grid-column: span 6;
            }

            .b-7,
            .b-8,
            .b-9 {
                grid-column: span 12;
            }
        }

        @media (max-width: 768px) {

            .main {
                padding: 84px 16px 40px;
            }

            .b-3,
            .b-4,
            .b-5,
            .b-6 {
                grid-column: span 12;
            }
        }

        /* =========================================
           GLASS PANEL + cursor spotlight + tilt
        ========================================= */
        .glass {
            position: relative;
            background: linear-gradient(180deg, var(--glass-strong), var(--glass));
            backdrop-filter: blur(22px) saturate(140%);
            -webkit-backdrop-filter: blur(22px) saturate(140%);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 24px;
            overflow: hidden;
            transition: transform .35s cubic-bezier(.16, 1, .3, 1), border-color .3s ease, box-shadow .3s ease;
        }

        .glass::after {
            content: '';
            position: absolute;
            top: 0;
            left: 12%;
            right: 12%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(240, 189, 90, .55), rgba(53, 224, 201, .55), transparent);
            opacity: .7;
        }

        .glass::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(280px circle at var(--mx, 50%) var(--my, 50%), rgba(255, 255, 255, .07), transparent 60%);
            opacity: 0;
            transition: opacity .35s ease;
            pointer-events: none;
        }

        .glass:hover::before {
            opacity: 1;
        }

        .glass:hover {
            border-color: rgba(255, 255, 255, .16);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .35);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .panel-head .eyebrow {
            font-family: var(--font-mono);
            font-size: 11px;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin: 0 0 4px;
        }

        .panel-head h2 {
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 700;
            margin: 0;
        }

        .tag {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 11px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
        }

        .tag-teal {
            background: rgba(53, 224, 201, .12);
            color: var(--teal);
            border: 1px solid rgba(53, 224, 201, .25);
        }

        .tag-gold {
            background: rgba(240, 189, 90, .12);
            color: var(--gold);
            border: 1px solid rgba(240, 189, 90, .25);
        }

        .tag-coral {
            background: rgba(255, 111, 111, .12);
            color: var(--coral);
            border: 1px solid rgba(255, 111, 111, .25);
        }

        /* =========================================
           SCROLL REVEAL
        ========================================= */
        .reveal {
            opacity: 0;
            transform: translateY(26px);
        }

        .reveal.in {
            opacity: 1;
            transform: translateY(0);
            transition: opacity .7s cubic-bezier(.16, 1, .3, 1), transform .7s cubic-bezier(.16, 1, .3, 1);
        }

        /* =========================================
           HERO KPI
        ========================================= */
        .hero-figure {
            font-family: var(--font-display);
            font-size: clamp(38px, 5vw, 60px);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -1px;
            margin: 6px 0 10px;
            filter: drop-shadow(0 0 22px rgba(240, 189, 90, .18));
        }

        .hero-figure.grad-teal {
            filter: drop-shadow(0 0 22px rgba(53, 224, 201, .2));
        }

        .hero-label {
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sparkline {
            width: 100%;
            height: 44px;
            margin-top: 6px;
        }

        .sparkline polyline {
            fill: none;
            stroke-width: 2.4;
        }

        .sparkline .fill {
            stroke: none;
        }

        /* =========================================
           TICKER STRIP
        ========================================= */
        .ticker {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ticker-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--glass);
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 10px 16px;
            backdrop-filter: blur(14px);
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .ticker-chip:hover {
            transform: translateY(-2px);
            border-color: rgba(53, 224, 201, .35);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .3);
        }

        .ticker-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--teal);
            flex-shrink: 0;
        }

        .ticker-dot.live {
            animation: pulseRing 1.8s ease-out infinite;
        }

        .ticker-dot.coral {
            background: var(--coral);
        }

        .ticker-chip .t-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
        }

        .ticker-chip .t-value {
            font-weight: 700;
            font-size: 14px;
        }

        /* =========================================
           MINI STAT TILES (bento within bento)
        ========================================= */
        .tile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
        }

        .tile {
            background: rgba(255, 255, 255, .025);
            border: 1px solid var(--line-soft);
            border-radius: var(--radius-sm);
            padding: 16px;
            transition: transform .25s ease, background .25s ease;
        }

        .tile:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, .05);
        }

        .tile .tile-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-bottom: 10px;
            background: rgba(53, 224, 201, .12);
            color: var(--teal);
        }

        .tile.gold-icon .tile-icon {
            background: rgba(240, 189, 90, .12);
            color: var(--gold);
        }

        .tile.coral-icon .tile-icon {
            background: rgba(255, 111, 111, .12);
            color: var(--coral);
        }

        .tile .tile-label {
            font-size: 11.5px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }

        .tile .tile-value {
            font-size: 20px;
            font-weight: 700;
        }

        /* =========================================
           NETWORK MAP (signature — customer)
        ========================================= */
        .netmap-wrap {
            position: relative;
            width: 100%;
            max-width: 460px;
            margin: 0 auto;
        }

        .netmap-wrap svg {
            width: 100%;
            height: auto;
        }

        .flow-path {
            fill: none;
            stroke-width: 2;
            stroke-dasharray: 5 8;
            animation: flowDash 1.1s linear infinite;
        }

        @keyframes flowDash {
            to {
                stroke-dashoffset: -26;
            }
        }

        .node-pulse {
            animation: nodeGrow 2.6s ease-in-out infinite;
            transform-origin: center;
        }

        @keyframes nodeGrow {

            0%,
            100% {
                r: var(--r);
            }

            50% {
                r: calc(var(--r) + 2px);
            }
        }

        .netmap-label {
            font-family: var(--font-mono);
            font-size: 11px;
            fill: var(--text-muted);
        }

        .netmap-count {
            font-family: var(--font-display);
            font-weight: 800;
            fill: var(--text);
        }

        /* =========================================
           RANK RINGS (signature — customer)
        ========================================= */
        .rings-wrap {
            position: relative;
            width: 190px;
            height: 190px;
            margin: 0 auto;
        }

        .rings-wrap svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .ring-track {
            fill: none;
            stroke: rgba(255, 255, 255, .07);
        }

        .ring-fill {
            fill: none;
            stroke-linecap: round;
            animation: ringFill 1.5s cubic-bezier(.16, 1, .3, 1) forwards;
            animation-delay: .25s;
        }

        @keyframes ringFill {
            to {
                stroke-dashoffset: var(--off);
            }
        }

        .rings-center {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .rings-center .lvl {
            font-family: var(--font-display);
            font-size: 30px;
            font-weight: 800;
        }

        .rings-center .lvl-label {
            font-size: 10.5px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .ring-legend {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .ring-legend span {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .ring-legend i {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        /* =========================================
           BUTTONS & INPUTS
        ========================================= */
        .btn {
            padding: 11px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
            transition: transform .15s ease, opacity .15s ease, box-shadow .2s ease;
        }

        .btn:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .btn:active {
            transform: scale(.96);
        }

        .btn-primary {
            background: linear-gradient(120deg, var(--gold-2), var(--gold));
            color: #241a04;
            box-shadow: 0 8px 24px rgba(240, 189, 90, .25);
        }

        .btn-primary:hover {
            box-shadow: 0 10px 30px rgba(240, 189, 90, .4);
        }

        .btn-secondary {
            background: var(--glass-strong);
            color: var(--text);
            border: 1px solid var(--line);
        }

        .btn-whatsapp {
            background: #25d366;
            color: #04220f;
        }

        .field-input {
            width: 100%;
            background: rgba(0, 0, 0, .35);
            border: 1px solid var(--line);
            color: var(--text);
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-family: var(--font-mono);
        }

        .leg-choice {
            display: inline-flex;
            gap: 4px;
            background: rgba(0, 0, 0, .35);
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 4px;
            margin-bottom: 16px;
            position: relative;
        }

        .leg-choice label {
            padding: 7px 18px;
            border-radius: 999px;
            font-size: 13px;
            cursor: pointer;
            color: var(--text-muted);
            position: relative;
        }

        .leg-choice input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .leg-choice label:has(input:checked) {
            background: linear-gradient(120deg, var(--teal-2), var(--teal));
            color: #04231f;
            font-weight: 700;
        }

        /* =========================================
           LEADERBOARD TILES (admin packages)
        ========================================= */
        .leaderboard {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .lb-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px;
            border-radius: var(--radius-sm);
            border: 1px solid transparent;
            background: rgba(255, 255, 255, .02);
            cursor: pointer;
            text-align: left;
            width: 100%;
            color: inherit;
            transition: background .2s ease, border-color .2s ease, transform .2s ease;
        }

        .lb-row:hover,
        .lb-row:focus-visible {
            background: rgba(255, 255, 255, .06);
            border-color: var(--line);
            transform: translateX(3px);
        }

        .mini-ring {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            flex-shrink: 0;
            background: conic-gradient(var(--teal) calc(var(--pct) * 1%), rgba(255, 255, 255, .08) 0);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mini-ring span {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #0d1119;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .lb-main {
            flex: 1;
            min-width: 0;
        }

        .lb-main .lb-rank {
            font-family: var(--font-mono);
            font-size: 10.5px;
            color: var(--text-dim);
        }

        .lb-main .lb-name {
            font-size: 13.5px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lb-amount {
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 14px;
            color: var(--gold);
            white-space: nowrap;
        }

        /* =========================================
           TABLES & CHARTS
        ========================================= */
        .table-wrap {
            overflow-x: auto;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        table.data-table th {
            text-align: left;
            padding: 10px 12px;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: 1px solid var(--line);
        }

        table.data-table td {
            padding: 11px 12px;
            border-bottom: 1px solid var(--line-soft);
        }

        table.data-table tbody tr {
            transition: background .15s ease;
        }

        table.data-table tbody tr:hover {
            background: rgba(255, 255, 255, .04);
        }

        canvas.chart-surface {
            background: rgba(0, 0, 0, .25);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 12px;
            max-height: 300px;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .summary-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-box {
            background: rgba(0, 0, 0, .25);
            border: 1px solid var(--line);
            border-top: 3px solid var(--teal);
            border-radius: 12px;
            padding: 15px;
        }

        .summary-box.all-time {
            border-top-color: var(--gold);
        }

        .summary-box p {
            margin: 0 0 6px;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
        }

        .summary-box h3 {
            margin: 0;
            font-size: 21px;
            font-weight: 700;
        }

        /* =========================================
           QUEUE / ALERT CARD
        ========================================= */
        .queue-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 0;
            border-bottom: 1px solid var(--line-soft);
            position: relative;
            z-index: 1;
        }

        .queue-row:last-child {
            border-bottom: none;
        }

        .queue-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .queue-value {
            font-weight: 700;
            font-size: 15px;
            font-family: var(--font-mono);
        }

        .status-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }

        .status-banner.ok {
            background: rgba(53, 224, 201, .1);
            color: var(--teal);
        }

        .status-banner.alert {
            background: rgba(255, 111, 111, .1);
            color: var(--coral);
        }

        /* =========================================
           MODALS
        ========================================= */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(3, 4, 8, .75);
            backdrop-filter: blur(10px);
            z-index: 999;
            align-items: center;
            justify-content: center;
            animation: fadeIn .2s ease-in-out;
        }

        .modal-box {
            background: linear-gradient(180deg, #141826, #0e121c);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            width: 92%;
            max-width: 460px;
            padding: 26px;
            position: relative;
            box-shadow: 0 30px 90px rgba(0, 0, 0, .6);
        }

        .modal-box.wide {
            max-width: 720px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            padding: 0;
            overflow: hidden;
        }

        .modal-box.wide .modal-head {
            padding: 18px 22px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-box.wide .modal-body-scroll {
            padding: 16px 22px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(53, 224, 201, .4) transparent;
        }

        .modal-box.wide .modal-body-scroll::-webkit-scrollbar {
            width: 7px;
        }

        .modal-box.wide .modal-body-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--teal), var(--gold));
            border-radius: 6px;
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: var(--glass-strong);
            border: 1px solid var(--line);
            color: var(--text-muted);
            font-size: 17px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: rgba(255, 111, 111, .12);
            color: var(--coral);
        }

        .modal-box h2 {
            margin: 0 0 18px;
            font-family: var(--font-display);
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .form-group input {
            width: 100%;
            padding: 11px;
            border-radius: 9px;
            border: 1px solid var(--line);
            background: rgba(0, 0, 0, .35);
            color: var(--text);
        }

        .reward-banner {
            background: rgba(0, 0, 0, .3);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 16px;
            text-align: center;
        }

        .reward-banner.winner {
            background: linear-gradient(135deg, var(--teal), #1fae9f);
            color: #04231f;
            border: none;
        }

        .modal-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }

        .modal-summary-grid div {
            background: rgba(0, 0, 0, .3);
            border: 1px solid var(--line);
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }

        .accordion-item {
            background: rgba(0, 0, 0, .3);
            border: 1px solid var(--line);
            border-radius: 10px;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .accordion-header {
            width: 100%;
            background: none;
            border: none;
            color: var(--text);
            padding: 13px 14px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .accordion-header::after {
            content: '+';
            color: var(--text-muted);
        }

        .accordion-header[aria-expanded="true"]::after {
            content: '–';
        }

        .accordion-body {
            display: none;
            padding: 12px 14px;
            border-top: 1px solid var(--line);
        }

        .voucher-pill {
            display: inline-block;
            padding: 7px 12px;
            margin: 4px;
            border-radius: 20px;
            background: var(--glass-strong);
            font-size: 12px;
        }

        .voucher-pill.used {
            color: var(--text-dim);
        }

        .voucher-pill.unused {
            background: rgba(53, 224, 201, .12);
            color: var(--teal);
        }

        /* =========================================
           CONFETTI
        ========================================= */
        .confetti-piece {
            position: fixed;
            top: -10px;
            width: 8px;
            height: 14px;
            z-index: 1000;
            pointer-events: none;
            animation: confettiFall linear forwards;
        }

        @keyframes confettiFall {
            to {
                transform: translateY(105vh) rotate(540deg);
                opacity: .3;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        @media (prefers-reduced-motion: reduce) {

            html {
                scroll-behavior: auto;
            }

            #netCanvas {
                display: none;
            }

            .reveal {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }

            .flow-path,
            .node-pulse,
            .ticker-dot.live,
            .page-head .live-dot {
                animation: none !important;
            }

            .ring-fill {
                animation: none !important;
                stroke-dashoffset: var(--off) !important;
            }
        }
    </style>

    <canvas id="netCanvas" aria-hidden="true"></canvas>
    <div class="bg-grid" aria-hidden="true"></div>

    @if (Auth::user()->hasRole('customer'))
        @php
            $t1 = min((($progress->tier_1_count ?? 0) / 3) * 100, 100);
            $t2 = min((($progress->tier_2_count ?? 0) / 9) * 100, 100);
            $t3 = min((($progress->tier_3_count ?? 0) / 27) * 100, 100);
            $r1 = 85; $r2 = 68; $r3 = 51;
            $c1 = 2 * M_PI * $r1; $c2 = 2 * M_PI * $r2; $c3 = 2 * M_PI * $r3;
            $off1 = $c1 * (1 - $t1 / 100);
            $off2 = $c2 * (1 - $t2 / 100);
            $off3 = $c3 * (1 - $t3 / 100);
            $maxLeg = max($leftDownline ?? 0, $rightDownline ?? 0, 1);
        @endphp

        <div class="page-head reveal">
            <div>
                <p class="eyebrow"><span class="live-dot" aria-hidden="true"></span> User Console</p>
                <h1>Your Dashboard</h1>
                <p class="sub">Referral growth &amp; earnings at a glance</p>
            </div>
            <div class="user-chip">
                <span class="avatar" aria-hidden="true">{{ strtoupper(substr(Auth::user()->uname ?? Auth::user()->name, 0, 1)) }}</span>
                <span>
                    <span class="name">{{ Auth::user()->uname ?? Auth::user()->name }}</span>
                    <span class="role">Customer</span>
                </span>
            </div>
        </div>

        <!-- HERO KPI ROW -->
        <div class="bento">
            <div class="glass b-6 reveal">
                <p class="hero-label"><i class="fa-solid fa-sack-dollar" aria-hidden="true"></i> Total Earning</p>
                <div class="hero-figure grad-gold">₹{{ number_format(($directIncome ?? 0) + ($pairIncome ?? 0), 0) }}</div>
                <p style="margin:0; font-size:12px; color:var(--text-muted);">Direct + Pair income combined</p>
            </div>
            <div class="glass b-6 reveal">
                <p class="hero-label"><i class="fa-solid fa-diagram-project" aria-hidden="true"></i> Total Downline</p>
                <div class="hero-figure grad-teal mono">{{ $totalDownline ?? 0 }}</div>
                <p style="margin:0; font-size:12px; color:var(--text-muted);">{{ $leftDownline ?? 0 }} left · {{ $rightDownline ?? 0 }} right</p>
            </div>
        </div>

        <!-- REFERRAL + LIVE NETWORK MAP -->
        <div class="bento">
            <div class="glass b-6 reveal">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Grow your network</p>
                        <h2>Referral Center</h2>
                    </div>
                </div>

                <div class="leg-choice" role="radiogroup" aria-label="Referral leg">
                    <label><input type="radio" name="leg" value="1" checked><span>Left</span></label>
                    <label><input type="radio" name="leg" value="2"><span>Right</span></label>
                </div>

                <label for="refLink" class="visually-hidden">Your referral link</label>
                <input type="text" id="refLink" class="field-input" readonly
                    value="{{ url('/register') }}?refid={{ Auth::user()->id }}&leg=1&name={{ urlencode(Auth::user()->username ?? Auth::user()->name) }}"
                    style="margin-bottom: 14px;">

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="btn btn-primary" onclick="copyLink()">Copy Link</button>
                    <button type="button" class="btn btn-whatsapp" onclick="shareWhatsApp()">Share on WhatsApp</button>
                </div>
            </div>

            <div class="glass b-6 reveal">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Live network map</p>
                        <h2>Your Matrix</h2>
                    </div>
                </div>
                <div class="netmap-wrap">
                    <svg viewBox="0 0 320 190" role="img" aria-label="Network map showing {{ $leftDownline ?? 0 }} left leg members and {{ $rightDownline ?? 0 }} right leg members">
                        <path class="flow-path" d="M160,34 C120,72 92,92 62,140" stroke="#35e0c9" />
                        <path class="flow-path" d="M160,34 C200,72 228,92 258,140" stroke="#f0bd5a" />
                        <circle cx="160" cy="34" r="26" fill="rgba(255,255,255,.06)" stroke="url(#gradYou)" stroke-width="2" />
                        <circle class="node-pulse" style="--r:8px" cx="62" cy="140" r="8" fill="#35e0c9" opacity=".9" />
                        <circle cx="62" cy="140" r="30" fill="rgba(53,224,201,.08)" stroke="#35e0c9" stroke-width="1.5" />
                        <circle class="node-pulse" style="--r:8px" cx="258" cy="140" r="8" fill="#f0bd5a" opacity=".9" />
                        <circle cx="258" cy="140" r="30" fill="rgba(240,189,90,.08)" stroke="#f0bd5a" stroke-width="1.5" />

                        <text x="160" y="30" text-anchor="middle" class="netmap-count" font-size="13">You</text>
                        <text x="160" y="46" text-anchor="middle" class="netmap-label" font-size="9">Level {{ $user->rank_level }}</text>

                        <text x="62" y="136" text-anchor="middle" class="netmap-count" font-size="17">{{ $leftDownline ?? 0 }}</text>
                        <text x="62" y="168" text-anchor="middle" class="netmap-label">Left Leg</text>

                        <text x="258" y="136" text-anchor="middle" class="netmap-count" font-size="17">{{ $rightDownline ?? 0 }}</text>
                        <text x="258" y="168" text-anchor="middle" class="netmap-label">Right Leg</text>

                        <defs>
                            <linearGradient id="gradYou" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#ffd98a" />
                                <stop offset="100%" stop-color="#35e0c9" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>

        <!-- RANK RINGS + PERFORMANCE TILES -->
        <div class="bento">
            <div class="glass b-5 reveal">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Progression</p>
                        <h2>Rank Rings</h2>
                    </div>
                    <span class="tag tag-gold">LVL {{ $user->rank_level }}</span>
                </div>
                <div class="rings-wrap">
                    <svg viewBox="0 0 190 190">
                        <circle class="ring-track" cx="95" cy="95" r="{{ $r1 }}" stroke-width="9" />
                        <circle class="ring-track" cx="95" cy="95" r="{{ $r2 }}" stroke-width="9" />
                        <circle class="ring-track" cx="95" cy="95" r="{{ $r3 }}" stroke-width="9" />
                        <circle class="ring-fill" cx="95" cy="95" r="{{ $r1 }}" stroke-width="9" stroke="#35e0c9"
                            style="--circ:{{ $c1 }}; --off:{{ $off1 }}; stroke-dasharray:{{ $c1 }};" />
                        <circle class="ring-fill" cx="95" cy="95" r="{{ $r2 }}" stroke-width="9" stroke="#f0bd5a"
                            style="--circ:{{ $c2 }}; --off:{{ $off2 }}; stroke-dasharray:{{ $c2 }}; animation-delay:.4s;" />
                        <circle class="ring-fill" cx="95" cy="95" r="{{ $r3 }}" stroke-width="9" stroke="#ff6f6f"
                            style="--circ:{{ $c3 }}; --off:{{ $off3 }}; stroke-dasharray:{{ $c3 }}; animation-delay:.55s;" />
                    </svg>
                    <div class="rings-center">
                        <span class="lvl">{{ $user->rank_level }}</span>
                        <span class="lvl-label">Rank Level</span>
                    </div>
                </div>
                <div class="ring-legend">
                    <span><i style="background:#35e0c9;"></i> Tier 1 · {{ $progress->tier_1_count ?? 0 }}/3</span>
                    <span><i style="background:#f0bd5a;"></i> Tier 2 · {{ $progress->tier_2_count ?? 0 }}/9</span>
                    <span><i style="background:#ff6f6f;"></i> Tier 3 · {{ $progress->tier_3_count ?? 0 }}/27</span>
                </div>
            </div>

            <div class="glass b-7 reveal">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Earnings</p>
                        <h2>Performance</h2>
                    </div>
                </div>
                <div class="tile-grid">
                    <div class="tile">
                        <div class="tile-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                        <p class="tile-label">Payout Received</p>
                        <p class="tile-value mono">{{ $payoutReceived ?? 0 }}</p>
                    </div>
                    <div class="tile coral-icon">
                        <div class="tile-icon"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i></div>
                        <p class="tile-label">Payout Pending</p>
                        <p class="tile-value mono">{{ $payoutPending ?? 0 }}</p>
                    </div>
                    <div class="tile gold-icon">
                        <div class="tile-icon"><i class="fa-solid fa-hand-holding-dollar" aria-hidden="true"></i></div>
                        <p class="tile-label">Direct Income</p>
                        <p class="tile-value mono">₹{{ number_format($directIncome ?? 0, 2) }}</p>
                    </div>
                    <div class="tile gold-icon">
                        <div class="tile-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
                        <p class="tile-label">Level Income</p>
                        <p class="tile-value mono">₹{{ number_format($totalLevelIncome ?? 0, 2) }}</p>
                    </div>
                    <div class="tile gold-icon">
                        <div class="tile-icon"><i class="fa-solid fa-people-arrows" aria-hidden="true"></i></div>
                        <p class="tile-label">Pair Income</p>
                        <p class="tile-value mono">₹{{ number_format($pairIncome ?? 0, 2) }}</p>
                    </div>
                    <div class="tile">
                        <div class="tile-icon"><i class="fa-solid fa-wallet" aria-hidden="true"></i></div>
                        <p class="tile-label">Topup Wallet</p>
                        <p class="tile-value mono">₹{{ number_format($walletBalance ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- LUCKY VOUCHERS -->
        @if ($cycle)
            <div class="glass b-12 reveal" style="margin-bottom:20px;">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Rewards</p>
                        <h2>🎟 Lucky Vouchers</h2>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="luckyModalA11y.open(this)">View details</button>
                </div>
                <div class="tile-grid">
                    <div class="tile">
                        <p class="tile-label">Months Completed</p>
                        <p class="tile-value mono">{{ $cycle->current_month }} / 16</p>
                    </div>
                    <div class="tile">
                        <p class="tile-label">Total Vouchers</p>
                        <p class="tile-value mono">{{ $totalVouchers }}</p>
                    </div>
                    <div class="tile">
                        <p class="tile-label">Active Vouchers</p>
                        <p class="tile-value mono">{{ $unusedVouchers }}</p>
                    </div>
                    <div class="tile {{ $cycle->status === 'won' ? 'gold-icon' : '' }}">
                        <p class="tile-label">Reward Status</p>
                        <p class="tile-value">{{ $rewardStatus }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="glass b-12 reveal" style="margin-bottom:20px;">
                <p class="eyebrow">🎟 Lucky Vouchers</p>
                <p style="color:var(--text-muted); margin:0;">Purchase a ₹50,000 or ₹1,00,000 package to participate in Lucky Draw Rewards.</p>
            </div>
        @endif

        <div id="luckyModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="luckyModalTitle" aria-hidden="true">
            <div class="modal-box">
                <button type="button" class="modal-close" aria-label="Close voucher details" onclick="luckyModalA11y.close()">&times;</button>
                <h2 id="luckyModalTitle">🎟 Lucky Vouchers &amp; Rewards</h2>

                @if ($cycle)
                    <div class="reward-banner {{ $cycle->status === 'won' ? 'winner' : '' }}">
                        <h3 style="margin:0 0 6px;">{{ $rewardStatus }}</h3>
                        <p style="margin:0;">{{ $rewardText }}</p>
                    </div>

                    <div class="modal-summary-grid">
                        <div>
                            <strong class="mono">{{ $cycle->current_month }}/16</strong>
                            <div style="font-size:11px; color:var(--text-muted);">Months</div>
                        </div>
                        <div>
                            <strong class="mono">{{ $totalVouchers }}</strong>
                            <div style="font-size:11px; color:var(--text-muted);">Total Vouchers</div>
                        </div>
                        <div>
                            <strong class="mono">{{ $unusedVouchers }}</strong>
                            <div style="font-size:11px; color:var(--text-muted);">Active</div>
                        </div>
                    </div>
                @else
                    <p style="color:var(--text-muted);">No active voucher cycle found. Complete EMI/Topup to activate vouchers.</p>
                @endif

                @if ($cycle && $voucherGroups)
                    <div class="accordion">
                        @foreach ($voucherGroups as $month => $vouchers)
                            <div class="accordion-item">
                                <button type="button" class="accordion-header" aria-expanded="false" onclick="toggleAccordion(this)">
                                    Month {{ $month }} Vouchers
                                </button>
                                <div class="accordion-body">
                                    @foreach ($vouchers as $v)
                                        <span class="voucher-pill {{ $v->status }}">{{ $v->voucher_code }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if (Auth::user()->hasRole('admin'))
        @php
            $maxPkgTotal = max($starterTotal, $sevenTotal, $thirteenTotal, $fiftyKTotal, $oneLakhTotal, 1);
            $pkgLeaderboard = [
                ['rank' => '01', 'key' => 'starter', 'name' => 'Starter (1000)', 'total' => $starterTotal],
                ['rank' => '02', 'key' => '7000', 'name' => 'Seven + One (7000)', 'total' => $sevenTotal],
                ['rank' => '03', 'key' => '13000', 'name' => 'Thirteen + Three (13000)', 'total' => $thirteenTotal],
                ['rank' => '04', 'key' => '50000', 'name' => 'Golden (50000)', 'total' => $fiftyKTotal],
                ['rank' => '05', 'key' => '100000', 'name' => 'Super Golden (100000)', 'total' => $oneLakhTotal],
            ];

            $sparkSeries = collect($fundData ?? [])->values();
            $sparkMax = $sparkSeries->max() ?: 1;
            $sparkMin = $sparkSeries->min() ?: 0;
            $sparkRange = ($sparkMax - $sparkMin) ?: 1;
            $sparkCount = $sparkSeries->count();
            $sparkPoints = $sparkSeries->map(function ($v, $i) use ($sparkCount, $sparkMin, $sparkRange) {
                $x = $sparkCount > 1 ? ($i / ($sparkCount - 1)) * 200 : 0;
                $y = 40 - ((($v - $sparkMin) / $sparkRange) * 34) - 3;
                return round($x, 1) . ',' . round($y, 1);
            })->implode(' ');
        @endphp

        <div class="page-head reveal">
            <div>
                <p class="eyebrow"><span class="live-dot" aria-hidden="true"></span> Operations Console</p>
                <h1>Admin Overview</h1>
                <p class="sub">Live network &amp; revenue overview — {{ now()->format('d M, Y') }}</p>
            </div>
            <div class="user-chip">
                <span class="avatar" aria-hidden="true">{{ strtoupper(substr(Auth::user()->username ?? Auth::user()->name, 0, 1)) }}</span>
                <span>
                    <span class="name">{{ Auth::user()->username ?? Auth::user()->name }}</span>
                    <span class="role">Admin</span>
                </span>
            </div>
        </div>

        <!-- HERO: WALLET FIGURE + ATTENTION QUEUE -->
        <div class="bento">
            <div class="glass b-7 reveal">
                <p class="hero-label"><i class="fa-solid fa-wallet" aria-hidden="true"></i> Total Wallet Balance · All-Time</p>
                <div class="hero-figure grad-gold">₹{{ number_format($totalWallet ?? 0, 0) }}</div>
                <svg class="sparkline" viewBox="0 0 200 40" preserveAspectRatio="none" role="img" aria-label="Funds added trend this month">
                    <defs>
                        <linearGradient id="sparkFill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#f0bd5a" stop-opacity=".35" />
                            <stop offset="100%" stop-color="#f0bd5a" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    @if ($sparkCount > 0)
                        <polyline class="fill" points="0,40 {{ $sparkPoints }} 200,40" fill="url(#sparkFill)" />
                        <polyline points="{{ $sparkPoints }}" stroke="#f0bd5a" />
                    @endif
                </svg>
                <p style="margin:6px 0 0; font-size:11.5px; color:var(--text-muted);">Funds added this month, day by day</p>
            </div>

            <div class="glass b-5 reveal queue-card">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Needs review</p>
                        <h2>Attention Queue</h2>
                    </div>
                    <i class="fa-solid fa-bell" style="color:var(--text-muted);" aria-hidden="true"></i>
                </div>
                <div class="queue-row">
                    <span class="queue-label"><i class="fa-solid fa-clock" aria-hidden="true"></i> Requested today</span>
                    <span class="queue-value" style="color: {{ ($todayPendingWithdraws ?? 0) > 0 ? 'var(--coral)' : 'var(--text)' }};">{{ $todayPendingWithdraws ?? 0 }}</span>
                </div>
                <div class="queue-row">
                    <span class="queue-label"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i> Pending (all-time)</span>
                    <span class="queue-value" style="color: {{ ($pendingWithdraws ?? 0) > 0 ? 'var(--coral)' : 'var(--text)' }};">{{ $pendingWithdraws ?? 0 }}</span>
                </div>

                @if (($pendingWithdraws ?? 0) > 0)
                    <div class="status-banner alert">
                        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                        {{ $pendingWithdraws }} withdrawal{{ $pendingWithdraws > 1 ? 's' : '' }} waiting on you
                    </div>
                @else
                    <div class="status-banner ok">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        All withdrawals cleared
                    </div>
                @endif
            </div>
        </div>

        <!-- LIVE TICKER -->
        <div class="glass b-12 reveal" style="margin-bottom:20px;">
            <div class="panel-head">
                <div>
                    <p class="eyebrow">Right now</p>
                    <h2>Today's Pulse</h2>
                </div>
                <span class="tag tag-teal">Updated {{ now()->format('h:i A') }}</span>
            </div>
            <div class="ticker">
                <div class="ticker-chip">
                    <span class="ticker-dot live" aria-hidden="true"></span>
                    <span class="t-label">New Users</span>
                    <span class="t-value mono count-up" data-count-up="{{ $todaysData['todayUsers'] ?? 0 }}">0</span>
                </div>
                <div class="ticker-chip">
                    <span class="ticker-dot live" aria-hidden="true"></span>
                    <span class="t-label">Top-ups</span>
                    <span class="t-value mono count-up" data-count-up="{{ $todaysData['todayTopups'] ?? 0 }}">0</span>
                </div>
                <div class="ticker-chip">
                    <span class="ticker-dot live" aria-hidden="true"></span>
                    <span class="t-label">Renewals</span>
                    <span class="t-value mono count-up" data-count-up="{{ $todaysData['todayRenewals'] ?? 0 }}">0</span>
                </div>
                <div class="ticker-chip">
                    <span class="ticker-dot coral {{ ($todayPendingWithdraws ?? 0) > 0 ? 'live' : '' }}" aria-hidden="true"></span>
                    <span class="t-label">Withdrawals Req.</span>
                    <span class="t-value mono count-up" data-count-up="{{ $todayPendingWithdraws ?? 0 }}">0</span>
                </div>
                <div class="ticker-chip">
                    <span class="ticker-dot" aria-hidden="true"></span>
                    <span class="t-label">Withdrawals Paid</span>
                    <span class="t-value mono count-up" data-count-up="{{ $todaysData['todayCompletedWithdraws'] ?? 0 }}">0</span>
                </div>
            </div>
        </div>

        <!-- SYSTEM OVERVIEW TILES -->
        <div class="glass b-12 reveal" style="margin-bottom:20px;">
            <div class="panel-head">
                <div>
                    <p class="eyebrow">All-time</p>
                    <h2>System Overview</h2>
                </div>
                <span class="tag tag-gold">Cumulative</span>
            </div>
            <div class="tile-grid">
                <div class="tile">
                    <div class="tile-icon"><i class="fa-solid fa-users" aria-hidden="true"></i></div>
                    <p class="tile-label">Total Users</p>
                    <p class="tile-value mono count-up" data-count-up="{{ $totalUsers ?? 0 }}">0</p>
                </div>
                <div class="tile">
                    <div class="tile-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                    <p class="tile-label">Withdrawals Paid</p>
                    <p class="tile-value mono count-up" data-count-up="{{ $completedWithdraws ?? 0 }}">0</p>
                </div>
                <div class="tile gold-icon">
                    <div class="tile-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
                    <p class="tile-label">Total Top-ups</p>
                    <p class="tile-value mono count-up" data-count-up="{{ $totalTopups ?? 0 }}">0</p>
                </div>
                <div class="tile coral-icon">
                    <div class="tile-icon"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i></div>
                    <p class="tile-label">Pending Withdrawals</p>
                    <p class="tile-value mono count-up" data-count-up="{{ $pendingWithdraws ?? 0 }}">0</p>
                </div>
            </div>
        </div>

        <!-- GROWTH CHART + PACKAGE LEADERBOARD -->
        <div class="bento">
            <div class="glass b-8 reveal">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Trend</p>
                        <h2>📈 Monthly Growth</h2>
                    </div>
                </div>
                <canvas id="growthChart" class="chart-surface" style="width:100%;"></canvas>
            </div>

            <div class="glass b-4 reveal">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Ranked by revenue</p>
                        <h2>Package Leaderboard</h2>
                    </div>
                </div>
                <div class="leaderboard">
                    @foreach ($pkgLeaderboard as $pkg)
                        <button type="button" class="lb-row package-card" data-package="{{ $pkg['key'] }}">
                            <span class="mini-ring" style="--pct: {{ round(($pkg['total'] / $maxPkgTotal) * 100) }};">
                                <span>{{ $pkg['rank'] }}</span>
                            </span>
                            <span class="lb-main">
                                <span class="lb-rank">RANK {{ $pkg['rank'] }}</span>
                                <div class="lb-name">{{ $pkg['name'] }}</div>
                            </span>
                            <span class="lb-amount count-up" data-prefix="₹" data-decimals="0" data-count-up="{{ $pkg['total'] }}">₹0</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- PRODUCT SALES -->
        <div class="glass b-12 reveal" style="margin-bottom:20px;">
            <div class="panel-head">
                <div>
                    <p class="eyebrow">Today vs all-time</p>
                    <h2>🛒 Product Sales</h2>
                </div>
            </div>

            <div class="summary-strip">
                <div class="summary-box">
                    <p>Today's Orders</p>
                    <h3 class="mono count-up" data-count-up="{{ $todayPackageTotals['qty'] ?? 0 }}">0</h3>
                </div>
                <div class="summary-box">
                    <p>Today's Revenue</p>
                    <h3 class="mono count-up" data-prefix="₹" data-decimals="0" data-count-up="{{ $todayPackageTotals['amount'] ?? 0 }}">₹0</h3>
                </div>
                <div class="summary-box all-time">
                    <p>All-Time Orders</p>
                    <h3 class="mono count-up" data-count-up="{{ $allTimePackageTotals['qty'] ?? 0 }}">0</h3>
                </div>
                <div class="summary-box all-time">
                    <p>All-Time Revenue</p>
                    <h3 class="mono count-up" data-prefix="₹" data-decimals="0" data-count-up="{{ $allTimePackageTotals['amount'] ?? 0 }}">₹0</h3>
                </div>
            </div>

            <div class="chart-grid" style="margin-bottom:20px;">
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr><th>Today — Product</th><th>Qty</th><th>Amount</th></tr>
                        </thead>
                        <tbody>
                            @forelse (($todayPackageStats ?? []) as $row)
                                <tr>
                                    <td>{{ $row->package_name }}</td>
                                    <td class="mono">{{ $row->qty }}</td>
                                    <td class="mono">₹{{ number_format($row->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center; color:var(--text-muted);">No sales yet today</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr><th>All-Time — Product</th><th>Qty</th><th>Amount</th></tr>
                        </thead>
                        <tbody>
                            @forelse (($allTimePackageStats ?? []) as $row)
                                <tr>
                                    <td>{{ $row->package_name }}</td>
                                    <td class="mono">{{ $row->qty }}</td>
                                    <td class="mono">₹{{ number_format($row->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center; color:var(--text-muted);">No sales recorded yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="chart-grid">
                <canvas id="pkgChartToday" class="chart-surface"></canvas>
                <canvas id="pkgChartAllTime" class="chart-surface"></canvas>
            </div>
        </div>
    @endif

    <!-- Package Users Modal -->
    <div id="packageModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-box wide">
            <div class="modal-head">
                <h3 id="modalTitle" style="margin:0; font-family: var(--font-display); font-size:16px;">Package Users</h3>
                <button type="button" class="modal-close" style="position:static;" id="closeModal" aria-label="Close package users">&times;</button>
            </div>
            <div class="modal-body-scroll" id="modalBody"></div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div id="passwordModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="pwModalTitle" aria-hidden="true">
        <div class="modal-box">
            <button type="button" class="modal-close" aria-label="Close password change" onclick="closeModal()">&times;</button>
            <h2 id="pwModalTitle">Change Password</h2>
            <form id="passwordForm" method="POST" action="{{ route('changep.update') }}">
                @csrf
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirm Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Update Password</button>
            </form>
            @if (session('success'))
                <p style="color:var(--teal); text-align:center;">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p style="color:var(--coral); text-align:center;">{{ session('error') }}</p>
            @endif
        </div>
    </div>

    <script>
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // =========================================
        // Ambient particle network background
        // =========================================
        (function() {
            const canvas = document.getElementById('netCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let w, h, particles;

            function size() {
                w = canvas.width = window.innerWidth;
                h = canvas.height = window.innerHeight;
            }

            function init() {
                size();
                const count = Math.min(60, Math.floor((w * h) / 26000));
                particles = Array.from({ length: count }, () => ({
                    x: Math.random() * w,
                    y: Math.random() * h,
                    vx: (Math.random() - 0.5) * 0.25,
                    vy: (Math.random() - 0.5) * 0.25,
                    c: Math.random() > 0.5 ? '53,224,201' : '240,189,90'
                }));
            }

            function frame() {
                ctx.clearRect(0, 0, w, h);
                for (const p of particles) {
                    p.x += p.vx;
                    p.y += p.vy;
                    if (p.x < 0 || p.x > w) p.vx *= -1;
                    if (p.y < 0 || p.y > h) p.vy *= -1;
                }
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i + 1; j < particles.length; j++) {
                        const a = particles[i], b = particles[j];
                        const dx = a.x - b.x, dy = a.y - b.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 140) {
                            ctx.strokeStyle = `rgba(${a.c},${(1 - dist / 140) * 0.15})`;
                            ctx.lineWidth = 1;
                            ctx.beginPath();
                            ctx.moveTo(a.x, a.y);
                            ctx.lineTo(b.x, b.y);
                            ctx.stroke();
                        }
                    }
                    ctx.fillStyle = `rgba(${p.c},0.5)`;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, 1.6, 0, Math.PI * 2);
                    ctx.fill();
                }
                if (!prefersReducedMotion) requestAnimationFrame(frame);
            }

            init();
            if (!prefersReducedMotion) {
                requestAnimationFrame(frame);
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) requestAnimationFrame(frame);
                });
            } else {
                frame();
            }
            window.addEventListener('resize', init);
        })();

        // =========================================
        // Cursor spotlight on glass cards
        // =========================================
        document.querySelectorAll('.glass').forEach(card => {
            card.addEventListener('mousemove', e => {
                const rect = card.getBoundingClientRect();
                card.style.setProperty('--mx', (e.clientX - rect.left) + 'px');
                card.style.setProperty('--my', (e.clientY - rect.top) + 'px');
            });
        });

        // =========================================
        // Scroll reveal
        // =========================================
        (function() {
            const items = document.querySelectorAll('.reveal');
            if (prefersReducedMotion || !('IntersectionObserver' in window)) {
                items.forEach(el => el.classList.add('in'));
                return;
            }
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => entry.target.classList.add('in'), i * 60);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            items.forEach(el => observer.observe(el));
        })();

        // =========================================
        // Accessible modal helper
        // =========================================
        function makeAccessibleModal(modal, opts) {
            opts = opts || {};
            // Guard: on role-specific pages some modals (e.g. #luckyModal for
            // admins) never render. Without this check, addEventListener on
            // null throws and halts every statement after it in this script
            // block — including the count-up animations further down, which
            // is why stats could get stuck at 0.
            if (!modal) {
                return { open: function() {}, close: function() {} };
            }
            let lastFocused = null;

            function focusables() {
                return Array.from(modal.querySelectorAll('a[href], button, input, select, textarea, [tabindex]:not([tabindex="-1"])'))
                    .filter(el => el.offsetParent !== null);
            }

            function open(trigger) {
                lastFocused = trigger || document.activeElement;
                modal.style.display = 'flex';
                modal.setAttribute('aria-hidden', 'false');
                const items = focusables();
                if (items.length) items[0].focus();
            }

            function close() {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
                if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
            }

            modal.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') { close(); return; }
                if (e.key === 'Tab') {
                    const items = focusables();
                    if (!items.length) return;
                    const first = items[0], last = items[items.length - 1];
                    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
                }
            });

            if (opts.closeOnBackdrop) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) close();
                });
            }

            return { open, close };
        }

        const luckyModalA11y = makeAccessibleModal(document.getElementById('luckyModal'), { closeOnBackdrop: true });
        const passwordModalA11y = makeAccessibleModal(document.getElementById('passwordModal'), { closeOnBackdrop: true });
        const packageModalA11y = makeAccessibleModal(document.getElementById('packageModal'), { closeOnBackdrop: false });

        function openModal() { passwordModalA11y.open(); }
        function closeModal() { passwordModalA11y.close(); }

        function toggleAccordion(btn) {
            const body = btn.nextElementSibling;
            const isOpen = body.style.display === 'block';
            body.style.display = isOpen ? 'none' : 'block';
            btn.setAttribute('aria-expanded', String(!isOpen));
        }

        // =========================================
        // Count-up animation
        // =========================================
        (function() {
            const els = document.querySelectorAll('.count-up[data-count-up]');

            function formatValue(el, value) {
                const decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
                const prefix = el.getAttribute('data-prefix') || '';
                const formatted = decimals > 0 ?
                    value.toLocaleString('en-IN', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) :
                    Math.round(value).toLocaleString('en-IN');
                return prefix + formatted;
            }

            function animateCount(el) {
                const target = parseFloat(el.getAttribute('data-count-up')) || 0;
                if (prefersReducedMotion) {
                    el.textContent = formatValue(el, target);
                    return;
                }
                const duration = 1100;
                const start = performance.now();
                function tick(now) {
                    const progress = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = formatValue(el, target * eased);
                    if (progress < 1) requestAnimationFrame(tick);
                }
                requestAnimationFrame(tick);
            }

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCount(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.3 });
                els.forEach(el => observer.observe(el));
            } else {
                els.forEach(animateCount);
            }
        })();

        // =========================================
        // Confetti for lucky-draw winners
        // =========================================
        function fireConfetti() {
            if (prefersReducedMotion) return;
            const colors = ['#f0bd5a', '#35e0c9', '#ff6f6f', '#ffd98a'];
            for (let i = 0; i < 60; i++) {
                const piece = document.createElement('div');
                piece.className = 'confetti-piece';
                piece.style.left = Math.random() * 100 + 'vw';
                piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                piece.style.animationDuration = (2.5 + Math.random() * 1.5) + 's';
                piece.style.opacity = String(0.7 + Math.random() * 0.3);
                document.body.appendChild(piece);
                setTimeout(() => piece.remove(), 4200);
            }
        }
    </script>

    @if (Auth::user()->hasRole('customer') && $cycle && $cycle->status === 'won')
        <script>document.addEventListener('DOMContentLoaded', fireConfetti);</script>
    @endif

    @if (Auth::user()->hasRole('admin'))
        <script>
            const packageUsers = @json($packageUsers ?? []);

            document.querySelectorAll('.package-card').forEach(card => {
                card.addEventListener('click', function() {
                    let packageType = this.getAttribute('data-package');
                    let users = [];

                    if (packageType === 'starter') {
                        users = [...(packageUsers['1000'] || []), ...(packageUsers['1100'] || [])];
                    } else {
                        users = packageUsers[packageType] || [];
                    }

                    let modalBody = document.getElementById('modalBody');
                    let modalTitle = document.getElementById('modalTitle');
                    modalTitle.innerText = `Users for Package (${users.length})`;

                    let html = `
                        <table class="data-table" width="100%">
                            <thead><tr><th>User Name</th><th>Email</th><th>Date</th></tr></thead>
                            <tbody>`;

                    if (users.length > 0) {
                        users.forEach(user => {
                            html += `
                                <tr>
                                    <td>${user.name ?? ''}</td>
                                    <td>${user.email ?? ''}</td>
                                    <td>${new Date(user.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                                </tr>`;
                        });
                    } else {
                        html += `<tr><td colspan="3" style="text-align:center;">No users found</td></tr>`;
                    }

                    html += `</tbody></table>`;
                    modalBody.innerHTML = html;
                    packageModalA11y.open(card);
                });
            });

            document.getElementById('closeModal').addEventListener('click', function() {
                packageModalA11y.close();
            });

            const growthCanvas = document.getElementById('growthChart');
            if (growthCanvas) {
                new Chart(growthCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($labels ?? []) !!},
                        datasets: [
                            {
                                label: '👥 New Users',
                                data: {!! json_encode($userData ?? []) !!},
                                borderColor: '#35e0c9',
                                backgroundColor: 'rgba(53,224,201,0.12)',
                                tension: 0.35,
                                fill: true,
                                borderWidth: 2
                            },
                            {
                                label: '💰 Funds Added (₹)',
                                data: {!! json_encode($fundData ?? []) !!},
                                borderColor: '#f0bd5a',
                                backgroundColor: 'rgba(240,189,90,0.12)',
                                tension: 0.35,
                                fill: true,
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { grid: { color: 'rgba(255,255,255,.06)' }, ticks: { color: '#9298ab' } },
                            y: { grid: { color: 'rgba(255,255,255,.06)' }, ticks: { color: '#9298ab' } }
                        },
                        plugins: {
                            legend: { labels: { color: '#f6f7fb' } },
                            title: { display: true, text: 'User & Fund Growth (Current Month)', color: '#9298ab' }
                        }
                    }
                });
            }

            (function() {
                const todayStats = @json($todayPackageStats ?? []);
                const allTimeStats = @json($allTimePackageStats ?? []);
                const gridColor = 'rgba(255,255,255,.06)';
                const tickColor = '#9298ab';

                const todayCanvas = document.getElementById('pkgChartToday');
                if (todayCanvas) {
                    new Chart(todayCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: todayStats.map(r => r.package_name),
                            datasets: [{
                                label: 'Amount (₹) — Today',
                                data: todayStats.map(r => r.amount),
                                backgroundColor: 'rgba(53,224,201,0.6)',
                                borderColor: '#35e0c9',
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: { grid: { color: gridColor }, ticks: { color: tickColor } },
                                y: { grid: { color: gridColor }, ticks: { color: tickColor } }
                            },
                            plugins: {
                                legend: { labels: { color: '#f6f7fb' } },
                                title: { display: true, text: "Today's Sales by Product", color: '#35e0c9' }
                            }
                        }
                    });
                }

                const allTimeCanvas = document.getElementById('pkgChartAllTime');
                if (allTimeCanvas) {
                    const palette = ['#f0bd5a', '#35e0c9', '#ff6f6f', '#8b5cf6', '#7cf2e2', '#ffd98a'];
                    new Chart(allTimeCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: allTimeStats.map(r => r.package_name),
                            datasets: [{
                                data: allTimeStats.map(r => r.amount),
                                backgroundColor: allTimeStats.map((_, i) => palette[i % palette.length]),
                                borderColor: '#060811',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { color: '#f6f7fb' } },
                                title: { display: true, text: 'All-Time Revenue by Product', color: '#f0bd5a' }
                            }
                        }
                    });
                }
            })();
        </script>
    @endif

    @if (Auth::user()->hasRole('customer'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const legStats = { 1: {{ $leftDownline ?? 0 }}, 2: {{ $rightDownline ?? 0 }} };
                const baseUrl = "{{ url('/register') }}";
                const refId = "{{ Auth::user()->id }}";
                const name = "{{ urlencode(Auth::user()->username ?? Auth::user()->name) }}";

                function updateReferralLink() {
                    const leg = document.querySelector('input[name="leg"]:checked').value;
                    document.getElementById('refLink').value = baseUrl + "?refid=" + refId + "&leg=" + leg + "&name=" + name;
                }

                document.querySelectorAll('input[name="leg"]').forEach(radio => {
                    radio.addEventListener('change', updateReferralLink);
                });

                updateReferralLink();
            });

            function copyLink() {
                const input = document.getElementById('refLink');
                input.select();
                document.execCommand('copy');
                alert('Referral link copied!');
            }

            function shareWhatsApp() {
                const link = document.getElementById('refLink').value;
                window.open('https://wa.me/?text=' + encodeURIComponent(link), '_blank');
            }
        </script>
    @endif

    <script>
        const logoutLink = document.getElementById('logout-link');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('logout-form').submit();
            });
        }

        document.querySelectorAll('li span').forEach(item => {
            if (item.textContent.trim() === 'Password') {
                item.parentElement.addEventListener('click', function() {
                    passwordModalA11y.open(item.parentElement);
                });
            }
        });

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPass = document.getElementById('new_password').value.trim();
            const confirmPass = document.getElementById('new_password_confirmation').value.trim();
            if (newPass !== confirmPass) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
@endsection