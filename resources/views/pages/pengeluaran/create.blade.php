@extends('layouts.main')

@section('content')
    <div class='card'>
        <div class='card-header'>
            Buat Pengeluaran Baru
        </div>
        <div class='card-body'>
            <form action="{{ route('pengeluaran.store') }}" method='POST' enctype='multipart/form-data'>
                @csrf
                <div class='form-group'>
                    <label for='catalog_id'>Catalog</label>
                    <select class='form-control' name='catalog_id' id='catalog_id' required>
                        @foreach ($catalogs as $catalog)
                            <option value="{{ $catalog->id }}" {{ \Session::get('catalogsession') == $catalog->id ? 'selected="selected"' : '' }}>{{ $catalog->catalog_title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class='form-group'>
                    <label for='judul'>Judul</label>
                    <input type='text' class='form-control' name='judul' id='judul' placeholder='' required>
                </div>
                <div class='form-group'>
                    <label for='keterangan'>Keterangan</label>
                    <textarea name="keterangan" id="keterangan" class='form-control'></textarea>
                </div>

                <div class='form-group'>
                    <label for='datetime'>Tanggal</label>
                    <div class="input-group mb-3">
                        <div class="datepicker date input-group p-0">
                            <input type="text" id="datetime" name="datetime" class="form-control" readonly value="{{ date('Y-m-d') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button"><i class="ion-android-calendar" style="font-size: 1rem"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type='submit' class='btn btn-primary'>Submit</button>
            </form>
        </div>
    </div>
@endsection

@section('customjs')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.datepicker').datepicker({
                clearBtn: true,
                useCurrent:true,
                autoclose:true,
                endDate:'0d',
                format: "yyyy-mm-dd"
            });
        });
    </script>
@endsection