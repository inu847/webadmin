{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <!-- <div class='card-header'>
        Update Foodcourt Catalog
    </div> -->
    <div class='card-body'>
        <form action='{{ route('update.FoodcourtCatalog', [$edit->id]) }}' method='POST' enctype='multipart/form-data'>
            @csrf
            <div class="form-group">
                <label for="">Catalog</label>
                <select name="catalog_id" id="" class="form-control">
                    <option value="" selected disabled>-Select Catalog-</option>
                    @foreach ($catalogs as $catalog)
                        <option value="{{ $catalog->id }}" {{ ($catalog->id == $edit->catalog_id) ? 'selected':'' }}>{{ $catalog->catalog_title }}</option>
                    @endforeach
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Submit</button>
        </form>
    </div>
</div>