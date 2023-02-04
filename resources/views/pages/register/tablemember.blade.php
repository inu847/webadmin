<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>No</th>
                <th class="pt-4 pb-4 text-center" nowrap>Invoice</th>
                <th class="pt-4 pb-4" nowrap>Register Date</th>
                <th class="pt-4 pb-4" nowrap>Customer Name</th>
                <th class="pt-4 pb-4" nowrap>Package Name</th>
                <th class="pt-4 pb-4" nowrap>Voucher Code</th>
                <th class="pt-4 pb-4" nowrap>Duration</th>
                <th class="pt-4 pb-4" nowrap>Expired</th>
                <th class="pt-4 pb-4" nowrap>Status</th>
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                <td class="text-center" nowrap>{{ $value['invoice'] }}</td>
                <td nowrap>{{ Date::fullDate($value['created_at']) }}</td>
                <td nowrap>{{ $value['name'] }}</td>
                <td nowrap>{{ $value['package_name'] }}</td>
                <td nowrap>{{ $value['voucher_code'] }}</td>
                <td nowrap>{{ $value['duration'] }}</td>
                <td nowrap>{{ Date::myDate($value['expired']) }}</td>
                <td nowrap>{{ $value['status'] }}</td>
                <td class="text-center" nowrap>
                    <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                        <a href="javascript:void(0)" data-id="{{ $value['id'] }}" data-invoice="{{ $value['invoice'] }}" class="detaillink btn-hover-shine btn btn-dark btn-shadow btn-sm">
                            Detail
                        </a>
                        @if($status == 'Checkout')
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" data-invoice="{{ $value['invoice'] }}" class="rejectlink btn-hover-shine btn btn-danger btn-shadow btn-sm">
                                Reject
                            </a>
                        @elseif($status == 'Confirmation')
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="approvelink btn-hover-shine btn btn-success btn-shadow btn-sm">
                                Approve
                            </a>
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" data-invoice="{{ $value['invoice'] }}" class="rejectlink btn-hover-shine btn btn-danger btn-shadow btn-sm">
                                Reject
                            </a>
                        @elseif($status == 'Approved')
                            -
                        @endif
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