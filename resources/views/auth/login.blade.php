<x-layouts.auth :title="'Login | '.config('app.name')">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <style>
        /* ── Lock page to viewport — no scroll ── */
        body.auth-shell {
            height: 100vh;
            overflow: hidden;
        }
        body.auth-shell > div {
            height: 100vh;
            min-height: unset !important;
            padding-top: 1rem;
            padding-bottom: 1rem;
            align-items: stretch;
        }

        /* ── Animations ── */
        @keyframes loginFadeUp {
            from { opacity: 0; visibility: hidden; transform: translateY(20px); }
            to   { opacity: 1; visibility: visible; transform: translateY(0); }
        }
        @keyframes loginFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes orb1 {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(18px,-22px) scale(1.08); }
        }
        @keyframes orb2 {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(-14px,16px) scale(1.06); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        @keyframes wordmarkReveal {
            0% {
                opacity: 0;
                transform: translateY(20px) rotate(-6.8deg) scale(0.985);
                filter: blur(6px) saturate(95%);
            }
            55% {
                opacity: 0.34;
                transform: translateY(10px) rotate(-5.8deg) scale(1);
                filter: blur(2px) saturate(108%);
            }
            100% {
                opacity: 1;
                transform: translateY(0) rotate(-4.8deg) scale(1);
                filter: blur(0) saturate(118%);
            }
        }

        @keyframes wordmarkSettleBlur {
            0% {
                opacity: 0.3;
                filter: blur(0) saturate(118%);
            }
            100% {
                opacity: 0.23;
                filter: blur(1.8px) saturate(110%);
            }
        }

        @keyframes cardRiseIn {
            0% {
                opacity: 0;
                visibility: hidden;
                transform: translateY(26px) scale(0.986);
                filter: blur(6px);
            }
            1% {
                visibility: visible;
            }
            100% {
                opacity: 1;
                visibility: visible;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        .login-panel {
            height: 100%;
            animation: cardRiseIn 0.9s cubic-bezier(.22,.61,.36,1) 2s both;
        }

        .login-card {
            animation: cardRiseIn 0.9s cubic-bezier(.22,.61,.36,1) 2.26s both;
        }

        .login-form {
            animation: loginFadeUp 0.75s cubic-bezier(.22,.61,.36,1) 2.26s both;
        }

        .login-orb-1 {
            position:absolute; width:300px; height:300px; border-radius:50%; pointer-events:none;
            background: radial-gradient(circle, rgba(36,184,255,0.22) 0%, transparent 70%);
            top:-70px; left:-80px; animation: orb1 9s ease-in-out infinite;
        }
        .login-orb-2 {
            position:absolute; width:240px; height:240px; border-radius:50%; pointer-events:none;
            background: radial-gradient(circle, rgba(52,211,153,0.18) 0%, transparent 70%);
            bottom:-50px; right:-60px; animation: orb2 11s ease-in-out infinite;
        }
        .login-orb-3 {
            position:absolute; width:180px; height:180px; border-radius:50%; pointer-events:none;
            background: radial-gradient(circle, rgba(244,193,93,0.14) 0%, transparent 70%);
            top:40%; right:-40px; animation: orb1 13s ease-in-out infinite reverse;
        }

        /* ── Welcome panel feature cards ── */
        .feat-card {
            display:flex; align-items:flex-start; gap:11px;
            padding:11px 13px; border-radius:14px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(14px);
            transition: border-color 0.25s, background 0.25s, transform 0.22s;
        }
        .feat-card:hover {
            border-color: rgba(36,184,255,0.28);
            background: rgba(255,255,255,0.09);
            transform: translateY(-1px);
        }
        .feat-icon {
            flex-shrink:0; width:34px; height:34px; border-radius:10px;
            display:flex; align-items:center; justify-content:center;
            font-size:14px;
        }
        /* ── Welcome stat row ── */
        .stat-pill {
            display:flex; flex-direction:column; align-items:center;
            padding:9px 14px; border-radius:12px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.06);
            flex:1;
        }

        .brand-shimmer {
            background: linear-gradient(120deg,
                rgba(36,184,255,0.9) 0%,
                rgba(29,214,255,1)   30%,
                rgba(255,255,255,0.95) 50%,
                rgba(29,214,255,1)   70%,
                rgba(36,184,255,0.9) 100%);
            background-size:200% auto;
            -webkit-background-clip:text; background-clip:text;
            -webkit-text-fill-color:transparent;
            animation: shimmer 5s linear infinite;
        }

        /* ── Input icon — never overlaps text ── */
        .login-input-wrap { position:relative; }
        .login-input-icon {
            position:absolute; left:13px; top:50%; transform:translateY(-50%);
            width:18px; text-align:center;
            color: rgba(168,189,226,0.55); font-size:13px; pointer-events:none;
            transition: color 0.2s;
        }
        .login-input-wrap:focus-within .login-input-icon { color: rgba(36,184,255,0.85); }
        /* Force input padding so icon never touches placeholder */
        .login-input-wrap input {
            padding-left: 2.6rem !important;
        }
        .login-input-wrap input.has-toggle {
            padding-right: 2.8rem !important;
        }

        .login-card-accent {
            position:absolute; top:0; left:0; right:0; height:3px; border-radius:16px 16px 0 0;
            background: linear-gradient(90deg, #1390ff 0%, #1aafff 35%, #34d399 68%, #f4c15d 100%);
            opacity:.85;
        }

        .btn-signin {
            position:relative; overflow:hidden;
            transition: transform 0.22s ease, filter 0.22s ease;
        }
        .btn-signin::after {
            content:''; position:absolute; inset:0;
            background: linear-gradient(135deg,rgba(255,255,255,0.14),transparent 50%);
        }
        .btn-signin:hover  { transform:translateY(-2px); filter:brightness(1.1); }
        .btn-signin:active { transform:translateY(0);    filter:brightness(0.96); }

        .remember-check {
            appearance:none; width:16px; height:16px; border-radius:4px; cursor:pointer;
            border: 1.5px solid rgba(168,204,255,0.28);
            background: rgba(8,22,49,0.6);
            position:relative; flex-shrink:0;
            transition: border-color 0.2s, background 0.2s;
        }
        .remember-check:checked {
            border-color: rgba(36,184,255,0.7);
            background: linear-gradient(135deg,rgba(19,144,255,0.8),rgba(29,214,255,0.7));
        }
        .remember-check:checked::after {
            content:'✓'; position:absolute; top:50%; left:50%;
            transform:translate(-50%,-52%);
            font-size:10px; color:#fff; font-weight:700;
        }
        .toggle-pwd {
            position:absolute; right:0; top:0; height:100%;
            display:flex; align-items:center; padding:0 13px;
            background:none; border:none; cursor:pointer;
            color:rgba(168,189,226,0.5); font-size:13px;
            transition: color 0.2s;
        }
        .toggle-pwd:hover { color:rgba(36,184,255,0.85); }

        .login-theme-picker .theme-picker-trigger {
            min-width: 9.6rem;
            min-height: 2.65rem;
            padding: 0.44rem 0.68rem;
        }

        .login-theme-picker .theme-picker-trigger-icon {
            width: 1.85rem;
            height: 1.85rem;
        }

        .login-theme-picker .theme-picker-kicker {
            font-size: 0.57rem;
            letter-spacing: 0.12em;
        }

        .login-theme-picker .theme-picker-label {
            font-size: 0.79rem;
        }

        .login-theme-floating {
            position: fixed;
            top: 0.85rem;
            right: 0.95rem;
            z-index: 120;
            animation: cardRiseIn 0.82s cubic-bezier(.22,.61,.36,1) 2.18s both;
        }

        @media (prefers-reduced-motion: reduce) {
            .login-bg-wordmark-track,
            .login-panel,
            .login-card,
            .login-form,
            .login-theme-floating {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
                filter: none !important;
            }
        }

        .login-bg-wordmark {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(2.2rem, 6vh, 4.8rem) clamp(0.6rem, 1.6vw, 1.6rem) clamp(1rem, 2.4vh, 2.1rem);
        }

        .login-bg-wordmark-track {
            position: relative;
            transform: translateY(2%) rotate(-4.8deg);
            display: grid;
            gap: clamp(0.32rem, 0.8vh, 0.62rem);
            padding-top: clamp(0.7rem, 1.8vh, 1.25rem);
            opacity: 0.34;
            filter: saturate(118%);
            animation:
                wordmarkReveal 2s cubic-bezier(.22,.61,.36,1) both,
                wordmarkSettleBlur 1.1s ease-out 3.3s forwards;
        }

        .login-bg-wordmark-line {
            font-family: "Cinzel", "Trajan Pro", "Cormorant Garamond", "Times New Roman", serif;
            font-size: clamp(2rem, 6.2vw, 5rem);
            line-height: 1.14;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            white-space: nowrap;
            background: linear-gradient(135deg, rgba(198, 224, 255, 0.32), rgba(133, 177, 238, 0.14) 45%, rgba(228, 240, 255, 0.3));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(196, 221, 255, 0.24);
            text-shadow:
                0 0 52px rgba(36, 184, 255, 0.16),
                0 14px 36px rgba(3, 10, 28, 0.26),
                0 1px 0 rgba(255, 255, 255, 0.12);
            font-variation-settings: "wght" 700;
        }

        .login-bg-wordmark-line-accent {
            background: linear-gradient(135deg, rgba(255, 233, 178, 0.38), rgba(247, 201, 104, 0.31) 34%, rgba(174, 231, 207, 0.29) 66%, rgba(214, 239, 255, 0.24));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(242, 248, 255, 0.28);
            letter-spacing: 0.13em;
            filter: saturate(112%) brightness(1.05);
        }

        .login-bg-wordmark-oasis {
            display: inline-block;
            padding: 0 0.08em;
            margin: 0 0.04em;
            background: linear-gradient(135deg, rgba(255, 248, 225, 0.95) 0%, rgba(255, 212, 120, 0.96) 44%, rgba(160, 233, 206, 0.92) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(252, 243, 225, 0.55);
            text-shadow:
                0 0 26px rgba(255, 212, 120, 0.38),
                0 0 12px rgba(160, 233, 206, 0.26);
            filter: saturate(124%) brightness(1.14);
        }

        .login-bg-wordmark-love {
            display: inline-block;
            padding: 0 0.08em;
            margin-left: 0.04em;
            background: linear-gradient(135deg, rgba(255, 242, 214, 0.98) 0%, rgba(255, 192, 137, 0.97) 44%, rgba(247, 150, 129, 0.94) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(255, 224, 195, 0.64);
            text-shadow:
                0 0 28px rgba(255, 176, 122, 0.42),
                0 0 12px rgba(247, 150, 129, 0.34);
            filter: saturate(128%) brightness(1.16);
        }

        .login-panel,
        .login-card {
            position: relative;
            z-index: 2;
        }

        .login-theme-floating .theme-picker-menu {
            right: 0;
            left: auto;
            min-width: 14rem;
        }

        @media (max-width: 900px) {
            .login-bg-wordmark {
                padding: 2.55rem 0.45rem 0.9rem;
            }

            .login-bg-wordmark-track {
                transform: translateY(3%) rotate(-3.2deg);
            }

            .login-bg-wordmark-line {
                font-size: clamp(1.52rem, 7vw, 2.85rem);
                line-height: 1.2;
                letter-spacing: 0.125em;
            }

            .login-theme-floating {
                top: 0.55rem;
                right: 0.55rem;
            }

            .login-theme-floating .theme-picker-trigger {
                min-width: 8.4rem;
                padding: 0.4rem 0.56rem;
            }

            .login-theme-floating .theme-picker-menu {
                min-width: 12rem;
            }
        }

        @media (max-height: 780px) {
            .login-bg-wordmark {
                padding-top: 2.8rem;
            }

            .login-bg-wordmark-line {
                font-size: clamp(1.45rem, 5.1vw, 3.6rem);
            }
        }

        /* ── Theme-specific login color overrides ── */
        [data-theme='solarized'] .login-panel {
            border-color: rgba(196, 146, 86, 0.32) !important;
            background: linear-gradient(148deg, rgba(37, 23, 10, 0.99) 0%, rgba(68, 41, 18, 0.98) 40%, rgba(49, 31, 14, 0.99) 72%, rgba(61, 38, 16, 1) 100%) !important;
            box-shadow: 0 30px 80px rgba(28, 17, 8, 0.58), inset 0 1px 0 rgba(255, 245, 224, 0.06) !important;
        }

        [data-theme='solarized'] .login-bg-wordmark-line {
            background: linear-gradient(135deg, rgba(246, 203, 138, 0.34), rgba(240, 160, 0, 0.16) 44%, rgba(255, 240, 214, 0.3));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(240, 160, 0, 0.26);
            text-shadow:
                0 0 34px rgba(240, 160, 0, 0.12),
                0 8px 24px rgba(42, 31, 15, 0.28);
        }

        [data-theme='solarized'] .login-bg-wordmark-line-accent {
            background: linear-gradient(135deg, rgba(255, 240, 206, 0.38), rgba(237, 191, 84, 0.29) 40%, rgba(255, 248, 230, 0.29));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(241, 198, 104, 0.31);
        }

        [data-theme='solarized'] .login-bg-wordmark-oasis {
            background: linear-gradient(135deg, rgba(255, 249, 232, 0.98) 0%, rgba(255, 208, 112, 0.98) 52%, rgba(245, 176, 66, 0.95) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(255, 219, 145, 0.62);
            text-shadow: 0 0 28px rgba(244, 179, 77, 0.44);
        }

        [data-theme='solarized'] .login-bg-wordmark-love {
            background: linear-gradient(135deg, rgba(255, 246, 225, 0.98) 0%, rgba(248, 190, 96, 0.98) 50%, rgba(231, 139, 67, 0.95) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(255, 208, 138, 0.66);
            text-shadow: 0 0 28px rgba(236, 164, 72, 0.48);
        }

        [data-theme='forest'] .login-panel {
            border-color: rgba(102, 122, 92, 0.34) !important;
            background: linear-gradient(148deg, rgba(17, 24, 18, 0.99) 0%, rgba(29, 40, 31, 0.98) 40%, rgba(23, 32, 25, 0.99) 72%, rgba(30, 40, 31, 1) 100%) !important;
            box-shadow: 0 30px 80px rgba(11, 16, 13, 0.62), inset 0 1px 0 rgba(236, 253, 245, 0.05) !important;
        }

        [data-theme='forest'] .login-bg-wordmark-line {
            background: linear-gradient(135deg, rgba(184, 219, 197, 0.3), rgba(95, 124, 94, 0.17) 44%, rgba(215, 225, 212, 0.24));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(102, 122, 92, 0.28);
            text-shadow:
                0 0 30px rgba(134, 167, 124, 0.12),
                0 8px 24px rgba(15, 24, 20, 0.3);
        }

        [data-theme='forest'] .login-bg-wordmark-line-accent {
            background: linear-gradient(135deg, rgba(223, 234, 218, 0.31), rgba(148, 182, 136, 0.24) 44%, rgba(206, 228, 213, 0.26));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(149, 172, 140, 0.3);
        }

        [data-theme='forest'] .login-bg-wordmark-oasis {
            background: linear-gradient(135deg, rgba(240, 251, 244, 0.96) 0%, rgba(168, 228, 191, 0.95) 48%, rgba(129, 199, 159, 0.92) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(208, 236, 220, 0.58);
            text-shadow: 0 0 24px rgba(129, 199, 159, 0.34);
        }

        [data-theme='forest'] .login-bg-wordmark-love {
            background: linear-gradient(135deg, rgba(241, 251, 244, 0.97) 0%, rgba(177, 228, 176, 0.95) 48%, rgba(128, 190, 144, 0.92) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            -webkit-text-stroke: 1px rgba(203, 233, 205, 0.62);
            text-shadow: 0 0 24px rgba(128, 190, 144, 0.36);
        }

        [data-theme='solarized'] .login-orb-1 {
            background: radial-gradient(circle, rgba(240, 160, 0, 0.24) 0%, transparent 70%);
        }

        [data-theme='solarized'] .login-orb-2 {
            background: radial-gradient(circle, rgba(232, 184, 77, 0.2) 0%, transparent 70%);
        }

        [data-theme='solarized'] .login-orb-3 {
            background: radial-gradient(circle, rgba(196, 146, 86, 0.18) 0%, transparent 70%);
        }

        [data-theme='forest'] .login-orb-1 {
            background: radial-gradient(circle, rgba(110, 231, 183, 0.16) 0%, transparent 70%);
        }

        [data-theme='forest'] .login-orb-2 {
            background: radial-gradient(circle, rgba(88, 104, 69, 0.2) 0%, transparent 70%);
        }

        [data-theme='forest'] .login-orb-3 {
            background: radial-gradient(circle, rgba(148, 163, 184, 0.14) 0%, transparent 70%);
        }

        [data-theme='solarized'] .brand-shimmer {
            background: linear-gradient(120deg, rgba(240, 160, 0, 0.92) 0%, rgba(232, 184, 77, 0.98) 30%, rgba(255, 245, 224, 0.95) 50%, rgba(232, 184, 77, 0.98) 70%, rgba(240, 160, 0, 0.92) 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        [data-theme='forest'] .brand-shimmer {
            background: linear-gradient(120deg, rgba(167, 243, 208, 0.9) 0%, rgba(134, 239, 172, 0.96) 30%, rgba(236, 253, 245, 0.95) 50%, rgba(134, 239, 172, 0.96) 70%, rgba(167, 243, 208, 0.9) 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        [data-theme='solarized'] .login-panel .text-cyan-300,
        [data-theme='solarized'] .login-panel .text-cyan-400\/80 {
            color: #f6cb8a !important;
        }

        [data-theme='forest'] .login-panel .text-cyan-300,
        [data-theme='forest'] .login-panel .text-cyan-400\/80 {
            color: #b8dbc5 !important;
        }

        [data-theme='solarized'] .feat-card:hover {
            border-color: rgba(240, 160, 0, 0.3);
            background: rgba(255, 241, 219, 0.1);
        }

        [data-theme='solarized'] .login-top-accent {
            background: linear-gradient(90deg, #d97706, #f0a000 35%, #e8b84d 68%, #f6cb8a) !important;
        }

        [data-theme='solarized'] .login-feat-icon {
            background: linear-gradient(135deg, rgba(240, 160, 0, 0.22), rgba(153, 68, 0, 0.12)) !important;
            color: rgba(245, 199, 130, 0.96) !important;
        }

        [data-theme='forest'] .feat-card:hover {
            border-color: rgba(102, 122, 92, 0.34);
            background: rgba(217, 225, 207, 0.08);
        }

        [data-theme='forest'] .login-top-accent {
            background: linear-gradient(90deg, #4c6b4f, #5f7c5e 35%, #86a77c 68%, #b8dbc5) !important;
        }

        [data-theme='forest'] .login-panel .text-emerald-300 {
            color: #c6ddcf !important;
        }

        [data-theme='forest'] .login-feat-icon {
            background: linear-gradient(135deg, rgba(88, 104, 69, 0.28), rgba(39, 52, 42, 0.18)) !important;
            color: rgba(198, 221, 207, 0.96) !important;
        }

        [data-theme='solarized'] .login-card-accent {
            background: linear-gradient(90deg, #d97706 0%, #f0a000 35%, #e8b84d 68%, #f6cb8a 100%);
        }

        [data-theme='forest'] .login-card-accent {
            background: linear-gradient(90deg, #4c6b4f 0%, #5f7c5e 35%, #86a77c 68%, #b8dbc5 100%);
        }

        [data-theme='solarized'] .login-input-wrap:focus-within .login-input-icon,
        [data-theme='solarized'] .toggle-pwd:hover {
            color: rgba(240, 160, 0, 0.9);
        }

        [data-theme='forest'] .login-input-wrap:focus-within .login-input-icon,
        [data-theme='forest'] .toggle-pwd:hover {
            color: rgba(134, 239, 172, 0.88);
        }

        [data-theme='solarized'] .remember-check:checked {
            border-color: rgba(240, 160, 0, 0.75);
            background: linear-gradient(135deg, rgba(217, 119, 6, 0.84), rgba(240, 160, 0, 0.74));
        }

        [data-theme='forest'] .remember-check:checked {
            border-color: rgba(134, 239, 172, 0.74);
            background: linear-gradient(135deg, rgba(76, 107, 79, 0.84), rgba(95, 124, 94, 0.74));
        }
    </style>

    {{-- Two-column layout: welcome panel + login card, visible from md (768px) up --}}
    <div class="relative w-full h-full" style="display:grid; grid-template-columns:1fr 390px; gap:1.25rem; align-items:stretch;">

        <div class="login-bg-wordmark" aria-hidden="true">
            <div class="login-bg-wordmark-track">
                <div class="login-bg-wordmark-line">Bethel City Church</div>
                <div class="login-bg-wordmark-line login-bg-wordmark-line-accent">The <span class="login-bg-wordmark-oasis">Oasis</span> of <span class="login-bg-wordmark-love">Love</span></div>
            </div>
        </div>

        <div class="login-theme-floating">
            <div class="login-theme-picker flex justify-end">
                <div class="theme-picker">
                    <button type="button" class="theme-picker-trigger" data-theme-picker-trigger
                            aria-label="Theme Select" aria-expanded="false" aria-haspopup="menu" title="Theme Select">
                        <span class="theme-picker-trigger-icon" aria-hidden="true">
                            <svg class="theme-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3c-4.97 0-9 4.03-9 9a9 9 0 0 0 9 9c1.3 0 2.34-1.04 2.34-2.34 0-.58-.22-1.13-.61-1.55a2.2 2.2 0 0 1-.59-1.5c0-1.25 1.02-2.27 2.27-2.27h2.13A3.46 3.46 0 0 0 21 9.87C21 6.08 16.97 3 12 3Z"/>
                                <circle cx="7.75" cy="10" r="1.15" fill="currentColor" stroke="none"/>
                                <circle cx="11.2" cy="7.15" r="1.05" fill="currentColor" stroke="none"/>
                                <circle cx="15.7" cy="8.7" r="1.05" fill="currentColor" stroke="none"/>
                            </svg>
                        </span>
                        <span class="theme-picker-trigger-copy">
                            <span class="theme-picker-kicker">Theme Select</span>
                            <span class="theme-picker-label" data-theme-label>Dark</span>
                        </span>
                    </button>

                    <div class="theme-picker-menu" data-hidden="true" role="menu">
                        <div class="theme-option" data-theme="light" role="menuitem">
                            <div class="theme-option-title">Light</div>
                            <div class="theme-option-preview">Clean & bright</div>
                        </div>
                        <div class="theme-option" data-theme="dark" role="menuitem">
                            <div class="theme-option-title">Dark</div>
                            <div class="theme-option-preview">Classic dark</div>
                        </div>
                        <div class="theme-option" data-theme="solarized" role="menuitem">
                            <div class="theme-option-title">Solarized</div>
                            <div class="theme-option-preview">Warm amber & cream</div>
                        </div>
                        <div class="theme-option" data-theme="forest" role="menuitem">
                            <div class="theme-option-title">Forest</div>
                            <div class="theme-option-preview">Green & natural</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Left welcome panel — visible md+ beside the login card ── --}}
        <div class="login-panel flex flex-col relative overflow-hidden rounded-[1.75rem] text-white"
             style="
                border: 1px solid rgba(95,140,230,0.22);
                background: linear-gradient(148deg, rgba(5,18,58,0.99) 0%, rgba(7,38,110,0.98) 40%, rgba(5,22,68,0.99) 72%, rgba(6,28,80,1) 100%);
                box-shadow: 0 30px 80px rgba(2,6,20,0.65), inset 0 1px 0 rgba(255,255,255,0.06);
             ">
            <div class="login-orb-1"></div>
            <div class="login-orb-2"></div>
            <div class="login-orb-3"></div>

            {{-- Decorative top accent line --}}
            <div class="login-top-accent" style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#1390ff,#1aafff 35%,#34d399 68%,#f4c15d);opacity:0.7;"></div>

            <div class="relative z-10 flex h-full flex-col p-7">

                {{-- Church branding row --}}
                <div class="mb-5 flex items-center gap-3">
                    <div class="rounded-xl bg-white/10 p-2 backdrop-blur-sm">
                        <img src="{{ asset('images/bcc-logo.png') }}" alt="BCC" class="h-8 w-8 rounded-lg object-contain block">
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-cyan-300">Bethel Community Church</p>
                        <p class="text-[11px] text-white/50">Management System</p>
                    </div>
                    <div class="ml-auto inline-flex items-center gap-1.5 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-2.5 py-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400" style="animation:pulse 2s infinite;"></span>
                        <span class="text-[10px] font-semibold text-emerald-300">Live</span>
                    </div>
                </div>

                {{-- Welcome headline --}}
                <div class="mb-1">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-cyan-400/80">Welcome to BCC CMS</p>
                    <h1 class="mt-1.5 text-[1.75rem] font-extrabold leading-[1.18]" style="letter-spacing:-0.025em;">
                        Church operations,<br>
                        <span class="brand-shimmer">all in one place.</span>
                    </h1>
                    <p class="mt-2.5 text-[0.82rem] leading-[1.65] text-white/58">
                        A secure, unified platform built to streamline how your church
                        manages people, money, ministry, and communication.
                    </p>
                </div>

                {{-- Divider --}}
                <div class="my-4" style="height:1px;background:linear-gradient(90deg,transparent,rgba(168,204,255,0.15),transparent);"></div>

                {{-- Feature cards grid --}}
                <div class="grid grid-cols-2 gap-2">
                    <div class="feat-card">
                        <span class="feat-icon login-feat-icon" style="background:linear-gradient(135deg,rgba(36,184,255,0.22),rgba(29,214,255,0.12)); color:rgba(36,184,255,0.95);">
                            <i class="fas fa-users"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Membership</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Profiles, zones &amp; families</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon login-feat-icon" style="background:linear-gradient(135deg,rgba(52,211,153,0.22),rgba(16,185,129,0.12)); color:rgba(52,211,153,0.95);">
                            <i class="fas fa-wallet"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Finance</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Tithe, budgets &amp; reports</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon login-feat-icon" style="background:linear-gradient(135deg,rgba(244,193,93,0.22),rgba(217,155,43,0.12)); color:rgba(244,193,93,0.95);">
                            <i class="fas fa-church"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Ministry</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Departments &amp; events</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon login-feat-icon" style="background:linear-gradient(135deg,rgba(255,111,145,0.22),rgba(225,77,109,0.12)); color:rgba(255,111,145,0.95);">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Security</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Role-based access control</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon login-feat-icon" style="background:linear-gradient(135deg,rgba(167,139,250,0.22),rgba(139,92,246,0.12)); color:rgba(167,139,250,0.95);">
                            <i class="fas fa-bell"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Communication</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Alerts &amp; announcements</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon login-feat-icon" style="background:linear-gradient(135deg,rgba(34,211,238,0.22),rgba(6,182,212,0.12)); color:rgba(34,211,238,0.95);">
                            <i class="fas fa-chart-bar"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Reports</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Audit-ready insights</p>
                        </div>
                    </div>
                </div>

                {{-- Bottom quote --}}
                <p class="mt-auto pt-4 text-[10.5px] italic text-white/25">
                    &ldquo;Built for the church. Trusted by leaders.&rdquo;
                </p>
            </div>
        </div>

        {{-- ── Right form card ── --}}
        <div class="login-card surface-card relative mx-auto w-full max-w-[400px] self-center rounded-2xl p-5 sm:p-6 flex flex-col"
             style="overflow-y:auto; max-height:calc(100vh - 2rem);">
            <div class="login-card-accent"></div>

            {{-- Logo & heading --}}
            <div class="mb-3 flex flex-col items-center text-center">
                <div class="mb-2 rounded-md bg-white p-[3px] shadow-[0_2px_12px_rgba(0,0,0,0.18)]">
                    <img src="{{ asset('images/bcc-logo.png') }}" alt="BCC Logo"
                         class="h-7 w-7 rounded object-contain block">
                </div>
                <span class="mb-0.5 text-[10px] font-bold uppercase tracking-[0.26em] text-[var(--color-brand-500)]">BCC CMS</span>
                <h2 class="text-[1.2rem] font-extrabold leading-tight text-[var(--color-ink-950)]"
                    style="letter-spacing:-0.02em;">Welcome back</h2>
                <p class="mt-1 max-w-xs text-[0.76rem] leading-[1.5]" style="color:var(--text-muted);">
                    Sign in to access the church management workspace.
                </p>
            </div>

            <x-ui.flash-message />

            {{-- Form --}}
            <form method="POST" action="{{ route('login.store') }}" class="login-form space-y-2.5">
                @csrf

                {{-- Username --}}
                <div class="flex flex-col gap-1">
                    <label for="username" class="form-label !mb-0 text-[11px] font-semibold uppercase tracking-[0.08em]">
                        Username
                    </label>
                    <div class="login-input-wrap">
                        <span class="login-input-icon"><i class="fas fa-user"></i></span>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username') }}"
                            class="form-input font-medium"
                            style="height:38px; font-size:0.85rem;"
                            placeholder="Enter your username"
                            autocomplete="username"
                            required
                        >
                    </div>
                    @error('username')
                        <p class="text-[11px]" style="color:var(--color-danger-700);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="flex flex-col gap-1">
                    <label for="password" class="form-label !mb-0 text-[11px] font-semibold uppercase tracking-[0.08em]">
                        Password
                    </label>
                    <div class="login-input-wrap">
                        <span class="login-input-icon"><i class="fas fa-lock"></i></span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-input has-toggle font-medium"
                            style="height:38px; font-size:0.85rem;"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-pwd" tabindex="-1" onclick="togglePassword()">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px]" style="color:var(--color-danger-700);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <label class="flex cursor-pointer items-center gap-2">
                    <input type="checkbox" name="remember" value="1" class="remember-check">
                    <span class="text-[0.78rem] font-medium" style="color:var(--text-muted);">
                        Keep me signed in on this device
                    </span>
                </label>

                {{-- Submit --}}
                <button type="submit"
                        class="btn-primary btn-signin w-full flex items-center justify-center gap-2.5 font-bold"
                        style="height:40px; font-size:0.85rem; border-radius:12px;">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    Sign in to your account
                </button>
            </form>

            {{-- Footer --}}
            <p class="mt-3 text-center text-[10px]" style="color:rgba(168,189,226,0.28); letter-spacing:.04em;">
                BCC Management System &nbsp;&middot;&nbsp; Secure &amp; Encrypted
            </p>
        </div>

    </div>

    <script>
        function togglePassword() {
            const pwd  = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            const isHidden = pwd.type === 'password';
            pwd.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye',      !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
        }
    </script>
</x-layouts.auth>
