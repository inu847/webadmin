<div class="row">
    <div class="col-md-12">
        <div class="mb-3 card">
            <div class="card-header-tab card-header">
                <div class="card-header-title font-size-lg text-capitalize font-weight-normal">Monthly Order Conversion Rate - {{ now()->format('F Y') }}</div>
                <!-- <div class="btn-actions-pane-right text-capitalize">
                    <button class="btn btn-warning">Actions</button>
                </div> -->
            </div>
            <div class="pt-0 card-body">
                <div id="chart"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3 card">
            <div class="card-header-tab card-header">
                <div class="card-header-title font-size-lg text-capitalize font-weight-normal">Daily Unique Customer Conversion Rate - {{ now()->format('F Y') }}</div>
                <!-- <div class="btn-actions-pane-right text-capitalize">
                    <button class="btn btn-warning">Actions</button>
                </div> -->
            </div>
            <div class="pt-0 card-body">
                <div id="chart_user"></div>
            </div>
        </div>
    </div>
</div>

<div class="row d-none">
    <div class="col-lg-12 col-xl-12">
        <div class="main-card mb-3 card">
            <div class="grid-menu grid-menu-3col">
                <div class="no-gutters row">
                    <div class="col-md-4">
                        <a href="{{ url('/transaction/checkout') }}" style="color: #495057; text-decoration: none;">
                            <div class="widget-chart widget-chart-hover">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-secondary"></div>
                                    <i class="pe-7s-next-2"></i>
                                </div>
                                <div class="widget-numbers">{{ count($checkout) }}</div>
                                <div class="widget-subheading">Checkout</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/transaction/approve') }}" style="color: #495057; text-decoration: none;">
                            <div class="widget-chart widget-chart-hover">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-primary"></div>
                                    <i class="lnr-select"></i>
                                </div>
                                <div class="widget-numbers">{{ count($approve) }}</div>
                                <div class="widget-subheading">Approve</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/transaction/process') }}" style="color: #495057; text-decoration: none;">
                            <div class="widget-chart widget-chart-hover">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-info"></div>
                                    <i class="lnr-hourglass"></i>
                                </div>
                                <div class="widget-numbers">{{ count($process) }}</div>
                                <div class="widget-subheading">Process</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/transaction/delivered') }}" style="color: #495057; text-decoration: none;">
                            <div class="widget-chart widget-chart-hover br-br">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-warning"></div>
                                    <i class="lnr-location"></i>
                                </div>
                                <div class="widget-numbers">{{ count($delivered) }}</div>
                                <div class="widget-subheading">Delivered</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/transaction/completed') }}" style="color: #495057; text-decoration: none;">
                            <div class="widget-chart widget-chart-hover br-br">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-success"></div>
                                    <i class="lnr-checkmark-circle"></i>
                                </div>
                                <div class="widget-numbers">{{ count($completed) }}</div>
                                <div class="widget-subheading">Completed</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/transaction/cancel') }}" style="color: #495057; text-decoration: none;">
                            <div class="widget-chart widget-chart-hover br-br">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-danger"></div>
                                    <i class="lnr-cross-circle"></i>
                                </div>
                                <div class="widget-numbers">{{ count($cancel) }}</div>
                                <div class="widget-subheading">Cancel</div>
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
                <div class="card-header-title font-size-lg text-capitalize font-weight-normal">Latest Order - {{ now()->format('F Y') }}</div>
            </div>
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" nowrap>#</th>
                            <th class="text-center" nowrap>Date</th>
                            <th class="text-center" nowrap>Catalog</th>
                            <th class="text-center" nowrap>Order Via</th>
                            <th class="text-center" nowrap>Status</th>
                            <th class="text-right d-none" nowrap>Total</th>
                            <th nowrap class="d-none">Note</th>
                            <th class="text-center" nowrap>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestactivity as $latestactivity)
                        <tr>
                            <td class="text-center text-muted" nowrap>{{ $latestactivity['invoice_number'] }}</td>
                            <td class="text-center" nowrap>{{ Date::fullDate($latestactivity['created_at']) }}</td>
                            <td nowrap>
                                <div class="widget-content p-0">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left mr-3">
                                            <div class="widget-content-left">
                                                <img width="40" class="rounded" src="{{ str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$latestactivity['catalog_logo'].'?'.time() }}" alt="" />
                                            </div>
                                        </div>
                                        <div class="widget-content-left flex2">
                                            <div class="widget-heading">{{ $latestactivity['catalog_title'] }}</div>
                                            <div class="widget-subheading opacity-7">
                                                <a href="https://{{ $latestactivity['catalog_username'].'.'.$latestactivity['domain'] }}" target="_blank" style="color: #888; text-decoration: none;">
                                                    https://{{ $latestactivity['catalog_username'].'.'.$latestactivity['domain'] }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center" nowrap>{{ $latestactivity['via'] }}</td>
                            <td class="text-center" nowrap>
                                <div class="badge badge-{{ myFunction::colorStatus($latestactivity['status']) }}" style="width: 100px;">{{ $latestactivity['status'] }}</div>
                            </td>
                            <td class="text-right d-none" nowrap>
                                {{ number_format(getData::getTotalInvoice($latestactivity['id'])) }}
                            </td>
                            <td nowrap class="d-none">
                                {{ $latestactivity['note_order'] }}
                            </td>
                            <td class="text-center" nowrap>
                                <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="loadDetail('{{ $latestactivity['invoice_number'] }}')">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="8" nowrap>No Data Available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@if(count($hotitems) > 0)
