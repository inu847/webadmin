<div class='card'>
    <div class='card-header'>
        Detail Role
    </div>
    <div class='card-body'>
        <div class="main-card mb-3 card" style="min-height: 250px;">
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $detail->name }}</td>
                    </tr>
                    <tr>
                        <th>Owner</th>
                        <td>{{ $detail->user->name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>