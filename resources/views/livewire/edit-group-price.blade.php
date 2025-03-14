<form action="{{ route('groupprice.update', $group->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="groups" class="form-label">Groups</label>
        <input type="text" name="groups" class="form-control" value="{{ old('groups', $group->groups) }}">
        @error('groups')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="1" {{ $group->status ? 'selected' : '' }}>Active</option>
            <option value="0" {{ !$group->status ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <div class="col-md-3">
            <div class="form-floating form-floating-outline mb-4">
                <select class="form-select" id="level" name="level" aria-label="Default select example">
                    <option value="">select level</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->levelname }}</option>
                    @endforeach
                </select>
                <label for="level">level</label>
                <div id="level-error" class="text-danger small"></div>
            </div>
        </div>
        {{-- <label for="level" class="form-label">Level</label>
        <input type="number" name="level" class="form-control" value="{{ old('level', $group->level) }}"> --}}
        @error('level')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
</form>
