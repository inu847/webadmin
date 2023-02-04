{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header'>
        New Metode Pembayaran Group
    </div>
    <div class='card-body'>
        <form action='{{ route('metode-pembayaran.store') }}' method='POST' enctype='multipart/form-data'>
            @csrf
            <div class="form-group">
                <label for="">name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <label for="">minimal_bisa_cicilan</label>
                <input type="number" class="form-control" name="minimal_bisa_cicilan" required>
            </div>
            <div class="form-group">
                <label for="">maksimal_bisa_cicilan</label>
                <input type="number" class="form-control" name="maksimal_bisa_cicilan" required>
            </div>
            <div class="form-group">
                <label for="">image</label>
                <input type="file" class="form-control" name="image" required>
            </div>
            <div class="form-group">
                <label for="">keterangan</label>
                <textarea name="keterangan" id="" cols="30" rows="5" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="">urutan</label>
                <input type="number" class="form-control" name="urutan" required>
            </div>
            <div class="form-group">
                <label for="active">active</label>
                <select name="active" id="active" class="form-control">
                    <option value="" selected disabled>-Select Options-</option>
                    <option value="1">active</option>
                    <option value="0">inactive</option>
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>