<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>No</th>
                <th class="pt-4 pb-4" nowrap>Title</th>
                <th class="pt-4 pb-4" nowrap>Image</th>
                <th class="pt-4 pb-4 text-right" nowrap>Min. Order</th>
                <th class="pt-4 pb-4 text-right" nowrap>Max. Order</th>
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('loyalty.destroy', $value['id']) }}" method="post">
                    @csrf
                    @method('delete')
                    <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                    <td nowrap>
                        {{ $value['name'] }}
                    </td>
                    <td nowrap>
                        <img src="{{ asset($value['photo']) }}" alt="" style="max-height: 50px; max-width: 100px;">
                    </td>
                    <td class="text-right" nowrap>
                        {{ number_format($value['min_order']) }}
                    </td>
                    <td class="text-right" nowrap>
                        {{ number_format($value['max_order']) }}
                    </td>
                    <td class="text-center" nowrap>
                        <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
                        </div>
                    </td>
                </form>
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