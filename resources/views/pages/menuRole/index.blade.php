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
                    <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('role.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
            </div>
        </div>
    </div>
    
    <div id="indexVue">
        <div class="tabs-animation">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div>
                            <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> 
                                Create New 
                            </a>
                        </div>
                        <div class="col-12 col-md-3 p-0 mb-3">
                            <input id="searchfield" type="text" class="form-control" placeholder="Search..." />
                        </div>
                    </div>
                    <div class="main-card mb-3 card" style="min-height: 250px;">
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Menu</th>
                                        <th>Roles</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($menus as $key => $menu)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $menu->menu->name }}</td>
                                            <td>{{ $menu->role->name }}</td>
                                            <td>{{ $menu->status }}</td>
                                            <td>
                                                {{-- <a href="javascript:void(0)" data-id="{{ $menu->id }}" class="detaillink btn btn-shadow btn-primary">Detail</a> --}}
                                                <a href="javascript:void(0)" data-id="{{ $menu->id }}" class="editlink btn btn-shadow btn-info">Edit</a>
                                                <a href="javascript:void(0)" data-id="{{ $menu->id }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
                                                {{-- <a href="{{ route('menu-roles.show', $menu->id) }}" class="btn btn-info"><i class="fa fa-list"></i></a>
                                                <a href="{{ route('menu-roles.edit', [$menu->id]) }}" class="btn btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                                <form
                                                    onsubmit="return confirm('Are you sure?')"
                                                    class="d-inline"
                                                    action="{{route('menu-roles.destroy', [$menu->id])}}"
                                                    method="POST">
                                                        @csrf
                                                        <input
                                                        type="hidden"
                                                        name="_method"
                                                        value="DELETE">
                                                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                                </form> --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-block justify-content-center card-footer">
                            <nav class="mt-3">
                                {{$menus->links()}}
                            </nav>
                        </div>
                    </div>
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
                    $("#modalSize").removeClass("modal-sm");
                    $("#modalSize").addClass("modal-xl");
                    if (action == "create") {
                        $("#titleModal").html("Create New");
                        $.ajax({
                            url: "{{ url('/menu-roles/create') }}", 
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
                    }else if (action == "edit") {
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
                            .get("{{ url('/menu-roles') }}" +"/" + id  )
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
            },
        });
    </script>
@endsection