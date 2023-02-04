@extends('layouts.main')
@section('content')
    <div class="row">
        {{-- REPORT SALES --}}
        <div class="col-md-6 mb-3">
            <div class='card'>
                <div class='card-header'>
                    {{ now()->format('d-F') }} Sales
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Catalog</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daily_sales as $key => $item)
                                @if ($item['item_id'] && $item['catalog_id'])
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ getData::catalog($item['catalog_id'])->catalog_username }}</td>
                                        <td>{{ number_format($item['amount']) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class='card'>
                <div class='card-header'>
                    {{ now()->format('F') }} Sales
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Catalog</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthly_sales as $key => $item)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ getData::catalog($item['catalog_id'])->catalog_username ?? 'catalog not found' }}</td>
                                    <td>{{ number_format($item['amount']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- FAVORIT ITEM --}}
        <div class="col-md-6 mb-3">
            <div class='card'>
                <div class='card-header'>
                    {{ now()->format('d-M') }} Favorite Item 
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Catalog</th>
                                <th>Item</th>
                                <th>Quantity Order</th>
                            </tr>
                        </thead>
                        @php
                            $no = 1;
                        @endphp 
                        <tbody>
                            @if ($daily_favorite_item)
                                @foreach ($daily_favorite_item as $key => $item)    
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>{{ getData::catalog($item['catalog_id'])->catalog_username }}</td>
                                        @if (getData::item($item['item_id']))
                                            <td>{{ getData::item($item['item_id'])->items_name }}</td>
                                        @else
                                            <td class="text muted"><i>item tidak ditemukan</i></td>
                                        @endif
                                            <td>{{ $item['qty'] }}</td>
                                    </tr>
                                    @php
                                        $no +=1;
                                    @endphp
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class='card'>
                <div class='card-header'>
                    {{ now()->format('F') }} Favorite Item
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Catalog</th>
                                <th>Item</th>
                                <th>Quantity Order</th>
                            </tr>
                        </thead>
                        @php
                            $no = 1;
                        @endphp 
                        <tbody>
                            @if($monthly_favorite_item)
                                @foreach ($monthly_favorite_item as $key => $item)    
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>{{ getData::catalog($item['catalog_id'])->catalog_username }}</td>
                                        @if (getData::item($item['item_id']))
                                            <td>{{ getData::item($item['item_id'])->items_name }}</td>
                                        @else
                                            <td class="text muted"><i>item tidak ditemukan</i></td>
                                        @endif
                                        <td>{{ $item['qty'] }}</td>
                                    </tr>
                                    @php
                                        $no +=1;
                                    @endphp
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {{-- NOT FAVORIT ITEM --}}
        <div class="col-md-6 mb-3">
            <div class='card'>
                <div class='card-header'>
                    {{ now()->format('d-M') }} Not Favorite Item 
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Catalog</th>
                                <th>Item</th>
                                <!-- <th>Quantity Order</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp 
                            @foreach ($daily_not_favorite_item as $key => $item)   
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ getData::catalog($item->first()['catalog_id'])->catalog_username ?? 'catalog not found' }}</td>
                                    @if (getData::item($item->first()['item_id']))
                                        <td>{{ getData::item($item->first()['item_id'])->items_name }}</td>
                                    @else
                                        <td class="text muted"><i>item tidak ditemukan</i></td>
                                    @endif
                                    <!-- <td>{{$item->first()['qty']}}</td> -->
                                </tr>
                                    @php
                                        $no +=1;
                                    @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class='card'>
                <div class='card-header'>
                    {{ now()->format('F') }} Not Favorite Item
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Catalog</th>
                                <th>Item</th>
                                <!-- <th>Quantity Order</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp 
                            @foreach ($monthly_not_favorite_item as $key => $item)    
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ getData::catalog($item->first()['catalog_id'])->catalog_username ?? 'catalog not found' }}</td>
                                    @if (getData::item($item->first()['item_id']))
                                        <td>{{ getData::item($item->first()['item_id'])->items_name }}</td>
                                    @else
                                        <td class="text muted"><i>item tidak ditemukan</i></td>
                                    @endif
                                    <!-- <td>{{$item->first()['qty']}}</td> -->
                                </tr>
                                @php
                                    $no +=1;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customjs')
<script type="text/javascript">
    
</script>
@endsection