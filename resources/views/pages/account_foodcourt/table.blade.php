<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>No</th>
                <th class="pt-4 pb-4" nowrap>Foodcourt Name</th>
                <th class="pt-4 pb-4" nowrap>Percent</th>
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key => $value)
            <tr>
                <td class="text-center" nowrap>{{ $key+1 }}</td>
                <td nowrap>{{ $value->foodcourt->name }}</td>
                <td nowrap>{{ $value->affiliate_percent }}</td>
                <td class="text-center" nowrap>
                    <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="detaillink btn btn-shadow btn-primary">Detail</a>
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">
                            Delete
                        </a>
                    </div>
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