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
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
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

        .login-panel { animation: loginFadeIn  0.7s ease-out both; height: 100%; }
        .login-card  { animation: loginFadeUp  0.65s cubic-bezier(.22,.61,.36,1) both; }
        .login-form  { animation: loginFadeUp  0.75s cubic-bezier(.22,.61,.36,1) 0.1s both; }

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
    </style>

    {{-- Two-column layout: welcome panel + login card, visible from md (768px) up --}}
    <div class="w-full h-full" style="display:grid; grid-template-columns:1fr 390px; gap:1.25rem; align-items:stretch;">

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
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#1390ff,#1aafff 35%,#34d399 68%,#f4c15d);opacity:0.7;"></div>

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
                        <span class="feat-icon" style="background:linear-gradient(135deg,rgba(36,184,255,0.22),rgba(29,214,255,0.12)); color:rgba(36,184,255,0.95);">
                            <i class="fas fa-users"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Membership</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Profiles, zones &amp; families</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon" style="background:linear-gradient(135deg,rgba(52,211,153,0.22),rgba(16,185,129,0.12)); color:rgba(52,211,153,0.95);">
                            <i class="fas fa-wallet"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Finance</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Tithe, budgets &amp; reports</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon" style="background:linear-gradient(135deg,rgba(244,193,93,0.22),rgba(217,155,43,0.12)); color:rgba(244,193,93,0.95);">
                            <i class="fas fa-church"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Ministry</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Departments &amp; events</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon" style="background:linear-gradient(135deg,rgba(255,111,145,0.22),rgba(225,77,109,0.12)); color:rgba(255,111,145,0.95);">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Security</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Role-based access control</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon" style="background:linear-gradient(135deg,rgba(167,139,250,0.22),rgba(139,92,246,0.12)); color:rgba(167,139,250,0.95);">
                            <i class="fas fa-bell"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-bold text-white/90">Communication</p>
                            <p class="text-[10px] text-white/45 leading-snug mt-0.5">Alerts &amp; announcements</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon" style="background:linear-gradient(135deg,rgba(34,211,238,0.22),rgba(6,182,212,0.12)); color:rgba(34,211,238,0.95);">
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
