<div class="table-responsive">
	<table class="align-middle mb-0 table table-borderless table-striped table-hover">
	    <thead>
	        <tr>
	            <th nowrap class="pt-4 pb-4 ">Material Name</th>
	            <th class="pt-4 pb-4 text-right" nowrap>Serving Size</th>
	            <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
	        </tr>
	    </thead>
	    <tbody>
	        @foreach($detail as $key => $vdetail)
	        <tr>
	            <td nowrap>{{ $vdetail['items_name'] }}</td>
	            <td class="text-right" nowrap>{{ $vdetail['serving_size'] }} {{ $vdetail['item_unit'] }}</td>
	            <td class="text-center" nowrap>
	                <div role="group" class="btn-group-sm btn-group btn-group-toggle">
	                	<a href="javascript:void(0)" data-id="{{ $vdetail['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                        <a href="javascript:void(0)" data-id="{{ $vdetail['id'] }}" data-item="{{ $item['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
	                </div>
	            </td>
	        </tr>
	        @endforeach
	    </tbody>
	</table>
</div>