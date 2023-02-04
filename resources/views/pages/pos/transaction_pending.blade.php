<div class="p-3 text-center mt-2">
    <h5 class="text-uppercase"><b>{{ getData::getCatalogSession('catalog_title') }}</b></h5>
    <p>{{ Date::myDate($invoice['created_at']) }}</p>
    <p>
        <b>Order Number : {{ $invoice['invoice_number'] }}</b>
    </p>
</div>
<hr style="border: none;border-bottom: 1px dashed;margin: 0;padding: 0">
<div id="pendingVue" class="mt-4">
	@if(count($item) > 0)
	<form id="myCartForm" @submit.prevent="pendingForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
		@php
			$grand = 0;
			$itemgroup= [];
		@endphp
		@foreach($item as $item)
		<p><b>{{ $item['category'] }}</b></p>
		<div class="ml-2">
			<div class="table-responsive">
				<table style="width: 100%">
					@php
						$total = 0;
						$totaladdons = 0;
					@endphp
					@foreach(getData::getItemCart($invoice['invoice_number'],$item['category']) as $listitem)
						@php
							$price = ($listitem['price']-$listitem['discount']) * $listitem['qty'];
							$total = $total + $price;
							$itemgroup[]= $listitem['item'].' x '.$listitem['qty'];
						@endphp
					<tr>
						<td style="width: 50%" style="border-top: none" class="align-top">
							<p style="font-size: 12px; margin-bottom: 0;">
								{{ $listitem['item'] }}
							</p>

						</td>
						<td style="width:20%;border-top: none" class="text-center align-top">
							<p style="font-size: 12px; margin-bottom: 0;">
								x {{ $listitem['qty'] }}
							</p>
						</td>
						<td style="width:30%;border-top: none" class="text-right align-top">
							<p style="font-size: 12px; margin-bottom: 0;">
								{{ number_format($price) }}
							</p>
						</td>
					</tr>
					@if(!empty($listitem['note']))
					<tr>
						<td colspan="3">
							<small class="text-muted" style="font-size: .7rem;font-weight: bold;">*Note: {{ $listitem['note'] }}</small>
						</td>
					</tr>
					@endif
					@if(getData::getInvoiceAddons($listitem['id'])->count() > 0)
						@foreach(getData::getInvoiceAddons($listitem['id']) as $addondata)
							@php
								$priceaddons = $addondata['addon_qty']*getData::addonsPrice($addondata['single_addon'],$addondata['multiple_addon']);
								$totaladdons = $totaladdons+$priceaddons;
							@endphp
							<tr>
								<td style="width: 50%" style="border-top: none;" class="align-top">
									<p style="line-height: 35px;font-size: 12px;padding-left: 10px;color: #999">
										{{ getData::decodeAddons($addondata['single_addon']) }} 
										{{ (!empty($addondata['multiple_addon']) && !empty(getData::decodeAddons($addondata['single_addon'])))?'|':'' }} 
										{{ getData::decodeAddons($addondata['multiple_addon']) }}
									</p>
								</td>
								<td style="width:20%;border-top: none" class="text-center align-top">
									<p style="line-height: 35px;font-size: 12px;color: #999">
										x {{ $addondata['addon_qty'] }}
									</p>
								</td>
								<td style="width:30%;border-top: none" class="text-right align-top">
									<p style="line-height: 35px;font-size: 12px;color: #999">
										{{ number_format($priceaddons) }} 
									</p>
								</td>
							</tr>
						@endforeach
					@endif
					@if(($listitem['qty'] - getData::getInvoiceAddonsSum($listitem['id'])) > 0 && getData::getAddons($listitem['item_id'])->count() > 0)
						<tr>
							<td style="width: 50%" style="border-top: none;" class="align-top">
								<p style="line-height: 35px;font-size: 12px;padding-left: 10px;color: #999">
									No Add Ons
								</p>
							</td>
							<td style="width:20%;border-top: none" class="text-center align-top">
								<p style="line-height: 35px;font-size: 12px;color: #999">
									x {{ $listitem['qty'] - getData::getInvoiceAddonsSum($listitem['id']) }}
								</p>
							</td>
							<td style="width:30%;border-top: none" class="text-right align-top">
								<p style="line-height: 35px;font-size: 12px;color: #999">
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
						(Extra) Tax {{ getData::getCatalogSession('tax') }}%
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
		<div id="paymentInfo" class="mt-3">
			<div class="position-relative form-group">
				<div class="row">
					<div class="col-md-12">
						<label>Amount</label>
					</div>
					<div class="col-md-12">
						<input value="{{ $grand+$gettax }}" type="text" id="amount_pending" name="amount_pending" class="form-control text-right" autocomplete="off" style="font-size: 1.2rem;">
						<span v-if="formErrors['amount_pending']" class="errormsg">@{{ formErrors['amount_pending'][0] }}</span>
					</div>
				</div>
			</div>
			<div class="position-relative form-group">
				<div class="row">
					<div class="col-md-12">
						<label>Payment Method</label>
					</div>
					<div class="col-md-12">
						<select id="paymentmethod_pending" class="form-control" onchange="paymentAction();">
							<option value="1">Bayar di Kasir / Tunai</option>
							@if(getData::getCatalogSession('transfer_payment') == 'Y')
							<option value="2">Bank Transfer</option>
							<option value="4">QRIS</option>
							@endif
						</select>
					</div>
				</div>
			</div>
		</div>

		{{--
		

		<!-- <div id="transferinfo" style="width: 100%;position: relative;background: #F5F5F5;border: 1px solid #DDD;padding: 10px;" class="d-none">
			<p style="margin-bottom: 5px;"><b>MANUAL</b></p>
			<p style="font-size: .7rem">Transfer Bank ke <b>{!! getData::getCatalogSession('bank_info') !!}</b>.</p>

			<div id="payment_slip_pending" class="position-relative form-group d-none mt-1">
				<label>Payment Slip Image</label>
				<input type="file" id="imagefile" name="imagefile" class="form-control"/>
				<div v-if="formErrors['imagefile']" class="errormsg alert alert-danger mt-1">
				@{{ formErrors['imagefile'][0] }}
				</div>
			</div>
		</div> -->

		<!-- <div id="transferinfo" style="width: 100%;position: relative;background: #F5F5F5;border: 1px solid #DDD;padding: 10px;" class="d-none transferinfo">
			<p><b>INFORMATION</b></p>
			<p style="font-size: .7rem">Transfer Bank ke <b>{!! getData::getCatalogSession('bank_info') !!}</b>.</p>
		</div>
		<div id="payment_slip" class="position-relative form-group d-none mt-2">
		    <label>Payment Slip</label>
		    <input type="file" id="imagefile" name="imagefile" class="form-control"/>
		    <span v-if="formErrors['imagefile']" class="errormsg">@{{ formErrors['imagefile'][0] }}</span>
		</div> -->
		--}}

		<div class="mt-2">
			@if(!empty($xendit['invoice_url']))
				<a target="_blank" href="{{ $xendit['invoice_url'] }}" class="btn btn-primary btn-block d-none transferinfo_pending">Bayar Online Sekarang</a>
			@endif
			@if(!empty($qris_image))
				<button type="button" id="btn_qris" class="btn btn-primary btn-block d-none qris_info_pending" onclick="showQrCode()">Bayar QRIS Sekarang</button>
			@endif

			<button type="submit" id="btnChk_pending" class="btn btn-success btn-lg btn-block">Complete Order</button>
			<button type="submit" id="btnPrint" class="btn btn-info btn-lg d-none" onclick="printInvoice()">Print</button>
		</div>
	</form>
	@endif
