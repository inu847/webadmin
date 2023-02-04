<div id="itemVue{{ $invoice['id'] }}">
	<form id="editStatusFrom" method="post" enctype="multipart/form-data">
		<div class="position-relative form-group">
			<div class="row">
				<div class="col-md-12 text-center">
					<h5>UPDATE DATA</h5>
				</div>
			</div>

			<input id="invoice" name="invoice" type="hidden" value="{{ $invoice->invoice_number }}">

			<div class="row mt-3">
				<div class="col-md-12">
					<label><b>Order Status</b></label>
					<select id="status" name="status" class="custom-select">
						<option value="Checkout" {{ $invoice->status == 'Checkout' ? 'selected' : '' }}>Checkout</option>
						@if(@json_decode($getProfile->steps))
						@foreach(json_decode($getProfile->steps) as $value)
							<option value="{{ $value }}" {{ $invoice->status == $value ? 'selected' : '' }}>{{ $value }}</option>
						@endforeach
						@endif
						<option value="Completed" {{ $invoice->status == 'Completed' ? 'selected' : '' }}>Completed</option>
						<option value="Cancel" {{ $invoice->status == 'Cancel' ? 'selected' : '' }}>Cancel</option>
					</select>
				</div>
				<div class="col-md-12">
					<label><b>Lunas Status</b></label>
					<select id="lunas" name="lunas" class="custom-select">
						<option value="0" {{ $invoice->lunas != 1 ? 'selected' : '' }}>Belum Lunas</option>
						<option value="1" {{ $invoice->lunas == 1 ? 'selected' : '' }}>Lunas</option>
					</select>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-md-12">
					<button type="submit" class="btn btn-primary">Save Data</button>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function() { 
		$("#editStatusFrom").submit(function(e) {
			e.preventDefault();
			var invoice = $(this).find("#invoice").val();
			var status = $(this).find("#status").val();
			var lunas = $(this).find("#lunas").val();
			url = "{{ url('transaction/status') }}" + "/" + invoice + "/" + status + "/" + lunas;
			console.log(url);
			$.ajax({
				url: url,
				type: "GET",
			})
			.done(function (data) {
				loadTablePending();
				Swal.fire("Success!", "Your data was updated.", "success");
			})
			.fail(function () {
				Swal.fire("Ops!", "Load data failed.", "error");
			});
		});
	})

</script>