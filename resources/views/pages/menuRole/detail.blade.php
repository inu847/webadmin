{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class='card-header'>
        Detail Table
    </div>
    <div class='card-body'>
        <div class="main-card mb-3 card" style="min-height: 250px;">
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <td>{{ $detail->menu->name }}</td>
                        </tr>
                        <tr>
                            <th>Roles</th>
                            <td>{{ $detail->role->name }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($detail->status == "active")
                                    <span class="badge badge-success">Active</span>
                                @elseif ($detail->status == "inactive")
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                </table>
            </div>
        </div>
    </div>
</div>