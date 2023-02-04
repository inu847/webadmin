@extends('layouts.main')

@section('content')
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
                                <th>Meja</th>
                                <td>{{ $detail->catalog->catalog_title }}</td>
                            </tr>
                            <tr>
                                <th>Meja</th>
                                <td>{{ $detail->table }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($detail->status == 0)
                                        <span class="badge badge-success">Ready</span>
                                    @elseif ($detail->status == 1)
                                        <span class="badge badge-danger">Ordered</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>No Invoice</th>
                                <td>-</td>
                            </tr>
                            <tr>
                                <th>Action</th>
                                <td>
                                    <a href="{{ route('table.edit', $detail->id) }}" class="btn btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                    <form
                                        onsubmit="return confirm('Are you sure?')"
                                        class="d-inline"
                                        action="{{route('table.destroy', [$detail->id])}}"
                                        method="POST">
                                            @csrf
                                            <input
                                            type="hidden"
                                            name="_method"
                                            value="DELETE">
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class='card'>
        <div class='card-header'>
            Detail Transaction In Table {{ $detail->catalog->catalog_title." - ".$detail->table }}
        </div>
        <div class='card-body'>
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice Number</th>
                        <th>Via</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $key => $transaction)    
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $transaction->invoice_number }}</td>
                            <td>{{ $transaction->via }}</td>
                            <td>{{ $transaction->status }}</td>
                            <td>{{ $transaction->amount }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection