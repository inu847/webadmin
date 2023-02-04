@extends('layouts.main')

@section('content')
    <div class="d-flex flex-wrap justify-content-between">
        <div>
            <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" onclick="create()"><i class="fa fa-plus"></i> 
                Create New 
            </a>
        </div>
        <div class="col-12 col-md-3 p-0 mb-3">
            <input id="searchfield" type="text" class="form-control" placeholder="Search..." />
        </div>
    </div>
    <div class='card'>
        <div class='card-header'>
            Detail Foodcourt
        </div>
        <div class='card-body'>
            <div class="main-card mb-3 card">
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $detail->name }}</td>
                        </tr>
                        <tr>
                            <th>Owner</th>
                            <td>{{ $detail->owner }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>{{ $detail->address }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="main-card mb-3 card">
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Catalog Logo</th>
                            <th>Name</th>
                            <th>Catalog Username</th>
                            <th>Catalog Name</th>
                            <th>Action</th>
                        </tr>
                        <tbody>
                            @foreach ($foodcourtsCatalog as $item)
                                <tr>
                                    <td><img src="{{ $item->catalog->catalog_logo }}" alt="" width="100px"></td>
                                    <td>
                                        @if ($item->catalog->custom_domain)
                                            <a href="{{ $item->catalog->custom_domain }}">
                                                {{$item->catalog->custom_domain}}
                                            </a>
                                        @else
                                            <a href="{{ 'https://'.$item->catalog->catalog_username.".".$item->catalog->domain }}">
                                                {{ 'https://'.$item->catalog->catalog_username.".".$item->catalog->domain }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ $item->catalog->catalog_username }}</td>
                                    <td>{{ $item->catalog->catalog_title }}</td>
                                    <td>
                                        <a href="javascript:void(0)" data-id="{{ $item->id }}" class="editlink btn btn-shadow btn-info">Edit</a>
                                        <a href="javascript:void(0)" data-id="{{ $item->id }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div id="modalSize" class="modal-dialog modal-xl" role="document">
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
        </div>
    </div>
</div>
@endsection 

@section('customjs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript" src="{{ url('js/jquery.domenu-0.0.1.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function create() {
            $("#titleModal").html("Create New");
            $.ajax({
                url: "{{ route('create.FoodcourtCatalog', [$detail->id]) }}", 
                type: "GET", 
            })
            .done(function(data) {
                $("#modalForm").modal("show");
                $("#contentForm").html(data);
                afterpreloader();
            })
            .fail(function() {
                Swal.fire("Ops!", "Load data failed.", "error");
            });
        }
    </script>
    <script type="text/javascript">
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
                $(document).on("click", ".editlink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.showForm("edit", id);
                });
                $(document).on("click", ".detaillink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.showForm("detail", id);
                });
                $(document).on("click", ".deletelink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.confirmDialog(id);
                });
            },
            methods: {
                showForm: function (action, id = null) {
                    // preloader();
                    console.log("TEST");
                    $("#modalSize").removeClass("modal-sm");
                    $("#modalSize").addClass("modal-xl");
                    if (action == "edit") {
                        preloader();
                        $("#titleModal").html("Edit Data");
                        $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                        $("#biodata1").css("display", "block");
                        $("#biodata2").css("display", "block");
                        $("#wrappassword").css("display", "none");
                        axios
                            .get("{{ url('/editFoodcourtCatalog') }}" +"/" + id +"/edit"  )
                            .then((response) => {
                                var data = response.data;
                                $("#modalForm").modal("show");
                                $("#contentForm").html(data);
                                afterpreloader();
                            })
                            .catch(function (error) {
                                Swal.fire("Ops!", "Load data failed.", "error");
                            });
                    }else if (action == "detail") {
                        preloader();
                        $("#titleModal").html("Detail Data");
                        $("#biodata1").css("display", "block");
                        $("#biodata2").css("display", "block");
                        $("#wrappassword").css("display", "none");
                        axios
                            .get("{{ url('/editFoodcourtCatalog') }}" +"/" + id  )
                            .then((response) => {
                                var data = response.data;
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
                            axios.delete("{{ url('destroyFoodcourtCatalog') }}" + "/" + id)
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
            },
        });
    </script>
@endsection