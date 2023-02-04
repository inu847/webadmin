@extends('layouts.main')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-printer icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            @if(!empty($request['searchfield']))
                <a href="{{ url('/transaction/'.$status) }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
            @endif
        </div>
    </div>
</div>

<div class="tabs-animation">
    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <form id="myForm" method="POST">
                    {!! csrf_field() !!}
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" id="status" name="status" value="Completed">
                            <div class="col-md-4">
                                <div class="input-group mb-3">
                                    <div class="datepicker date input-group p-0">
                                        <input type="text" id="start" name="start" class="form-control" readonly value="{{ $start }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-primary" type="button"><i class="ion-android-calendar" style="font-size: 1rem"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group mb-3">
                                    <div class="datepicker date input-group p-0">
                                        <input type="text" id="end" name="end" class="form-control" readonly value="{{ $end }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-primary" type="button"><i class="ion-android-calendar" style="font-size: 1rem"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-info btn-block" style="padding: 9px 10px" onclick="submitForms()">Search</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-header {{ (Session::get('catalogsession')=='All')?'d-none':'' }}">
                        <ul class="nav nav-justified">
                            <li class="nav-item"><a data-toggle="tab" href="#tab-eg7-0" class="nav-link {{ (Session::get('catalogsession')=='All')?'d-none':'active' }}">General</a></li>
                            <!-- <li class="nav-item"><a data-toggle="tab" href="#tab-eg7-1" class="nav-link">Pengeluaran</a></li>
                            <li class="nav-item"><a data-toggle="tab" href="#tab-eg7-2" class="nav-link">Pemasukan</a></li> -->
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ (Session::get('catalogsession')=='All')?'d-none':'active' }}" id="tab-eg7-0" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <canvas id="pieChart" style="height:200px; min-height:200px"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th nowrap>Transaction Type</th>
                                                        <th class="text-right" nowrap>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($queries as $key => $value)
                                                    <tr>
                                                        <td nowrap>{{ $key }}</td>
                                                        <td class="text-right" nowrap>{{ $value }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <canvas id="barChart" style="min-height:230px"></canvas>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th nowrap>Detail Name</th>
                                                        <th class="text-right" nowrap>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sorted_sold as $key => $value)
                                                    <tr>
                                                        <td nowrap>{{ $key }}</td>
                                                        <td class="text-right" nowrap>{{ $value }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane {{ (Session::get('catalogsession')=='All')?'active':'d-none' }}" id="tab-eg7-1" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th nowrap>Catalog</th>
                                                <th class="text-right" nowrap>Pemasukan</th>
                                                <th class="text-right" nowrap>Pengeluaran</th>
                                                <th class="text-right" nowrap>Keuntungan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($catalogs as $key => $value)
                                                <?php
                                                    if(isset($summary[$key])){
                                                        $pemasukan = isset($summary[$key]['pemasukan']) ? array_sum($summary[$key]['pemasukan']) : 0;
                                                        $pengeluaran = isset($summary[$key]['pengeluaran']) ? array_sum($summary[$key]['pengeluaran']) : 0;
                                                        $keuntungan = $pemasukan - $pengeluaran;
                                                    }
                                                    else{
                                                        $keuntungan = $pemasukan = $pengeluaran = 0;
                                                    }
                                                ?>
                                                <tr>
                                                    <td nowrap>{{ $value }}</td>
                                                    <td class="text-right" nowrap>{{ number_format($pemasukan) }}</td>
                                                    <td class="text-right" nowrap>{{ number_format($pengeluaran) }}</td>
                                                    <td class="text-right" nowrap>{{ number_format($keuntungan) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane {{ (Session::get('catalogsession')=='All')?'d-none':'' }}" id="tab-eg7-2" role="tabpanel">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item"><a data-toggle="tab" href="#tab-item-month" class="nav-link active">Month</a></li>
                                    <li class="nav-item"><a data-toggle="tab" href="#tab-item-day" class="nav-link">Day</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane" id="tab-item-day" role="tabpanel" style="max-height:400px; overflow:auto;">
                                        <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th nowrap>Date</th>
                                                    <th nowrap>Item</th>
                                                    <th class="text-center" nowrap>Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane active" id="tab-item-month" role="tabpanel" style="max-height:400px; overflow:auto;">
                                        <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th nowrap>Months</th>
                                                    <th nowrap>Item</th>
                                                    <th class="text-center" nowrap>Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="pie_data" value="{{ $pie['data'] }}">
<input type="hidden" id="pie_label" value="{{ $pie['label'] }}">
<input type="hidden" id="pie_color" value="{{ $pie['color'] }}">

<input type="hidden" id="sold_data" value="{{ $sold['data'] }}">
<input type="hidden" id="sold_label" value="{{ $sold['label'] }}">
<input type="hidden" id="viewed_data" value="">
<input type="hidden" id="viewed_label" value="">

@endsection

@section('modal')
<div class="modal fade" id="modalData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="modalContent" class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="printInvoice()">Print</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
    <script type="text/javascript">
        $(document).ready(function() {
            // $("#status").val("{{ $status }}");
            $('.datepicker').datepicker({
                clearBtn: true,
                useCurrent:true,
                autoclose:true,
                endDate:'0d',
                format: "yyyy-mm-dd"
            });

            //-------------
            //- PIE CHART -
            //-------------
            var donutData = {
                labels: $('#pie_label').val().split(","),
                datasets: [
                    {
                        data: $('#pie_data').val().split(","),
                        backgroundColor : $('#pie_color').val().split(","),
                    }
                ]
            }

            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
            var pieData        = donutData;
            var pieOptions     = {
              maintainAspectRatio : false,
              responsive : true,
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            var pieChart = new Chart(pieChartCanvas, {
              type: 'pie',
              data: pieData,
              options: pieOptions      
            })

            //-------------
            //- BAR CHART -
            //-------------
            var areaChartData = {
                labels  : $('#sold_label').val().split(","),
                datasets: [
                    {
                        label               : '10 Biggest Out',
                        backgroundColor     : 'rgba(60,141,188,0.9)',
                        borderColor         : 'rgba(60,141,188,0.8)',
                        pointRadius          : false,
                        pointColor          : '#3b8bba',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data                : $('#sold_data').val().split(",")
                    },
                ]
            }

            var barChartCanvas = $('#barChart').get(0).getContext('2d')
            var barChartData = jQuery.extend(true, {}, areaChartData)
            var temp0 = areaChartData.datasets[0]
            barChartData.datasets[0] = temp0

            var barChartOptions = {
                responsive              : true,
                maintainAspectRatio     : false,
                datasetFill             : false,
                // legend: {
                //     display: false
                // },
            }

            var barChart = new Chart(barChartCanvas, {
                type: 'bar', 
                data: barChartData,
                options: barChartOptions
            })
        });
        function submitForms(){
            $("#myForm").attr('action', "{{ url('/transaction/income/report') }}"+'/'+$("#status").val()+'/'+$("#start").val()+'/'+$("#end").val());
            $("#myForm").submit();
        }
        function loadDetail(invoice){
            $("#modalData").modal('show');
            $("#titleModal").html("Detail order : "+invoice);
            $.ajax({
                url: "{{ url('/transaction/detailpopup') }}"+'/'+invoice,
                type: 'GET',
            })
            .done(function(data) {
                $("#modalContent").html(data)
            })
            .fail(function() {
                Swal.fire("Ops!", "Load data failed.", "error");
            });
        }
        function printInvoice(){
            inv = $("#invoice_data").val();
            $("#prints").attr("src", "{{ url('/transaction/print') }}"+'/'+inv);
            
            // window.open("{{ url('/transaction/print') }}"+'/'+inv);
        }
    </script>
@endsection