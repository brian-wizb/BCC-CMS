@php
    $flashes = [];
    if (session('success'))  $flashes[] = ['type' => 'success', 'msg' => session('success')];
    if (session('status'))   $flashes[] = ['type' => 'success', 'msg' => session('status')];
    if (session('info'))     $flashes[] = ['type' => 'info',    'msg' => session('info')];
    if (session('warning'))  $flashes[] = ['type' => 'warning', 'msg' => session('warning')];
    if (session('error'))    $flashes[] = ['type' => 'error',   'msg' => session('error')];
    foreach ($errors->all() as $err) {
        $flashes[] = ['type' => 'error', 'msg' => $err];
    }
@endphp

@if (count($flashes) > 0)
<div id="bcc-toast-data" aria-hidden="true" class="hidden">
    @foreach($flashes as $flash)
        <span data-toast="{{ $flash['type'] }}" data-toast-msg="{{ e($flash['msg']) }}"></span>
    @endforeach
</div>
@endif
