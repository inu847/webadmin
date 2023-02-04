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
            <div class='form-group'>
                <label>Foodcourt</label>
                <select class='form-control select2' name='foodcourt_catalog_id' id='foodcourt_catalog_id'>
                    <option value="" selected>No Foodcourt Data</option>
                    @foreach($foodcourts as $value)
                        <option value="{{ $value->id }}">{{ $value->foodcourt->name ?? null }}</option>
                    @endforeach
                </select>
            </div>
            <div class='form-group'>
                <label for='affiliate_percent'>Percent Affiliate</label>
                <input type='number' class='form-control' value="" name='affiliate_percent' id='affiliate_percent'>
            </div>
        </div>
    </div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>