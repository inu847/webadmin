<div class="px-3 pt-3 text-center">
    <h5 class="text-uppercase"><b>{{ getData::getCatalogSession('catalog_title') }}</b></h5>
    <p>{{ Date::myDate(Date('Y-m-d')) }}</p>

    @if(Session::has('cartInvoice'))
    <p class="d-none">
        <b>Invoice Number : {{ Session::get('cartInvoice') }}</b>
    </p>
    @endif
</div>

	@php
		$display_email = $display_email ? ($display_email == 'null' ? '' : $display_email) : '';
	@endphp

<hr style="border: none;border-bottom: 1px dashed;margin: 0;padding: 0">
<div class="row p-3">
	<div class="col-md-12">
		<label>Customer Email</label>
		<div class="input-group">
			<input type="text" value="{{ $getProfile ? ($display_email == $getProfile->email_contact ? '' : $display_email) : $display_email }}" id="inv_email" name="inv_email" class="form-control">
			<div class="input-group-append">
				<button class="btn btn-secondary" onclick="loadTransaction($('#inv_email').val())"><i class="fa fa-save"></i> Save</button>
			</div>
		</div>

		<label>Masukkan Email untuk menerima detail pesanan, khusus pembayaran transfer.<a id="btn_pay_online" href="javascript:;" class="" target="_blank"></a></label>
	</div>
</div>

