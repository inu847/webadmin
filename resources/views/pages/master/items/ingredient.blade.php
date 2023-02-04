@extends('layouts.main') @section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-paint-bucket icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $item['items_name'] }} ( Ingredient )
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ route('items.index') }}" class="btn-shadow btn btn-success btn-sm"><i class="icon lnr-arrow-left"></i> Back</a>
            <a href="{{ \Request::url() }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
        </div>
    </div>
</div>

<div id="indexVue">
    <div class="tabs-animation">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between mb-3">
                    <div>
                        <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> Create New</a>
                    </div>
                    <div class="col-12 col-md-3 p-0 mb-3">
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
@include('pages.master.items.formingredient')
@endsection

@section('customjs')
<script type="text/javascript" src="https://www.jqueryscript.net/demo/color-picker-predefined-palette/jquery.simple-color.min.js"></script>
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
                var addon = $(this).attr("data-id");
                var item = $(this).attr("data-item");
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
                    $("#modalForm").modal("show");
                } else {
                    preloader();
                    $("#titleModal").html("Edit Data");
                    $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                    axios
                        .get("{{ url('/items/detailingredient') }}" +'/'+ id)
                        .then((response) => {
                            var data = response.data;
                            $("#modalForm").modal("show");
                            $("#id").val(data.id);
                            $("#ingredient_id").val(data.ingredient_id);
                            //$("#unit").val(data.item_unit);
                            $("#serving_size").val(data.serving_size);
                            afterpreloader();
                        })
                        .catch(function (error) {
                          Swal.fire("Ops!", "Load data failed.", "error");
                        });
                }
            },
            confirmDialog: function (item, addon) {
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
                        axios.get("{{ url('/items/delete/ingredient') }}" + "/" + item + "/" + addon)
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
            var url = "{{ url('/items/ingredient') }}"+"/{{ $item['id'] }}";
        } else {
            var url = "{{ url('/items/ingredient') }}" + "/" + page;
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
