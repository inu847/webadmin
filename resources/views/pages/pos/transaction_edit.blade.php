<div class="p-3 text-center">
    <h5 class="text-uppercase"><b>{{ getData::getCatalogSession('catalog_title') }}</b></h5>
    <p>{{ Date::myDate(Date('Y-m-d')) }}</p>
    <p>
        <b>Order Number : {{ $invoice['invoice_number'] }}</b>
    </p>
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
					@foreach(getData::getItemCart($invoice['invoice_number'],$item['category']) as $listitem)
						@php
							$price = ($listitem['price']-$listitem['discount']) * $listitem['qty'];
							$total = $total + $price;
							$itemgroup[]= $listitem['item'].' x '.$listitem['qty'];
						@endphp
					<tr>
						<td style="width: 70%" style="border-top: none" class="align-top">
							<p style="font-size: 12px">
								@if($listitem['item_status'] == 'Order' or $listitem['item_status'] == 'Checkout' && getData::getCatalogSession('advance_payment') == 'N')
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
										@if($listitem['item_status'] == 'Order' or $listitem['item_status'] == 'Checkout' && getData::getCatalogUsername(myFunction::get_username(),'advance_payment') == 'N')
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
									@if($invoice['status'] == 'Order' or $invoice['status'] == 'Checkout' && getData::getCatalogUsername(myFunction::get_username(),'advance_payment') == 'N')
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
			$gettax = 0;
			if(getData::getCatalogSession('tax') > 0){
				$gettax = ($grand*getData::getCatalogSession('tax')) /100;
			}
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
		<input type="hidden" id="grand" value="{{ $grand+$gettax }}">
		<table style="width: 100%" class="mt-3">
			<tr>
				<td class="text-right" style="font-size: 12px;border-top: 1px dashed #CCC;border-bottom: 1px dashed #CCC">
					<p style="padding: 15px 0;">
						<b>{{ number_format($grand+$gettax) }}</b>
					</p>
				</td>
			</tr>
		</table>
		<div class="mt-2">
			<button type="submit" id="btnChk" class="mr-1 btn btn-success btn-lg">Save Changes</button>
		</div>
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
	        checkoutForm: function (e) {
	        	$('.preloader').css('display','block');
	        	var form = e.target || e.srcElement;
	        	var action = "{{ url('/pos/updatecartbackpayemnt') }}";
	        	var csrfToken = "{{ csrf_token() }}";
				
	        	let datas = new FormData();
	        	datas.append("id", "{{ $invoice['id'] }}");
	        	datas.append("grand", $('#grand').val());
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
	        	            window.location.replace("{{ url('/pos') }}");
	        	        }else{
	        	            //afterpreloader();
	        	            toastr.error(notif.message);
	        	        }
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
</script>