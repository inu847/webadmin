@extends('layouts.main')

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
    
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-display2 icon-gradient bg-ripe-malin"> </i>
                </div>
                <div>
                    {{ $maintitle }}
                    <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('catalog.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
            </div>
        </div>
    </div>
    
    <div id="indexVue">
        <div class="tabs-animation">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between mb-4">
                        <div class="">
                            <a href="{{ route('pengeluaran.create') }}" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> 
                                Create New 
                            </a>
                        </div>
                        <form class="form-inline" method="GET">
                            <div class="position-relative form-group">
                                <label for="searchfield" class="sr-only">Keyword</label>
                                <input name="searchfield" id="searchfield" placeholder="Type Keyword..." type="text" class="mr-2 form-control" value="{{ $searchfield ?? '' }}">
                            </div>
                            <div class="position-relative form-group">
                                <label for="searchMonth" class="sr-only">Month</label>
                                <select id="searchMonth" name="searchMonth" class="mr-2 form-control">
                                    <option value="all" {{ $searchMonth == 'all' ? 'selected' : '' }}>All Months</option>
                                    <option value="1" {{ $searchMonth == 1 ? 'selected' : '' }}>January</option>
                                    <option value="2" {{ $searchMonth == 2 ? 'selected' : '' }}>February</option>
                                    <option value="3" {{ $searchMonth == 3 ? 'selected' : '' }}>March</option>
                                    <option value="4" {{ $searchMonth == 4 ? 'selected' : '' }}>April</option>
                                    <option value="5" {{ $searchMonth == 5 ? 'selected' : '' }}>May</option>
                                    <option value="6" {{ $searchMonth == 6 ? 'selected' : '' }}>June</option>
                                    <option value="7" {{ $searchMonth == 7 ? 'selected' : '' }}>July</option>
                                    <option value="8" {{ $searchMonth == 8 ? 'selected' : '' }}>August</option>
                                    <option value="9" {{ $searchMonth == 9 ? 'selected' : '' }}>September</option>
                                    <option value="10" {{ $searchMonth == 10 ? 'selected' : '' }}>October</option>
                                    <option value="11" {{ $searchMonth == 11 ? 'selected' : '' }}>November</option>
                                    <option value="12" {{ $searchMonth == 12 ? 'selected' : '' }}>December</option>
                                </select>
                            </div>
                            <div class="position-relative form-group">
                                <label for="searchYear" class="sr-only">Year</label>
                                <select id="searchYear" name="searchYear" class="mr-2 form-control">
                                    <option value="">All Year</option>
                                    <option value="2021" {{ $searchYear == 2021 ? 'selected' : '' }}>2021</option>
                                    <option value="2022" {{ $searchYear == 2022 ? 'selected' : '' }}>2022</option>
                                    <option value="2023" {{ $searchYear == 2023 ? 'selected' : '' }}>2023</option>
                                </select>
                            </div>
                            <button type="submit" id="searchButton" class="btn btn-primary">Search</button>
                        </form>
                        <!-- <input id="searchfield" type="text" class="form-control" placeholder="Search..." /> -->
                    </div>
                    <div class="main-card mb-3 card" style="min-height: 250px;">
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Catalog</th>
                                        <th>Judul</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengeluarans as $key => $value)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ optional($value->catalog)->catalog_title }}</td>
                                            <td>{{ $value->judul }}</td>
                                            <td>{{ $value->datetime ? \Carbon\Carbon::parse($value->datetime)->format('d/m/Y') : '' }}</td>
                                            <td>{{ $value->keterangan }}</td>
                                            <td>
                                                <a href="{{ route('pengeluaran.show', $value->id) }}" class="btn btn-info"><i class="fa fa-list"></i></a>
                                                <a href="{{ route('pengeluaran.edit', [$value->id]) }}" class="btn btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                                <form
                                                    onsubmit="return confirm('Are you sure?')"
                                                    class="d-inline"
                                                    action="{{route('pengeluaran.destroy', [$value->id])}}"
                                                    method="POST">
                                                        @csrf
                                                        <input
                                                        type="hidden"
                                                        name="_method"
                                                        value="DELETE">
                                                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection