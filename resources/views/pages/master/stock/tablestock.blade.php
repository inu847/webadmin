<div class="table-responsive">
	<table class="align-middle mb-0 table table-borderless table-striped table-hover">
	    <thead>
	        <tr>
	            <th class="pt-4 pb-4" nowrap>Date</th>
	            <th class="pt-4 pb-4" nowrap>Inserted by</th>
	            <th class="pt-4 pb-4 " nowrap>Stock Input</th>
	            <th class="pt-4 pb-4" nowrap>Notes</th>
	            <th class="pt-4 pb-4 " nowrap>Actions</th>
	        </tr>
	    </thead>
	    <tbody>
	        @foreach($getData as $key => $value)
	        <tr>
	            <td nowrap>{{ Date::fullDate($value['created_at']) }}</td>
	            <td nowrap>{{ $value['name'] }}</td>
	            <td nowrap>{{ myFunction::formatNumber($value['stock']) }}</td>
	            <td nowrap>{{ $value['notes'] }}</td>
	            <td nowrap>
	            	<!-- <a href="javascript:void(0)" data-id="{{ $item['id'] }}" data-addon="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Update</a> -->
					<a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
	            	<a href="javascript:void(0)" data-id="{{ $item['id'] }}" data-addon="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
	            </td>
	        </tr>
	        @endforeach
	    </tbody>
	</table>
</div>
<div class="d-block justify-content-center card-footer">
    <nav class="mt-3">
        {!! $pagination !!}
    </nav>
</div>