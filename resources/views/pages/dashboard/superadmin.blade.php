@extends('layouts.main')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-screen icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            
        </div>
    </div>
</div>

<div class="tabs-animation">
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="main-card mb-3 card">
                <div class="grid-menu grid-menu-2col">
                    <div class="no-gutters row">
                        <div class="col-sm-6">
                            <a href="{{ url('/transaction/checkout') }}" style="color: #495057; text-decoration: none;">
                                <div class="widget-chart widget-chart-hover">
                                    <div class="icon-wrapper rounded-circle">
                                        <div class="icon-wrapper-bg bg-secondary"></div>
                                        <i class="lnr-users"></i>
                                    </div>
                                    <div class="widget-numbers">{{ count($countowners) }}</div>
                                    <div class="widget-subheading">Owner(s</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ url('/transaction/approve') }}" style="color: #495057; text-decoration: none;">
                                <div class="widget-chart widget-chart-hover">
                                    <div class="icon-wrapper rounded-circle">
                                        <div class="icon-wrapper-bg bg-primary"></div>
                                        <i class="pe-7s-display2"></i>
                                    </div>
                                    <div class="widget-numbers">{{ count($countcatalog) }}</div>
                                    <div class="widget-subheading">Catalog(s)</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ url('/transaction/process') }}" style="color: #495057; text-decoration: none;">
                                <div class="widget-chart widget-chart-hover">
                                    <div class="icon-wrapper rounded-circle">
                                        <div class="icon-wrapper-bg bg-info"></div>
                                        <i class="lnr-layers"></i>
                                    </div>
                                    <div class="widget-numbers">{{ count($countitems) }}</div>
                                    <div class="widget-subheading">Product(s)</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ url('/transaction/delivered') }}" style="color: #495057; text-decoration: none;">
                                <div class="widget-chart widget-chart-hover br-br">
                                    <div class="icon-wrapper rounded-circle">
                                        <div class="icon-wrapper-bg bg-warning"></div>
                                        <i class="pe-7s-note2"></i>
                                    </div>
                                    <div class="widget-numbers">{{ count($counttransaction) }}</div>
                                    <div class="widget-subheading">Transaction(s)</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">New Registration</div>
                </div>
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Register Date</th>
                                <th>Customer Name</th>
                                <th>Package Name</th>
                                <th>Duration</th>
                                <th>Expired</th>
                                <th>Status</th>
                                <th class="text-right">Package Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registration as $vregistration)
                                <tr>
                                    <td>{{ Date::fullDate($vregistration['created_at']) }}</td>
                                    <td>{{ $vregistration['name'] }}</td>
                                    <td>{{ $vregistration['package_name'] }}</td>
                                    <td>{{ $vregistration['duration'] }}</td>
                                    <td>{{ Date::myDate($vregistration['expired']) }}</td>
                                    <td>{{ $vregistration['status'] }}</td>
                                    <td class="text-right">{{ number_format($vregistration['price']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">Latest Catalog</div>
                </div>
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Owner</th>
                                <th>Created Date</th>
                                <th>Catalog Name</th>
                                <th>URL</th>
                                <th>Catalog Email</th>
                                <th>Catalog Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($catalog as $value)
                                <tr>
                                    <td>{{ $value['name'] }}</td>
                                    <td>{{ Date::fullDate($value['created_at']) }}</td>
                                    <td>{{ $value['catalog_title'] }}</td>
                                    <td>
                                        <a href="https://{{ $value['catalog_username'].'.'.$value['domain'] }}" target="_blank">{{ $value['catalog_username'].'.'.$value['domain'] }}</a>
                                    </td>
                                    <td>{{ $value['email_contact'] }}</td>
                                    <td>{{ $value['phone_contact'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('customjs')
<script type="text/javascript">
    
</script>
@endsection