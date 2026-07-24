<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/bcc-logo.png') }}?v=20260724">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/bcc-logo.png') }}?v=20260724">
    <title>Self Check-In — {{ $service->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <style>
        body { background: #f0f4ff; }
    </style>
</head>
<body class="h-full flex flex-col items-center justify-center px-4 py-10">

    <main class="w-full max-w-sm">
        {{-- Church branding header --}}
        <div class="mb-8 text-center">
            <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-indigo-600 text-white shadow-lg mb-3">
                <i class="fa-solid fa-church text-3xl"></i>
            </span>
            <h1 class="text-xl font-bold text-gray-900">Self Check-In</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $service->name }}</p>
            <p class="text-xs text-gray-400">{{ $service->service_date?->format('l, d M Y') }}</p>
            @if ($service->start_time)
                <p class="text-xs text-gray-400">{{ $service->start_time }}{{ $service->end_time ? ' – '.$service->end_time : '' }}</p>
            @endif
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-xl bg-green-100 border border-green-300 px-4 py-3 text-sm text-green-800 text-center font-medium">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-5 rounded-xl bg-red-100 border border-red-300 px-4 py-3 text-sm text-red-700 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
            <p class="text-sm text-gray-600 mb-5 text-center">
                Select your name to mark yourself as present.
            </p>

            <form method="POST"
                  action="{{ URL::signedRoute('attendance.checkin.store', ['service' => $service->id]) }}">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                <div id="memberList" class="max-h-72 overflow-y-auto space-y-2 mb-5">
                    {{-- Members are loaded via the member search below or pre-injected --}}
                    <input type="text" id="memberSearch"
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700 mb-3 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                           placeholder="Search your name…">
                    <select name="member_id" id="memberSelect"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            size="8" required>
                        @foreach (\App\Models\Member::query()->orderBy('full_name')->get(['id','full_name']) as $m)
                            <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-white font-semibold text-sm shadow hover:bg-indigo-700 active:scale-95 transition-all">
                    <i class="fa-solid fa-hand mr-2"></i>Check In
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-gray-400">
            BCC Management Platform &middot; Attendance Check-In
        </p>
    </main>

    <script>
        const search = document.getElementById('memberSearch');
        const select = document.getElementById('memberSelect');
        const allOptions = Array.from(select.options).map(o => ({ value: o.value, text: o.text }));

        search.addEventListener('input', () => {
            const q = search.value.toLowerCase();
            select.innerHTML = '';
            allOptions
                .filter(o => o.text.toLowerCase().includes(q))
                .forEach(o => {
                    const opt = document.createElement('option');
                    opt.value = o.value;
                    opt.textContent = o.text;
                    select.appendChild(opt);
                });
        });
    </script>
</body>
</html>
