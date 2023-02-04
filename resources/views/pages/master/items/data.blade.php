@extends('layouts.main')
@section('customcss')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<style>
    #myForm label{
        font-weight: bold;
    }
</style>
@endsection
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-layers icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ route('items.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
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
                    <div class="col-md-7 p-0 mb-3 d-flex flex-row-reverse">
                        <form class="form-inline">
                            <div class="position-relative form-group"><label for="searchfield" class="sr-only">Email</label><input name="searchfield" id="searchfield" placeholder="Type Keyword..." type="text" class="mr-2 form-control"></div>
                            <div class="position-relative form-group">
                                <label for="searchCatalog" class="sr-only">Password</label>
                                <select id="searchCatalog" name="searchCatalog" class="mr-2 form-control">
                                    <option value="">All Catalogs</option>
                                    @foreach($catalog as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" id="searchButton" class="btn btn-primary">Search</button>
                        </form>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="formLoader">
                    <div class="jumper">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
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

    @include('pages.master.items.form_gallery')
    @include('pages.master.items.form_update')

    <!-- MODAL QRCode -->
    <div class="modal fade" id="modal-qrcode" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelTitleId">Scan Barcode</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Keluar">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="qr-reader" style="width:500px"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-qrcode_update" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelTitleId">Scan Barcode</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Keluar">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="qr-reader_update" style="width:500px"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('customjs')
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
    <script type="text/javascript" src="https://www.jqueryscript.net/demo/color-picker-predefined-palette/jquery.simple-color.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
    <!-- <script src="https://unpkg.com/html5-qrcode"></script> -->
    <!-- include the library -->
    <script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            loadView();
            $('.colorpicker').simpleColor({ hideInput: false, inputCSS: { 'border-style': 'dashed','margin-bottom':'5px' } });
            $('.simpleColorDisplay').css({'width':'25px','height':'25px','position':'absolute','right':'5px','top':'-35px','border-radius':'50px'});
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
                    $(".errormsg").hide();
                    if (action == "create") {
                        // $("#myForm")[0].reset();
                        $("#titleModal").html("Create New");
                        $("#colposition").addClass('d-none');

                        $.ajax({
                            url: "{{ url('/items/create') }}",
                            type: 'GET',
                        })
                        .done(function(data) {
                            $("#modalForm").modal('show');
                            $("#contentForm").html(data);
                            afterpreloader();
                        })
                        .fail(function() {
                            Swal.fire("Ops!", "Load data failed.a", "error");
                        });

                        // $("#titleModal").html("Create New");
                        // $("input[name=_method]").remove();
                        // $("#modalForm").modal("show");
                    } else {
                        // preloader();
                        $("#titleModal_update").html("Edit Data");
                        $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                        axios
                            .get("{{ url('/items') }}" +'/'+ id)
                            .then((response) => {
                                var data = response.data.data;
                                var catalog = response.data.catalog;
                                console.log(catalog);
                                $("#modalForm_update").modal("show");
                                $("#myForm #id").val(data.id);
                                $("#myForm #item_image_one").val(data.item_image_one);
                                $("#myForm #item_image_two").val(data.item_image_two);
                                $("#myForm #item_image_three").val(data.item_image_three);
                                $("#myForm #item_image_four").val(data.item_image_four);
                                $("#myForm #item_image_primary").val(data.item_image_primary);
                                $("#myForm #items_name").val(data.items_name);
                                $("#myForm #items_price").val(data.items_price);
                                $("#myForm #hpp").val(data.hpp);
                                $("#myForm #stock").val(data.stock?data.stock:0);
                                $("#myForm #hitung_stok").val(data.hitung_stok?data.hitung_stok:0).trigger('change');
                                $("#myForm #centered_stock").val(data.centered_stock?data.centered_stock:0).trigger('change');
                                $("#myForm #items_discount").val(data.items_discount);
                                $("#myForm #items_youtube").val(data.items_youtube);
                                $("#myForm #items_color").val(data.items_color);
                                $("#myForm #items_description").html(CKEDITOR.instances.items_description.setData(data.items_description));

                                $("#myForm input:checkbox[name*=catalogs]").prop( "checked", false );
                                $.each(catalog, function(index, value) {
                                    $("#myForm #catalog"+value).prop( "checked", true );
                                });

                                afterpreloader();
                            })
                            .catch(function (error) {
                                // console.log(error);
                                Swal.fire("Ops!", "Load data failed.b", "error");
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
                            axios.get("{{ url('/items/delete') }}" +'/'+ id)
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
                                    Swal.fire("Ops!", "Load data failed.c", "error");
                                    afterPreloadContent();
                                });
                        }
                    });
                },
            },
        });
    </script>
    <script type="text/javascript">
        // $("#searchfield").on("keyup", function (e) {
        //     loadView();
        // });
        $("#searchButton").on("click", function (e) {
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
                var url = "{{ url('/items/data') }}";
            } else {
                var url = "{{ url('/items') }}" + "/" + page;
            }
            var obj = new Object();
            obj.searchfield = $("#searchfield").val();
            obj.searchCatalog = $("#searchCatalog").val();
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
        function loadGallery(id){
            $("#contentGallery").html("Please wait...");
            $.ajax({
                url: "{{ url('/items/gallery') }}" +'/'+ id,
                type: 'GET',
                dataType: 'html',
            })
            .done(function(data) {
                $("#titleModalGallery").html("Gallery : "+id);
                $("#modalGallery").modal('show');
                $("#contentGallery").html(data);
            })
            .fail(function() {
                Swal.fire("Ops!", "Load data failed.d", "error");
            });
        }
        function setPrimary(image,id){
            $.ajax({
                url: "{{ url('/items/primaryimage') }}" +'/'+ id +'/'+ image,
                type: 'GET',
                dataType: 'html',
            })
            .done(function(data) {
                // window.location.reload();
                window.location.href = window.location.href;
            })
            .fail(function() {
                Swal.fire("Ops!", "Load data failed.e", "error");
            });
        }
        function deleteImage(image,id,position){
            Swal.fire({
              title: "Confirmation",
              text: "Do you want to remove this image?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Yes",
              cancelButtonText: "Cancel"
            }).then((result) => {
              if (result.value) {
                $.ajax({
                    url: "{{ url('/items/deleteimage') }}" +'/'+ id +'/'+ image +'/'+ position,
                    type: 'GET',
                    dataType: 'html',
                })
                .done(function(data) {
                    // window.location.reload();
                    window.location.href = window.location.href;
                })
                .fail(function() {
                    Swal.fire("Ops!", "Load data failed.f", "error");
                });
              }
            })
        }
    </script>
@endsection
