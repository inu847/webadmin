<div class="text-right">
    <form class="form-inline" method="GET">
        <div class="position-relative form-group d-none">
            <label for="searchfield" class="sr-only">Keyword</label>
            <input name="searchfield" id="searchfield" placeholder="Type Keyword..." type="text" class="mr-2 form-control" value="{{ $searchfield ?? '' }}">
        </div>
        <div class="position-relative form-group">
            <label for="searchMonth" class="sr-only">Month</label>
            <select id="searchMonth" name="searchMonth" class="mr-2 form-control">
                <option value="all" {{ $searchMonth == 'all' ? 'selected' : '' }}>All Months</option>
                <option value="1" {{ $searchMonth == 1 ? 'selected' : '' }}>January</option>
                <option value="2" {{ $searchMonth == 2 ? 'selected' : '' }}>February</option>
                <option value="3" {{ $searchMonth == 3 ? 'selected' : '' }}>March</option>
                <option value="4" {{ $searchMonth == 4 ? 'selected' : '' }}>April</option>
                <option value="5" {{ $searchMonth == 5 ? 'selected' : '' }}>May</option>
                <option value="6" {{ $searchMonth == 6 ? 'selected' : '' }}>June</option>
                <option value="7" {{ $searchMonth == 7 ? 'selected' : '' }}>July</option>
                <option value="8" {{ $searchMonth == 8 ? 'selected' : '' }}>August</option>
                <option value="9" {{ $searchMonth == 9 ? 'selected' : '' }}>September</option>
                <option value="10" {{ $searchMonth == 10 ? 'selected' : '' }}>October</option>
                <option value="11" {{ $searchMonth == 11 ? 'selected' : '' }}>November</option>
                <option value="12" {{ $searchMonth == 12 ? 'selected' : '' }}>December</option>
            </select>
        </div>
        <div class="position-relative form-group">
            <label for="searchYear" class="sr-only">Year</label>
            <select id="searchYear" name="searchYear" class="mr-2 form-control">
                <option value="">All Year</option>
                <option value="2021" {{ $searchYear == 2021 ? 'selected' : '' }}>2021</option>
                <option value="2022" {{ $searchYear == 2022 ? 'selected' : '' }}>2022</option>
                <option value="2023" {{ $searchYear == 2023 ? 'selected' : '' }}>2023</option>
            </select>
        </div>
        <button type="button" id="get_monitor" class="btn btn-primary">Search</button>
    </form>
</div>

<hr>

<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Catalog Name</th>
                <th>Email</th>
                <th>Transaksi Tunai</th>
                <th>Transaksi Online</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice as $key => $item)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $item->catalog->catalog_title }}</td>
                    <td>{{ $item->catalog->email_contact }}</td>
                    <td>Rp. {{ number_format($item->transaksiTunai) }}</td>
                    <td>Rp. {{ number_format($item->transaksiOnline) }}</td>
                    <td>Rp. {{ number_format($item->amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>