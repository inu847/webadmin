{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header'>
        Edit Menu Role
    </div>
    <div class='card-body'>
        <form action='{{ route('menu-roles.update', $edit->id) }}' method='POST' enctype='multipart/form-data'>
            @csrf
            @method("PUT")
            <div class='form-group'>
                <label for='role_id'>Role <small>*required</small></label>
                <select class='form-control' name='role_id' id='role_id' required>
                    <option value="">-Chose Options-</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ ($edit->role_id == $role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class='form-group'>
                <label for='status'>Status <small>*required</small></label>
                <div class="input-group">
                    <input type='radio' name='status' class='mr-1' id='status_y' value="active" {{ ($edit->status == 'active') ? 'checked' : '' }} required>
                    <label for="status_y" class='mr-3'>Active</label>
                    <input type='radio' name='status' class='mr-1' id='status_n' value="inactive" {{ ($edit->status == 'inactive') ? 'checked' : '' }} required>
                    <label for="status_n" class='mr-3'>Inactive</label>
                </div>
            </div>
            <div class='form-group d-none'>
                <label for='user_id'>User</label>
                <select class='form-control' name='user_id' id='user_id'>
                    <option value="">-Chose Options-</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ ($edit->user_id == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class='form-group'>
                <label for='menu_id'>Menu <small>*required</small></label>
                <select class='form-control' name='menu_id' id='menu_id' required>
                    <option value="">-Chose Options-</option>
                    @foreach ($menus as $menu)
                        <option value="{{ $menu->id }}" {{ ($edit->menu_id == $menu->id) ? 'selected' : '' }}>{{ $menu->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>