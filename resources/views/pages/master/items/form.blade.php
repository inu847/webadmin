<div id="wrapVue">
    <form id="myFormNew" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" class="form-control"/>
        <input type="hidden" id="item_image_one" name="item_image_one" class="form-control"/>
        <input type="hidden" id="item_image_two" name="item_image_two" class="form-control"/>
        <input type="hidden" id="item_image_three" name="item_image_three" class="form-control"/>
        <input type="hidden" id="item_image_four" name="item_image_four" class="form-control"/>
        <input type="hidden" id="item_image_primary" name="item_image_primary" class="form-control"/>

        <div id="modalContent" class="modal-body">

            <div class="card-header card-header-tab-animation">
                <ul class="nav nav-justified">
                    <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-0" class="active nav-link">Basic Data</a></li>
                    <!-- <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-1" class="nav-link">Price</a></li> -->
                    <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-2" class="nav-link">Display</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-eg115-0" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>SKU</label>
                                    <!-- <input type="text" id="item_sku" name="item_sku" class="form-control"/> -->

                                    <div class="input-group">
                                        <input type="text" value="" id="item_sku" name="item_sku" class="form-control">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary btn-open-model-qrcode"><i class="fa fa-qrcode"></i> Scan</button>
                                        </div>
                                    </div>
                                    <div v-if="formErrors['item_sku']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['item_sku'][0] }}
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Centered Stock</label>
                                    <select id="centered_stock" name="centered_stock" class="form-control">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <span v-if="formErrors['centered_stock']" class="errormsg">@{{ formErrors['centered_stock'][0] }}</span>
                                </div>
                            </div> -->
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Item Name <sup class="text-danger">* (Required)</sup></label>
                                    <input type="text" id="items_name" name="items_name" class="form-control"/>
                                    <div v-if="formErrors['items_name']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['items_name'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Main Image<sup class="text-danger">* (Required)</sup></label>
                                    <input type="file" id="imagefile_one_new" name="imagefile_one_new" class="form-control"/>
                                    <div v-if="formErrors['imagefile_one_new']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['imagefile_one_new'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 d-none">
                                <div class="position-relative form-group">
                                    <label>Available Item</label>
                                    <select id="ready_stock" name="ready_stock" class="form-control">
                                    <option value="Y">
                                        Yes
                                    </option>
                                    <option value="N">
                                        No
                                    </option>
                                    </select>
                                    <span v-if="formErrors['ready_stock']" class="errormsg">@{{ formErrors['ready_stock'][0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Default Price <sup class="text-danger">* (Required)</sup></label>
                                    <input type="text" id="items_price" name="items_price" class="form-control"/>
                                    <div v-if="formErrors['items_price']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['items_price'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Discount <sup class="text-danger">* (Required)</sup></label>
                                    <input type="text" id="items_discount" name="items_discount" class="form-control" value="0" />
                                    <div v-if="formErrors['items_discount']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['items_discount'][0] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>HPP</label>
                                    <input type="text" id="hpp" name="hpp" class="form-control"/>
                                    <div v-if="formErrors['hpp']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['hpp'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Centered Stock</label>
                                    <select id="centered_stock" name="centered_stock" class="form-control">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <span v-if="formErrors['centered_stock']" class="errormsg">@{{ formErrors['centered_stock'][0] }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row d-none">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Count Stock</label>
                                    <select name="hitung_stok" id="hitung_stok" class="form-control">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <div v-if="formErrors['hitung_stok']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['hitung_stok'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Stock</label>
                                    <input type="text" id="stock" name="stock" class="form-control" value="0"/>
                                    <div v-if="formErrors['stock']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['stock'][0] }}
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <input type="hidden" id="hitung_stok" name="hitung_stok" class="form-control" value="0"/>
                        <input type="hidden" id="stock" name="stock" class="form-control" value="0"/>

                        <div class="position-relative form-group">
                            <label for="myLabel" class="">Description</label>
                            <textarea id="items_description_new" name="items_description_new" class="form-control" rows="5"></textarea>
                            <div v-if="formErrors['items_description_new']" class="errormsg alert alert-danger mt-1">
                            @{{ formErrors['items_description_new'][0] }}
                            </div>
                        </div>
                    </div>
                    <!-- 
                    {{--
                    <div class="tab-pane" id="tab-eg115-1" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Variant Price</label>
                                    <div class="row">
                                        @foreach($price_types as $price)
                                        <div class="col-md-4">
                                            <div class="mt-2">
                                                <input type="checkbox" id="price{{ $price['id'] }}" name="prices[]" value="{{ $price['id'] }}" {{
                                                (getData::checkPriceCatalog($price['id'],(!empty($getData))?$getData['id']:0))?'checked':'' }} />
                                                <label style="font-weight:normal;" for="price{{ $price['id'] }}">{{ $price['price_name'] }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}
                    -->
                    <div class="tab-pane" id="tab-eg115-2" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label>Additional Image #1</label>
                                    <input type="file" id="imagefile_two_new" name="imagefile_two_new" class="form-control"/>
                                    <div v-if="formErrors['imagefile_two_new']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['imagefile_two_new'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label>Additional Image #2</label>
                                    <input type="file" id="imagefile_three_new" name="imagefile_three_new" class="form-control"/>
                                    <div v-if="formErrors['imagefile_three_new']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['imagefile_three_new'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label>Additional Image #3</label>
                                    <input type="file" id="imagefile_four_new" name="imagefile_four_new" class="form-control"/>
                                    <div v-if="formErrors['imagefile_four_new']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['imagefile_four_new'][0] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Youtube URL</label>
                                    <input type="text" id="items_youtube" name="items_youtube" class="form-control"/>
                                    <div v-if="formErrors['items_youtube']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['items_youtube'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Font Color</label>
                                    <input id="items_color" name="items_color" type="text" class="colorpicker form-control" value='#000' readonly />
                                    <div v-if="formErrors['items_color']" class="errormsg alert alert-danger mt-1">
                                    @{{ formErrors['items_color'][0] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                {{--
                                <!-- <div class="position-relative form-group">
                                    <label>Show in Catalog</label>
                                    <div class="row">
                                        @foreach($catalogs as $catalog)
                                        <div class="col-md-4">
                                            <div class="mt-2">
                                                <input type="checkbox" id="catalog{{ $catalog['id'] }}" name="catalogs[]" value="{{ $catalog['id'] }}" {{
                                                (getData::checkPriceCatalog($catalog['id'],(!empty($getData))?$getData['id']:0))?'checked':'' }} />
                                                <label style="font-weight:normal;" for="catalog{{ $catalog['id'] }}">{{ $catalog['catalog_title'] }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div> -->
                                --}}
                                <div class="position-relative form-group">
                                    <label style="font-weight: bold;">Show in Catalog</label>
                                    <div class="row">
                                        @foreach($catalog as $key => $value)
                                        <div class="col-md-6">
                                            <div class="mt-2">
                                                <input type="checkbox" id="catalog{{ $key }}" name="catalogs[]" value="{{ $key }}" />
                                                <label style="font-weight:normal;" for="catalog{{ $key }}">{{ $value }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Data</button>
        </div>
    </form>
</div>
<script type="text/javascript">

    $(document).ready(function(){
        activaTab('tab-eg115-0');
        CKEDITOR.replace('items_description_new');

        $('.btn-open-model-qrcode').click(function() {
            const formatsToSupport = [
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.AZTEC,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.CODE_93,
                Html5QrcodeSupportedFormats.ITF,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.DATA_MATRIX,
                Html5QrcodeSupportedFormats.RSS_14,
            ];

            const html5QrCode = new Html5Qrcode(
                "qr-reader", { formatsToSupport: formatsToSupport });

            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                html5QrCode.stop().then((ignore) => {
                    html5QrCode.clear();
                    $('#modalForm [name="item_sku"]').val(decodedText);
                    $('#modal-qrcode').modal('hide');
                }).catch((err) => {
                    // Stop failed, handle it.
                });
            };

            // const config = { fps: 30, qrbox: { width: 250, height: 250 }, aspectRatio: 1.333334 };
            const config = { fps: 10, qrbox: 250 };

            // If you want to prefer back camera
            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);

            $('#modal-qrcode').modal('show')
        });

        $('#modal-qrcode').on('show.bs.modal', function() {
            $('#modalForm').modal('hide');
        });

        $('#modal-qrcode').on('hidden.bs.modal', function() {
            $('#modalForm').modal('show');
        });
    });

    function activaTab(tab){
        $('.nav-justified a[href="#' + tab + '"]').tab('show');
    };

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
                var form = e.target || e.srcElement;
                if($("#myFormNew #id").val() > 0){
                  var action = "{{ route('items.update',0) }}";
                  var put = form.querySelector('input[name="_method"]').value;
                }else{
                  var action = "{{ route('items.store') }}";
                  var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                var arraddons=[];
                $("#myFormNew input:checkbox[name*=items]:checked").each(function(){
                    arraddons.push($(this).val());
                });

                // var prices=[];
                // $("input:checkbox[name*=prices]:checked").each(function(){
                //     prices.push($(this).val());
                // });

                var catalogs=[];
                $("#myFormNew input:checkbox[name*=catalogs]:checked").each(function(){
                    catalogs.push($(this).val());
                });

                let datas = new FormData();
                datas.append("id", $("#myFormNew #id").val());
                datas.append("item_image_one", $("#myFormNew #item_image_one").val());
                datas.append("item_image_two", $("#myFormNew #item_image_two").val());
                datas.append("item_image_three", $("#myFormNew #item_image_three").val());
                datas.append("item_image_four", $("#myFormNew #item_image_four").val());
                datas.append("item_image_primary", $("#myFormNew #item_image_primary").val());

                datas.append("items_name", $("#myFormNew #items_name").val());
                //datas.append("ready_stock", $("#myFormNew #ready_stock").val());
                datas.append("items_price", $("#myFormNew #items_price").val());
                datas.append("hpp", $("#myFormNew #hpp").val());
                datas.append("item_sku", $("#myFormNew #item_sku").val());
                datas.append("centered_stock", $("#myFormNew #centered_stock").val());
                datas.append("stock", $("#myFormNew #stock").val());
                datas.append("hitung_stok", $("#myFormNew #hitung_stok").val());
                datas.append("items_discount", $("#myFormNew #items_discount").val());
                // datas.append("items_description", $("#myFormNew #items_description").val());
                datas.append("items_description", CKEDITOR.instances['items_description_new'].getData());
                // datas.append("prices", prices);

                datas.append('imagefile_one', document.getElementById('imagefile_one_new').files[0]);
                datas.append('imagefile_two', document.getElementById('imagefile_two_new').files[0]);
                datas.append('imagefile_three', document.getElementById('imagefile_three_new').files[0]);
                datas.append('imagefile_four', document.getElementById('imagefile_four_new').files[0]);

                datas.append("items_youtube", $("#myFormNew #items_youtube").val());
                datas.append("items_color", $("#myFormNew #items_color").val());
                datas.append("catalogs", catalogs);
                datas.append("item_type", 'Main');

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
                            $("#modalForm").modal("hide");
                            afterSubmitForm();
                            loadView();
                        }else{
                            afterSubmitForm();
                            toastr.error(notif.message);
                        }
                    })
                    .catch((error) => {
                        afterSubmitForm();
                        $('.errormsg').show();
                        this.formErrors = error.response.data.errors;
                    });
                
            },
            isChecked:function(addons) {
                $(document).on('show.bs.modal', '#modalForm', function () {
                    setTimeout(function(){ 
                        $.ajax({
                            url: "{{ url('/items/checkaddons') }}"+'/'+$("#id").val()+'/'+addons,
                            type: 'GET',
                        })
                        .done(function(data) {
                            if(data == 1){
                                $("#item"+addons).prop('checked', true);
                            }else{
                                $("#item"+addons).prop('checked', false);
                            }
                        })
                        .fail(function() {
                            console.log("error");
                        });
                    }, 1000);
                });
            },
        },
    });
</script>