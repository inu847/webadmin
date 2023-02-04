{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header'>
        New Metode Pembayaran
    </div>
    <div class='card-body'>
        <form action='{{ route('update.MetodePembayaran', [$edit->id]) }}' method='POST' enctype='multipart/form-data'>
            @csrf
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" name="name" value="{{ $edit->name }}" required>
            </div>
            <div class="form-group">
                <label for="">Orderview</label>
                <input type="number" class="form-control" name="order_view" value="{{ $edit->order_view }}" required>
            </div>
            <div class="form-group">
                <label for="">Logo</label>
                <input type="file" class="form-control" name="logo" value="{{ $edit->logo }}">
            </div>
            <div class="form-group">
                <label for="">Code</label>
                <input type="text" class="form-control" name="code" value="{{ $edit->code }}" required>
            </div>
            <div class="form-group">
                <label for="active">active</label>
                <select name="active" id="active" class="form-control">
                    <option value="" selected disabled>-Select Options-</option>
                    <option value="1" {{ ($edit->active == 1) ? 'selected':'' }}>active</option>
                    <option value="0" {{ ($edit->active == 0) ? 'selected':'' }}>inactive</option>
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>