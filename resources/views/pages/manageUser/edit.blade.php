{{-- @extends('layouts.main')

@section('content')
    @if (session('success'))
        <div class="alert alert-success mb-5">
            {{ session('success') }}
        </div>
    @endif
    @if (session('failed'))
        <div class="alert alert-success mb-5">
            {{ session('failed') }}
        </div>
    @endif

    @endsection --}}
    <div class='card'>
        <div class='card-body'>
            @csrf
            @method("PUT")
            <input type="hidden" id="id" name="id" class="form-control" value="{{ $edit->id }}"/>
            <div class='form-group'>
                <label for='email'>Email</label>
                <input type='email' class='form-control' value="{{ $edit->email }}" name='email' id='email' disabled>
            </div>
            <div class='form-group'>
                <label for='username'>Username</label>
                <input type='text' class='form-control' value="{{ $edit->username }}" name='username' id='username' autocomplete="off" required>
            </div>
            <div class='form-group'>
                <label for='name'>Name</label>
                <input type='text' class='form-control' value="{{ $edit->name }}" name='name' id='name' required>
            </div>
            <div class='form-group'>
                <label for='password'>Password <small>*only for change</small></label>
                <input type='password' class='form-control' name='password' id='password'>
            </div>
            <div class='form-group'>
                <label for='repassword'>Confirmation Password <small>*only for change</small></label>
                <input type='password' class='form-control' name='repassword' id='repassword'>
            </div>
            @if($affiliate)
                <input type='hidden' name='as_affiliate' value='1'>
            @endif
            @if(!$affiliate)
            <div class='form-group'>
                <label for='password_edit'>Password Edit Access</label>
                <input type='text' class='form-control' value="{{ $edit->password_edit }}" name='password_edit' id='password_edit'>
            </div>
            <div class='form-group'>
                <label for='password_edit'>Password Edit Timeout (in second)</label>
                <input type='number' class='form-control' value="{{ $edit->password_edit_timeout }}" name='password_edit_timeout' id='password_edit_timeout'>
                <small>eg. 1 minute = 60 seconds.</small>
            </div>
            @endif
            <div class='form-group'>
                <label for='photos'>Photo <small>*only for change</small></label>
                <input type='file' class='form-control' value="{{ $edit->photos }}" name='photos' id='photos'>
            </div>
            <div class='form-group'>
                <label for='phone'>Phone</label>
                <input type='text' class='form-control' value="{{ $edit->phone }}" name='phone' id='phone' required>
            </div>
            @if(!$affiliate)
            <div class='form-group'>
                <label for='number_catalog'>Max. Number of Catalog</label>
                <input type='number' class='form-control' value="{{ $edit->number_catalog }}" name='number_catalog' id='number_catalog' required>
            </div>
            @endif
            <div class='form-group'>
                <label for='address'>Address</label>
                <textarea name="address" id="address" rows="2" class='form-control'>{{ $edit->address }}</textarea>
            </div>
            @if(!$affiliate)
            <div class='form-group'>
                <label>Level</label>
                <select class='form-control' name='level' id='level' required>
                    <option value="User" {{ ($edit->level == 'User') ? 'selected' : '' }}>User</option>
                    <option value="Member" {{ ($edit->level == 'Member') ? 'selected' : '' }}>Member</option>
                    <option value="Super Admin" {{ ($edit->level == 'Super Admin') ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
            @endif
            <div class='form-group'>
                <label>Status</label>
                <select class='form-control' name='active' id='active' required>
                    <option value="Y" {{ ($edit->active == 'Y') ? 'selected' : '' }}>Active</option>
                    <option value="N" {{ ($edit->active == 'N') ? 'selected' : '' }}>Not Active</option>
                </select>
            </div>
            @if(!$affiliate)
            <div class='form-group'>
                <label>Owner</label>
                <select class='form-control' name='owner' id='owner' required>
                    <option value="1" {{ ($edit->owner == '1') ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ ($edit->owner == '0') ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class='form-group'>
                <label>Affiliate From</label>
                <select class='form-control select2' name='affiliate_id' id='affiliate_id'>
                    <option value="" selected>No Affiliate Data</option>
                    @foreach($users as $value)
                        <option value="{{ $value->id }}" {{ ($edit->affiliate_id == $value->id) ? 'selected' : '' }}>{{ $value->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class='form-group'>
                <label for='affiliate_percent'>Percent Affiliate</label>
                <input type='number' class='form-control' value="{{ $edit->affiliate_percent }}" name='affiliate_percent' id='affiliate_percent'>
            </div>
            @endif
        </div>
    </div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>