</div>

<script type="text/javascript">
	new Vue({
	    el: "#pendingVue",
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
	        	if( parseInt($("#amount_pending").val()) <  parseInt("{{ $grand+$gettax }}")){
	        		Swal.fire("Ops!", "Invalid amount.", "error");
	        		$('.preloader').css('display','none');
	        		return false;
	        	}
	        	var form = e.target || e.srcElement;
	        	var action = "{{ url('/pos/completepending') }}?online=1";
	        	var csrfToken = "{{ csrf_token() }}";

	        	let datas = new FormData();
	        	datas.append("id", "{{ $invoice['id'] }}");
	        	datas.append("amount", $("#amount_pending").val());
	        	datas.append("payment_method", 2);
	        	// datas.append("payment_method", "{{ ($invoice['payment_method'] > 0)?$invoice['payment_method']:1 }}");
	        	@if(getData::getCatalogSession('transfer_payment') == 'Y')
	        	datas.append('imagefile', 'https://dashboard.xendit.co/images/xendit-header-logo.svg');
	        	@endif
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
	        	        	printInvoice();
	        	        	// $("#btnChk_pending").addClass('d-none');
	        	        	// $("#btnPrint").removeClass('d-none');
	        	        }else{
	        	            //afterpreloader();
	        	            toastr.error(notif.message);
	        	        }
	        	        $('.preloader').css('display','none');
	        	    })
	        	    .catch((error) => {
	        	        afterpreloader();
	        	        $('.errormsg').css('visibility','visible');
	        	        $('.preloader').css('display','none');
	        	        this.formErrors = error.response.data.errors;
	        	    });
	        },
	        pendingForm: function (e) {
	        	$('.preloader').css('display','block');

				console.log(parseInt($("#amount_pending").val()));
				console.log(parseInt("{{ $grand }}"));
				console.log(parseInt("{{ $gettax }}"));
				console.log(parseInt("{{ $grand+$gettax }}"));


	        	if( parseInt($("#amount_pending").val()) <  parseInt("{{ $grand+$gettax }}")){
	        		Swal.fire("Ops!", "Invalid amount.", "error");
	        		$('.preloader').css('display','none');
	        		return false;
	        	}
	        	var form = e.target || e.srcElement;
	        	var action = "{{ url('/pos/completepending') }}";
	        	var csrfToken = "{{ csrf_token() }}";

	        	let datas = new FormData();
	        	datas.append("id", "{{ $invoice['id'] }}");
	        	datas.append("amount", $("#amount_pending").val());

				payment = $("#paymentmethod_pending").val();
				if (payment == 2 || payment == 4) {
					@if(getData::getCatalogSession('transfer_payment') == 'Y')
					datas.append('imagefile', document.getElementById('imagefile').files[0]);
					@endif
				}

	        	// datas.append("payment_method", "{{ ($invoice['payment_method'] > 0)?$invoice['payment_method']:1 }}");
	        	datas.append("payment_method", payment);

	        	axios.post(action, datas, {
	        	        headers: {
	        	            "X-CSRF-TOKEN": csrfToken,
	        	            Accept: "application/json",
	        	        },
	        	    })
	        	    .then((response) => {
						loadTablePending();
						loadTableOnline();
	        	        let self = this;
	        	        var notif = response.data;
	        	        var getstatus = notif.status;
	        	        if (getstatus == "success") {
	        	        	printInvoice();
	        	        	// $("#btnChk_pending").addClass('d-none');
	        	        	// $("#btnPrint").removeClass('d-none');
	        	        }else{
	        	            //afterpreloader();
	        	            toastr.error(notif.message);
	        	        }
	        	        $('.preloader').css('display','none');
	        	    })
	        	    .catch((error) => {
	        	        afterpreloader();
	        	        $('.errormsg').css('visibility','visible');
	        	        $('.preloader').css('display','none');
	        	        this.formErrors = error.response.data.errors;
	        	    });
	        },
	    },
	});

	function showQrCode(){
		$("#qrisModal").modal("show");
		$("#qrisModal .modal-body").html('{!! $qris_image !!}');
	}
	
	function paymentAction() {
        payment = $("#paymentmethod_pending").val();

        if (payment == 2) {
            // $("#transferinfo").removeClass("d-none");
            $("#btn_qris").addClass("d-none");
            $(".transferinfo_pending").removeClass("d-none");
            $("#btnChk_pending").text("Confirmation");
            $("#payment_slip_pending").removeClass("d-none");
            $("#btnChk_pending").addClass("d-none");
        } 
		else if (payment == 4) {
            $("#btn_qris").removeClass("d-none");
            $(".transferinfo_pending").addClass("d-none");
            $("#btnChk_pending").text("Complete Order");
            $("#payment_slip_pending").addClass("d-none");
            $("#btnChk_pending").addClass("d-none");
		}
		else {
            // $("#transferinfo").addClass("d-none");
            $("#btn_qris").addClass("d-none");
            $(".transferinfo_pending").addClass("d-none");
            $("#btnChk_pending").text("Complete Order");
            $("#payment_slip_pending").addClass("d-none");
            $("#btnChk_pending").removeClass("d-none");
        }
    }

	function printInvoice(){
		loadTablePending();
	    $("#loadContentpending").html('');
	    toastr.success("Order completed");
	    $("#prints").attr("src", "{{ url('/transaction/print/'.$invoice['id']) }}");
	    //window.open("{{ url('/transaction/print/'.$invoice['invoice_number']) }}", '_blank');
	}
</script>