<div class="row d-none">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-header">
                <div class="card-header-title font-size-lg text-capitalize font-weight-normal">Hot Items</div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($hotitems as $hotitems)
                    <div class="col-sm-6 col-md-4 col-xl-2">
                        <div class="card-shadow-primary card-border text-white mb-3 card bg-focus">
                            <div class="dropdown-menu-header">
                                <div class="dropdown-menu-header-inner bg-focus">
                                    <div class="menu-header-content">
                                        <div class="avatar-icon-wrapper mb-3 avatar-icon-xl">
                                            <div class="avatar-icon">
                                                <a href="javascript:void(0)" data-featherlight="{{ str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$hotitems['item_image_primary'].'?'.time() }}">
                                                    <img src="{{ str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).str_replace('/thumbs','',$hotitems['item_image_primary']) }}" alt="{{ $hotitems['items_name'] }}" />
                                                </a>
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="menu-header-title" style="font-size: .8rem">{{ $hotitems['items_name'] }}</h5>
                                            <h6 class="menu-header-subtitle">{{ $hotitems['category_name'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>        
        var user_days = '{{ $user_days }}';
        var user_total = '{{ $user_total }}';
        
        user_days = user_days.split(',');
        user_total = user_total.split(',');

        var option_user = {
          series: [{
          name: 'Total',
          data: user_total
        }],
          chart: {
          height: 350,
          type: 'bar',
        },
        plotOptions: {
          bar: {
            borderRadius: 10,
            dataLabels: {
              position: 'center', // top, center, bottom
            },
          }
        },
        dataLabels: {
          enabled: true,
        //   formatter: function (val) {
        //     return val + "%";
        //   },
          offsetY: -20,
          style: {
            fontSize: '12px',
            colors: ["#304758"]
          }
        },
        
        xaxis: {
          categories: user_days,
          position: 'bottom',
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
          crosshairs: {
            fill: {
              type: 'gradient',
              gradient: {
                colorFrom: '#D8E3F0',
                colorTo: '#BED1E6',
                stops: [0, 100],
                opacityFrom: 0.4,
                opacityTo: 0.5,
              }
            },
          },
          tooltip: {
            enabled: true,
            // formatter: function (val) {
            //   return val + "%";
            // }
          },
        //   labels: {
        //     show: true,
        //   }
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
            // formatter: function (val) {
            //   return val + "%";
            // }
          }
        
        },
        // title: {
        //   text: 'Monthly Inflation in Argentina, 2002',
        //   floating: true,
        //   offsetY: 330,
        //   align: 'center',
        //   style: {
        //     color: '#444'
        //   }
        // }
        };



        // var option_user = {
        //   series: [{
        //   name: 'Total',
        //   data: user_total
        // }],
        // //   annotations: {
        // //   points: [{
        // //     x: 'Bananas',
        // //     seriesIndex: 0,
        // //     label: {
        // //       borderColor: '#775DD0',
        // //       offsetY: 0,
        // //       style: {
        // //         color: '#fff',
        // //         background: '#775DD0',
        // //       },
        // //       text: 'Bananas are good',
        // //     }
        // //   }]
        // // },
        // chart: {
        //   height: 350,
        //   type: 'bar',
        // },
        // plotOptions: {
        //   bar: {
        //     borderRadius: 10,
        //     columnWidth: '50%',
        //   }
        // },
        // dataLabels: {
        //   enabled: false
        // },
        // stroke: {
        //   width: 2
        // },
        
        // grid: {
        //   row: {
        //     colors: ['#fff', '#f2f2f2']
        //   }
        // },
        // xaxis: {
        //   labels: {
        //     rotate: -45
        //   },
        //   categories: user_days,
        //   tickPlacement: 'on'
        // },
        // yaxis: {
        //   title: {
        //     text: 'Customers',
        //   },
        // },
        // fill: {
        //   type: 'gradient',
        //   gradient: {
        //     shade: 'light',
        //     type: "horizontal",
        //     shadeIntensity: 0.25,
        //     gradientToColors: undefined,
        //     inverseColors: true,
        //     opacityFrom: 0.85,
        //     opacityTo: 0.85,
        //     stops: [50, 0, 100]
        //   },
        // }
        // };

        var chart_user = new ApexCharts(document.querySelector("#chart_user"), option_user);
        chart_user.render();

        var options = {
          series: [{
          name: 'Total',
          data: [{{ count($checkout) }}, {{ count($approve) }}, {{ count($process) }}, {{ count($delivered) }}, {{ count($completed) }}, {{ count($cancel) }}]
        }],
        //   annotations: {
        //   points: [{
        //     x: 'Bananas',
        //     seriesIndex: 0,
        //     label: {
        //       borderColor: '#775DD0',
        //       offsetY: 0,
        //       style: {
        //         color: '#fff',
        //         background: '#775DD0',
        //       },
        //       text: 'Bananas are good',
        //     }
        //   }]
        // },
        chart: {
          height: 350,
          type: 'bar',
        },
        plotOptions: {
          bar: {
            borderRadius: 10,
            columnWidth: '50%',
            dataLabels: {
              position: 'center', // top, center, bottom
            },
          }
        },
        // dataLabels: {
        //   enabled: false
        // },

        dataLabels: {
          enabled: true,
        //   formatter: function (val) {
        //     return val + "%";
        //   },
          offsetY: -20,
          style: {
            fontSize: '12px',
            colors: ["#304758"]
          }
        },

        stroke: {
          width: 2
        },
        
        grid: {
          row: {
            colors: ['#fff', '#f2f2f2']
          }
        },
        xaxis: {
          labels: {
            rotate: -45
          },
          categories: ['Checkout', 'Approve', 'Process', 'Delivered', 'Completed', 'Cancel'
          ],
          tickPlacement: 'on'
        },
        yaxis: {
          title: {
            text: 'Trackings',
          },
        },
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 0.85,
            opacityTo: 0.85,
            stops: [50, 0, 100]
          },
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
</script>
