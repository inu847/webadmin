<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>No</th>
                <th class="pt-4 pb-4" nowrap>Name</th>
                <th class="pt-4 pb-4" nowrap>Email</th>
                <th class="pt-4 pb-4" nowrap>Phone</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $value)
                <tr>
                    <td nowrap>{{ $key+1 }}</td>
                    <td nowrap>{{ $value->customer_name ?? '-' }}</td>
                    <td nowrap>
                        {{ $value->customer_email ?? '-' }}
                    </td>
                    <td nowrap>
                        {{ $value->customer_phone ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>