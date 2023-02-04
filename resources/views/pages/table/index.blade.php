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
                    <div class="d-flex flex-wrap justify-content-between">
                        <div>
                            <a href="{{ route('table.create') }}" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> 
                                Create New 
                            </a>
                        </div>
                        <div class="col-12 col-md-3 p-0 mb-3">
                            <input id="searchfield" type="text" class="form-control" placeholder="Search..." />
                        </div>
                    </div>
                    <div class="main-card mb-3 card" style="min-height: 250px;">
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Catalog</th>
                                        <th>Qr Code</th>
                                        <th>Meja</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tables as $key => $table)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ optional($table->catalog)->catalog_title }}</td>
                                            @php
                                                $url = url('https://'.$table->catalog->catalog_username.'.'.$table->catalog->domain.'/table/'.$table->table);
                                                $qrcode = DNS2D::getBarcodePNGPath($url, 'QRCODE', 5,5)
                                            @endphp
                                            <td>
                                                <a href="{{ $qrcode }}" download target="_blank">
                                                    <img src="{{ $qrcode }}" alt="">
                                                    {{-- Download --}}
                                                </a>
                                            </td>
                                            <td>{{ $table->table }}</td>
                                            <td>
                                                @if ($table->status == 0)
                                                    <span class="badge badge-success">Available</span>
                                                @elseif ($table->status == 1)
                                                    <span class="badge badge-danger">Ordered</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('table.show', $table->id) }}" class="btn btn-info"><i class="fa fa-list"></i></a>
                                                <a href="{{ route('table.edit', [$table->id]) }}" class="btn btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                                <form
                                                    onsubmit="return confirm('Are you sure?')"
                                                    class="d-inline"
                                                    action="{{route('table.destroy', [$table->id])}}"
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