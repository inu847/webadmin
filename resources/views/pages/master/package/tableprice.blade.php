<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>No</th>
                <th class="pt-4 pb-4">Price</th>
                <th class="pt-4 pb-4 text-center">Duration</th>
                <th class="pt-4 pb-4">Notes</th>
                <th class="pt-4 pb-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no=1;
            @endphp
            @foreach($getData as $key=>$value)
            	<tr>
                    <th class="text-center text-muted" nowrap>{{ $no++ }}</th>
            	    <td>
            	        <div class="widget-content p-0">
            	            <div class="widget-content-wrapper">
            	                <div class="widget-content-left flex2">
            	                    <div class="widget-heading">{{ number_format($value['price']) }}</div>
            	                    <div class="widget-subheading opacity-7">Created : {{ Date::fullDate($value['created_at']) }}</div>
            	                </div>
            	            </div>
            	        </div>
            	    </td>
            	    <td class="text-center">
            	        {{ $value['period'].' '.$value['unit'] }}
            	    </td>
            	    <td>
            	        {{ (!empty($value['notes']))?$value['notes']:'-' }}
            	    </td>
            	    <td class="text-center">
                        <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
                        </div>
            	    </td>
            	</tr>
            @endforeach
        </tbody>
    </table>
</div>