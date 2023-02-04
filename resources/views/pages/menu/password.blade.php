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
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers..</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ route('menu_password.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
        </div>
    </div>
</div>

<div id="indexVue">
    <div class="tabs-animation">
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card" style="min-height: 250px;">
                    @include('blocks.skeleton') 
                    
                    <form action='{{ route('menu_password.store') }}' method='POST' enctype='multipart/form-data'>
                        @csrf
                        <div class='card'>
                            <div class='card-header d-none'>
                                Manage Menu
                            </div>
                            <div class='card-body'>
                                <label class="text-primary"><b>LIST MENU</b></label>
                                <div class="position-relative form-group">
                                    <div class="row">
                                        @foreach($menus as $key => $value)
                                        <div class="col-md-4">
                                            <div class="mt-4">
                                                <b>{{ $value->name }}</b>
                                            </div>
                                            @if($value->menu)
                                            <div class="row ml-2">
                                                @foreach($value->menu as $mvalue)
                                                    <div class="col-md-12">
                                                        <div class="custom-checkbox custom-control mt-2">
                                                            <input type="checkbox" id="menu{{ $mvalue->id }}" name="menu[]" class="custom-control-input" value="{{ $mvalue->id }}"
                                                            {{ in_array($mvalue->url, $password_menus) ? 'checked' : '' }}
                                                            />
                                                            <label class="custom-control-label" for="menu{{ $mvalue->id }}">{{ $mvalue->name }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class='card-footer'>
                                <button type='submit' class='btn btn-primary'>Save</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
