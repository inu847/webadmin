<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>#</th>
                <th class="pt-4 pb-4" nowrap>Date</th>
                <th class="pt-4 pb-4" nowrap>Title</th>
                <th class="pt-4 pb-4" nowrap>Room</th>
                <th class="pt-4 pb-4" nowrap>Customer</th>
                <th class="pt-4 pb-4" nowrap>Status</th>
                <!-- <th class="pt-4 pb-4" nowrap>payment</th> -->
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            @if($value->customer)
            <tr>
                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('items.destroy', $value['id']) }}" method="post">
                    @csrf
                    @method('delete')
                    <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                    <td nowrap>
                        {{ optional($value->created_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td nowrap>
                        {{ optional($value->detail)->title }}
                    </td>
                    <td nowrap>
                        {{ $value->room }}
                    </td>
                    <td nowrap>
                        {{ $value->customer->customer_name ? $value->customer->customer_name : $value->customer->customer_email }}
                    </td>
                    <td nowrap>
                        {{ $value->job_result ? 'Selesai' : 'Belum Diproses' }}
                    </td>
                    {{--
                    <td nowrap>
                        {{ $value->lunas ? 'Lunas' : 'Belum Dibayar' }}
                    </td>
                    --}}
                    <td class="text-center" nowrap>
                        <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
                        </div>
                    </td>
                </form>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-block justify-content-center card-footer">
    <nav class="mt-3">
        {!! $pagination !!}
    </nav>
</div>