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
        <div class="page-title-actions d-none">
            <button type="button" data-toggle="tooltip" title="Example Tooltip" data-placement="bottom" class="btn-shadow mr-3 btn btn-dark">
                <i class="fa fa-star"></i>
            </button>
        </div>
    </div>
</div>

<div class="tabs-animation">
    <div class="row">
        <div class="col-lg-12 col-xl-6">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">Income Report</h5>
                    <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 m-0">
                        <div style="height: 227px;">
                            <canvas id="line-chart"></canvas>
                        </div>
                    </div>
                    <h5 class="card-title">Target Sales</h5>
                    <div class="mt-3 row">
                        <div class="col-sm-12 col-md-4">
                            <div class="widget-content p-0">
                                <div class="widget-content-outer">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left">
                                            <div class="widget-numbers text-dark">65%</div>
                                        </div>
                                    </div>
                                    <div class="widget-progress-wrapper mt-1">
                                        <div class="progress-bar-xs progress-bar-animated-alt progress">
                                            <div class="progress-bar bg-info" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100" style="width: 65%;"></div>
                                        </div>
                                        <div class="progress-sub-label">
                                            <div class="sub-label-left font-size-md">Sales</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="widget-content p-0">
                                <div class="widget-content-outer">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left">
                                            <div class="widget-numbers text-dark">22%</div>
                                        </div>
                                    </div>
                                    <div class="widget-progress-wrapper mt-1">
                                        <div class="progress-bar-xs progress-bar-animated-alt progress">
                                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="22" aria-valuemin="0" aria-valuemax="100" style="width: 22%;"></div>
                                        </div>
                                        <div class="progress-sub-label">
                                            <div class="sub-label-left font-size-md">Profiles</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="widget-content p-0">
                                <div class="widget-content-outer">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left">
                                            <div class="widget-numbers text-dark">83%</div>
                                        </div>
                                    </div>
                                    <div class="widget-progress-wrapper mt-1">
                                        <div class="progress-bar-xs progress-bar-animated-alt progress">
                                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="83" aria-valuemin="0" aria-valuemax="100" style="width: 83%;"></div>
                                        </div>
                                        <div class="progress-sub-label">
                                            <div class="sub-label-left font-size-md">Tickets</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-6">
            <div class="main-card mb-3 card">
                <div class="grid-menu grid-menu-2col">
                    <div class="no-gutters row">
                        <div class="col-sm-6">
                            <div class="widget-chart widget-chart-hover">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-primary"></div>
                                    <i class="lnr-cog text-primary"></i>
                                </div>
                                <div class="widget-numbers">45.8k</div>
                                <div class="widget-subheading">Total Views</div>
                                <div class="widget-description text-success">
                                    <i class="fa fa-angle-up"></i>
                                    <span class="pl-1">175.5%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="widget-chart widget-chart-hover">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-info"></div>
                                    <i class="lnr-graduation-hat text-info"></i>
                                </div>
                                <div class="widget-numbers">63.2k</div>
                                <div class="widget-subheading">Bugs Fixed</div>
                                <div class="widget-description text-info">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="pl-1">175.5%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="widget-chart widget-chart-hover">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-danger"></div>
                                    <i class="lnr-laptop-phone text-danger"></i>
                                </div>
                                <div class="widget-numbers">5.82k</div>
                                <div class="widget-subheading">Reports Submitted</div>
                                <div class="widget-description text-primary">
                                    <span class="pr-1">54.1%</span>
                                    <i class="fa fa-angle-up"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="widget-chart widget-chart-hover br-br">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-success"></div>
                                    <i class="lnr-screen"></i>
                                </div>
                                <div class="widget-numbers">17.2k</div>
                                <div class="widget-subheading">Profiles</div>
                                <div class="widget-description text-warning">
                                    <span class="pr-1">175.5%</span>
                                    <i class="fa fa-arrow-left"></i>
                                </div>
                            </div>
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
                    Latest Order
                </div>
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">Order ID</th>
                                <th class="text-center">Date</th>
                                <th>Catalog</th>
                                <th class="text-center">Order Via</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Total</th>
                                <th>Note</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestactivity as $latestactivity)
                            <tr>
                                <td class="text-center text-muted">{{ $latestactivity['invoice_number'] }}</td>
                                <td class="text-center">{{ Date::fullDate($latestactivity['created_at']) }}</td>
                                <td>
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="widget-content-left">
                                                    <img width="40" class="rounded" src="{{ strpos($latestactivity['catalog_logo'], 'amazonaws.com') !== false ? $latestactivity['catalog_logo'] : str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$latestactivity['catalog_logo'].'?'.time() }}" alt="" />
                                                </div>
                                            </div>
                                            <div class="widget-content-left flex2">
                                                <div class="widget-heading">{{ $latestactivity['catalog_title'] }}</div>
                                                <div class="widget-subheading opacity-7">
                                                    <a href="https://{{ $latestactivity['catalog_username'].'.'.$latestactivity['domain'] }}" target="_blank" style="color: #888;text-decoration: none;">https://{{ $latestactivity['catalog_username'].'.'.$latestactivity['domain'] }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $latestactivity['via'] }}</td>
                                <td class="text-center">
                                    <div class="badge badge-{{ myFunction::colorStatus($latestactivity['status']) }}" style="width: 100px">{{ $latestactivity['status'] }}</div>
                                </td>
                                <td class="text-right">
                                    {{ number_format(getData::getTotalInvoice($latestactivity['id'])) }}
                                </td>
                                <td>
                                    {{ $latestactivity['note_order'] }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('/transaction/detail/'.$latestactivity['invoice_number']) }}" class="btn btn-primary btn-sm">
                                        Detail
                                    </a>
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
@endsection