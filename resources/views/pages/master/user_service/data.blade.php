@extends('layouts.main')
@section('customcss')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endsection
@section('content')
<style>
	div.bootstrap-datetimepicker-widget.dropdown-menu{
		font-size: 0.8rem;
	}
	
	div.datepicker-days{
		padding-left: 13px;
		padding-top: 13px;
		padding-bottom: 13px;
	}

	div.timepicker.col-md-6 div.timepicker-picker table.table-condensed tbody tr td,
	div.timepicker.col-md-6 div.timepicker-picker table.table-condensed tbody tr td a.btn i,
	div.timepicker.col-md-6 div.timepicker-picker table.table-condensed tbody tr td span
	{
		height: 30px !important;
		line-height: 30px !important;
		width: 30px;
	}
    .closeText:before {
        font-weight: bold;
        content: "X Close";
        font-style: normal;
        color: red;
    }
</style>

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
            <a href="{{ route('user_service.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
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
    @include('pages.master.user_service.form')
@endsection

@section('customjs')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('build/css/bootstrap-datetimepicker.min.css') }}">
    <script src="{{ asset('build/js/bootstrap-datetimepicker.min.js') }}"></script>

    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
    <script type="text/javascript" src="https://www.jqueryscript.net/demo/color-picker-predefined-palette/jquery.simple-color.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script type="text/javascript">
    	$(function () {
            $('#handled_on').datetimepicker({
                sideBySide: true,
                format: 'DD-MM-YYYY HH:mm',
                minDate: moment(),
                toolbarPlacement: 'bottom',
                showClose: true,
                icons: {
                    close: 'closeText'
                },
                // debug: true
            });
        });

        $(document).ready(function() {  
            CKEDITOR.replace('job_result');

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
                            .get("{{ url('/user_service') }}" +'/'+ id)
                            .then((response) => {
                                var data = response.data;
                                $("#modalForm").modal("show");
                                $("#id").val(data.id);
                                $("#customer").val(data.customer_name);
                                $("#phone").val(data.customer_phone);
                                $("#title").val(data.title);
                                $("#room").val(data.room);
                                $("#description").val(data.description);
                                $("#handler").val(data.handler);
                                $("#lunas").val(data.lunas).trigger('change');
                                $("#handled_on").val(data.handled_on);
                                CKEDITOR.instances['job_result'].setData(data.job_result);
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
                            axios.get("{{ url('/user_service/delete') }}" +'/'+ id)
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
                var url = "{{ url('/user_service/data') }}";
            } else {
                var url = "{{ url('/user_service') }}" + "/" + page;
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
                url: "{{ url('/user_service/gallery') }}" +'/'+ id,
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
    </script>
@endsection
