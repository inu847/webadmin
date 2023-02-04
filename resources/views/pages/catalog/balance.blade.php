@extends('layouts.main') 

@section('content')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">

<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-display2 icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }} ( {{ $catalog['catalog_title'] }} )
                <div class="page-title-subheading d-none">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ route('catalog.index') }}" class="btn-shadow btn btn-success btn-sm"><i class="icon lnr-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div id="indexVue">
    <div class="tabs-animation">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between mb-3">
                    <div>
                        <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" v-on:click="update_balance()">Update Balance</a>
                    </div>
                    <div class="col-12 col-md-3 p-0 mb-3"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-xl-6">
                <div class="card mb-3 widget-chart">
                    <div class="widget-chart-content">
                        <div class="icon-wrapper rounded">
                            <div class="icon-wrapper-bg bg-warning"></div>
                            <i class="lnr-laptop-phone text-warning"></i></div>
                        <div class="widget-numbers">
                            <span id="display_balance">{{ number_format($catalog->balance) }}</span>
                        </div>
                        <div class="widget-subheading fsize-1 pt-2 opacity-10 text-warning font-weight-bold">Last Balance</div>
                        <div class="widget-description opacity-8">
                                <span class="text-danger pr-1">
                                    
                                    <span class="pl-1"></span>
                                </span>
                            
                        </div>
                    </div>
                    <div class="widget-chart-wrapper">
                        <div id="dashboard-sparklines-simple-1"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-xl-6">
                <div class="card mb-3 widget-chart">
                    <div class="widget-chart-content">
                        <div class="icon-wrapper rounded">
                            <div class="icon-wrapper-bg bg-danger"></div>
                            <i class="lnr-graduation-hat text-danger"></i>
                        </div>
                        <div class="widget-numbers"><span>{{ number_format($pending) }}</span></div>
                        <div class="widget-subheading fsize-1 pt-2 opacity-10 text-danger font-weight-bold">
                            Pending
                        </div>
                        <div class="widget-description opacity-8">
                            
                            <span class="text-info pl-1">
                                    
                                    <span class="pl-1"></span>
                                </span>
                        </div>
                    </div>
                    <div class="widget-chart-wrapper">
                        <div id="dashboard-sparklines-simple-2"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-xl-4 d-none">
                <div class="card mb-3 widget-chart">
                    <div class="widget-chart-content">
                        <div class="icon-wrapper rounded">
                            <div class="icon-wrapper-bg bg-info"></div>
                            <i class="lnr-diamond text-info"></i></div>
                        <div class="widget-numbers text-danger"><span>$294</span></div>
                        <div class="widget-subheading fsize-1 pt-2 opacity-10 text-info font-weight-bold">Withdrawals</div>
                        <div class="widget-description opacity-8">
                            Down by
                            <span class="text-success pl-1">
                                <i class="fa fa-angle-down"></i>
                                    <span class="pl-1">21.8%</span>
                                </span>
                        </div>
                    </div>
                    <div class="widget-chart-wrapper">
                        <div id="dashboard-sparklines-simple-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                <div class="mb-3 card">
                    <div class="card-header card-header-tab-animation">
                        <ul class="nav nav-justified">
                            <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-0" class="active nav-link">Transaction List</a></li>
                            <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-2" class="nav-link">Pending List</a></li>
                            <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-1" class="nav-link">Withdrawal List</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                        <div class="tab-pane active" id="tab-eg115-0" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table style="width: 100%;" id="xendit_table" class="table table-hover table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Reference</th>
                                                    <th>Status</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($xendit_trx as $value)
                                                    <tr>
                                                        <td>{{ $value['tanggal'] }}</td>
                                                        <!-- <td>{{ $value['tanggal_label'] }}</td> -->
                                                        <td>{{ $value['tipe'] }}</td>
                                                        <td>{{ $value['referensi'] }}</td>
                                                        <td>{{ $value['status'] }}</td>
                                                        <td class="{{ $value['jumlah'] > 0 ? 'text-success' : 'text-danger' }}">{{ number_format($value['jumlah']) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab-eg115-2" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table style="width: 100%;" id="pending_table" class="table table-hover table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Settlement</th>
                                                    <th>Type</th>
                                                    <th>Reference</th>
                                                    <th>Status</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pending_trx as $value)
                                                    <tr>
                                                        <td>{{ $value['tanggal'] }}</td>
                                                        <td>{{ $value['add_two_days'] }}</td>
                                                        <!-- <td>{{ $value['tanggal_label'] }}</td> -->
                                                        <td>{{ $value['tipe'] }}</td>
                                                        <td>{{ $value['referensi'] }}</td>
                                                        <td>{{ $value['status'] }}</td>
                                                        <td class="{{ $value['jumlah'] > 0 ? 'text-success' : 'text-danger' }}">{{ number_format($value['jumlah']) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab-eg115-1" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-4 text-right">
                                            <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> Request Withdrawal</a>
                                        </div>
                                        <hr>
                                        <table style="width: 100%;" id="example" class="table table-hover table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Catalog</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Bank</th>
                                                    <th>Account Name</th>
                                                    <th>Account Number</th>
                                                    <th>Option</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($withdrawal as $value)
                                                    <?php
                                                        $dis_id = '';
                                                        $xendit_data = $value->xendit_data ? json_decode($value->xendit_data) : '';
                                                        if($xendit_data){
                                                            $dis_id = $xendit_data->id;

                                                            if($xendit_data->status == 'FAILED'){
                                                                $dis_id = '';
                                                            }
                                                            elseif($xendit_data->status == 'error'){
                                                                $dis_id = '';
                                                            }
                                                            
                                                            if($value->status == 'FAILED'){
                                                                $dis_id = '';
                                                            }
                                                            elseif($value->status == 'error'){
                                                                $dis_id = '';
                                                            }
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td>{{ optional($value->created_at)->format('Y/m/d H:i') }}</td>
                                                        <td>{{ optional($value->catalog)->catalog_title }}</td>
                                                        <td>{{ $value->total }}</td>
                                                        <td>{{ $value->status }}</td>
                                                        <td>{{ $value->bank['name'] }}</td>
                                                        <td>{{ $value->bank_account_name }}</td>
                                                        <td>{{ $value->bank_account_number }}</td>
                                                        @if($dis_id)
                                                            <td><a href="{{ url('/catalog/cek_balance/'.$catalog['id']) }}?single={{ $value->id }}&uid={{ $dis_id }}" class="btn-shadow btn btn-dark btn-sm">Re-Check</a></td>
                                                        @else
                                                            <td>Invalid</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Catalog</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Bank</th>
                                                    <th>Account Name</th>
                                                    <th>Account Number</th>
                                                    <th>Option</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@section('modal')
<form id="myForm" method="post">
    
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <!-- <input type="text" id="total" name="total" value="0" class="form-control"> -->
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div id="modalSize" class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Form Loader -->
                <div class="formLoader">
                    <div class="jumper">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <!-- End -->
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModal"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="position-relative form-group">
                        <label>Total</label>
                        <input type="text" id="total" name="total" value="0" class="form-control">
                    </div>
                    <div class="position-relative form-group">
                        <label>Description</label>
                        <input type="text" id="description" name="description" value="" class="form-control">
                    </div>
                    <div class="position-relative form-group">
                        <label>Email</label>
                        <input type="text" id="catalog_email" name="" value="{{ $catalog['email_contact'] }}" class="form-control" readonly>
                    </div>
                    <div class="position-relative form-group">
                        <label>Bank Name</label>
                        <input type="text" id="bank_code" name="" value="{{ optional($catalog['bank'])['name'] }}" class="form-control" readonly>
                    </div>
                    <div class="position-relative form-group">
                        <label>Bank Account Number</label>
                        <input type="text" id="bank_account_number" name="" value="{{ $catalog['bank_account_number'] }}" class="form-control" readonly>
                    </div>
                    <div class="position-relative form-group">
                        <label>Bank Account Name</label>
                        <input type="text" id="bank_account_name" name="" value="{{ $catalog['bank_account_name'] }}" class="form-control" readonly>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Data</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection 

@section('customjs')
<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#xendit_table, #pending_table").DataTable({
            "order": [[ 0, "desc" ]]
        });

        loadView();

        @if(isset($status))
            Swal.fire("Info!", "{{ $message }}", "{{ $status }}");
        @endif
    });
    new Vue({
        el: "#indexVue",
        data() {
            return {
                csrf: "",
                deletedata: "",
                formErrors: {},
                notif: [],
            };
        },
        mounted: function () {
            this.csrf = "{{ csrf_token() }}";
            this.deletedata = "DELETE";
            let self = this;
        },
        methods: {
            update_balance(){
                preloader();
                $.ajax({
                    url: "{{ url('/catalog/cek_balance/'.$catalog['id']) }}", 
                    type: "GET", 
                })
                .done(function(data) {
                    $('#display_balance').html(data.balance)
                    Swal.fire("Success!", "Update data succesfully.", "success");
                    afterpreloader();
                })
                .fail(function() {
                    Swal.fire("Ops!", "Load data failed.", "error");
                    afterpreloader();
                });
            },
            showForm: function (action, id = null) {
                if (action == "create") {
                    $("#titleModal").html("Request New Withdrawal");
                    $("#modalForm").modal("show");
                }
            },
        },
    });
</script>
<script type="text/javascript">
    function loadView() {
        preloadContent();
        var url = "{{ url('/catalog/items/') }}" + "/{{ $catalog['id'] }}";
        var obj = new Object();
        obj.searchfield = $("#searchfield").val();
        axios.post(url, obj, {
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    Accept: "application/json",
                },
            })
            .then((response) => {
                $("#loadpage").html(response.data);
                afterPreloadContent();
            })
            .catch((error) => {
                afterpreloader();
                $(".errormsg").css("visibility", "visible");
                this.formErrors = error.response.data.errors;
            });
    }
</script>
@endsection
