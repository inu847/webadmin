<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Invoice {{ $invoice['invoice_number'] }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300&display=swap" rel="stylesheet" />
	<style type="text/css" media="print">
	    body {
	        background: #f2f2f2;
	    }
	    page[size="A4"] {
	        position: relative;
	        background: white;
	        width: 350px;
	        height: auto;
	        margin: 0 auto;
	        padding: 10px;
	    }
	    @media print {
	        body,
	        page[size="A4"] {
	            margin: 0;
	            box-shadow: 0;
	        }
	        a {
	            display: none;
	        }
	    }
	    @print {
	        @page :footer {
	            display: none;
	        }

	        @page :header {
	            display: none;
	        }
	    }
	</style>
</head>
<body>
	<page size="A4">
		<div style="position: relative;margin: 10px auto;width: 300px;">
			@php
				$grand = 0;
				$itemgroup= [];
			@endphp
			<table style="width: 100%">
				<tr>
					<td style="text-align: center;" colspan="3">
						<img src="{{ str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).getData::getCatalogSession('catalog_logo').'?'.time() }}" style="margin-bottom: 10px;">
					</td>
				</tr>
				<tr>
					<td style="text-align: center;" colspan="3">
						<code style="font-size: 1.2rem">{{ getData::getCatalogSession('catalog_title') }}</code>
					</td>
				</tr>
				<tr>
					<td style="text-align: center;" colspan="3">
						<code>{{ getData::getCatalogSession('phone_contact') }}</code>
					</td>
				</tr>
				<tr>
					<td style="text-align: center;" colspan="3">
						<code>{{ getData::getCatalogSession('email_contact') }}</code>
					</td>
				</tr>
				<tr>
					<td style="text-align: center;" colspan="3">
						<hr style="border:none;border-top: 1px dashed">
					</td>
				</tr>
				<tr>
					<td>
						<div><code>No.Order</code></div>
					</td>
					<td>
						<div><code>:</code></div>
					</td>
					<td style="text-align: right;">
						<div><code>{{ $invoice['invoice_number'] }}</code></div>
					</td>
				</tr>
				<tr>
					<td>
						<div><code>Tanggal</code></div>
					</td>
					<td>
						<div><code>:</code></div>
					</td>
					<td style="text-align: right;">
						<div><code>{{ Date::myDate($invoice['created_at']) }}</code></div>
					</td>
				</tr>
				<tr>
					<td style="text-align: center;" colspan="3">
						<hr style="border:none;border-top: 1px dashed">
					</td>
				</tr>
				@foreach($item as $item)
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
							<td style="width: 50%">
								<div><code>{{ $listitem['item'] }}</code></div>
							</td>
							<td style="width: 25%">
								<div><code>x {{ $listitem['qty'] }}</code></div>
							</td>
							<td style="text-align: right;width: 25%">
								<div><code>{{ number_format($price) }}</code></div>
							</td>
						</tr>
						@if(getData::getInvoiceAddons($listitem['id'])->count() > 0)
						    @foreach(getData::getInvoiceAddons($listitem['id']) as $addondata)
						        @php
						            $priceaddons = $addondata['addon_qty']*getData::addonsPrice($addondata['single_addon'],$addondata['multiple_addon']);
						            $totaladdons = $totaladdons+$priceaddons;
						        @endphp
						        <tr style="font-size: .7rem">
						            <td style="width: 50%">
						                <div style="color: #999">
						                    <code>
						                        {{ getData::decodeAddons($addondata['single_addon']) }} 
						                        {{ (!empty($addondata['multiple_addon']) && !empty(getData::decodeAddons($addondata['single_addon'])))?'|':'' }} 
						                        {{ getData::decodeAddons($addondata['multiple_addon']) }}
						                    </code>
						                </div>
						            </td>
						            <td style="width: 25%" valign="top">
						                <div style="color: #999;"><code>: {{ $addondata['addon_qty'] }}</code></div>
						            </td>
						            <td style="text-align: right;width: 25%" valign="top">
						                <div style="color: #999">
						                    <code>
						                        {{ number_format($priceaddons) }}
						                    </code>
						                </div>
						            </td>
						        </tr>
						    @endforeach
						@endif
					@endforeach
					@php
						$grand = $grand +  $total + $totaladdons;
					@endphp
				@endforeach
				@php
					$gettax = ($grand*getData::getCatalogSession('tax'))/100;
				@endphp
				<tr>
					<td style="text-align: center;" colspan="3">
						<hr style="border:none;border-top: 1px dashed">
					</td>
				</tr>
				@if(getData::getCatalogSession('tax') > 0)
				<tr>
					<td>
						<div><code>PPN {{ getData::getCatalogSession('tax') }}%</code></div>
					</td>
					<td>
						<div><code>:</code></div>
					</td>
					<td style="text-align: right;">
						<div><code>{{ number_format($gettax) }}</code></div>
					</td>
				</tr>
				@endif
				<tr>
					<td>
						<div><code>Total</code></div>
					</td>
					<td>
						<div><code>:</code></div>
					</td>
					<td style="text-align: right;">
						<div><code>{{ number_format($grand+$gettax) }}</code></div>
					</td>
				</tr>
				@if($invoice['amount'] > 0)
				<tr>
					<td>
						<div><code>Amount</code></div>
					</td>
					<td>
						<div><code>:</code></div>
					</td>
					<td style="text-align: right;">
						<div><code>{{ number_format($invoice['amount']) }}</code></div>
					</td>
				</tr>
				<tr>
					<td>
						<div><code>Change</code></div>
					</td>
					<td>
						<div><code>:</code></div>
					</td>
					<td style="text-align: right;">
						<div><code>{{ number_format($invoice['amount']-($grand+$gettax)) }}</code></div>
					</td>
				</tr>
				@endif
				<tr>
					<td style="text-align: center;" colspan="3">
						<hr style="border:none;border-top: 1px dashed">
					</td>
				</tr>
				<tr>
					<td style="text-align: center;" colspan="3">
						<code>TERIMA KASIH ATAS KUNJUNGAN ANDA</code>
					</td>
				</tr>
			</table>
			<div style="margin-top: 20px; text-align: center;">
			    <a href="javascript:void(0)" style="background: #1873d3; color: #fff; text-decoration: none; padding: 5px 15px; font-family: 'Lato', sans-serif; font-size: 0.9rem;" onclick="printStruk()">Cetak</a>
			</div>
		</div>
	</page>
	
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
	        window.print();
	    });
	    // function printStruk() {
	    //     window.print();
	    // }
	</script>
</body>
</html>