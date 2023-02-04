<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,500,700,300' rel='stylesheet' type='text/css'>
    <style type="text/css">
        /*Global Styles*/
        body {
            background: transparent;
            margin: 0;
            padding: 0;
            min-width: 100%!important;
        }
        a {
            color: #333333;
            text-decoration: none;
        }
        img {
            height: auto;
        }
        .content {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        /*Media Queries*/
        @media only screen and (max-width: 721px) {
            .columns {
                width: 100% !important;
            }
            .columncontainer {
                display: block !important;
                width: 100% !important;
            }
            .listitem,
            .role {
                font-size: 9px;
            }
            .role img {
                display: none
            }
            .role a {
                display: block
            }
            .role .divid {
                display: none
            }
            .footer {
                font-size: 9.5px;
            }
            .name {
                font-size: 16px;
            }
        }
        @media only screen and (min-width: 721px) {
            .content {
                width: 720px !important;
            }
            .role {
                font-size: 13px;
            }
            .footer {
                font-size: 13px;
            }
        }
    </style>
</head>
<body style="font-family: 'Roboto', sans-serif;font-size:13px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <!--[if (gte mso 9)|(IE)]>
                  <table width="540" align="center" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td>
                <![endif]-->
                <!--Content Wrapper-->
                <table class="content" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="padding:0 10px 30px 10px">
                        	<p style="line-height:25px;">
                        	    <b>Informasi Pemesanan : </b>
                        	</p>
                            <div style="padding:15px;line-height:25px;background:#FFF;border:1px dashed #DDD">
                                <table style="width: 100%">
                                    <tr>
                                        <td style="width: 40%">Nomor Invoice</td><td style="width: 60%"><b>: {{ $invoice['invoice_number'] }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%">Tanggal Transaksi</td><td style="width: 60%">: {{ Date::myDate($invoice['created_at']) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%">Checkout Via</td><td style="width: 60%">: {{ $invoice['via'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%">Nomor Meja / Kamar</td><td style="width: 60%">: {{ $invoice['position'] }}</td>
                                    </tr>
                                    @if(!empty($invoice['note']))
                                    <tr>
                                        <td style="width: 40%">Catatan</td><td style="width: 60%">: {{ $invoice['note'] }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice['payment_method'] > 0)
                                    <tr>
                                        <td style="width: 40%">Metode Pembayaran</td><td style="width: 60%">: {{ myFunction::payment_type($invoice['payment_method']) }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="width: 40%">Status Transaksi</td><td style="width: 60%">: {{ $invoice['status'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            <p style="line-height:25px;">
                                <b>Rincian Pesanan : </b>
                            </p>
                            <div style="margin-top:10px;background:#f7f7fe;border:1px dashed #DDD">
                                <div style="padding:5px 15px;line-height:25px">
                                	@php
                                	    $grand = 0;
                                	@endphp
                                	@foreach($item as $item)
                                		<p><b>{{ $item['category'] }}</b></p>
                                		@php
                                		    $total = 0;
                                		    $totaladdons = 0;
                                		@endphp
	                                	<table style="width: 100%">
	                                		@foreach(getData::getItemCart($invoice['invoice_number'],$item['category']) as $listitem)
		                                		@php
		                                		    $price = ($listitem['price']-$listitem['discount']) * $listitem['qty'];
		                                		    $total = $total + $price;
		                                		    $itemgroup[]= $listitem['item'].' x '.$listitem['qty'];
		                                		@endphp
			                                	<tr>
			                                		<td style="width: 70%">{{ $listitem['item'] }}</td>
			                                		<td style="width: 10%">x {{ $listitem['qty'] }}</td>
			                                		<td style="width: 20%" align="right">{{ number_format($listitem['price']-$listitem['discount']) }}</td>
			                                	</tr>
			                                	@if(!empty($listitem['note']))
			                                		<tr>
			                                			<td style="width: 100%" colspan="3">*Note : {{ $listitem['note'] }}</td>
			                                		</tr>
			                                	@endif
			                                	@if(getData::getInvoiceAddons($listitem['id'])->count() > 0)
			                                		@foreach(getData::getInvoiceAddons($listitem['id']) as $addondata)
			                                			@php
			                                				$priceaddons = $addondata['addon_qty']*getData::addonsPrice($addondata['single_addon'],$addondata['multiple_addon']);
			                                				$totaladdons = $totaladdons+$priceaddons;
			                                			@endphp
			                                			<tr>
			                                				<td style="width: 70%" style="border-top: none;">
			                                					<span style="padding-left: 20px;color: #999">
			                                						{{ getData::decodeAddons($addondata['single_addon']) }} 
			                                						{{ (!empty($addondata['multiple_addon']) && !empty(getData::decodeAddons($addondata['single_addon'])))?'|':'' }} 
			                                						{{ getData::decodeAddons($addondata['multiple_addon']) }}
			                                					</span>
			                                				</td>
			                                				<td style="width:20%;border-top: none">
			                                					<span style="color: #999">
			                                						x {{ $addondata['addon_qty'] }}
			                                					</span>
			                                				</td>
			                                				<td align="right" style="width:10%;border-top: none">
			                                					<span style="color: #999">
			                                						{{ number_format($priceaddons) }} 
			                                					</span>
			                                				</td>
			                                			</tr>
			                                		@endforeach
			                                	@endif
			                                	@if(($listitem['qty'] - getData::getInvoiceAddonsSum($listitem['id'])) > 0 && getData::getAddons($listitem['item_id'])->count() > 0)
			                                		<tr>
			                                			<td style="width: 70%" style="border-top: none;">
			                                				<span style="padding-left: 20px;color: #999">
			                                					No Add Ons
			                                				</span>
			                                			</td>
			                                			<td style="width:20%;border-top: none">
			                                				<span style="color: #999">
			                                					x {{ $listitem['qty'] - getData::getInvoiceAddonsSum($listitem['id']) }}
			                                				</span>
			                                			</td>
			                                			<td align="right" style="width:10%;border-top: none">
			                                				<span style="color: #999">
			                                					0
			                                				</span>
			                                			</td>
			                                		</tr>
			                                	@endif
		                                	@endforeach
		                                	@php
		                                		$grand = $grand +  $total + $totaladdons;
		                                	@endphp
		                                </table>
                                	@endforeach
                                	@php
                                		$gettax = ($grand*getData::getCatalogSession('tax')) /100;
                                	@endphp
                                	@if(getData::getCatalogSession('tax') > 0)
                                	<table style="width: 100%;margin-top:20px;">
                                		<tr>
                                			<td style="width: 50%;border-top: 1px dashed #CCC;border-bottom: 1px dashed #CCC">
                                				<span>
                                					<b>( Extra ) Tax {{ getData::getCatalogSession('tax') }}%</b>
                                				</span>
                                			</td>
                                			<td style="width: 50%;border-top: 1px dashed #CCC;border-bottom: 1px dashed #CCC" align="right">
                                				<span>
                                					<b>{{ number_format(ceil($gettax)) }}</b>
                                				</span>
                                			</td>
                                		</tr>
                                	</table>
                                	@endif
                                	<table style="width: 100%;margin-top:10px;">
                                		<tr>
                                			<td style="width: 50%;border-top: 1px dashed #CCC;border-bottom: 1px dashed #CCC">
                                				<span>
                                					<b>Total</b>
                                				</span>
                                			</td>
                                			<td style="width: 50%;border-top: 1px dashed #CCC;border-bottom: 1px dashed #CCC" align="right">
                                				<span>
                                					<b>{{ number_format($grand+$gettax) }}</b>
                                				</span>
                                			</td>
                                		</tr>
                                	</table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--Signature-->
                    <tr>
                        <td style="padding: 0px 0px 30px 0px;border-top: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8;">
                            <table border="0" cellpadding="0" cellspacing="0" width="720" class="columns">
                                <tr valign="top">
                                    <td width="10%" class="columncontainer" style="display:block; width:120px; margin-right: 30px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td height="150" style="display: block; width: 120px; height: 120px; text-align: center; font-family: sans-serif; line-height: 120px; font-size: 52px; color: #ffffff; margin: 0 0px 0px 10px;">
                                                    <a href="https://{{ getData::getCatalogSession('catalog_username').'.'.getData::getCatalogSession('domain') }}">
                                                        <img src="{{ asset('/images'.getData::getCatalogSession('catalog_logo')) }}" width="100">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="90%" class="columncontainer">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td valign="top" style="padding: 30px 30px 0px 10px;">
                                                    <table border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td class="name">
                                                                <span style="font-size: 16px;font-weight: bold;color: #404042;">{{ getData::getCatalogSession('catalog_title') }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="role" style="padding: 10px 0 0 0;">
                                                                <a href="tel:"><span style="position: relative;top: -5px;left: 1%;">{{ getData::getCatalogSession('phone_contact') }}</span></a>
                                                                <a href="#" style="margin:0 11px;"><span class="divid" style="position: relative;top: -5px;">|</span>
                                                                    </a>
                                                                <a href="mailto:hello@yoscan.com"><span style="position: relative;top: -5px;">{{ getData::getCatalogSession('email_contact') }}</span></a>
                                                                <a href="#" style="margin:0 11px;"><span class="divid" style="position: relative;top: -5px;">|</span>
                                                                    </a>
                                                                <a href="https://{{ getData::getCatalogSession('catalog_username').'.'.getData::getCatalogSession('domain') }}" target="_blank">{{ getData::getCatalogSession('catalog_username').'.'.getData::getCatalogSession('domain') }}</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!--Legal-->
                    <tr>
                        <td style="padding: 10px 30px 30px 10px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="footer" style="padding: 20px 0 0 0;line-height:25px">
                                        Disclaimer: This message contains confidential information and is intended only for the addressee. If you are not addressee
                                        you should not disseminate, distribute or copy this e-mail. Please notify the sender
                                        if you receive this e-mail by mistake and delete this e-mail from your system. Thank
                                        you.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
