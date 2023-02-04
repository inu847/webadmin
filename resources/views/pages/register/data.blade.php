@extends('layouts.main')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="{{ $icon }} icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            @if(!empty($request['searchfield']))
                <a href="{{ url('/register/'.strtolower($status)) }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
            @endif
        </div>
    </div>
</div>

<div id="indexVue">
    <div class="tabs-animation">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between">
                    <div>
                    </div>
                    <div class="col-12 col-md-3 p-0 mb-3">
                        <input id="searchfield" type="text" class="form-control" placeholder="Search..." />
                    </div>
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
    @include('pages.register.formreject')
    @include('pages.register.generalmodal')
@endsection

@section('customjs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            loadView();
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
                $(document).on("click", ".detaillink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    var invoice = $(this).attr("data-invoice");
                    self.detailForm(id,invoice);
                });
                $(document).on("click", ".rejectlink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    var invoice = $(this).attr("data-invoice");
                    self.promptReject(id,invoice);
                });
                $(document).on("click", ".approvelink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.promptApprove(id);
                });
            },
            methods: {
                detailForm:function(id,invoice){
                    preloader();
                    $("#titleModalGeneral").html("Transaction Detail Invoice "+invoice);
                    $.ajax({
                        url: "{{ url('/register/detail') }}" +'/'+ id,
                        type: 'GET',
                    })
                    .done(function(data) {
                        $("#modalGeneral").modal('show');
                        $("#contentGeneral").html(data)
                        afterpreloader();
                    })
                    .fail(function() {
                        Swal.fire("Ops!", "Load data failed.", "error");
                    });
                },
                showForm: function (action, id = null) {
                    preloader();
                    $("#titleModal").html("Edit Data");
                    $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                    $.ajax({
                        url: "{{ url('/register/show') }}" +'/'+ id,
                        type: 'GET',
                        dataType: 'json',
                    })
                    .done(function(data) {
                        $("#id").val(data.id);
                        $("#package_id").val(data.package_id);
                        $("#catalog").val(data.number_catalog);
                        $("#modalForm").modal('show');
                        afterpreloader();
                    })
                    .fail(function() {
                        Swal.fire("Ops!", "Load data failed.", "error");
                    });
                },
                promptApprove: function (id) {
                    let self = this;
                    Swal.fire({
                        title: "Confirmation",
                        text: "Are you sure to approve ?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes",
                        cancelButtonText: "Cancel",
                    }).then((result) => {
                        if (result.value) {
                            preloadContent();
                            axios.get("{{ url('/register/approve') }}" +'/'+ id)
                                .then((response) => {
                                    let self = this;
                                    var notif = response.data;
                                    var getstatus = notif.status;
                                    if (getstatus == "success") {
                                        $("#modalForm").modal("hide");
                                        $("#modalGeneral").modal("hide");
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
                promptReject: function(id,invoice){
                    Swal.fire({
                      title: "Confirmation",
                      text: "Are you sure to reject?",
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: "Yes",
                      cancelButtonText: "Cancel"
                    }).then((result) => {
                      if (result.value) {
                        preloader();
                        $.ajax({
                            url: "{{ url('/register/show') }}" +'/'+ id,
                            type: 'GET',
                            dataType: 'json',
                        })
                        .done(function(data) {
                            $("#titleModal").html("Reject Invoice "+invoice);
                            $("#id").val(id);
                            $("#package_id").val(data.package_id);
                            $("#notes").val('');
                            $("#modalGeneral").modal("hide");
                            $("#modalForm").modal('show');
                            afterpreloader();
                        })
                        .fail(function() {
                            Swal.fire("Ops!", "Load data failed.", "error");
                        });
                      }
                    })
                }
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
        function loadView(page = null) {
            preloadContent();
            if (page == null) {
                var url = "{{ url('/register/data/'.$status) }}";
            } else {
                var url = "{{ url('/register/data') }}" + "/" + page;
            }
            var obj = new Object();
            obj.searchfield = $("#searchfield").val();
            axios.post(url.toLowerCase(), obj, {
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
