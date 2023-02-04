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
            <a href="{{ route('service.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
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
    @include('pages.master.service.form')
    
    <div id="wrapVueDetail">
        <form id="myFormDetail" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
            <div class="modal fade" id="modalFormDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
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
                            <h5 class="modal-title" id="titleModalDetail"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div id="modalContentDetail" class="modal-body">
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <!-- <button type="submit" class="btn btn-primary">Save Data</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customjs')
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
    <script type="text/javascript" src="https://www.jqueryscript.net/demo/color-picker-predefined-palette/jquery.simple-color.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {  
            $('#modalForm').on('shown.bs.modal', function() {
                CKEDITOR.replace('description');
                $(document).off('focusin.modal');
            });
            $('#modalFormDetail').on('shown.bs.modal', function() {
                // CKEDITOR.replace('description_detail', {
                //         height: 100,
                //     });
                // $(document).off('focusin.modal');
                $('#modalFormDetail .btn_add_detail').on('click', function() {
                    $('.list_detail').addClass('d-none');
                    $('.add_edit_detail').removeClass('d-none');
                });
                
                $('#modalFormDetail .btn_back_to_list').on('click', function() {
                    $('.add_edit_detail').addClass('d-none');
                    $('.list_detail').removeClass('d-none');
                    $("#id_detail").val('')
                    $("#item_image_detail").val('')
                });
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
                    } 
                    else {
                        // preloader();
                        $("#titleModal").html("Edit Data");
                        $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                        axios
                            .get("{{ url('/service') }}" +'/'+ id)
                            .then((response) => {
                                var data = response.data;
                                $("#modalForm").modal("show");
                                $("#id").val(data.id);
                                $("#item_image").val(data.image);
                                $("#title").val(data.title);
                                // $("#description").html(data.description);
                                CKEDITOR.instances['description'].setData(data.description);
                                afterpreloader();
                            })
                            .catch(function (error) {
                                axios
                                    .get("{{ url('/service') }}" +'/'+ id)
                                    .then((response) => {
                                        var data = response.data;
                                        $("#modalForm").modal("show");
                                        $("#id").val(data.id);
                                        $("#item_image").val(data.image);
                                        $("#title").val(data.title);
                                        // $("#description").html(data.description);
                                        CKEDITOR.instances['description'].setData(data.description);
                                        afterpreloader();
                                    })
                                    .catch(function (error) {
                                        Swal.fire("Ops!", "Load data failed.", "error");
                                    });
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
                            axios.get("{{ url('/service/delete') }}" +'/'+ id)
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
                var url = "{{ url('/service/data') }}";
            } else {
                var url = "{{ url('/service') }}" + "/" + page;
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
                url: "{{ url('/service/gallery') }}" +'/'+ id,
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
                url: "{{ url('/service/primaryimage') }}" +'/'+ id +'/'+ image,
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
                    url: "{{ url('/service/deleteimage') }}" +'/'+ id +'/'+ image +'/'+ position,
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
        function loadViewDetail(id = null, id_detail = null, image = null) {
            axios
                .get("{{ url('/service') }}" +'/'+ id+'?detail=1&id_detail='+id_detail)
                .then((response) => {
                    var data = response.data;
                    if(id_detail){
                    }
                    else{
                        $("#modalFormDetail").modal("show");
                    }
                    $("#modalContentDetail").html(data);

                    CKEDITOR.replace('description_detail', {
                        height: 100,
                    });
                    
                    $('#modalFormDetail .btn_add_detail').on('click', function() {
                        $('.list_detail').addClass('d-none');
                        $('.add_edit_detail').removeClass('d-none');
                    });
                    
                    $('#modalFormDetail .btn_back_to_list').on('click', function() {
                        $('.add_edit_detail').addClass('d-none');
                        $('.list_detail').removeClass('d-none');
                        $("#id_detail").val('')
                        $("#item_image_detail").val('')
                    });
                    
                    if(id_detail){
                        $('#modalFormDetail .btn_add_detail').click();
                        $("#id_detail").val(id_detail)
                        $("#item_image_detail").val(image)
                    }
                })
                .catch(function (error) {
                    Swal.fire("Ops!", "Load data failed.", "error");
                });
        }
    </script>

    <script type="text/javascript">
        new Vue({
            el: "#wrapVueDetail",
            data() {
                return {
                    csrf: "",
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
                    self.showFormDetail("detail", id);
                });
                $(document).on("click", ".editdetail", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    var service_id = $(this).attr("data-service_id");
                    var image = $(this).attr("data-image");
                    self.showFormDetail("edit", id, service_id, image);
                });
                $(document).on("click", ".deletedetail", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    var service_id = $(this).attr("data-service_id");
                    self.confirmDialog("delete", id, service_id);
                });
            },
            methods: {
                showFormDetail: function (action, id = null, service_id = null, image = null) {
                    $("#myFormDetail")[0].reset();
                    if (action == "detail") {
                        $("#titleModalDetail").html("Detail Data");
                        loadViewDetail(id);
                    }
                    else if (action == "edit") {
                        $("#titleModalDetail").html("Detail Data");
                        loadViewDetail(service_id, id, image);
                    }
                },
                submitForm: function (e) {
                    submitForm();
                    var form = e.target || e.srcElement;
                    var action = "{{ url('service') }}/"+$("#service_id").val();
                    var put = 'PUT';
                    var csrfToken = "{{ csrf_token() }}";

                    let datas = new FormData();
                    datas.append("_method", "PUT");
                    datas.append("id_detail", $("#id_detail").val());
                    datas.append("title_detail", $("#title_detail").val());
                    datas.append("item_image_detail", $("#item_image_detail").val());
                    datas.append("price", $("#price").val());
                    datas.append('image_detail', document.getElementById('image_detail').files[0]);
                    datas.append("description_detail", CKEDITOR.instances['description_detail'].getData());
                    
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
                                afterSubmitForm();
                                loadViewDetail($("#service_id").val());
                            }else{
                                afterSubmitForm();
                                toastr.error(notif.message);
                            }
                        })
                        .catch((error) => {
                            afterSubmitForm();
                            // $('.errormsg').show();
                            this.formErrors = error.response.data.errors;
                        });
                },
                confirmDialog: function (method_name, id, service_id) {
                    var id_detail = id;

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
                            $(".btn_delete_"+id_detail).html('Deleting...')

                            var action = "{{ url('service') }}/"+service_id;
                            var put = 'PUT';
                            var csrfToken = "{{ csrf_token() }}";

                            let datas = new FormData();
                            datas.append("_method", "PUT");
                            datas.append("delete", 1);
                            datas.append("id_detail", id_detail);

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
                                        loadViewDetail(service_id);
                                    }else{
                                        toastr.error(notif.message);
                                        $(".btn_delete_"+id_detail).html('Delete')
                                    }
                                })
                                .catch(function (error) {
                                    Swal.fire("Ops!", "Load data failed.", "error");
                                    $(".btn_delete_"+id_detail).html('Delete')
                                });
                        }
                    });
                },
            },
        });
    </script>
@endsection
