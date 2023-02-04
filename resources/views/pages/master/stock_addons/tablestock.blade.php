<div class="table-responsive">
	<table class="align-middle mb-0 table table-borderless table-striped table-hover">
	    <thead>
	        <tr>
	            <th class="pt-4 pb-4" nowrap>Last Update</th>
	            <th class="pt-4 pb-4" nowrap>Updated by</th>
	            <th class="pt-4 pb-4 text-right" nowrap>Stock Input</th>
	            <th class="pt-4 pb-4 text-center" nowrap>Unit</th>
	            <th class="pt-4 pb-4" nowrap>Notes</th>
	            <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
	        </tr>
	    </thead>
	    <tbody>
	        @foreach($getData as $key => $value)
	        <tr>
	            <td nowrap>{{ Date::fullDate($value['created_at']) }}</td>
	            <td nowrap>{{ $value->user->name ?? null }}</td>
	            <td class="text-right" nowrap>{{ $value['stock'] }}</td>
	            <td class="text-center" nowrap>{{ $item['uom'] }}</td>
	            <td nowrap>{{ $value['notes'] }}</td>
	            <td class="text-center" nowrap>
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