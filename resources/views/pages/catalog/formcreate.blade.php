<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <div class="modal-body">
            <input type="hidden" id="id" name="id" class="form-control" />
            <input type="hidden" id="catalog_logo" name="catalog_logo" class="form-control" />
            <input type="hidden" id="background_header_image" name="background_header_image" class="form-control" />
            <div class="position-relative form-group">
                <div class="row">

                    @if(Auth::user()->id == 1)
                        <div class="col-md-4">
                            <label>Type</label>
                            <select id="catalog_type" name="catalog_type" class="custom-select" onchange="selectType()">
                                <option value="1">Resto</option>
                                <option value="2" {{ (!empty($getData) && $getData['catalog_type'] == 2) ? 'selected' : '' }} >Hotel</option>
                                <option value="3" {{ (!empty($getData) && $getData['catalog_type'] == 3) ? 'selected' : '' }} >Food Court</option>
                            </select>
                        </div>
                    @else
                        <input type="hidden" id="catalog_type" name="catalog_type" value="1" />
                    @endif

                    <input type="hidden" id="show_catalog" name="show_catalog" value="Open" />
                    <input type="hidden" id="feature" name="feature" value="Full" />
                    <input type="hidden" id="checkout_type" name="checkout_type" value="System" />
                    <input type="hidden" id="transfer_payment" name="transfer_payment" value="Y" />
                    <input id="payment_gateway" type="hidden" name="payment_gateway" value="N">
                    <input type="hidden" id="online_type" name="online_type" value="xendit_live" />
                </div>
            </div>
            
            @if(Auth::user()->id == 1)
                @if(!empty($catalog_list))
                <div id="div_hotel" class="{{ (!empty($getData) && $getData['catalog_type'] == 2) ? '' : 'd-none' }}">
                    <hr />
                    <p>
                        <b>
                            Catalog Menu on Hotel :
                        </b>
                    </p>

                    <div class="row">
                        @for($i=1; $i<=count($catalog_list); $i++)
                            <div class="col-md-3 col-sm-6">
                                <label>Menu #{{$i}}</label>
                                <select id="catalog_list_{{$i}}" name="catalog_list[]" class="custom-select">
                                        <option value="0">Not Used</option>
                                    @foreach($catalog_list as $key => $value)
                                        <option value="{{ $value->id }}"
                                            {{ (!empty($catalog_type) && isset($catalog_type[($i-1)]) && $catalog_type[($i-1)] == $value->id) ? 'selected' : '' }}
                                            >{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endfor
                    </div>
                </div>
                @endif
            @endif

            <hr />
            <p>
                <b>
                    General Information :
                </b>
            </p>
            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <label>Custom Domain</label>
                        <input type="text" id="custom_domain" name="custom_domain" class="form-control" placeholder="example.com" />
                        <div v-if="formErrors['custom_domain']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['custom_domain'][0] }}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label>Subdomain <sup class="text-danger">* (Required)</sup></label>
                        <input type="text" id="catalog_username" name="catalog_username" class="form-control" />
                        <div v-if="formErrors['catalog_username']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['catalog_username'][0] }}
                        </div>
                    </div>
                    <input type="hidden" id="domain" name="domain" value="scaneat.id" />

                    <div class="col-md-3 col-sm-6">
                        <label>Catalog Title <sup class="text-danger">* (Required)</sup></label>
                        <input type="text" id="catalog_title" name="catalog_title" class="form-control" />
                        <div v-if="formErrors['catalog_title']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['catalog_title'][0] }}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label>Catalog Logo <sup class="text-danger">* (Required, png only)</sup></label>
                        <input type="file" id="logo" name="logo" class="form-control" />
                        <div v-if="formErrors['logo']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['logo'][0] }}
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3 col-sm-6">
                        <label>Contact Phone <sup class="text-danger">* (Required)</sup></label>
                        <input type="text" id="phone_contact" name="phone_contact" class="form-control" />
                        <div v-if="formErrors['phone_contact']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['phone_contact'][0] }}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label>Contact Email <sup class="text-danger">* (Required)</sup></label>
                        <input type="text" id="email_contact" name="email_contact" class="form-control" />
                        <small class="text-danger">Email can not be changed after saving.</small>
                        <div v-if="formErrors['email_contact']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['email_contact'][0] }}
                        </div>
                    </div>
                    {{-- <div class="col-md-3 col-sm-6">
                        <label>Catalog Password <sup class="text-danger"></sup></label>
                        <input type="password" id="catalog_password" name="catalog_password" class="form-control" />
                        <div v-if="formErrors['catalog_password']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['catalog_password'][0] }}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label>Detail produk</label>
                        <select id="show_detail" name="show_detail" class="custom-select">
                            <option value="Y">Show</option>
                            <option value="N">Hide</option>
                        </select>
                    </div> --}}
                </div>
                {{-- <div class="row mt-3">
                    <div class="col-md-2">
                        <label>Get Customer Data</label>
                        <select id="customer_data" name="customer_data" class="custom-select">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                        <div v-if="formErrors['customer_data']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['customer_data'][0] }}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label>Password Access</label>
                        <select id="password_access" name="password_access" class="custom-select">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                        <div v-if="formErrors['password_access']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['password_access'][0] }}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label>Catalog Tagline</label>
                        <input type="text" id="catalog_tagline" name="catalog_tagline" class="form-control" />
                        <div v-if="formErrors['catalog_tagline']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['catalog_tagline'][0] }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label>Catalog Key</label>
                        <input type="text" id="catalog_key" name="catalog_key" class="form-control" readonly />
                        <div v-if="formErrors['catalog_key']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['catalog_key'][0] }}
                        </div>
                    </div>

                </div> --}}
            </div>
            <br>
            <p>
                <b>
                    New Catalog needs +- 10 minutes for verifiying process. Then you can edit catalog for completing catalog data.
                </b>
            </p>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="btn_save_catalog">Save Data</button>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
    });

    new Vue({
        el: "#wrapVue",
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
        },
        methods: {
            submitForm: function (e) {
                submitForm();
                $('.errormsg').hide();

                $("#btn_save_catalog").html('Saving data...')
                $("#btn_save_catalog").prop('disabled', true)

                var form = e.target || e.srcElement;
                if($("#id").val() > 0){
                    $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                    var action = "{{ route('catalog.update',0) }}";
                    var put = form.querySelector('input[name="_method"]').value;
                }else{
                    $("input[name=_method]").remove();
                    var action = "{{ route('catalog.store') }}";
                    var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                var arrsliders=[];
                $("input:checkbox[name*=sliders]:checked").each(function(){
                    arrsliders.push($(this).val());
                });

                var arrmetode=[];
                $("input:checkbox[name*=metode]:checked").each(function(){
                    arrmetode.push($(this).val());
                });

                var arrsteps=[];
                $("input:checkbox[name*=steps]:checked").each(function(){
                    arrsteps.push($(this).val());
                });

                var arr_catalog_list=[];
                $("[name*=catalog_list]").each(function(){
                    arr_catalog_list.push($(this).val());
                });

                var arrpayment_mehod=[];
                $("input:checkbox[name*=payment_method]:checked").each(function(){
                    arrpayment_mehod.push($(this).val());
                });

                // if(arrpayment_mehod.length == 0){
                //     @if(!empty($package) && $package['package_id']=='2')
                //         toastr.error('Online Payment must be checked.');
                //         $("input:checkbox[name*=payment_method]").focus();
                //         afterSubmitForm();
                //         return false;
                //     @endif
                // }

                var arr_pay_opt=[];
                $("input:checkbox[name*=pay_opt]:checked").each(function(){
                    arr_pay_opt.push($(this).val());
                });

                var arr_prices=[];
                $("input:checkbox[name*=prices]:checked").each(function(){
                    arr_prices.push($(this).val());
                });
                
                let datas = new FormData();
                datas.append("catalog_list", arr_catalog_list);
                datas.append("pay_opt", arr_pay_opt);
                datas.append("prices", arr_prices);
                datas.append("catalog_type", $("#catalog_type").val());
                datas.append("packageid", "{{ !empty($package) ? $package['package_id'] : '' }}");
                datas.append("id", $("#id").val());
                datas.append("catalog_logo", $("#catalog_logo").val());
                datas.append("background_header_image", $("#background_header_image").val());
                datas.append("background_header_image", $("#background_header_image").val());
                datas.append("domain", $("#domain").val());
                datas.append("catalog_username", $("#catalog_username").val());
                datas.append("custom_domain", $("#custom_domain").val());
                datas.append("catalog_title", $("#catalog_title").val());
                datas.append("catalog_tagline", $("#catalog_tagline").val());
                datas.append("catalog_key", $("#catalog_key").val());
                datas.append('logo', document.getElementById('logo').files[0]);
                datas.append("phone_contact", $("#phone_contact").val());
                datas.append("email_contact", $("#email_contact").val());
                datas.append("distance", $("#distance").val());
                datas.append("lat", $("#lat").val());
                datas.append("long", $("#long").val());
                datas.append("catalog_address", $("#searchInput").val());
                datas.append("show_detail", $("#show_detail").val());
                datas.append("background_color", $("#background_color").val());
                // datas.append('catalogbg', document.getElementById('catalogbg').files[0]);
                // datas.append("layout", $("#layout").val());
                datas.append("show_notification", $("#show_notification").val());
                datas.append("theme_color", $("#theme_color").val());
                datas.append("show_catalog", $("#show_catalog").val());
                datas.append("sliders", arrsliders);
                datas.append("metode", arrmetode);
                datas.append("steps", arrsteps);
                datas.append("catalog_password", $("#catalog_password").val());
                datas.append("feature", $("#feature").val());
                datas.append("customer_data", $("#customer_data").val());
                datas.append("password_access", $("#password_access").val());

                datas.append("bank_id", $("#bank_id").val());
                datas.append("bank_account_number", $("#bank_account_number").val());
                datas.append("bank_account_name", $("#bank_account_name").val());
                
                @if(!empty($package) && $package['package_id']=='2')
                    datas.append("wa_number", $("#wa_number").val());
                    datas.append("wa_show_item", $("#wa_show_item").val());
                    datas.append("wa_show_cart", $("#wa_show_cart").val());
                    datas.append("checkout_type", $("#checkout_type").val());
                    datas.append("advance_payment", $("#advance_payment").val());
                    datas.append("delivery_option", $("#delivery_option").val());
                    datas.append("transfer_payment", $("#transfer_payment").val());
                    datas.append("bank_info", $("#bank_info").val());
                    datas.append("payment_gateway", $("#payment_gateway").val());
                    datas.append("tax", $("#tax").val());
                    datas.append("client_key", $("#client_key").val());
                    datas.append("server_key", $("#server_key").val());
                    datas.append("payment_mehod", arrpayment_mehod);
                    datas.append("online_type", $("#online_type").val());
                @endif

                axios.post(action, datas, {
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "X-HTTP-Method-Override": put,
                            Accept: "application/json",
                        },
                    })
                    .then((response) => {
                        $("#btn_save_catalog").html('Save Data')
                        $("#btn_save_catalog").prop('disabled', false)
                        let self = this;
                        var notif = response.data;
                        var getstatus = notif.status;
                        if (getstatus == "success") {
                            $("#modalForm").modal("hide");
                            afterSubmitForm();
                            loadView();
                        }else{
                            afterSubmitForm();
                            toastr.error(notif.message);
                        }
                    })
                    .catch((error) => {
                        $("#btn_save_catalog").html('Save Data')
                        $("#btn_save_catalog").prop('disabled', false)

                        var formErrors = error.response.data.errors;
                        let [first] = Object.keys(formErrors)
                        toastr.error(formErrors[first][0]);

                        afterSubmitForm();
                        $('.errormsg').show();
                        // this.formErrors = error.response.data.errors;
                    });
                
            },
        },
    });
    $(document).ready(function() {
        $('#pay_opt1').change(function () {
            if(this.checked) {
                $('.delivery_options').removeClass('d-none');
            }else{
                $('.delivery_options').addClass('d-none');
            }
        });

        $("#myForm")[0].reset();
        $('.colorpicker').simpleColor({ hideInput: false, inputCSS: { 'border-style': 'dashed','margin-bottom':'5px' } });
        $('.simpleColorDisplay').css({'width':'25px','height':'25px','position':'absolute','right':'5px','top':'-35px','border-radius':'50px'});

        @if(!empty($getData))
            $("#id").val("{{ $getData['id'] }}");
            $("#feature").val("{{ $getData['feature'] }}");
            $("#customer_data").val("{{ $getData['customer_data'] }}");
            $("#password_access").val("{{ $getData['password_access'] }}");
            selectFeature();
            $("#show_catalog").val("{{ $getData['show_catalog'] }}");
            $("#custom_domain").val("{{ $getData['custom_domain'] }}");
            $("#catalog_username").val("{{ $getData['catalog_username'] }}");
            $("#domain").val("{{ $getData['domain'] }}");
            $("#catalog_title").val("{{ $getData['catalog_title'] }}");
            $("#catalog_logo").val("{{ $getData['catalog_logo'] }}");
            $("#phone_contact").val("{{ $getData['phone_contact'] }}");

            $("#email_contact").prop('readonly', false);
            $("#email_contact").val("{{ $getData['email_contact'] }}");
            if($("#email_contact").val()){
                $("#email_contact").prop('readonly', true);
            }

            $("#show_detail").val("{{ $getData['show_detail'] }}");
            $("#catalog_tagline").val("{{ $getData['catalog_tagline'] }}");
            $("#catalog_key").val("{{ $getData['catalog_key'] }}");
            $("#distance").val("{{ $getData['distance'] }}");
            $("#lat").val("{{ $getData['lat'] }}");
            $("#long").val("{{ $getData['long'] }}");

            $("#background_color").val("{{ $getData['background_header_color'] }}");
            $("#background_header_image").val("{{ $getData['background_header_image'] }}");
            $("#show_notification").val("{{ $getData['show_notification'] }}");
            $("#theme_color").val("{{ $getData['theme_color'] }}");

            var bgheadercolor = "{{ $getData['background_header_color'] }}";
            $('#background_color_wrap .simpleColorDisplay').css({'background':bgheadercolor,'width':'25px','height':'25px','position':'absolute','right':'5px','top':'-35px','border-radius':'50px'});
            var themecolor = "{{ $getData['theme_color'] }}";
            $('#theme_color_wrap .simpleColorDisplay').css({'background':themecolor,'width':'25px','height':'25px','position':'absolute','right':'5px','top':'-35px','border-radius':'50px'});
            
            $("#searchInput").val("{{ $getData['catalog_address'] }}");
            selectType();
            selectAdvance();
            // $("#layout").val("{{ $getData['layout'] }}");

            $("#bank_id").val("{{ $getData['bank_id'] }}").trigger('change');;
            $("#bank_account_number").val("{{ $getData['bank_account_number'] }}");
            $("#bank_account_name").val("{{ $getData['bank_account_name'] }}");

            @if($package['package_id']=='2')
                $("#checkout_type").val("{{ $getData['checkout_type'] }}");
                $("#tax").val("{{ $getData['tax'] }}");
                $("#transfer_payment").val("{{ $getData['transfer_payment'] }}");
                $("#payment_gateway").val("{{ $getData['payment_gateway'] }}");
                $("#client_key").val("{{ $getData['client_key'] }}");
                $("#server_key").val("{{ $getData['server_key'] }}");
                $("#advance_payment").val("{{ $getData['advance_payment'] }}");
                $("#bank_info").val("{{ $getData['bank_info'] }}");
                $("#wa_number").val("{{ $getData['wa_number'] }}");
                $("#wa_show_item").val("{{ $getData['wa_show_item'] }}");
                $("#wa_show_cart").val("{{ $getData['wa_show_cart'] }}");
                $("#online_type").val("{{ $getData['online_type'] }}");
            @endif
        @endif
    });
    function resetTheme(){
        var themecolor = "#fb5849";
        $("#theme_color").val(themecolor);
        $('#theme_color_wrap .simpleColorDisplay').css({'background':themecolor,'width':'25px','height':'25px','position':'absolute','right':'5px','top':'-35px','border-radius':'50px'});
    }
    function getLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
      }
    }
    function showPosition(position) {
      $("#lat").val(position.coords.latitude);
      $("#long").val(position.coords.longitude);
    }
    function showError(error) {
      switch(error.code) {
        case error.PERMISSION_DENIED:
            Swal.fire("Ops!", "User denied the request for Geolocation.", "error");
            break;
        case error.POSITION_UNAVAILABLE:
            Swal.fire("Ops!", "Location information is unavailable.", "error");
            break;
        case error.TIMEOUT:
            Swal.fire("Ops!", "The request to get user location timed out.", "error");
            break;
        case error.UNKNOWN_ERROR:
            Swal.fire("Ops!", "An unknown error occurred.", "error");
            break;
      }
    }
    function selectFeature(){
        let feature = $("#feature").val();
        if(feature == 'Full'){
            $("#advanceOnly").removeClass('d-none');
        }else{
            $("#advanceOnly").addClass('d-none');
        }
    }
    function selectType(){
        let feature = $("#catalog_type").val();
        if(feature == 2){
            $("#div_hotel").removeClass('d-none');
        }else{
            $("#div_hotel").addClass('d-none');
        }
    }
    function selectAdvance(){
        let val = $("#advance_payment").val();
        if(val == "Y"){
            $('.pre_pay_opt').removeClass('d-none');
        }
        else{
            $('.pre_pay_opt').addClass('d-none');
        }
    }
</script>
<style>
    .simpleColorChooser {
        z-index: 9999;
    }
</style>
