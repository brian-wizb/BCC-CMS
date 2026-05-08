@if (session('status'))
    <div class="flash flash-success mb-6">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="flash flash-error mb-6">
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
