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
							<p style="font-size: 12px">
								@if($invoice['status'] == 'Order' or $invoice['status'] == 'Checkout' && getData::getCatalogSession('advance_payment') == 'N')
									<a  href="javascript:void(0)" onclick="removeItem({{ $listitem['id'] }},{{ $listitem['clone_invoice'] }})" style="text-decoration: none" class="actioncart"><i class="icon lnr-trash mr-1" style="color: red"></i></a>
								@endif
								{{ $listitem['item'] }}
								@if(!empty($listitem['note']))
									<br><small class="text-muted" style="font-size: .7rem;font-weight: bold;">* Note : {{ $listitem['note'] }}</small>
								@endif
							</p>

						</td>
						<td style="width:20%;border-top: none" class="text-center align-top">
							<p style="font-size: 12px">
								x {{ $listitem['qty'] }}
							</p>
						</td>
						<td style="width:30%;border-top: none" class="text-right align-top">
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
		<div id="paymentInfo" class="mt-3">
			<div class="position-relative form-group">
				<div class="row">
					<div class="col-md-5">
						<label>Amount</label>
					</div>
					<div class="col-md-7">
						<input type="text" id="amount" name="amount" class="form-control text-right" autocomplete="off" value="{{ $invoice['amount'] }}">
						<span v-if="formErrors['amount']" class="errormsg">@{{ formErrors['amount'][0] }}</span>
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
							@if(getData::getCatalogSession('transfer_payment') == 'Y')
							<option value="2">Bank Transfer</option>
							@endif
						</select>
					</div>
				</div>
			</div>
		</div>
		<div id="transferinfo" style="width: 100%;position: relative;background: #F5F5F5;border: 1px solid #DDD;padding: 10px;" class="d-none">
			<p><b>INFORMATION</b></p>
			<p style="font-size: .7rem">Transfer Bank ke <b>{!! getData::getCatalogSession('bank_info') !!}</b>.</p>
		</div>
		<div id="payment_slip" class="position-relative form-group d-none mt-2">
		    <label>Payment Slip</label>
		    <input type="file" id="imagefile" name="imagefile" class="form-control"/>
		    <span v-if="formErrors['imagefile']" class="errormsg">@{{ formErrors['imagefile'][0] }}</span>
		</div>
		<div class="mt-2">
			<button type="submit" id="btnChk" class="btn btn-success btn-lg">Complete Order</button>
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
	        pendingForm: function (e) {
	        	$('.preloader').css('display','block');
	        	if( parseInt($("#amount").val()) <  parseInt("{{ $grand+$gettax }}")){
	        		Swal.fire("Ops!", "Invalid amount.", "error");
	        		$('.preloader').css('display','none');
	        		return false;
	        	}
	        	var form = e.target || e.srcElement;
	        	var action = "{{ url('/pos/completepending') }}";
	        	var csrfToken = "{{ csrf_token() }}";

	        	let datas = new FormData();
	        	datas.append("id", "{{ $invoice['id'] }}");
	        	datas.append("amount", $("#amount").val());
	        	datas.append("payment_method", "{{ ($invoice['payment_method'] > 0)?$invoice['payment_method']:1 }}");
	        	@if(getData::getCatalogSession('transfer_payment') == 'Y')
	        	datas.append('imagefile', document.getElementById('imagefile').files[0]);
	        	@endif
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
	        	        	// $("#btnChk").addClass('d-none');
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
	function paymentAction() {
        payment = $("#paymentmethod").val();
        if (payment == 2) {
            $("#transferinfo").removeClass("d-none");
            $("#btnChk").text("Confirmation");
            $("#payment_slip").removeClass("d-none");
        } else {
            $("#transferinfo").addClass("d-none");
            $("#btnChk").text("Checkout");
            $("#payment_slip").addClass("d-none");
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