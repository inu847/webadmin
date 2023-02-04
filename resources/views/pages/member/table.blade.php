<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>No</th>
                <th class="pt-4 pb-4" nowrap>Register Date</th>
                <th class="pt-4 pb-4" nowrap>Name</th>
                <th class="pt-4 pb-4" nowrap>Status</th>
                <th class="pt-4 pb-4" nowrap>Email</th>
                <th class="pt-4 pb-4" nowrap>Phone</th>
                <!-- <th class="pt-4 pb-4" nowrap>Catalog</th>
                <th class="pt-4 pb-4" nowrap>Member</th>
                <th class="pt-4 pb-4" nowrap>Affiliate</th> -->
                <!-- <th class="pt-4 pb-4 text-center" nowrap>Number of Catalog</th> -->
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                <td nowrap>{{ Date::fullDate($value['created_at']) }}</td>
                <td nowrap>{{ $value['name'] }}</td>
                <td nowrap class="{{ $value['active'] == 'Y' ? '' : 'text-danger' }}">{{ $value['active'] == 'Y' ? 'Active' : 'Not Active' }}</td>
                <td nowrap>{{ $value['email'] }}</td>
                <td nowrap>{{ $value['phone'] }}</td>
                <!-- <td nowrap>{{ optional($value->catalog())->count() }}</td>
                <td nowrap>{{ optional($value->member())->count() }}</td>
                <td nowrap>{{ $value['affiliate_id'] ? 'Y' : '' }}</td> -->
                <!-- <td class="text-center" nowrap>{{ ($value['legitimate']=='Y')?$value['number_catalog']:'-' }}</td> -->
                <td class="text-center" nowrap>
                    <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="detaillink btn btn-shadow btn-primary">Detail</a>
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" data-status="{{ ($value['active']=='Y')?'N':'Y' }}" class="deletelink btn btn-shadow btn-{{ ($value['active']=='Y')?'danger':'success' }}">
                            {{ ($value['active']=='Y')?'Block':'UnBlock' }}
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