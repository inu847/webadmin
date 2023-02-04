@extends('layouts.main')

@section('content')
    <div class='card'>
        <div class='card-header'>
            New Table Data
        </div>
        <div class='card-body'>
            <form action='{{ route('table.store') }}' method='POST' enctype='multipart/form-data'>
                @csrf
                <div class='form-group'>
                    <label for='table'>Table Name</label>
                    <input type='text' class='form-control' name='table' id='table' placeholder='' required>
                </div>
                <div class='form-group'>
                    <label for='status'>Status</label>
                    <select class='form-control' name='status' id='status' required>
                        <option value="0">Available</option>
                        <option value="1">Ordered</option>
                    </select>
                </div>
                <div class='form-group'>
                    <label for='catalog_id'>Catalog</label>
                    <select class='form-control' name='catalog_id' id='catalog_id' required>
                        @foreach ($catalogs as $catalog)
                            <option value="{{ $catalog->id }}">{{ $catalog->catalog_title }}</option>
                        @endforeach
                    </select>
                </div>
                <button type='submit' class='btn btn-primary'>Submit</button>
            </form>
        </div>
    </div>
@endsection