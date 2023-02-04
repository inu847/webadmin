@extends('layouts.main')

@section('customcss')
    <style>
        span.select2-selection.select2-selection--single {
            height: calc(2px + 2.25rem);
            padding-top: 3px;
        }
    </style>
@endsection

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
                    <i class="lnr-users icon-gradient bg-ripe-malin"> </i>
                </div>
                <div>
                    {{ $maintitle }}
                    <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
                </div>
            </div>
            <div class="page-title-actions">
                @if(!empty($request['searchfield']))
                    <a href="{{ route('member.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
                @endif
            </div>
        </div>
    </div>

    <div id="indexVue">
        <div class="tabs-animation">
            <div class="row">
                <div class="col-md-12">
                    <!-- <div class="col-md-12 p-0 mb-3 d-flex flex-row-reverse"> -->
                    <div class="d-flex flex-wrap justify-content-between">
                        <div>
                            <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> Create New</a>
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
                    </div>

                    <div class="main-card mb-3 card" style="min-height: 250px;">
                        @include('blocks.skeleton') 
                        <div id="loadpage"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm2" method="post" onsubmit="return false;" enctype="multipart/form-data">
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
                    <div id="contentForm"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary save_data_btn">Save Data</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="myForm2" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <div class="modal fade" id="modalForm2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div id="modalSize2" class="modal-dialog" role="document">
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
                        <h5 class="modal-title" id="titleModal2">Import Xendit Catalog Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="contentForm2">
                        <div class='card'>
                            <div class='card-body'>
                                @csrf
                                <div class="form-group">
                                    <label for="">Date Start</label>
                                    <!-- <input type="text" class="form-control" name="date_from" required> -->
                                    <div class="input-group mb-3">
                                        <div class="datepicker date input-group p-0">
                                            <input type="text" id="date_from" name="date_from" class="form-control" readonly value="{{ date('Y-m-d') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button"><i class="ion-android-calendar" style="font-size: 1rem"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Date End</label>
                                    <!-- <input type="text" class="form-control" name="date_to" required> -->
                                    <div class="input-group mb-3">
                                        <div class="datepicker date input-group p-0">
                                            <input type="text" id="date_to" name="date_to" class="form-control" readonly value="{{ date('Y-m-d') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button"><i class="ion-android-calendar" style="font-size: 1rem"></i></button>
                                            </div>
                                        </div>
                                        <small class="text-danger">Maksimal 30 hari rentang waktu mulai dan akhir.</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="import_type">Import Type</label>
                                    <select name="import_type" id="import_type" class="form-control">
                                        <option value="BALANCE_HISTORY">BALANCE HISTORY</option>
                                        <option value="TRANSACTIONS">TRANSACTIONS</option>
                                    </select>
                                </div>
                                <input type="hidden" id="user_id" name="user_id">
                                <div class="form-group">
                                    <label for="">Catalog</label>
                                    <select name="catalogs" id="catalogs" class="form-control">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary import_data_btn">Import Data</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection 

