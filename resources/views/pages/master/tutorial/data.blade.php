@extends('layouts.main')

@section('customcss')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
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
            <a href="{{ route('tutorial.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
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
    @include('pages.master.tutorial.form')
@endsection

@section('customjs')
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
    <script type="text/javascript" src="https://www.jqueryscript.net/demo/color-picker-predefined-palette/jquery.simple-color.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script type="text/javascript">
        $(document).ready(function() {  
            CKEDITOR.replace('description');
            $('#modalForm').on('shown.bs.modal', function() {
                $(document).off('focusin.modal');
            });
        });

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
                    $("#myForm")[0].reset();
                    if (action == "create") {
                        $("#titleModal").html("Create New");
                        $("input[name=_method]").remove();
                        $("#modalForm").modal("show");
                    } else {
                        // preloader();
                        $("#titleModal").html("Edit Data");
                        $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                        axios
                            .get("{{ url('/tutorial') }}" +'/'+ id)
                            .then((response) => {
                                var data = response.data;
                                $("#modalForm").modal("show");
                                $("#id").val(data.id);
                                $("#item_image").val(data.image);
                                $("#title").val(data.title);
                                // $("#description").html(data.description);
                                CKEDITOR.instances['description'].setData(data.description);
                                $("#video").val(data.video);
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
                            axios.get("{{ url('/tutorial/delete') }}" +'/'+ id)
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
                var url = "{{ url('/tutorial/data') }}";
            } else {
                var url = "{{ url('/tutorial') }}" + "/" + page;
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
        function loadGallery(id){
            $("#contentGallery").html("Please wait...");
            $.ajax({
                url: "{{ url('/tutorial/gallery') }}" +'/'+ id,
                type: 'GET',
                dataType: 'html',
            })
            .done(function(data) {
                $("#titleModalGallery").html("Gallery : "+id);
                $("#modalGallery").modal('show');
                $("#contentGallery").html(data);
            })
            .fail(function() {
                Swal.fire("Ops!", "Load data failed.", "error");
            });
        }
        function setPrimary(image,id){
            $.ajax({
                url: "{{ url('/tutorial/primaryimage') }}" +'/'+ id +'/'+ image,
                type: 'GET',
                dataType: 'html',
            })
            .done(function(data) {
                // window.location.reload();
                window.location.href = window.location.href;
            })
            .fail(function() {
                Swal.fire("Ops!", "Load data failed.", "error");
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
                    url: "{{ url('/tutorial/deleteimage') }}" +'/'+ id +'/'+ image +'/'+ position,
                    type: 'GET',
                    dataType: 'html',
                })
                .done(function(data) {
                    // window.location.reload();
                    window.location.href = window.location.href;
                })
                .fail(function() {
                    Swal.fire("Ops!", "Load data failed.", "error");
                });
              }
            })
        }
    </script>
@endsection
