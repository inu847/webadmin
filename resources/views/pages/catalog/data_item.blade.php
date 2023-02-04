@extends('layouts.main')
@section('customcss')
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
                {{ $maintitle }} ( {{ $catalog['catalog_title'] }} )
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ route('catalog.index') }}" class="btn-shadow btn btn-success btn-sm"><i class="icon lnr-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="page-content browse container-fluid p-0">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3 card">
                <div class="card-header card-header-tab-animation">
                    <ul class="nav nav-justified">
                        <li class="nav-item"><a href="{{ url('/catalog/items/'.$catalog->id) }}" class="nav-link">Item List</a></li>
                        <li class="nav-item"><a href="{{ url('/viewmenus/'.$catalog['id']) }}" class="nav-link">Item View</a></li>
                        <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-2" class="active nav-link">Item Price</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-eg115-0" role="tabpanel">

                        </div>
                        <div class="tab-pane" id="tab-eg115-1" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane active" id="tab-eg115-2" role="tabpanel">
                            <div id="indexVue">
                                <div class="row">
                                    <div class="col-md-12" style="min-height: 250px;">
                                        @include('blocks.skeleton') 
                                        <div id="loadpage"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('modal')
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div id="modalSize" class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Form Loader -->
                <!-- <div class="formLoader">
                    <div class="jumper">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div> -->
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

    <script type="text/javascript">
        $(document).ready(function() {
            loadView();
        });

        new Vue({
            el: "#indexVue",
            data() {
                return {
                    csrf: "",
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
            },
            methods: {
                showForm: function (action, id = null) {
                    // preloader();
                    // $("#modalSize").removeClass("modal-sm");
                    // $("#modalSize").addClass("modal-xl");
                    $("#titleModal").html("Manage Item Price");
                    $.ajax({
                        url: "{{ url('/catalog/manage_item_prices/'.$catalog['id']) }}/"+id, 
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
                },
            },
        });
    </script>
    <script type="text/javascript">
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
                var url = "{{ url('/catalog/dataPrice') }}";
            } else {
                var url = "{{ url('/catalog') }}" + "/" + page;
            }
            var obj = new Object();
            obj.searchCatalog = '{{ $catalog['id'] }}';
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
