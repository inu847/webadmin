{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header'>
        Edit Foodcourt
    </div>
    <div class='card-body'>
        <form action='{{ route('foodcourt-approval.update', [$edit->id]) }}' method='POST' enctype='multipart/form-data'>
            @csrf
            @method("PUT")
            <div class="form-group">
                <label for="foodcourt_id">Foodcourt</label>
                <select name="foodcourt_id" id="foodcourt_id" class="form-control">
                    <option value="" selected disabled>Chose Foodcourt</option>
                    @foreach ($foodcourts as $foodcourt)
                        <option value="{{ $foodcourt->id }}">{{ $foodcourt->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>