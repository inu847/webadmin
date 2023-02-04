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