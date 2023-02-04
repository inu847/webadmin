{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <!-- <div class='card-header'>
        New Name
    </div> -->
    <div class='card-body'>
        <form action='{{ route('menu.store') }}' method='POST' enctype='multipart/form-data'>
            @csrf
            <div class='form-group'>
                <label for='table'>Menu Category Name</label>
                <input type='text' class='form-control' name='name' id='name' placeholder=''>
            </div>
            <!-- <div class='form-group'>
                <label for='table'>Order View</label>
                <input type='text' class='form-control' name='order_view' id='order_view' placeholder=''>
            </div> -->
            <div class="form-group">
                <label for='table'>Status</label>
                <select name="active" id="active" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Not Active</option>
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>