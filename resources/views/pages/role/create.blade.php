{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header d-none'>
        New Role
    </div>
    <div class='card-body'>
        <form action='{{ route('role.store') }}' method='POST' enctype='multipart/form-data'>
            @csrf
            <div class='form-group'>
                <label for='table'>Role Name</label>
                <input type='text' class='form-control' name='name' id='name' placeholder=''>
            </div>
            <div class="form-group d-none">
                <label for='table'>Owner</label>
                <select name="owner_id" id="owner_id" class="form-control">
                    <option value="" selected disabled>-Select Option-</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>