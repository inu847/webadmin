{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-body'>
        <form action='{{ route('store.Menu', [$id]) }}' method='POST' enctype='multipart/form-data'>
            @csrf
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <label for="">Url</label>
                <input type="text" class="form-control" name="url" required>
            </div>
            <div class="form-group">
                <label for="">Order View</label>
                <input type="number" class="form-control" name="order_view" required>
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