<hr style="border: none;border-bottom: 1px dashed;margin: 0;padding: 0">
<div id="cartVue" class="p-3">
	@php
		$grand = 0;
		$itemgroup= [];
		$gettax = 0;
	@endphp
	@if(count($item) > 0)
	<form id="myCartForm" @submit.prevent="checkoutForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
		@foreach($item as $item)
		<p><b>{{ $item['category'] }}</b></p>
		<div class="ml-2">
			<div class="table-responsive">
				<table style="width: 100%">
					@php
						$total = 0;
						$totaladdons = 0;
					@endphp
					@foreach(getData::getItemCart(Session::get('cartInvoice'),$item['category']) as $listitem)
						@php
							$price = ($listitem['price']-$listitem['discount']) * $listitem['qty'];
							$total = $total + $price;
							$itemgroup[]= $listitem['item'].' x '.$listitem['qty'];
						@endphp
					<tr>
						<td style="width: 70%" style="border-top: none" class="align-top">
							<p style="font-size: 12px">
								@if($invoice && $invoice['status'] == 'Order' or $invoice['status'] == 'Checkout' && getData::getCatalogSession('advance_payment') == 'N')
									@if(getData::getInvoiceAddons($listitem['id'])->count() > 0)
										<a  href="javascript:void(0)" onclick="editNote({{ $listitem['id'] }})" style="text-decoration: none" class="actioncart"><i class="icon lnr-pencil mr-1 text-success"></i> </a>
									@else
										<a  href="javascript:void(0)" onclick="editItem({{ $listitem['id'] }})" style="text-decoration: none" class="actioncart"><i class="icon lnr-pencil mr-1 text-success"></i> </a>
									@endif
									<a  href="javascript:void(0)" onclick="removeItem({{ $listitem['id'] }},{{ $listitem['clone_invoice'] }})" style="text-decoration: none" class="actioncart"><i class="icon lnr-trash mr-1" style="color: red"></i></a>
								@endif
								{{ $listitem['item'] }}
								@if(!empty($listitem['note']))
									<br><small class="text-muted" style="font-size: .7rem;font-weight: bold;">* Note : {{ $listitem['note'] }}</small>
								@endif
							</p>

						</td>
						<td style="width:10%;border-top: none" class="text-center align-top">
							<p style="font-size: 12px">
								x {{ $listitem['qty'] }}
							</p>
						</td>
						<td style="width:20%;border-top: none" class="text-right align-top">
							<p style="font-size: 12px">
								{{ number_format($price) }}
							</p>
						</td>
					</tr>
					@if(getData::getInvoiceAddons($listitem['id'])->count() > 0)
						@foreach(getData::getInvoiceAddons($listitem['id']) as $addondata)
							@php
								$priceaddons = $addondata['addon_qty']*getData::addonsPrice($addondata['single_addon'],$addondata['multiple_addon']);
								$totaladdons = $totaladdons+$priceaddons;
							@endphp
							<tr>
								<td style="width: 70%" style="border-top: none;" class="align-top">
									<p style="font-size: 12px;padding-left: 20px;color: #999">
										@if($invoice && $invoice['status'] == 'Order' or $invoice['status'] == 'Checkout' && getData::getCatalogUsername(myFunction::get_username(),'advance_payment') == 'N')
										<a  href="javascript:void(0)" onclick="editAddon('{{ $listitem['item_id'] }}','{{ $listitem['id'] }}','{{ $addondata['row_group'] }}','{{ $addondata['addon_qty'] }}')"  style="text-decoration: none" class="actioncart"><i class="icon lnr-pencil mr-1 text-success"></i> </a>
										<a  href="javascript:void(0)" onclick="removeAdd('{{ $listitem['id'] }}','{{ $addondata['row_group'] }}')" style="text-decoration: none" class="actioncart"><i class="icon lnr-trash mr-1" style="color: red"></i> </a>
										@endif
										{{ getData::decodeAddons($addondata['single_addon']) }} 
										{{ (!empty($addondata['multiple_addon']) && !empty(getData::decodeAddons($addondata['single_addon'])))?'|':'' }} 
										{{ getData::decodeAddons($addondata['multiple_addon']) }}
									</p>
								</td>
								<td style="width:10%;border-top: none" class="text-center align-top">
									<p style="font-size: 12px;color: #999">
										x {{ $addondata['addon_qty'] }}
									</p>
								</td>
								<td style="width:20%;border-top: none" class="text-right align-top">
									<p style="font-size: 12px;color: #999">
										{{ number_format($priceaddons) }} 
									</p>
								</td>
							</tr>
						@endforeach
					@endif
					@if(($listitem['qty'] - getData::getInvoiceAddonsSum($listitem['id'])) > 0 && getData::getAddons($listitem['item_id'])->count() > 0)
						<tr>
							<td style="width: 70%" style="border-top: none;" class="align-top">
								<p style="font-size: 12px;padding-left: 20px;color: #999">
									@if($invoice && $invoice['status'] == 'Order' or $invoice['status'] == 'Checkout' && getData::getCatalogUsername(myFunction::get_username(),'advance_payment') == 'N')
									<a  href="javascript:void(0)" onclick="editAddon('{{ $listitem['item_id'] }}','{{ $listitem['id'] }}','0','{{ $listitem['qty'] - getData::getInvoiceAddonsSum($listitem['id']) }}')" style="text-decoration: none" class="actioncart"><i class="icon lnr-pencil mr-1 text-success"></i> </a>
									@endif
									No Add Ons
								</p>
							</td>
							<td style="width:10%;border-top: none" class="text-center align-top">
								<p style="font-size: 12px;color: #999">
									x {{ $listitem['qty'] - getData::getInvoiceAddonsSum($listitem['id']) }}
								</p>
							</td>
							<td style="width:20%;border-top: none" class="text-right align-top">
								<p style="font-size: 12px;color: #999">
									0
								</p>
							</td>
						</tr>
					@endif
					@endforeach
					@php
						$grand = $grand +  $total + $totaladdons;
					@endphp
				</table>
			</div>
		</div>
		@endforeach
		@php
			$gettax = ($grand*getData::getCatalogSession('tax')) /100;
		@endphp
		@if(getData::getCatalogSession('tax') > 0)
		<table style="width: 100%" class="mt-3">
			<tr>
				<td style="width: 50%" style="font-size: 12px;border-top: 1px dashed #CCC;border-bottom: 1px dashed #CCC">
					<p style="font-size: 12px">
						( Extra ) Tax {{ getData::getCatalogSession('tax') }}%
					</p>
				</td>
				<td style="width: 50%" style="font-size: 12px;border-top: 1px dashed #CCC;border-bottom: 1px dashed #CCC" class="text-right">
					<p style="font-size: 12px">
						{{ number_format($gettax) }}
					</p>
				</td>
			</tr>
		</table>
		@endif
		<input type="hidden" id="grand" value="{{ $grand }}">
		<table style="width: 100%" class="mt-3">
			<tr>
				<td class="text-right" style="font-size: 12px;border-top: 1px dashed #CCC;border-bottom: 1px dashed #CCC">
					<p style="padding: 15px 0;">
						<b>{{ number_format($grand+$gettax) }}</b>
					</p>
				</td>
			</tr>
		</table>

		@if($invoice && $invoice['status'] == 'Order')
		<div id="paymentInfo" class="mt-3">
			<div class="position-relative form-group">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="pendingCheck" name="pendingCheck" onclick="setPending()">
				    <label class="custom-control-label" for="pendingCheck">Save To Pending</label>
				</div>
			</div>
			<div class="position-relative form-group">
				<div class="row">
					<div class="col-md-5">
						<label>Table Number (Opsional) </label>
					</div>
					<div class="col-md-7">
						<input type="text" id="position" name="position" class="form-control" autocomplete="off">
						<div v-if="formErrors['position']" class="errormsg alert alert-danger mt-1">
						  @{{ formErrors['position'][0] }}
						</div>
					</div>
				</div>
			</div>
			<div id="wrapSetPending">
				<div class="position-relative form-group">
					<div class="row">
						<div class="col-md-5">
							<label>Amount</label>
						</div>
						<div class="col-md-7">
							<input value="{{ $grand+$gettax }}" type="text" id="amount" name="amount" class="form-control text-right" autocomplete="off">
							<div v-if="formErrors['amount']" class="errormsg alert alert-danger mt-1">
							  @{{ formErrors['amount'][0] }}
							</div>
						</div>
					</div>
				</div>

				<div class="position-relative form-group">
					<div class="row">
						<div class="col-md-5">
							<label>Order Type</label>
						</div>
						<div class="col-md-7">
							<select id="invoice_type_id" class="form-control">
								<option value="3">Dine In</option>
								<option value="2">Take Away</option>
							</select>
						</div>
					</div>
				</div>
				
				<div class="position-relative form-group">
					<div class="row">
						<div class="col-md-5">
							<label>Payment Method</label>
						</div>
						<div class="col-md-7">
							<select id="paymentmethod" class="form-control" onchange="paymentAction()">
								<option value="1">Bayar di Kasir</option>
								@if(getData::getCatalogSession('transfer_payment') == 'Y' && $display_email)
								<option value="2">Bank Transfer</option>
								@endif
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif

		{{--
		@if($invoice && $invoice['status'] == 'Order' && getData::getCatalogSession('transfer_payment') == 'Y')
		<!-- <div id="transferinfo" style="width: 100%;position: relative;background: #F5F5F5;border: 1px solid #DDD;padding: 10px;" class="d-none transferinfo">
			<p style="margin-bottom: 5px;"><b>MANUAL</b></p>
			<p style="font-size: .7rem">Transfer Bank ke <b>{!! getData::getCatalogSession('bank_info') !!}</b>.</p>

			<div id="payment_slip" class="position-relative form-group d-none mt-1">
				<label>Payment Slip Image</label>
				<input type="file" id="imagefile" name="imagefile" class="form-control"/>
				<div v-if="formErrors['imagefile']" class="errormsg alert alert-danger mt-1">
				@{{ formErrors['imagefile'][0] }}
				</div>
			</div>
		</div> -->
		@endif
		--}}

		<div class="mt-2">
			<button type="button" id="btnCancel" class="mr-1 btn btn-danger btn-lg" onclick="cancelOrder()">Cancel Order</button>

			@if($invoice && $invoice['status'] == 'Order' && getData::getCatalogSession('transfer_payment') == 'Y')
				<a v-on:click="doTransfer" href="javascript:;" class="mr-1 btn btn-primary d-none transferinfo">Bayar Online Sekarang</a>
			@endif

			<button type="submit" id="btnChk" class="mr-1 btn btn-success btn-lg">Pay Now</button>
			<button type="submit" id="btnPrint" class="btn btn-info btn-lg d-none" onclick="printInvoice()">Print</button>
		</div>
	</form>
	<form id="payment-form" method="post" action="{{ url('/payment/finish') }}">
	    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
	    <input type="hidden" name="result_type" id="result-type" value="">
	    <input type="hidden" name="result_data" id="result-data" value="">
	    <input type="hidden" name="payment_method" id="payment_method" value="3">
	    <input type="hidden" name="result_position" id="result-position">
	</form>
	@endif