@section('customjs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript" src="{{ url('js/jquery.domenu-0.0.1.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            loadView();

            $('.datepicker').datepicker({
                clearBtn: true,
                useCurrent:true,
                autoclose:true,
                endDate:'0d',
                format: "yyyy-mm-dd"
            });
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
                let self = this;
                $(document).on("click", ".detailMonitor", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    var month = $(this).attr("data-month");
                    var year = $(this).attr("data-year");
                    self.showForm("detail", id, month, year);
                });
                // $(document).on("click", ".editlink", function (evetn, id) {
                //     var id = $(this).attr("data-id");
                //     self.showForm("edit", id);
                // });
                // $(document).on("click", ".deletelink", function (evetn, id) {
                //     var id = $(this).attr("data-id");
                //     self.confirmDialog(id);
                // });
            },
            methods: {
                showForm: function (action, id = null, month = null, year = null) {
                    // preloader();
                    $("#modalSize").removeClass("modal-sm");
                    $("#modalSize").removeClass("modal-lg");

                    // if (action == "create") {
                    //     $("#titleModal").html("Create New");
                    //     $.ajax({
                    //         url: "{{ url('/menu-roles/create') }}", 
                    //         type: "GET", 
                    //     })
                    //     .done(function(data) {
                    //         $("#modalForm").modal("show");
                    //         $("#contentForm").html(data);
                    //         afterpreloader();
                    //     })
                    //     .fail(function() {
                    //         Swal.fire("Ops!", "Load data failed.", "error");
                    //     });
                    // }
                    if (action == "create") {
                        // $("#modalSize").addClass("modal-sm");
                        $("#modalSize").removeClass("modal-lg");

                        $("#titleModal").html("Create New");
                        $("input[name=_method]").remove();
                        axios
                            .get("{{ route('manage-user.create') }}")
                            .then((response) => {
                                var data = response.data;
                                $("#modalSize").removeClass("modal-lg");
                                $("#modalForm .save_data_btn").removeClass("hidden");
                                $("#modalForm").modal("show");
                                $("#contentForm").html(data);
                                $(".save_data_btn").css("display", "block");
                                afterpreloader();
                            })
                            .catch(function (error) {
                                Swal.fire("Ops!", "Load data failed.", "error");
                            });
                        // $("#modalForm").modal("show");
                    }
                    else if (action == "edit") {
                        // $("#modalSize").addClass("modal-sm");
                        $("#modalSize").removeClass("modal-lg");

                        preloader();
                        $("#titleModal").html("Edit Data");
                        $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                        $("#biodata1").css("display", "block");
                        $("#biodata2").css("display", "block");
                        $("#wrappassword").css("display", "none");
                        axios
                            .get("{{ url('/menu-roles') }}" +"/" + id +"/edit"  )
                            .then((response) => {
                                var data = response.data;
                                $("#modalSize").removeClass("modal-lg");
                                $("#modalForm .save_data_btn").removeClass("hidden");
                                $("#modalForm").modal("show");
                                $("#contentForm").html(data);
                                afterpreloader();
                            })
                            .catch(function (error) {
                                Swal.fire("Ops!", "Load data failed.", "error");
                            });
                    }else if (action == "detail") {
                        preloader();
                        $("#modalSize").addClass("modal-lg");

                        $("#titleModal").html("User Monitoring Merchant");
                        // $("#biodata1").css("display", "block");
                        // $("#biodata2").css("display", "block");
                        // $("#wrappassword").css("display", "none");
                        axios
                            .get("{{ url('/monitoring-merchant/detail') }}" +"/" + id + "?searchMonth=" + month + "&searchYear=" + year )
                            .then((response) => {
                                var data = response.data;
                                $("#modalSize").addClass("modal-lg");
                                $("#modalForm .save_data_btn").addClass("hidden");
                                $("#modalForm").modal("show");
                                $("#contentForm").html(data);
                                afterpreloader();
                            })
                            .catch(function (error) {
                                Swal.fire("Ops!", "Load data failed.", "error");
                            });
                    }
                },
                confirmDialog: function (id) {
                    let self = this;
                    Swal.fire({
                        title: "Are you sure ?",
                        text: "Data will be permanently deleted",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes",
                        cancelButtonText: "Cancel",
                    }).then((result) => {
                        if (result.value) {
                            preloadContent();
                            axios.delete("{{ url('/menu-roles') }}" + "/" + id)
                                .then((response) => {
                                    var notif = response.data;
                                    var getstatus = notif.status;
                                    console.log();
                                    if (getstatus == "success") {
                                        $("#modalForm").modal("hide");
                                        toastr.success(notif.message);
                                        location.reload()
                                    }else{
                                        afterPreloadContent();
                                        toastr.error(notif.message);
                                    }
                                })
                                .catch(function (error) {
                                    Swal.fire("Ops!", "Load data failed.", "error");
                                    afterPreloadContent();
                                });
                        }
                    });
                },
                submitForm2: function (e) {
                    submitForm2();
                    $('.errormsg').hide();
                    var form = e.target || e.srcElement;

                    if($("#id").val() > 0){
                        var action = "{{ route('manage-user.update', 0) }}";
                        var put = form.querySelector('input[name="_method"]').value;
                    }
                    else {
                    var action = "{{ route('manage-user.store') }}";
                    var put = '';
                    }

                    var csrfToken = "{{ csrf_token() }}";
                    let datas = new FormData($('#myForm')[0]);
                    
                    axios.post(action, datas, {
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                                "X-HTTP-Method-Override": put,
                                Accept: "application/json",
                            },
                        })
                        .then((response) => {
                            let self = this;
                            var notif = response.data;
                            var getstatus = notif.status;
                            if (getstatus == "success") {
                                $("#modalForm").modal("hide");
                                afterSubmitForm();
                                loadView();
                            }else{
                                afterSubmitForm();
                                toastr.error(notif.message);
                            }
                        })
                        .catch((error) => {
                            afterSubmitForm();
                            $('.errormsg').show();
                            this.formErrors = error.response.data.errors;
                        });
                },

            },
        });

        new Vue({
            el: "#wrapVue",
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
                let self = this;
                $(document).on("click", ".importLink", function (evetn, id) {
                    var data = $(this).data();
                    self.showForm("import", data);
                });
                $(document).on("click", ".detaillink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.detailForm("edit", id);
                });
                $(document).on("click", ".editlink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.showForm("edit", id);
                });
                $(document).on("click", ".deletelink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    var active = $(this).attr("data-status");
                    self.confirmDialog(id,active);
                });
            },
            methods: {
                detailForm: function (action, id = null) {
                    preloader();
                    $(".errormsg").hide();
                    $("#myForm")[0].reset();
                    $("#titleModal").html("Detail Data");
                    axios
                        .get("{{ url('/manage-user') }}" +"/" + id  )
                        .then((response) => {
                            var data = response.data;
                            $("#modalForm").modal("show");
                            $("#contentForm").html(data);
                            $(".save_data_btn").css("display", "none");
                            afterpreloader();
                        })
                        .catch(function (error) {
                            Swal.fire("Ops!", "Load data failed.", "error");
                        });
                },
                showForm: function (action, id = null, month = null, year = null) {
                    // preloader();

                    if (action == "import") {
                        preloader();
                        var catalogs = id.catalogs;
                        var id = id.id;
                    
                        $("#titleModal2").html("Import Xendit Catalog Data");
                        $("#modalForm2").modal("show");
                        $("#user_id").val(id);

                        var options = '';
                        $.each(catalogs, function(key, value) {
                            options += '<option value="'+key+'">'+value+'</option>';
                        });

                        $("#catalogs").html(options);
                        afterpreloader();
                    }
                    else if(action == "edit"){
                        $("#titleModal").html("Edit Data");
                        $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                        axios
                            .get("{{ url('/manage-user') }}" +"/" + id +"/edit?affiliate=0"  )
                            .then((response) => {
                                var data = response.data;
                                $("#modalForm").modal("show");
                                $("#contentForm").html(data);
                                $(".save_data_btn").css("display", "block");
                                afterpreloader();
                            })
                            .catch(function (error) {
                                Swal.fire("Ops!", "Load data failed.", "error");
                            });
                    }
                    
                },
                submitForm: function (e) {
                    submitForm();
                    var form = e.target || e.srcElement;
                    var action = "{{ route('catalog.monitoringMerchantImport') }}";
                    var csrfToken = "{{ csrf_token() }}";

                    $('.import_data_btn').prop('disabled', true);

                    let datas = new FormData();
                    datas.append("date_from", $("#date_from").val());
                    datas.append("date_to", $("#date_to").val());
                    datas.append("user_id", $("#user_id").val());
                    datas.append("catalogs", $("#catalogs").val());
                    datas.append("import_type", $("#import_type").val());

                    axios.post(action, datas, {
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                                Accept: "application/json",
                            },
                        })
                        .then((response) => {
                            let self = this;
                            var notif = response.data;
                            var getstatus = notif.status;
                            if (getstatus == "success") {
                                location.href = notif.link;
                                // location.href = 'https://transaction-report-files.s3.us-west-2.amazonaws.com/6252f67a3ff58dc7bf73e7b8/LIVE_UPCOMING_TRANSACTIONS_REPORT_b263681e-3aa8-4692-aef6-78d55ba2719f_20220518170000_20220615165959.csv?AWSAccessKeyId=AKIAWDX4EPHWHF77FGHM&Expires=1666289451&Signature=p5T9TYwbzT%2FBJqbGJo4QRkcUIS4%3D';
                                toastr.success(notif.message);
                            } else {
                                toastr.error(notif.message);
                            }
                            afterSubmitForm();
                            $('.import_data_btn').prop('disabled', false);
                        })
                        .catch((error) => {
                            afterSubmitForm();
                            $('.errormsg').css('visibility','visible');
                            this.formErrors = error.response.data.errors;
                            $('.import_data_btn').prop('disabled', false);
                        });
                },
                confirmDialog: function (id,active) {
                    let self = this;
                    Swal.fire({
                        title: "Are you sure ?",
                        text: "Data will be permanently updated",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes",
                        cancelButtonText: "Cancel",
                    }).then((result) => {
                        if (result.value) {
                            preloadContent();
                            axios.get("{{ url('/member/block') }}" +'/'+ id +'/'+ active)
                                .then((response) => {
                                    let self = this;
                                    var notif = response.data;
                                    var getstatus = notif.status;
                                    if (getstatus == "success") {
                                        $("#modalForm").modal("hide");
                                        $(".save_data_btn").css("display", "block");
                                        loadView();
                                    }else{
                                        afterPreloadContent();
                                        toastr.error(notif.message);
                                    }
                                })
                                .catch(function (error) {
                                    Swal.fire("Ops!", "Load data failed.", "error");
                                    afterPreloadContent();
                                });
                        }
                    });
                },
                submitForm2: function (e) {
                    submitForm2();
                    $('.errormsg').hide();
                    var form = e.target || e.srcElement;

                    if($("#id").val() > 0){
                        var action = "{{ route('manage-user.update', 0) }}";
                        var put = form.querySelector('input[name="_method"]').value;
                    }
                    else {
                    var action = "{{ route('manage-user.store') }}";
                    var put = '';
                    }

                    var csrfToken = "{{ csrf_token() }}";
                    let datas = new FormData($('#myForm')[0]);
                    
                    axios.post(action, datas, {
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                                "X-HTTP-Method-Override": put,
                                Accept: "application/json",
                            },
                        })
                        .then((response) => {
                            let self = this;
                            var notif = response.data;
                            var getstatus = notif.status;
                            if (getstatus == "success") {
                                $("#modalForm").modal("hide");
                                afterSubmitForm();
                                loadView();
                            }else{
                                afterSubmitForm();
                                toastr.error(notif.message);
                            }
                        })
                        .catch((error) => {
                            afterSubmitForm();
                            $('.errormsg').show();
                            this.formErrors = error.response.data.errors;
                        });
                },
            },
        });
    </script>

    <script type="text/javascript">
        $("#searchfield").on("keyup", function (e) {
            loadView();
        });
        $(document).on("click", ".pagination a", function (event) {
            $("li").removeClass("active");
            $(this).parent("li").addClass("active");
            event.preventDefault();
            var myurl = $(this).attr("href");
            var page = myurl.match(/([^\/]*)\/*$/)[1];
            loadView(page);
        });

        function submitForm2() {
            $('.errormsg').hide();
            var form = $('#myForm');

            if($("#id").val() > 0){
                var action = "{{ route('manage-user.update', 0) }}";
                var put = $('#myForm input[name="_method"]').val();
            }
            else {
            var action = "{{ route('manage-user.store') }}";
            var put = '';
            }

            var csrfToken = "{{ csrf_token() }}";
            let datas = new FormData($('#myForm')[0]);
            
            axios.post(action, datas, {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "X-HTTP-Method-Override": put,
                        Accept: "application/json",
                    },
                })
                .then((response) => {
                    let self = this;
                    var notif = response.data;
                    var getstatus = notif.status;
                    if (getstatus == "success") {
                        $("#modalForm").modal("hide");
                        afterSubmitForm();
                        loadView();
                    }else{
                        afterSubmitForm();
                        toastr.error(notif.message);
                    }
                })
                .catch((error) => {
                    afterSubmitForm();
                    $('.errormsg').show();
                    this.formErrors = error.response.data.errors;
                });
        }

        function loadView(page = null) {
            preloadContent();
            if (page == null) {
                var url = "{{ url('/monitoring-merchant/data') }}";
            } else {
                var url = "{{ url('/monitoring-merchant') }}" + "/" + page;
            }

            var obj = new Object();
            obj.searchfield = $("#searchfield").val();
            obj.searchMonth = $("#searchMonth").val();
            obj.searchYear = $("#searchYear").val();

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