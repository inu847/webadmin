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

                    <div class="col-md-4 {{ (!empty($getData) && $getData['catalog_type'] == 3) ? '' : 'd-none' }}" id="div_food_court">
                        <label>Food Court</label>
                        <select id="food_court_id" name="food_court_id" class="form-control select2" style="width:100%; height: calc(2.25rem + 2px);">
                            <option value="">Select Food Court</option>
                            @foreach($food_court as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Feature</label>
                        <select id="feature" id="feature" class="custom-select" onchange="selectFeature()">
                            <option value="Basic">Basic Feature</option>
                            @if(!empty($package) && $package['package_id']=='2')
                                <option value="Full">Full Feature</option>
                            @endif
                        </select>
                    </div>

                    <!-- <div class="col-md-4">
                        <label>Status</label>
                        <select id="show_catalog" name="show_catalog" class="custom-select">
                            <option value="Open">Open</option>
                            <option value="Close">Close</option>
                        </select>
                    </div> -->

                    <input type="hidden" id="show_catalog" name="show_catalog" value="Open" />

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
                    <div class="row mt-3">
                        <div class="col-md-3 col-sm-6">
                            <label>Restaurant Data</label>
                            <select id="belongsto_hotel" name="belongsto_hotel" class="custom-select">
                                @foreach($catalogs as $key => $value)
                                    <option value="{{ $value->id }}"
                                        {{ ($belongsto_hotel && $belongsto_hotel->id == $value->id) ? 'selected' : '' }}
                                        >{{ $value->catalog_title }}</option>
                                @endforeach
                            </select>
                        </div>
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
                    <!-- <div class="col-md-3 col-sm-6">
                        <label>Catalog Domain</label>
                        <select id="domain" name="domain" class="custom-select">
                        <option value="scaneat.id">scaneat.id</option> -->
                            <!-- <option value="">Select</option>
                            <option value="liatmenu.id">liatmenu.id</option>
                            <option value="liatharga.id">liatharga.id</option> -->
                        <!-- </select>
                        <div v-if="formErrors['domain']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['domain'][0] }}
                        </div>
                    </div> -->

                    <input type="hidden" id="domain" name="domain" value="scaneat.id" />

                    <div class="col-md-3 col-sm-6">
                        <label>Catalog Title <sup class="text-danger">* (Required)</sup></label>
                        <input type="text" id="catalog_title" name="catalog_title" class="form-control" />
                        <div v-if="formErrors['catalog_title']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['catalog_title'][0] }}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label>Catalog Logo <sup class="text-danger">* (png only)</sup></label>
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
                        <!-- <small class="text-danger">Email can not be changed after saving.</small> -->
                        <div v-if="formErrors['email_contact']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['email_contact'][0] }}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
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
                    </div>
                </div>
                <div class="row mt-3">
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
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        <div v-if="formErrors['password_access']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['password_access'][0] }}
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label>Catalog Tagline</label>
                        <input type="text" id="catalog_tagline" name="catalog_tagline" class="form-control" />
                        <div v-if="formErrors['catalog_tagline']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['catalog_tagline'][0] }}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label>Catalog Key</label>
                        <input type="text" id="catalog_key" name="catalog_key" class="form-control" readonly />
                        <div v-if="formErrors['catalog_key']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['catalog_key'][0] }}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label>Table</label>
                        <select name="set_table" id="set_table" class="form-control">
                            <option value="Y">Yes</option>
                            <option value="N">No</option>
                        </select>
                    </div>

                </div>
            </div>
            <hr />
            <!-- <p>
                <b>
                    Location :
                </b>
            </p>

            <div class="position-relative form-group">
                <label>Type Address</label>
                <input id="searchInput" name="catalog_address" class="form-control" type="text" value="">
            </div>

            <div id="map-canvas" class="mb-4"></div>

            <div id="infowindow-content">
                <span id="place-name" class="title" style="font-weight: bold;"></span><br />
                <span id="place-address"></span>
            </div>

            <div class="position-relative form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Max Distance (Meter) <sup class="text-danger">* (Required)</sup></label>
                        <input type="text" id="distance" name="distance" class="form-control" />
                        <div v-if="formErrors['distance']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['distance'][0] }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Latitude <sup class="text-danger">* (Required)</sup></label>
                        <input type="text" id="lat" name="lat" class="form-control" readonly />
                        <div v-if="formErrors['lat']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['lat'][0] }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Longitude <sup class="text-danger">* (Required)</sup></label>
                        <input type="text" id="long" name="long" class="form-control" readonly />
                        <div v-if="formErrors['long']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['long'][0] }}
                        </div>
                    </div>
                    <div class="col-md-3 d-none">
                        <label style="visibility: hidden;">Set Location</label>
                        <a href="javascript:void(0)" class="btn btn-info btn-block" onclick="getLocation()">Set Current Location</a>
                    </div>
                </div>
            </div> -->

            <!-- <hr /> -->
            @if(!empty($package) && $package['package_id']=='2')
            <div id="advanceOnly" style="background: #F8F8F8;border: 1px solid #DDD;padding: 10px;" class="mt-3 mb-3 d-none">
                <p>
                    <b>
                        Transaction Settings :
                    </b>
                </p>

                <input type="hidden" id="checkout_type" name="checkout_type" value="System" />

                <!-- <div class="position-relative form-group">
                    <div class="row"> -->
                        <!-- <div class="col">
                            <label>Checkout Type</label>
                            <select id="checkout_type" name="checkout_type" class="custom-select">
                                <option value="System">System</option> -->
                                <!-- <option value="Whatsapp">Whatsapp</option> -->
                            <!-- </select>
                        </div> -->

                        <!-- <div class="col" style="visibility: hidden">
                            <label>WA Number <sup class="text-danger">* (Required)</sup></label>
                            <input type="text" id="wa_number" name="wa_number" class="form-control" />
                            <div v-if="formErrors['wa_number']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['wa_number'][0] }}
                            </div>
                        </div>
                        <div class="col" style="visibility: hidden">
                            <label>WA For Item</label>
                            <select id="wa_show_item" name="wa_show_item" class="custom-select">
                                <option value="0">Hide</option>
                                <option value="1">Show</option>
                            </select>
                        </div>
                        <div class="col" style="visibility: hidden">
                            <label>WA For Cart</label>
                            <select id="wa_show_cart" name="wa_show_cart" class="custom-select">
                                <option value="0">Hide</option>
                                <option value="1">Show</option>
                            </select>
                        </div> -->
                    <!-- </div>
                </div> -->

                <div class="position-relative form-group">
                    <div class="row">
                        <div class="col">
                            <label>Count Stock Add Ons</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <div class="custom-checkbox custom-control mt-2">
                                <input type="radio" id="stockAddOnsy" name="stock_add_ons" class="custom-control-input" value="1" {{ (getData::countStockAddOns($getData['id']) == 1)?'checked':'' }} />
                                <label class="custom-control-label" for="stockAddOnsy">Yes</label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="custom-checkbox custom-control mt-2">
                                <input type="radio" id="stockAddOnsn" name="stock_add_ons" class="custom-control-input" value="0" {{ (getData::countStockAddOns($getData['id']) == 0)?'checked':'' }} />
                                <label class="custom-control-label" for="stockAddOnsn">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="position-relative form-group">
                    <div class="row">
                        <div class="col">
                            <label>Price Types</label>
                        </div>
                    </div>
                    <div class="row">
                        @foreach($prices as $key => $value)
                            <div class="col">
                                <div class="custom-checkbox custom-control mt-2">
                                    <input type="checkbox" id="prices{{ $key }}" name="prices[]" class="custom-control-input" value="{{ $key }}" {{ getData::checkPriceCatalog((!empty($getData)?$getData['id']:0), $key)?'checked':'' }} />
                                    <label class="custom-control-label" for="prices{{ $key }}">{{ $value }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <p>
                    <b>Payment Settings :</b>
                </p>
                <!-- <div class="position-relative form-group d-none">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Client Key (Midtrans)</label>
                            <input type="text" id="client_key" name="client_key" class="form-control" />
                            <div v-if="formErrors['client_key']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['client_key'][0] }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Server Key (Midtrans)</label>
                            <input type="text" id="server_key" name="server_key" class="form-control" />
                            <div v-if="formErrors['server_key']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['server_key'][0] }}
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="position-relative form-group">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Payment Type</label>
                            <select id="advance_payment" name="advance_payment" class="custom-select" onchange="selectAdvance()">
                                <option value="Y">Prepaid</option>
                                <option value="N" {{ (!empty($getData) && $getData['advance_payment'] == "N") ? 'selected' : '' }} >Postpaid</option>
                            </select>
                        </div>

                        <div class="col-md-3 offset-md-1">
                            <label>Payment Option For</label>
                            <div class="row">
                                <div class="col">
                                    <div class="custom-checkbox custom-control mt-2 pre_pay_opt">
                                        <input type="checkbox" id="pay_opt1" name="pay_opt[]" class="custom-control-input" value="1" {{
                                (getData::checkPaymentOption(1,(!empty($getData))?$getData['id']:''))?'checked':'' }} />
                                        <label class="custom-control-label" for="pay_opt1">Delivery</label>
                                    </div>
                                
                                    <div class="custom-checkbox custom-control mt-2 pre_pay_opt">
                                        <input type="checkbox" id="pay_opt2" name="pay_opt[]" class="custom-control-input" value="2" {{
                                (getData::checkPaymentOption(2,(!empty($getData))?$getData['id']:''))?'checked':'' }} />
                                        <label class="custom-control-label" for="pay_opt2">Take Away</label>
                                    </div>
                                
                                    <div class="custom-checkbox custom-control mt-2">
                                        <input type="checkbox" id="pay_opt3" name="pay_opt[]" class="custom-control-input" value="3" {{
                                (getData::checkPaymentOption(3,(!empty($getData))?$getData['id']:''))?'checked':'' }} />
                                        <label class="custom-control-label" for="pay_opt3">Dine In</label>
                                    </div>
                                    
                                    <div class="custom-checkbox custom-control mt-2">
                                        <input type="checkbox" id="pay_opt4" name="pay_opt[]" class="custom-control-input" value="4" {{
                                (getData::checkPaymentOption(4,(!empty($getData))?$getData['id']:''))?'checked':'' }} />
                                        <label class="custom-control-label" for="pay_opt4">Pick Up</label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 delivery_options {{ (getData::checkPaymentOption(1,(!empty($getData))?$getData['id']:''))?'':'d-none' }}">
                            <label>Delivery Input Option</label>
                            <select id="delivery_option" name="delivery_option" class="custom-select">
                                <option value="1" {{ (getData::checkDeliveryOption(1,(!empty($getData))?$getData['id']:''))?'selected':'' }}>Text Input</option>
                                <option class="d-none" value="2" {{ (getData::checkDeliveryOption(2,(!empty($getData))?$getData['id']:''))?'selected':'' }}>Live Map</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="position-relative form-group">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Tax (%) <sup class="text-danger">* (Required)</sup></label>
                            <input type="text" id="tax" name="tax" class="form-control" maxlength="2" />
                            <small class="text-danger">Value : 0 - 99</small>
                            <div v-if="formErrors['tax']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['tax'][0] }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Service Charge (%) <sup class="text-danger">* (Required)</sup></label>
                            <input type="text" id="charge" name="charge" class="form-control" maxlength="2" />
                            <small class="text-danger">Value : 0 - 99</small>
                            <div v-if="formErrors['charge']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['charge'][0] }}
                            </div>
                        </div>
                        <!-- <div class="col-md-3">
                            <label>Transfer Payment</label>
                            <select id="transfer_payment" name="transfer_payment" class="custom-select">
                                <option value="N">Hide</option>
                                <option value="Y">Show</option>
                            </select>
                        </div> -->

                        <input type="hidden" id="transfer_payment" name="transfer_payment" value="Y" />
                        <input type="hidden" id="online_type" name="online_type" value="xendit_live" />
                        <input type="hidden" id="payment_method" name="payment_method[]" value="online" />

                        <!-- <div class="col-md-6">
                            <label>Transfer Payment Option</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">
                                <input aria-label="Checkbox for following text input" type="checkbox" class="" name="payment_method[]" value="online" id="payment_method_online"
                                {{ (getData::checkPaymentMethodCatalog('online',(!empty($getData))?$getData['id']:0))?'checked':'' }}
                                > &nbsp; Online Payment</span></div>
                                <select id="online_type" name="online_type" class="custom-select">
                                    <option value="xendit_live" selected>Live Mode</option> -->
                                    <!-- <option value="xendit_test" {{ (getData::checkOnlineTypeyOption('xendit_test',(!empty($getData))?$getData['id']:''))?'selected':'' }}>Test Mode</option>
                                    <option value="xendit_live" {{ (getData::checkOnlineTypeyOption('xendit_live',(!empty($getData))?$getData['id']:''))?'selected':'' }}>Live Mode</option> -->
                                <!-- </select>
                            </div>
                            <div v-if="formErrors['payment_method']" class="errormsg alert alert-danger mt-1">
                                @{{ formErrors['payment_method'][0] }}
                            </div> -->

                            <!-- <div class="input-group mt-2">
                                <div class="input-group-prepend"><span class="input-group-text"><input aria-label="Checkbox for following text input" type="checkbox" class="" name="payment_method[]" value="manual"
                                {{ (getData::checkPaymentMethodCatalog('manual',(!empty($getData))?$getData['id']:0))?'checked':'' }}
                                > &nbsp; Manual Payment</span></div>
                                <input type="text" id="bank_info" name="bank_info" class="form-control" placeholder="Bank Information" value="{{ $getData['bank_info'] ?? '' }}">
                            </div>
                            <p>Fill with you Bank Information. Eg. Bank BCA 3000 000 633 a/n Michael Laundry</p> -->
                        <!-- </div> -->

                        <!-- <div class="col-md-4">
                            <label>Payment Gateway</label>
                            <select id="payment_gateway" name="payment_gateway" class="custom-select">
                                <option value="N">Hide</option>
                                <option value="Y">Show</option>
                            </select>
                        </div> -->
                        <input id="payment_gateway" type="hidden" name="payment_gateway" value="N">
                    </div>
                </div>

                <p>
                    <b>
                        Transaction Step :
                    </b>
                </p>
                <div class="position-relative form-group">
                    <div class="row">
                        <div class="col">
                            <div class="custom-checkbox custom-control mt-2">
                                <input type="checkbox" id="step1" class="custom-control-input" checked disabled />
                                <label class="custom-control-label" for="step1">Checkout</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="custom-checkbox custom-control mt-2">
                                <input type="checkbox" id="step2" name="steps[]" class="custom-control-input" value="Approve" {{
                                (getData::checkStepTransaction('Approve',(!empty($getData))?$getData['id']:''))?'checked':'' }} />
                                <label class="custom-control-label" for="step2">Approve</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="custom-checkbox custom-control mt-2">
                                <input type="checkbox" id="step3" name="steps[]" class="custom-control-input" value="Process" {{
                                (getData::checkStepTransaction('Process',(!empty($getData))?$getData['id']:''))?'checked':'' }} />
                                <label class="custom-control-label" for="step3">Process</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="custom-checkbox custom-control mt-2">
                                <input type="checkbox" id="step4" name="steps[]" class="custom-control-input" value="Delivered" {{
                                (getData::checkStepTransaction('Delivered',(!empty($getData))?$getData['id']:''))?'checked':'' }} />
                                <label class="custom-control-label" for="step4">Delivered/ Ready to Pick Up</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="custom-checkbox custom-control mt-2">
                                <input type="checkbox" id="step5" class="custom-control-input" checked disabled />
                                <label class="custom-control-label" for="step5">Completed</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="custom-checkbox custom-control mt-2">
                                <input type="checkbox" id="step6" class="custom-control-input" checked disabled />
                                <label class="custom-control-label" for="step6">Cancel</label>
                            </div>
                        </div>
                    </div>
                </div>

                <p>
                    <b>
                        Withdrawal Settings :
                    </b>
                </p>
                <div class="position-relative form-group">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Bank</label>
                            <select id="bank_id" name="bank_id" class="form-control select2" style="width:100%; height: calc(2.25rem + 2px);">
                                <option value="">Select Bank</option>
                                @foreach($bank as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Bank Account Number</label>
                            <input type="text" id="bank_account_number" name="bank_account_number" class="form-control" />
                            <div v-if="formErrors['bank_account_number']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['bank_account_number'][0] }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Bank Account Name</label>
                            <input type="text" id="bank_account_name" name="bank_account_name" class="form-control" />
                            <div v-if="formErrors['bank_account_name']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['bank_account_name'][0] }}
                            </div>
                        </div>
                    </div>
                </div>

                <p class="mb-0">
                    <b>
                        Payment Method Settings :
                    </b>
                </p>
                <div class="position-relative form-group">
                    <div class="row">
                        @foreach($metode as $key => $value)
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col mt-4">
                                    <label>{{ $value->name ? $value->name : '' }}</label>
                                </div>
                            </div>

                            @if($value->metode)
                                @foreach($value->metode as $val)
                                    <div class="custom-checkbox custom-control">
                                        <input type="checkbox" id="metode{{ $val->id }}" name="metode[]" class="custom-control-input" value="{{ $val->id }}" 
                                        {{ $val->active ? '' : 'disabled' }}
                                        {{
                                        (getData::checkMetodeCatalog($val->id,(!empty($getData))?$getData['id']:0))?'checked':'' }} />
                                        <label class="custom-control-label" for="metode{{ $val->id }}">{{ $val->name }} {{ $val->active ? '' : '(on progress)' }}</label>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                        @endforeach
                    </div>
                </div>


            </div>
            @endif
            <p>
                <b>
                    Styling :
                </b>
            </p>
            <div class="position-relative form-group">
                <div class="row">
                    <div id="background_color_wrap" class="col-md-3">
                        <label>Header Color</label>
                        <input type="text" id="background_color" name="background_color" class="colorpicker form-control" value="#000" readonly />
                        <div v-if="formErrors['background_color']" class="errormsg alert alert-danger mt-1">
                            @{{ formErrors['background_color'][0] }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Header Image</label>
                        <input type="file" id="catalogbg" name="catalogbg" class="form-control" />
                        <div v-if="formErrors['catalogbg']" class="errormsg alert alert-danger mt-1">
                            @{{ formErrors['catalogbg'][0] }}
                        </div>
                    </div>
                    <!-- <div class="col-md-2">
                        <label>Items Layout</label>
                        <select id="layout" name="layout" class="custom-select">
                            <option value="Column">Column</option>
                            <option value="List">List</option>
                        </select>
                    </div> -->
                    <div class="col-md-3">
                        <label>Show Notification</label>
                        <select id="show_notification" name="show_notification" class="custom-select">
                            <option value="Y">Show</option>
                            <option value="N">Hide</option>
                        </select>
                    </div>
                    <div id="theme_color_wrap" class="col-md-3">
                        <label>Theme Color</label>
                        <input type="text" id="theme_color" name="theme_color" class="colorpicker form-control" value="#fb5849" />
                        <span class="text-danger">{{ $errors->first('theme_color') }}</span>
                        <div style="position: absolute; top: 30px; right: 70px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="resetTheme()">Default Theme</button>
                        </div>
                        <div v-if="formErrors['theme_color']" class="errormsg alert alert-danger mt-1">
                            @{{ formErrors['theme_color'][0] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="position-relative form-group">
                <label>Select Sliders</label>
                <div class="row">
                    @foreach($sliders as $vslider)
                    <div class="col-md-3">
                        <img src="{{ strpos($vslider['sliders_image'], 'amazonaws.com') !== false ? $vslider['sliders_image'] : str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$vslider['sliders_image'].'?'.time() }}" class="img-fluid" />
                        <div class="custom-checkbox custom-control mt-2">
                            <input type="checkbox" id="slider{{ $vslider['id'] }}" name="sliders[]" class="custom-control-input" value="{{ $vslider['id'] }}" {{
                            (getData::checkSliderCatalog($vslider['id'],(!empty($getData))?$getData['id']:0))?'checked':'' }} />
                            <label class="custom-control-label" for="slider{{ $vslider['id'] }}">Select Image</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="custom-checkbox custom-control mr-auto">
                <input type="checkbox" id="license_agreement" name="license_agreement" class="custom-control-input" value="1"
                {{ (getData::checkLicenseAggrement(1, $getData['id']) == 1) ? 'checked':'' }} required/>
                <label class="custom-control-label" for="license_agreement"><i>Saya mengizinkan data yang bersifat rahasia disimpan di scaneat</i></label>
            </div>
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

                var stockAddOns = ''
                $("input[name=stock_add_ons]:checked").each(function(){
                    stockAddOns = $(this).val();
                });
                
                
                let datas = new FormData();
                datas.append("catalog_list", arr_catalog_list);
                datas.append("pay_opt", arr_pay_opt);
                datas.append("prices", arr_prices);
                datas.append("stock_add_ons", stockAddOns);
                datas.append("catalog_type", $("#catalog_type").val());
                datas.append("food_court_id", $("#food_court_id").val());
                datas.append("packageid", "{{ !empty($package) ? $package['package_id'] : '' }}");
                datas.append("id", $("#id").val());
                datas.append("catalog_logo", $("#catalog_logo").val());
                datas.append("background_header_image", $("#background_header_image").val());
                datas.append("background_header_image", $("#background_header_image").val());
                datas.append("domain", $("#domain").val());
                datas.append("catalog_username", $("#catalog_username").val());
                datas.append("custom_domain", $("#custom_domain").val());
                datas.append("belongsto_hotel", $("#belongsto_hotel").val());
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
                datas.append('catalogbg', document.getElementById('catalogbg').files[0]);
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
                datas.append("set_table", $("#set_table").val());
                datas.append("license_agreement", 1);

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
                    datas.append("tax", ($("#tax").val() == '' ? 0 : $("#tax").val()));
                    datas.append("charge", ($("#charge").val() == '' ? 0 : $("#charge").val()));
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
            $("#set_table").val("{{ $getData['set_table'] }}");
            selectFeature();
            $("#show_catalog").val("{{ $getData['show_catalog'] }}");
            $("#custom_domain").val("{{ $getData['custom_domain'] }}");
            $("#belongsto_hotel").val("{{ $getData['belongsto_hotel'] }}").trigger('change');
            $("#catalog_username").val("{{ $getData['catalog_username'] }}");
            $("#domain").val("{{ $getData['domain'] }}");
            $("#catalog_title").val("{{ $getData['catalog_title'] }}");
            $("#catalog_logo").val("{{ $getData['catalog_logo'] }}");
            $("#phone_contact").val("{{ $getData['phone_contact'] }}");
            $("#license_agreement").val("{{ $getData['license_agreement'] }}");

            // $("#email_contact").prop('readonly', false);
            $("#email_contact").val("{{ $getData['email_contact'] }}");
            // if($("#email_contact").val()){
            //     $("#email_contact").prop('readonly', true);
            // }

            $("#show_detail").val("{{ $getData['show_detail'] }}");
            $("#catalog_tagline").val("{{ $getData['catalog_tagline'] }}");
            $("#catalog_key").val("{{ $getData['catalog_key'] }}");
            // $("#distance").val("{{ $getData['distance'] }}");
            // $("#lat").val("{{ $getData['lat'] }}");
            // $("#long").val("{{ $getData['long'] }}");

            $("#background_color").val("{{ $getData['background_header_color'] }}");
            $("#background_header_image").val("{{ $getData['background_header_image'] }}");
            $("#show_notification").val("{{ $getData['show_notification'] }}");
            $("#theme_color").val("{{ $getData['theme_color'] }}");

            var bgheadercolor = "{{ $getData['background_header_color'] }}";
            $('#background_color_wrap .simpleColorDisplay').css({'background':bgheadercolor,'width':'25px','height':'25px','position':'absolute','right':'5px','top':'-35px','border-radius':'50px'});
            var themecolor = "{{ $getData['theme_color'] }}";
            $('#theme_color_wrap .simpleColorDisplay').css({'background':themecolor,'width':'25px','height':'25px','position':'absolute','right':'5px','top':'-35px','border-radius':'50px'});
            
            // $("#searchInput").val("{{ $getData['catalog_address'] }}");
            selectType();
            selectAdvance();
            // $("#layout").val("{{ $getData['layout'] }}");

            $("#food_court_id").val("{{ $getData['food_court_id'] }}").trigger('change');
            $("#bank_id").val("{{ $getData['bank_id'] }}").trigger('change');
            $("#bank_account_number").val("{{ $getData['bank_account_number'] }}");
            $("#bank_account_name").val("{{ $getData['bank_account_name'] }}");

            @if($package['package_id']=='2')
                $("#checkout_type").val("{{ $getData['checkout_type'] }}");
                $("#transfer_payment").val("{{ $getData['transfer_payment'] }}");
                $("#payment_gateway").val("{{ $getData['payment_gateway'] }}");
                // $("#client_key").val("{{ $getData['client_key'] }}");
                // $("#server_key").val("{{ $getData['server_key'] }}");
                $("#advance_payment").val("{{ $getData['advance_payment'] }}");
                // $("#bank_info").val("{{ $getData['bank_info'] }}");
                // $("#wa_number").val("{{ $getData['wa_number'] }}");
                // $("#wa_show_item").val("{{ $getData['wa_show_item'] }}");
                // $("#wa_show_cart").val("{{ $getData['wa_show_cart'] }}");
                $("#online_type").val("{{ $getData['online_type'] }}");
                $("#tax").val("{{ $getData['tax'] }}");
                $("#charge").val("{{ $getData['charge'] }}");
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
        $("#div_hotel").addClass('d-none');
        $("#div_food_court").addClass('d-none');

        if(feature == 2){
            $("#div_hotel").removeClass('d-none');
        }
        else if(feature == 3){
            $("#div_food_court").removeClass('d-none');
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