</div>
<script type="text/javascript">
	new Vue({
	    el: "#cartVue",
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
			doTransfer: function (e) {
	        	$('.preloader').css('display','block');
	        	// if($("#pendingCheck").prop('checked') == false){
	        	// 	var pending_status = 'N';
	        	//     if( parseInt($("#amount").val()) <  parseInt("{{ $grand+$gettax }}")){
	        	//     	Swal.fire("Ops!", "Invalid amount.", "error");
	        	//     	$('.preloader').css('display','none');
	        	//     	return false;
	        	//     }
	        	// }else{
	        		var pending_status = 'Y';
	        	// }
	        	
	        	var form = e.target || e.srcElement;
	        	var action = "{{ url('/pos/checkout') }}?online=1";
	        	var csrfToken = "{{ csrf_token() }}";

	        	let datas = new FormData();
	        	datas.append("amount", $("#amount").val());
	        	datas.append("position", $("#position").val());
	        	datas.append("payment_method", $("#paymentmethod").val());
	        	@if(getData::getCatalogSession('transfer_payment') == 'Y')
	        	datas.append('imagefile', 'https://dashboard.xendit.co/images/xendit-header-logo.svg');
	        	@endif
	        	datas.append("pendingstatus", pending_status);
	        	datas.append("invoice_type_id", $("#invoice_type_id").val());
	        	datas.append("inv_email", $("#inv_email").val());
				
	        	axios.post(action, datas, {
	        	        headers: {
	        	            "X-CSRF-TOKEN": csrfToken,
	        	            Accept: "application/json",
	        	        },
	        	    })
	        	    .then((response) => {
	        	        let self = this;
	        	        var notif = response.data;
	        	        var getstatus = notif.status;
	        	        if (getstatus == "success") {
	        	            $("#btnCancel").addClass('d-none');
	        	            $("#btnChk").addClass('d-none');
	        	            //$("#btnPrint").removeClass('d-none');
	        	            $("#paymentInfo").addClass('d-none');
	        	            // $("#transferinfo").addClass('d-none');
	        	            $(".transferinfo").addClass('d-none');
	        	            $("#payment_slip").addClass('d-none');
	        	            $('.actioncart').addClass('d-none')
	        	            toastr.success("Invoice created successfully");
	        	            $('.preloader').css('display','none');

							if(notif.invoice_url){
								// $('#btn_pay_online').prop("onclick", null).off("click");
								// $('#btn_pay_online').prop('href', notif.invoice_url);
								// // $('#btn_pay_online').attr("target","_blank");
								// $('#btn_pay_online')[0].click();
								// // window.open(notif.invoice_url, "_blank");

								$('#form_pay_online').attr('action', notif.invoice_url);
								$('#form_pay_online').submit();
							}

	        	            // if($("#pendingCheck").prop('checked') == false){
	        	            // 	printInvoice();
	        	            // }else{
	        	            	// window.location.reload('true');
	        	            // }
	        	            
	        	            window.location.reload('true');
	        	        }else{
	        	            //afterpreloader();
	        	            toastr.error(notif.message);
	        	        }
	        	    })
	        	    .catch((error) => {
	        	        afterpreloader();
	        	        $('.errormsg').css('visibility','visible');
	        	        $('.preloader').css('display','none');
	        	        // this.formErrors = error.response.data.errors;
	        	    });
            },
	        checkoutForm: function (e) {
	        	$('.preloader').css('display','block');
	        	if($("#pendingCheck").prop('checked') == false){
	        		var pending_status = 'N';
	        	    if( parseInt($("#amount").val()) <  parseInt("{{ $grand+$gettax }}")){
	        	    	Swal.fire("Ops!", "Invalid amount.", "error");
	        	    	$('.preloader').css('display','none');
	        	    	return false;
	        	    }
	        	}else{
	        		var pending_status = 'Y';
	        	}
	        	
	        	var form = e.target || e.srcElement;
	        	var action = "{{ url('/pos/checkout') }}";
	        	var csrfToken = "{{ csrf_token() }}";

	        	let datas = new FormData();
	        	datas.append("amount", $("#amount").val());
	        	datas.append("position", $("#position").val());
	        	datas.append("payment_method", $("#paymentmethod").val());

				if($("#paymentmethod").val() == 2){
					@if(getData::getCatalogSession('transfer_payment') == 'Y')
						datas.append('imagefile', document.getElementById('imagefile').files[0]);
					@endif
				}

	        	datas.append("pendingstatus", pending_status);
				datas.append("invoice_type_id", $("#invoice_type_id").val());
	        	datas.append("inv_email", $("#inv_email").val());

	        	axios.post(action, datas, {
	        	        headers: {
	        	            "X-CSRF-TOKEN": csrfToken,
	        	            Accept: "application/json",
	        	        },
	        	    })
	        	    .then((response) => {
	        	        let self = this;
	        	        var notif = response.data;
	        	        var getstatus = notif.status;
	        	        if (getstatus == "success") {
	        	            $("#btnCancel").addClass('d-none');
	        	            $("#btnChk").addClass('d-none');
	        	            //$("#btnPrint").removeClass('d-none');
	        	            $("#paymentInfo").addClass('d-none');
	        	            // $("#transferinfo").addClass('d-none');
	        	            $(".transferinfo").addClass('d-none');
	        	            $("#payment_slip").addClass('d-none');
	        	            $('.actioncart').addClass('d-none')
	        	            toastr.success("Invoice created successfully");
	        	            $('.preloader').css('display','none');
	        	            if($("#pendingCheck").prop('checked') == false){
	        	            	printInvoice();
	        	            }else{
	        	            	window.location.reload('true');
	        	            }
	        	            
	        	            //window.location.reload('true');
	        	        }else{
	        	            //afterpreloader();
	        	            toastr.error(notif.message);
	        	        }
	        	    })
	        	    .catch((error) => {
	        	        afterpreloader();
	        	        $('.errormsg').css('visibility','visible');
	        	        $('.preloader').css('display','none');
	        	        // this.formErrors = error.response.data.errors;
	        	    });
	        },
	        paymentGateway(){
			    $.ajax({
			      url: "{{ url('/payment/snap/') }}"+'/'+$("#grand").val(),
			      success: function(data) {
			        //location = data;
			        console.log('token = '+data);
			        var resultType = document.getElementById('result-type');
			        var resultData = document.getElementById('result-data');
			        function changeResult(type,data){
			          $("#result-type").val(type);
			          $("#result-data").val(JSON.stringify(data));
			          $("#result-position").val($("#position").val());
			        }
			        snap.pay(data, {
			          onSuccess: function(result){
			            changeResult('success', result);
			            console.log(result.status_message);
			            console.log(result);
			            $("#payment-form").submit();
			          },
			          onPending: function(result){
			            changeResult('pending', result);
			            console.log(result.status_message);
			            $("#payment-form").submit();
			          },
			          onError: function(result){
			            changeResult('error', result);
			            console.log(result.status_message);
			            $("#payment-form").submit();
			          }
			        });
			      }
			    });
			}
	    },
	});
	function cancelOrder(){
	    Swal.fire({
	      title: "Confirmation",
	      text: "Do you want to cancel order?",
	      icon: 'warning',
	      showCancelButton: true,
	      confirmButtonColor: '#3085d6',
	      cancelButtonColor: '#d33',
	      confirmButtonText: "Yes",
	      cancelButtonText: "Cancel"
	    }).then((result) => {
	      if (result.value) {
	        $.ajax({
	            url: "{{ url('/pos/cancel') }}",
	            type: 'GET',
	        })
	        .done(function(data) {
	            $("#modalForm").modal("hide");
	            $("#loadContent").html('');
	            window.location.reload(true);
	            // loadTable();
	            // loadTransaction();
	            // loadTablePending();
	        })
	        .fail(function() {
	            Swal.fire("Ops!", "Load data failed.", "error");
	        });
	      }
	    })
	}
	function printInvoice(){
		$("#modalForm").modal("hide");
		$("#loadContent").html('');
		loadTable();
		loadTransaction();
		loadTablePending();
		$("#prints").attr("src", "{{ url('/transaction/print/'.($invoice ? $invoice['id'] : 0)) }}");
	}
	function setPending(){
		if($("#pendingCheck").prop('checked') == true){
		    $("#wrapSetPending").addClass('d-none');
		    $("#btnChk").html("Save to Pending");
		    $("#amount").val(0);
		}else{
			$("#wrapSetPending").removeClass('d-none');
			$("#btnChk").html("Pay Now");
			$("#amount").val('');
		}
	}
	function paymentAction() {
        payment = $("#paymentmethod").val();
        if (payment == 2) {
            // $("#transferinfo").removeClass("d-none");
            $(".transferinfo").removeClass("d-none");
            $("#btnChk").text("Confirmation");
            $("#payment_slip").removeClass("d-none");
            $("#btnChk").addClass("d-none");
        } else {
            // $("#transferinfo").addClass("d-none");
            $(".transferinfo").addClass("d-none");
            $("#btnChk").text("Pay Now");
            $("#payment_slip").addClass("d-none");
            $("#btnChk").removeClass("d-none");
        }
    }

</script>