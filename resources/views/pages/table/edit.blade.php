@extends('layouts.main')

@section('content')
    <div class='card'>
        <div class='card-header'>
            Edit Table
        </div>
        <div class='card-body'>
            <form action='{{ route('table.update', $edit->id) }}' method='POST' enctype='multipart/form-data'>
                @csrf
                @method("PUT")
                <div class='form-group'>
                    <label for='table'>Table Name</label>
                    <input type='text' class='form-control' name='table' id='table' value="{{ $edit->table }}" required>
                </div>
                <div class='form-group'>
                    <label for='status'>Status</label>
                    <select class='form-control' name='status' id='status' required>
                        <option value="0" {{ ($edit->status == '0') ? 'selected' : '' }}>Available</option>
                        <option value="1" {{ ($edit->status == '1') ? 'selected' : '' }}>Ordered</option>
                    </select>
                </div>
                <div class='form-group'>
                    <label for='catalog_id'>Catalog</label>
                    <select class='form-control' name='catalog_id' id='catalog_id' required>
                        @foreach ($catalogs as $catalog)
                            <option value="{{ $catalog->id }}" {{ ($edit->catalog_id == $catalog->id) ? 'selected' : '' }}>{{ $catalog->catalog_title }}</option>
                        @endforeach
                    </select>
                </div>
                <button type='submit' class='btn btn-primary'>Submit</button>
            </form>
        </div>
    </div>
@endsection