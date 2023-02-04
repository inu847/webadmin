{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header'>
        Edit Foodcourt
    </div>
    <div class='card-body'>
        <form action='{{ route('catalog-approval.update', [$edit->id]) }}' method='POST' enctype='multipart/form-data'>
            @csrf
            @method("PUT")
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="" selected disabled>Chose Option</option>
                    <option value="1">Approve</option>
                    <option value="3">Tolak Pengajuan</option>
                </select>
            </div>
            
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>