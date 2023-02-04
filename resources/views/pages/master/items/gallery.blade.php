<div class="row">
	@if(!empty($getData['item_image_one']))
	<div class="col-md-6">
		<div class="form-group text-center">
			<a href="javascript:void(0)" data-featherlight="{{ str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).str_replace('/thumbs','',$getData['item_image_one']) }}">
				<img src="{{ strpos($getData['item_image_one'], 'amazonaws.com') !== false ? $getData['item_image_one'] : str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$getData['item_image_one'] }}" class="img-fluid img-thumbnail">
			</a>
			@if($getData['item_image_one']!=$getData['item_image_primary'])
			<div class="mt-3">
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-info btn-shadow btn-sm" onclick="setPrimary('{{ basename(parse_url($getData['item_image_one'])['path']) }}','{{ $getData['id'] }}')">Set as Primary</a>
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-danger btn-shadow btn-sm" onclick="deleteImage('{{ basename(parse_url($getData['item_image_one'])['path']) }}','{{ $getData['id'] }}','one')">Delete Images</a>
			</div>
			@else
			<div class="mt-3">
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-success btn-shadow btn-sm btn-block">Primary Image</a>
			</div>
			@endif
		</div>
	</div>
	@endif
	@if(!empty($getData['item_image_two']))
	<div class="col-md-6">
		<div class="form-group">
			<a href="javascript:void(0)" data-featherlight="{{ str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).str_replace('/thumbs','',$getData['item_image_two']) }}">
				<img src="{{ strpos($getData['item_image_two'], 'amazonaws.com') !== false ? $getData['item_image_two'] : str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$getData['item_image_two'] }}" class="img-fluid img-thumbnail">
			</a>
			@if($getData['item_image_two']!=$getData['item_image_primary'])
			<div class="mt-3">
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-info btn-shadow btn-sm" onclick="setPrimary('{{ basename(parse_url($getData['item_image_two'])['path']) }}','{{ $getData['id'] }}')">Set as Primary</a>
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-danger btn-shadow btn-sm" onclick="deleteImage('{{ basename(parse_url($getData['item_image_two'])['path']) }}','{{ $getData['id'] }}','two')">Delete Images</a>
			</div>
			@else
			<div class="mt-3">
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-success btn-shadow btn-sm btn-block">Primary Image</a>
			</div>
			@endif
		</div>
	</div>
	@endif
	@if(!empty($getData['item_image_three']))
	<div class="col-md-6">
		<div class="form-group">
			<a href="javascript:void(0)" data-featherlight="{{ str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).str_replace('/thumbs','',$getData['item_image_three']) }}">
				<img src="{{ strpos($getData['item_image_three'], 'amazonaws.com') !== false ? $getData['item_image_three'] : str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$getData['item_image_three'] }}" class="img-fluid img-thumbnail">
			</a>
			@if($getData['item_image_three']!=$getData['item_image_primary'])
			<div class="mt-3">
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-info btn-shadow btn-sm" onclick="setPrimary('{{ basename(parse_url($getData['item_image_three'])['path']) }}','{{ $getData['id'] }}')">Set as Primary</a>
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-danger btn-shadow btn-sm" onclick="deleteImage('{{ basename(parse_url($getData['item_image_three'])['path']) }}','{{ $getData['id'] }}','three')">Delete Images</a>
			</div>
			@else
			<div class="mt-3">
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-success btn-shadow btn-sm btn-block">Primary Image</a>
			</div>
			@endif
		</div>
	</div>
	@endif
	@if(!empty($getData['item_image_four']))
	<div class="col-md-6">
		<div class="form-group">
			<a href="javascript:void(0)" data-featherlight="{{ str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).str_replace('/thumbs','',$getData['item_image_four']) }}">
				<img src="{{ strpos($getData['item_image_four'], 'amazonaws.com') !== false ? $getData['item_image_four'] : str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$getData['item_image_four'] }}" class="img-fluid img-thumbnail">
			</a>
			@if($getData['item_image_four']!=$getData['item_image_primary'])
			<div class="mt-3">
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-info btn-shadow btn-sm" onclick="setPrimary('{{ basename(parse_url($getData['item_image_four'])['path']) }}','{{ $getData['id'] }}')">Set as Primary</a>
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-danger btn-shadow btn-sm" onclick="deleteImage('{{ basename(parse_url($getData['item_image_four'])['path']) }}','{{ $getData['id'] }}','four')">Delete Images</a>
			</div>
			@else
			<div class="mt-3">
				<a href="javascript:void(0)" class="btn-hover-shine btn btn-success btn-shadow btn-sm btn-block">Primary Image</a>
			</div>
			@endif
		</div>
	</div>
	@endif
</div>