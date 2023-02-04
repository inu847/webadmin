<div class="table-responsive">
	<table class="table">
		<tbody>
			<tr>
				<td style="border-top:none" colspan="2"><b>Customer Data</b></td>
			</tr>
			<tr>
				<td style="border-top:none">Name</td><td style="border-top:none">: {{ $myData['name'] }}</td>
			</tr>
			<tr>
				<td style="border-top:none">Email</td><td style="border-top:none">: {{ $myData['email'] }}</td>
			</tr>
			<tr>
				<td style="border-top:none">Phone</td><td style="border-top:none">: {{ $myData['phone'] }}</td>
			</tr>
			<tr>
				<td style="border-top:none" colspan="2"><b>Transaction Data</b></td>
			</tr>
			<tr>
				<td style="border-top:none">Status</td><td style="border-top:none">: {{ $myData['status'] }}</td>
			</tr>
			<tr>
				<td style="border-top:none">Invoice Number</td><td style="border-top:none">: {{ $myData['invoice'] }}</td>
			</tr>
			<tr>
				<td style="border-top:none">Register Date</td><td style="border-top:none">: {{ Date::fullDate($myData['created_at']) }}</td>
			</tr>
			<tr>
				<td style="border-top:none">Package Name</td><td style="border-top:none">: {{ $myData['package_name'] }}</td>
			</tr>
			<tr>
				<td style="border-top:none">Duration</td><td style="border-top:none">: {{ $myData['duration'] }}</td>
			</tr>
			@if(!empty($myData['expired']))
			<tr>
				<td style="border-top:none">Expired</td><td style="border-top:none">: {{ Date::myDate($myData['expired']) }}</td>
			</tr>
			@endif
			@if(!empty($myData['discount']))
			<tr>
				<td style="border-top:none">Voucher Code</td><td style="border-top:none">: {{ $myData['voucher_code'] }}</td>
			</tr>
			<tr>
				<td style="border-top:none">Discount</td><td style="border-top:none">: Rp. {{ number_format($myData['discount']) }}</td>
			</tr>
			@endif
			<tr>
				<td style="border-top:none">Package Price</td><td style="border-top:none">: Rp. {{ number_format($myData['price']-$myData['discount']) }}</td>
			</tr>
			@if($myData['status']=='Rejected')
			<tr>
				<td style="border-top:none">Notes</td><td style="border-top:none">: {{ $myData['notes'] }}</td>
			</tr>
			@endif
			@if($myData['confirmation']=='Y')
			<tr>
				<td style="border-top:none" colspan="2"><b>Payment Data</b></td>
			</tr>

			<tr>
				<td style="border-top:none" class="align-top">Payment To</td><td style="border-top:none;" class="text-info align-top">{!! $myData['account_to'] ? $myData['account_to'] : '-' !!}</td>
			</tr>

			@if($myData['confirmation_slip'])
			<tr>
				<td style="border-top:none" colspan="2">
					<a href="javascript:void(0)" data-featherlight="{{ asset($myData['confirmation_slip']) }}">
						<img src="{{ asset($myData['confirmation_slip']) }}" class="img-fluid">
					</a>
					<!-- <a href="javascript:void(0)" data-featherlight="https://yoscan.id{{ $myData['confirmation_slip'] }}">
						<img src="https://yoscan.id{{ $myData['confirmation_slip'] }}" class="img-fluid">
					</a> -->
				</td>
			</tr>
			@endif

			@if($myData['status'] == 'Approved' || $myData['status'] == 'Rejected')
			@else
				<tr>
					<td style="border-top:none" colspan="2"><b>&nbsp;</b></td>
				</tr>
				<tr>
					<td style="border-top:none" class="align-top"><b>Option</b></td><td style="border-top:none;" class="text-info align-top">
						@if($myData['status'] == 'Checkout')
							<a href="javascript:void(0)" data-id="{{ $myData['id'] }}" data-invoice="{{ $myData['invoice'] }}" class="rejectlink btn-hover-shine btn btn-danger btn-shadow btn-sm">
								Reject
							</a>
						@elseif($myData['status'] == 'Confirmation')
							<a href="javascript:void(0)" data-id="{{ $myData['id'] }}" class="approvelink btn-hover-shine btn btn-success btn-shadow btn-sm">
								Approve
							</a>
							<a href="javascript:void(0)" data-id="{{ $myData['id'] }}" data-invoice="{{ $myData['invoice'] }}" class="rejectlink btn-hover-shine btn btn-danger btn-shadow btn-sm">
								Reject
							</a>
						@elseif($myData['status'] == 'Approved' || $myData['status'] == 'Rejected')
							-
						@endif
					</td>
				</tr>
			@endif

			@endif
		</tbody>
	</table>
</div>