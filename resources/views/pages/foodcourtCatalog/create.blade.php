{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <!-- <div class='card-header'>
        New Foodcourt Catalog
    </div> -->
    <form action='{{ route('store.FoodcourtCatalog', [$id]) }}' method='POST' enctype='multipart/form-data'>
        <div class='card-body'>
            @csrf
            <div class="form-group">
                <label for="">Catalog Key</label>
                <div class="input-group">
                    <input required type="text" name="catalog_key" id="catalog_key" class="form-control">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-check_catalog_key" onClick="check_catalog_key()"><i class="fa fa-search"></i> Check</button>
                    </div>
                </div>
            </div>

            <span class="info_catalog text-success"></span>

            <input type="hidden" name="catalog_id" id="input-catalog_id">

            <!-- <div class="form-group">
                <label for="">Catalog</label>
                <select name="catalog_id" id="" class="form-control">
                    <option value="" selected disabled>-Select Catalog-</option>
                    @foreach ($catalogs as $catalog)
                        <option value="{{ $catalog->id }}">{{ $catalog->catalog_username }}</option>
                    @endforeach
                </select>
            </div> -->
        </div>
        <div class="card-footer">
            <button type='submit' class='btn btn-primary btn-submit_catalog' disabled>Submit</button>
        </div>
    </form>
</div>