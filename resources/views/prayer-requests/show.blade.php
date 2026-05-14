<x-layouts.app title="Prayer Request Details">
    <section class="surface-card p-6 max-w-3xl">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">{{ $requestItem->request_type }}</h3>
            <x-ui.status-badge :status="$requestItem->status" />
        </div>
        <p class="mt-3 text-sm text-slate-600">{{ $requestItem->request_text }}</p>

        <form method="POST" action="{{ route('prayer-requests.update', $requestItem) }}" class="mt-6 grid gap-3 md:grid-cols-2">
            @csrf
            @method('PUT')
            <input name="request_type" class="form-input" value="{{ $requestItem->request_type }}" required>
            <select name="status" class="form-input" required>
                @foreach(['open','in_progress','answered','closed'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected($requestItem->status === $statusOption)>{{ str_replace('_',' ',ucfirst($statusOption)) }}</option>
                @endforeach
            </select>
            <select name="visibility" class="form-input" required>
                @foreach(['private','public'] as $visibility)
                    <option value="{{ $visibility }}" @selected($requestItem->visibility === $visibility)>{{ ucfirst($visibility) }}</option>
                @endforeach
            </select>
            <select name="assigned_to" class="form-input">
                <option value="">Unassigned</option>
                @foreach($leaders as $leader)
                    <option value="{{ $leader->id }}" @selected((string)$requestItem->assigned_to === (string)$leader->id)>{{ $leader->full_name }}{{ $leader->role ? ' - '.$leader->role : '' }}</option>
                @endforeach
            </select>
            <textarea name="request_text" class="form-input md:col-span-2" rows="4" required>{{ $requestItem->request_text }}</textarea>
            <button class="btn-secondary md:col-span-2" type="submit">Update request</button>
        </form>

        <form method="POST" action="{{ route('prayer-requests.destroy', $requestItem) }}" class="mt-4" data-confirm="Delete this prayer request?">
            @csrf
            @method('DELETE')
            <button class="btn-secondary text-red-600" type="submit">Delete request</button>
        </form>
    </section>
</x-layouts.app>
