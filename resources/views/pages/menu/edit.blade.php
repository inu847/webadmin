{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <!-- <div class='card-header'>
        Edit Menu
    </div> -->
    <div class='card-body'>
        <form action='{{ route('menu.update', $edit->id) }}' method='POST' enctype='multipart/form-data'>
            @csrf
            @method("PUT")
            <div class='form-group'>
                <label for='menu'>Menu Category Name</label>
                <input type='text' class='form-control' name='name' id='name' value="{{ $edit->name }}" placeholder=''>
            </div>
            <!-- <div class='form-group'>
                <label for='table'>Order View</label>
                <input type='text' class='form-control' name='order_view' id='order_view' value="{{ $edit->order_view }}" placeholder=''>
            </div> -->
            <div class="form-group">
                <label for='table'>Status</label>
                <select name="active" id="active" class="form-control">
                    <option value="0">Not Active</option>
                    <option value="1" {{ (1 == $edit->active) ? 'selected':'' }}>Active</option>
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>