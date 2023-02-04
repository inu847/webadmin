<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>#</th>
                <th class="pt-4 pb-4" nowrap>Name</th>
                <th class="pt-4 pb-4" nowrap>Catalog</th>
                <th class="pt-4 pb-4" nowrap>Email</th>
                <th class="pt-4 pb-4" nowrap>Phone</th>
                <th class="pt-4 pb-4" nowrap>Role</th>
                <th class="pt-4 pb-4" class="text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                <td nowrap>
                    <div class="widget-content p-0">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left flex2">
                                <div class="widget-heading">{{ $value['name'] }}</div>
                                <div class="widget-subheading opacity-7">Created : {{ Date::fullDate($value['created_at']) }}</div>
                            </div>
                        </div>
                    </div>
                </td>
                <td nowrap>
                    {{ $value['catalog_title'] }}
                </td>
                <td nowrap>
                    {{ $value['email'] }}
                </td>
                <td nowrap>
                    {{ $value['phone'] }}
                </td>
                <td nowrap>
                    {{ optional($value['role'])->name }}
                </td>
                <td class="text-center" nowrap>
                    <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="passlink btn btn-shadow btn-warning">Password</a>
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
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
