@extends('layouts.main')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-list icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ route('package.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
        </div>
    </div>
</div>

<div id="indexVue">
    <div class="tabs-animation">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between">
                    <div>
                        <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> Create New</a>
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
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
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
                <div id="contentForm">
                    
                </div>
            </div>
        </div>
    </div>
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
                $(document).on("click", ".editlink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.showForm("edit", id);
                });
                $(document).on("click", ".deletelink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.confirmDialog(id);
                });
            },
            methods: {
                showForm: function (action, id = null) {
                    preloader();
                    $(".errormsg").hide();
                    if(action == 'create'){
                        $("#titleModal").html("Create New");
                        $("#colposition").addClass('d-none');
                        $.ajax({
                            url: "{{ url('/package/create') }}",
                            type: 'GET',
                        })
                        .done(function(data) {
                            $("#modalForm").modal('show');
                            $("#contentForm").html(data);
                            afterpreloader();
                        })
                        .fail(function() {
                            Swal.fire("Ops!", "Load data failed.", "error");
                        });
                    }else{
                        $("#titleModal").html("Edit Data");
                        $.ajax({
                            url: "{{ url('/package') }}" +'/'+ id +'/edit',
                            type: 'GET',
                            dataType: 'html',
                        })
                        .done(function(data) {
                            $("#contentForm").html(data);
                            $("#modalForm").modal('show');
                            afterpreloader();
                        })
                        .fail(function() {
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
                            axios.get("{{ url('/package/delete') }}" +'/'+ id)
                                .then((response) => {
                                    let self = this;
                                    var notif = response.data;
                                    var getstatus = notif.status;
                                    if (getstatus == "success") {
                                        $("#modalForm").modal("hide");
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
                var url = "{{ url('/package/data') }}";
            } else {
                var url = "{{ url('/package') }}" + "/" + page;
            }
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
