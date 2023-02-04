<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>No</th>
                <th class="pt-4 pb-4" nowrap>Name</th>
                <th class="pt-4 pb-4" nowrap>Status</th>
                <th class="pt-4 pb-4" nowrap>Email</th>
                <th class="pt-4 pb-4" nowrap>Catalog</th>
                <th class="pt-4 pb-4" nowrap>Member</th>
                <th class="pt-4 pb-4" nowrap>Transaksi Tunai</th>
                <th class="pt-4 pb-4" nowrap>Transaksi Online</th>
                <th class="pt-4 pb-4" nowrap>Total Transaksi</th>
                <th class="pt-4 pb-4" nowrap>Affiliate</th>
                <th class="pt-4 pb-4" nowrap>Affiliate Percent</th>
                <th class="pt-4 pb-4" nowrap>Affiliate Income</th>
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                <td nowrap>{{ $value['name'] }}</td>
                <td nowrap class="{{ $value['active'] == 'Y' ? '' : 'text-danger' }}">{{ $value['active'] == 'Y' ? 'Active' : 'Not Active' }}</td>
                <td nowrap>{{ $value['email'] }}</td>
                <td nowrap>{{ optional($value->catalog())->count() }}</td>
                <td nowrap>{{ optional($value->member())->count() }}</td>
                <td nowrap>Rp. {{ number_format($value->total_tunai) }}</td>
                <td nowrap>Rp. {{ number_format($value->total_online) }}</td>
                <td nowrap>Rp. {{ number_format($value->grand_total) }}</td>
                <td nowrap>{{ optional($value->affiliate)->name }}</td>
                <td class="text-center" nowrap>{{ $value->affiliate_percent }}</td>
                <td class="" nowrap>Rp. {{ number_format($value->affiliate_income) }}</td>
                <td class="text-center" nowrap>
                    <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="mr-2 dropdown-toggle btn btn-outline-link"><i class="fa fa-fw fa-wrench"></i></button>

                    <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a href="javascript:void(0);" 
                                data-id="{{ $value['id'] }}" 
                                data-month="{{ request()->searchMonth ? request()->searchMonth : date('m') }}"
                                data-year="{{ request()->searchYear ? request()->searchYear : date('Y') }}"
                                class="detailMonitor nav-link">Detail Monitor</a></li>
                            <li class="nav-item"><a href="javascript:void(0);" 
                                data-id="{{ $value['id'] }}" 
                                data-catalogs="{{ json_encode($value['catalogs']) }}"
                                class="importLink nav-link">Import Data</a></li>
                            <li class="nav-item"><a href="javascript:void(0)"
                                data-id="{{ $value['id'] }}" 
                                class="detaillink nav-link">Detail User</a></li>
                            <li class="nav-item"><a href="javascript:void(0)" 
                                data-id="{{ $value['id'] }}" 
                                class="editlink nav-link">Edit User</a></li>
                            <li class="nav-item"><a href="javascript:void(0)" 
                                data-id="{{ $value['id'] }}" 
                                data-status="{{ ($value['active']=='Y')?'N':'Y' }}" 
                                class="deletelink nav-link">{{ ($value['active']=='Y')?'Block':'UnBlock' }} User</a></li>
                        </ul>
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