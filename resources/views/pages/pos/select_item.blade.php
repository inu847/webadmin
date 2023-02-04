<div id="itemVue{{ $invoice['id'] }}">
	@if(count($item) > 0)
	<form id="myCartForm" @submit.prevent="pendingForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-12 text-center">
				<h5>Order Information</h5>
			</div>
		</div>
		<p>
			<b>Table/ Room :</b> {{ $invoice['position'] }}<br>
			<b>Email :</b> {{ $invoice['email'] }}<br>
			<b>Address :</b> {{ $invoice['address'] }}<br>
			<b>Phone :</b> {{ $invoice['phone'] }}
		</p>
		<hr>
		@php
			$grand = 0;
			$itemgroup= [];
		@endphp
		@foreach($item as $item)
		<b>{{ $item['category'] }}</b><br>
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
				
					<tr style="background-color: #FFFFFF;">
						<td style="width: 40%;border-top: none;padding: 0"  class="align-top">
							<!-- if($invoice['status'] == 'Order' || $invoice['status'] == 'Checkout') -->
							@if($invoice['via'] == 'System')
							@else
								@if(getData::buttonClone($listitem['id'],$listitem['item_id'])['clone_data']=='N')
								<a href="javascript:void(0)" class="btn-hover-shine btn btn-success btn-shadow btn-sm" onclick="selectItemList({{ $listitem['item_id'] }},{{ $listitem['id'] }})">
									Select
								</a>
								@endif
							@endif
							{{ $listitem['item'] }}
							<p style="font-size: 12px">
								@if(!empty($listitem['note']))
									<br><small class="text-muted" style="font-size: .7rem;font-weight: bold;">* Note : {{ $listitem['note'] }}</small>
								@endif
							</p>
						</td>
						<td style="width:20%;border-top: none;padding: 0" class="text-center align-top">
							<p style="font-size: 12px">
								x {{ $listitem['qty'] }}
							</p>
						</td>
						<td style="width:20%;border-top: none;padding: 0" class="text-right align-top">
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
							<tr style="background-color: #FFFFFF;">
								<td style="width: 50%" style=";border-top: none;padding: 0" class="align-top">
									<p style="line-height: 35px;font-size: 12px;padding-left: 10px;color: #999">
										{{ getData::decodeAddons($addondata['single_addon']) }} 
										{{ (!empty($addondata['multiple_addon']) && !empty(getData::decodeAddons($addondata['single_addon'])))?'|':'' }} 
										{{ getData::decodeAddons($addondata['multiple_addon']) }}
									</p>
								</td>
								<td style="width:20%;;border-top: none;padding: 0" class="text-center align-top">
									<p style="line-height: 35px;font-size: 12px;color: #999">
										x {{ $addondata['addon_qty'] }}
									</p>
								</td>
								<td style="width:30%;;border-top: none;padding: 0" class="text-right align-top">
									<p style="line-height: 35px;font-size: 12px;color: #999">
										{{ number_format($priceaddons) }} 
									</p>
								</td>
							</tr>
						@endforeach
					@endif
					@if(($listitem['qty'] - getData::getInvoiceAddonsSum($listitem['id'])) > 0 && getData::getAddons($listitem['item_id'])->count() > 0)
						<tr>
							<td style="width: 50%;border-top: none;padding: 0" class="align-top">
								<p style="line-height: 35px;font-size: 12px;padding-left: 10px;color: #999">
									No Add Ons
								</p>
							</td>
							<td style="width:20%;border-top: none;padding: 0" class="text-center align-top">
								<p style="line-height: 35px;font-size: 12px;color: #999">
									x {{ $listitem['qty'] - getData::getInvoiceAddonsSum($listitem['id']) }}
								</p>
							</td>
							<td style="width:30%;border-top: none;padding: 0" class="text-right align-top">
								<p style="line-height: 35px;font-size: 12px;color: #999">
									0
								</p>
							</td>
						</tr>
					@endif
			@endforeach
		</table>
		@endforeach
	</form>
	@endif
</div>

<script type="text/javascript">
	$(document).ready(function() {
		invoiceClone();
	});
	function selectItemList(item,detailinvoice){
		$('.preloader').css('display','block');
		$.ajax({
			url: "{{ url('/pos/clone') }}"+'/'+item+'/'+detailinvoice,
			type: 'GET',
		})
		.done(function(data) {
			$.ajax({
			    url: "{{ url('/pos/selectitem') }}" + "/{{ $invoice['id'] }}",
			    type: "GET",
			})
			.done(function (data) {
			    $("#loadDetail{{ $invoice['id'] }}").html(data);
			    loadTablePending();
			})
			.fail(function () {
			    Swal.fire("Ops!", "Load data failed.", "error");
			});

			$('.preloader').css('display','none');
		})
		.fail(function() {
			console.log("error");
		});
	}
</script>