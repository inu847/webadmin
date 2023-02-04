<input type="hidden" id="invoice_data" value="{{ $invoice['id'] }}">
<div class="table-responsive">
	<table class="table">
		<tr>
			<td style="border-top: none">Invoice Number</td><td style="border-top: none">: {{ $invoice['invoice_number'] }}</td>
		</tr>
		<tr>
			<td>Table / Room</td><td>: {{ $invoice['position'] }}</td>
		</tr>
		<tr>
			<td>Checkout Via</td><td>: {{ $invoice['via'] }}</td>
		</tr>
		<tr>
		    <td>Status</td><td>: {{ $invoice['status'] }}</td>
		</tr>
		<tr>
		    <td>Total</td><td>: Rp. <span id="total" style="font-weight: bold;"></span></td>
		</tr>
		<tr>
		    <td>Tax</td><td>: {{ $invoice['tax'].' %' }}</td>
		</tr>
		<tr>
		    <td>Payment</td><td>: Rp. <span id="payment" style="font-weight: bold;"></span></td>
		</tr>
		<tr>
		    <td>Payment Method</td><td>: {{ myFunction::payment_type($invoice['payment_method']) }}</td>
		</tr>
		@if(!empty($invoice['transfer_image']))
		    <tr>
		        <td></td>
		        <td>
		            <a href="javascript:void(0)" data-featherlight="{{ $invoice['transfer_image'].'?'.time() }}">
		                <img src="{{ $invoice['transfer_image'].'?'.time() }}" width="150">
		            </a>
		        </td>
		    </tr>
		@endif
	</table>
	<p><b>Detail Order</b></p>
	@php
	    $grand = 0;
	@endphp
	@foreach($item as $item)
	<p class="text-primary"><b>{{ $item['category'] }}</b></p>
	@php
	    $total = 0;
	    $totaladdons = 0;
	@endphp
	<ul class="list-group list-group-flush mb-3">
	    @foreach(getData::getItemCart($invoice['invoice_number'],$item['category']) as $listitem)
	    @php
	        $price = ($listitem['price']-$listitem['discount']) * $listitem['qty'];
	        $total = $total + $price;
	    @endphp
	    <li class="list-group-item">
	        <div class="widget-content p-0">
	            <div class="widget-content-wrapper">
	                <div class="widget-content-left">
	                    <div class="widget-heading">
	                        {{ $listitem['item'] }}
	                    </div>
	                    @if(!empty($listitem['note']))
	                    <div class="widget-subheading">*Note : {{ $listitem['note'] }}</div>
	                    @endif
	                </div>
	                <div class="widget-content-right">
	                    <div class="ml-auto badge badge-pill badge-info" style="min-width: 90px;text-align: right;">{{ $listitem['qty'] }} x {{ number_format($listitem['price']-$listitem['discount']) }}</div>
	                </div>
	            </div>
	        </div>
	    </li>
	    @if(getData::getInvoiceAddons($listitem['id'])->count() > 0)
	        <li class="list-group-item" style="background: #F7F7F7">
	            <div class="widget-content p-0">
	                <div class="text-muted" style="font-size: .7rem">
	                    <b>AddOns : </b>
	                </div>
	                @foreach(getData::getInvoiceAddons($listitem['id']) as $addondata)
	                @php
	                    $priceaddons = $addondata['addon_qty']*getData::addonsPrice($addondata['single_addon'],$addondata['multiple_addon']);
	                    $totaladdons = $totaladdons+$priceaddons;
	                @endphp
	                <div class="widget-content-wrapper" style="font-size: .7rem">
	                    <div class="widget-content-left">
	                        <div class="widget-subheading" style="line-height: 25px;">
	                            {{ getData::decodeAddons($addondata['single_addon']) }} 
	                            {{ (!empty($addondata['multiple_addon']) && !empty(getData::decodeAddons($addondata['single_addon'])))?'|':'' }} 
	                            {{ getData::decodeAddons($addondata['multiple_addon']) }}
	                        </div>
	                    </div>
	                    <div class="widget-content-right">
	                        <div class="ml-auto badge badge-pill badge-warning" style="min-width: 90px;text-align: right;">{{ $addondata['addon_qty'] }} x {{ number_format($priceaddons) }}</div>
	                    </div>
	                </div>
	                @endforeach
	            </div>
	        </li>
	    @endif
	    @endforeach
	</ul>
	    @php
	        $grand = $grand +  $total + $totaladdons;
	    @endphp
	@endforeach
</div>
<script type="text/javascript">
	$(document).ready(function() {
		total = "{{ $grand }}";
	    tax = "{{ ($grand*$invoice['tax'])/100 }}";
	    payment = parseInt(total)+parseInt(tax);
	    $("#total").html(formatCurrency(total));
	    $("#payment").html(formatCurrency(payment));
	});
</script>