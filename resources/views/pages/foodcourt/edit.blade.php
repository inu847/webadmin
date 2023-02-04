{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header'>
        Edit Foodcourt
    </div>
    <div class='card-body'>
        <form action='{{ route('foodcourt.update', [$edit->id]) }}' method='POST' enctype='multipart/form-data'>
            @csrf
            @method("PUT")
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" name="name" value="{{ $edit->name }}" class="form-control">
            </div>
            <div class="form-group">
                <label for="">Owner</label>
                <input required type="text" name="owner" value="{{ $edit->owner }}" class="form-control">
            </div>
            <div class="form-group">
                <label for="">Address</label>
                <input required type="text" name="address" value="{{ $edit->address }}" class="form-control">
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>