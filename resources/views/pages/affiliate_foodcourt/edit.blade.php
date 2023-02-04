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
            <input type="hidden" id="id" name="id" class="form-control" value="{{ $edit->id }}"/>
            <div class='form-group'>
                <label>Foodcourt</label>
                <select class='form-control select2' name='foodcourt_catalog_id' id='foodcourt_catalog_id'>
                    <option selected>No Foodcourt Data</option>
                    @foreach($foodcourts as $value)
                        <option value="{{ $value->id }}" {{ ($value->id == $edit->foodcourt_catalog_id) ? 'selected':'' }}>{{ $value->foodcourt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class='form-group row'>
                <label>Percent Affiliate</label>
                <input type='number' class='form-control' name='affiliate_percent' value="{{ $edit->affiliate_percent }}" id='affiliate_percent'>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js2/jquery.nice-select.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>