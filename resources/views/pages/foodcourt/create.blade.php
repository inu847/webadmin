{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header'>
        New Foodcourt
    </div>
    <div class='card-body'>
        <form action='{{ route('foodcourt.store') }}' method='POST' enctype='multipart/form-data'>
            @csrf
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" name="name" class="form-control">
            </div>
            <div class="form-group">
                <label for="">Owner</label>
                <input required type="text" name="owner" class="form-control">
            </div>
            <div class="form-group">
                <label for="">Address</label>
                <input required type="text" name="address" class="form-control">
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>