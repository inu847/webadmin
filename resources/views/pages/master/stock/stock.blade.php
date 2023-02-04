@extends('layouts.main') @section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-box1 icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }} ( {{ (Session::get('catalogsession')=='All')?'Warehouse':getData::getCatalogSession('catalog_title') }} )
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ route('stock.index') }}" class="btn-shadow btn btn-success btn-sm"><i class="icon lnr-arrow-left"></i> Back</a>
            <a href="{{ url('/stock/stock/'.$item['id'].'/'.$item['catalog']) }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
        </div>
    </div>
</div>

<div id="indexVue">
    <div class="tabs-animation">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between">
                    <div>
                        <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> Add Stock</a>
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
@include('pages.master.stock.formstock') 
@endsection 

@section('customjs')
<script type="text/javascript" src="https://www.jqueryscript.net/demo/color-picker-predefined-palette/jquery.simple-color.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    $(document).ready(function () {
        loadView();
    });
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
            this.deletedata = "DELETE";
            let self = this;
            $(document).on("click", ".editlink", function (evetn, id) {
                var id = $(this).attr("data-id");
                self.showForm("edit", id);
            });
            $(document).on("click", ".deletelink", function (evetn, id) {
                var item = $(this).attr("data-id");
                var addon = $(this).attr("data-addon");
                self.confirmDialog(item,addon);
            });
        },
        methods: {
            showForm: function (action, id = null) {
                $(".errormsg").hide();
                $("#myForm")[0].reset();
                if (action == "create") {
                    $("#titleModal").html("Create New");
                    $("input[name=_method]").remove();
                    $("#myForm")[0].reset();
                    $("#modalForm").modal("show");
                    $("#wrappassword").css("visibility", "visible");
                } else {
                    preloader();
                    $("#titleModal").html("Edit Data");
                    $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                    $("#wrappassword").css("visibility", "hidden");
                    axios.get("{{ url('/stock/detail') }}" +'/'+ id)
                        .then((response) => {
                            var data = response.data;
                            $("#modalForm").modal("show");
                            $("#id").val(data.id);
                            $("#stock").val(data.stock);
                            $("#notes").val(data.notes);
                            afterpreloader();
                        })
                        .catch(function (error) {
                            Swal.fire("Ops!", "Load data failed.", "error");
                        });
                }
            },
            confirmDialog: function (item,addon) {
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
                        axios.get("{{ url('/stock/delete/stock') }}" + "/" + item + "/" + addon)
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
            var url = "{{ url('/stock/stock') }}" + "/{{ $item['id'] }}/{{ $item['catalog'] }}";
        } else {
            var url = "{{ url('/stock/stock') }}" + "/" + page;
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