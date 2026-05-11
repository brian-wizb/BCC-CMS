@csrf

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="name" class="form-label">Department name</label>
        <input id="name" name="name" class="form-input mt-2" value="{{ old('name', $department->name) }}" required>
        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="leader_id" class="form-label">Department leader</label>
        <select id="leader_id" name="leader_id" class="form-input mt-2">
            <option value="">Select leader</option>
            @foreach ($leaders as $leader)
                <option value="{{ $leader->id }}" @selected((string) old('leader_id', $department->leader_id) === (string) $leader->id)>
                    {{ $leader->full_name }}{{ $leader->role ? ' - '.$leader->role : '' }}
                </option>
            @endforeach
        </select>
        @error('leader_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="4" class="form-input mt-2">{{ old('description', $department->description) }}</textarea>
        @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-input mt-2" required>
            <option value="active" @selected(old('status', $department->status ?: 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $department->status) === 'inactive')>Inactive</option>
        </select>
        @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
 </div>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('departments.index') }}" class="btn-secondary">Cancel</a>
</div>
