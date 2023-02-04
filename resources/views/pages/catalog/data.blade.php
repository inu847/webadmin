@extends('layouts.main')

@section('customcss')
    <style>
        #map-canvas {
            width: 100%;
            height: 400px;
        }
        .pac-container {
            z-index: 1051 !important;
        }
        span.select2-selection.select2-selection--single {
            height: calc(2px + 2.25rem);
            padding-top: 3px;
        }
    </style>

@endsection

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-display2 icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers..</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ route('catalog.index') }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
        </div>
    </div>
</div>

<div id="indexVue">
    <div class="tabs-animation">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between">
                    <div>
                        @if($total < Auth::user()->number_catalog)
                            <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> 
                                Create New 
                                @if(Auth::user()->number_catalog > 1)
                                    ( {{ $total.' of '.Auth::user()->number_catalog }} )
                                @endif
                            </a>
                        @endif
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
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
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
    <script type="text/javascript" src="https://www.jqueryscript.net/demo/color-picker-predefined-palette/jquery.simple-color.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <!-- Load google API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCGi7qiEe7zRoSi6_NAsfTBnS06qvbzptY&libraries=places" async defer></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function initialize(myCenter, with_input) {
            var marker = new google.maps.Marker({
                position: myCenter,
                draggable: true,
                anchorPoint: new google.maps.Point(0, -29)
            });

            var mapProp = {
                center: myCenter,
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scrollwheel: false
            };

            map = new google.maps.Map(document.getElementById("map-canvas"), mapProp);
            marker.setMap(map);

            var input = document.getElementById('searchInput');
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);
            autocomplete.setFields(["place_id", "geometry", "name", "formatted_address"]);
			//	map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            var infowindow = new google.maps.InfoWindow();   
            const infowindowContent = document.getElementById("infowindow-content");
            
            var geocoder = new google.maps.Geocoder();
            marker.addListener("click", () => {
                infowindow.open(map, marker);
            });

            if(with_input == 1){
                showTooltip(infowindow,marker,input.value);
            }

            autocomplete.addListener('place_changed', function() {
                infowindow.close();
                // marker.setVisible(false);
                var place = autocomplete.getPlace();
                if (!place.place_id) {
                    window.alert("Please select from the results.");
                    return;
                }
            
                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                
                bindDataToForm(place.formatted_address,place.geometry.location.lat(),place.geometry.location.lng());
                // infowindow.setContent(place.formatted_address);
                infowindowContent.children["place-name"].textContent = place.name;
                infowindowContent.children["place-address"].textContent = place.formatted_address;
                infowindow.setContent(infowindowContent);

                infowindow.open(map, marker);
                showTooltip(infowindow,marker,place.formatted_address);
            });
            
            google.maps.event.addListener(marker, 'dragend', function() {
                geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {        
                            bindDataToForm(results[0].formatted_address,marker.getPosition().lat(),marker.getPosition().lng());
                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(map, marker);
                            showTooltip(infowindow,marker,results[0].formatted_address);
                            document.getElementById('searchInput').value = results[0].formatted_address;
                        }
                    }
                });
            });
        };

        function bindDataToForm(address,lat,lng){
            // document.getElementById('autocomplete').value = address;
            //document.getElementById('location').value = address;
            document.getElementById('lat').value = lat;
            document.getElementById('long').value = lng;
        }

        function showTooltip(infowindow,marker,address){
            google.maps.event.addListener(marker, 'click', function() { 
                infowindow.setContent(address);
                infowindow.open(map, marker);
            });
        }

        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                return false;
                }
            });
        });

        $(document).ready(function() {
            var element = $(this);
            var map;

            $('#modalForm').on('shown.bs.modal', function(e) {
                var element = $(e.relatedTarget);
                // var lat = $("#lat").val();
                // var long = $("#long").val();
                // var latlng = new google.maps.LatLng(lat, long);
                // initialize(latlng, 1);
                // google.maps.event.trigger(map, 'resize');
            });

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
                $(document).on("click", ".edittype", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.showForm("type", id);
                });
                $(document).on("click", ".deletelink", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.confirmDialog(id);
                });
                $(document).on("click", ".btn-otp", function (evetn, id) {
                    var id = $(this).attr("data-id");
                    self.showForm("otp", id);
                });
                $(document).on("click", "#btn_send_otp", function (evetn, id) {
                    var id = $('#myForm #id').val();
                    $('#myForm .div_loading_otp').removeClass('d-none');
                    $('#myForm #btn_send_otp').prop('disabled', true);
                    self.showForm("send_otp", id);
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
                            url: "{{ url('/catalog/create') }}",
                            type: 'GET',
                        })
                        .done(function(data) {
                            $("#modalForm").modal('show');
                            $("#contentForm").html(data);
                            afterpreloader();

                            setTimeout(() => {
                                $("#catalog_password").val('');
                                $("#catalog_username").val('');
                            }, 200);

                        })
                        .fail(function() {
                            Swal.fire("Ops!", "Load data failed.", "error");
                        });
                    }
                    else if(action == 'otp'){
                        $("#titleModal").html("OTP Validation");
                        $("#colposition").addClass('d-none');
                        
                        $.ajax({
                            url: "{{ url('/catalog/otp') }}?id="+id,
                            type: 'GET',
                        })
                        .done(function(data) {
                            $("#modalForm").modal('show');
                            $("#contentForm").html(data);
                            $('#myForm #id').val(id);
                            afterpreloader();
                        })
                        .fail(function() {
                            $('#myForm .div_loading_otp').addClass('d-none');
                            $('#myForm #btn_send_otp').prop('disabled', false);
                            Swal.fire("Ops!", "Load data failed.", "error");
                        });
                    }
                    else if(action == 'send_otp'){
                        $("#colposition").addClass('d-none');
                        
                        $.ajax({
                            url: "{{ url('/catalog/send_otp') }}?id="+id,
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
                    }
                    else if(action == 'type'){
                        $("#titleModal").html("Edit Catalog Type");
                        $.ajax({
                            url: "{{ url('/catalog') }}" +'/'+ id +'/edit',
                            type: 'GET',
                            dataType: 'html',
                        })
                        .done(function(data) {
                            $("#modalForm").modal('show');
                            $("#contentForm").html(data);
                            $("#modalForm").modal('show');
                            afterpreloader();
                        })
                        .fail(function() {
                            Swal.fire("Ops!", "Load data failed.", "error");
                        });
                    }
                    else{
                        $("#titleModal").html("Edit Data");
                        $.ajax({
                            url: "{{ url('/catalog') }}" +'/'+ id +'/edit',
                            type: 'GET',
                            dataType: 'html',
                        })
                        .done(function(data) {
                            $("#modalForm").modal('show');
                            $("#contentForm").html(data);
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
                            axios.get("{{ url('/catalog/delete') }}" +'/'+ id)
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
                var url = "{{ url('/catalog/data') }}";
            } else {
                var url = "{{ url('/catalog') }}" + "/" + page;
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
