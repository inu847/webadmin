{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header'>
        New Foodcourt
    </div>
    <div class='card-body'>
        <form action='{{ route('request-catalog.store') }}' method='POST' enctype='multipart/form-data'>
            @csrf
            <div class="form-group">
                <label for="foodcourt_id">Foodcourt</label>
                <select name="foodcourt_id" id="foodcourt_id" class="form-control">
                    <option value="" selected disabled>Chose Foodcourt</option>
                    @foreach ($foodcourts as $foodcourt)
                        <option value="{{ $foodcourt->id }}">{{ $foodcourt->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="catalog_id">Catalog</label>
                <select name="catalog_id" id="catalog_id" class="form-control">
                    <option value="" selected disabled>Chose Catalog</option>
                    @foreach ($catalogs as $catalog)
                        <option value="{{ $catalog->id }}">{{ $catalog->catalog_username }}</option>
                    @endforeach
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Request</button>
        </form>
    </div>
</div>