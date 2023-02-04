{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <!-- <div class='card-header'>
        Update Payment Method Group
    </div> -->
    <div class='card-body'>
        <form action='{{ route('metode-pembayaran.update', [$edit->id]) }}' method='POST' enctype='multipart/form-data'>
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="">name</label>
                <input type="text" class="form-control" name="name" value="{{ $edit->name }}" required>
            </div>
            <div class="form-group">
                <label for="">minimal_bisa_cicilan</label>
                <input type="number" class="form-control" name="minimal_bisa_cicilan" value="{{ $edit->minimal_bisa_cicilan }}" required>
            </div>
            <div class="form-group">
                <label for="">maksimal_bisa_cicilan</label>
                <input type="number" class="form-control" name="maksimal_bisa_cicilan" value="{{ $edit->maksimal_bisa_cicilan }}" required>
            </div>
            <div class="form-group">
                <label for="">image *change image</label>
                <input type="file" class="form-control" name="image">
            </div>
            <div class="form-group">
                <label for="">keterangan</label>
                <textarea name="keterangan" id="" cols="30" rows="5" class="form-control" required>{{ $edit->keterangan }}</textarea>
            </div>
            <div class="form-group">
                <label for="">urutan</label>
                <input type="number" class="form-control" name="urutan" value="{{ $edit->urutan }}" required>
            </div>
            <div class="form-group">
                <label for="active">active</label>
                <select name="active" id="active" class="form-control">
                    <option value="" selected disabled>-Select Options-</option>
                    <option value="1" {{ ($edit->active == 1) ? 'selected' : '' }}>active</option>
                    <option value="0" {{ ($edit->active == 0) ? 'selected' : '' }}>inactive</option>
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>