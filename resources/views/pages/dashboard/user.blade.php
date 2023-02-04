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
                    Rp.{{number_format($daily_sales)}}
                    {{-- <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daily_sales as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$item->item}}</td>
                                    <td>{{$item->qty}}</td>
                                    <td>{{ number_format($item->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table> --}}
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class='card'>
                <div class='card-header'>
                    {{ now()->format('F') }} Sales
                </div>
                <div class='card-body'>
                    Rp.{{number_format($monthly_sales)}}

                    {{-- <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthly_sales as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$item->item}}</td>
                                    <td>{{$item->qty}}</td>
                                    <td>{{ number_format($item->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table> --}}
                </div>
            </div>
        </div>

        {{-- FAVORIT ITEM --}}
        <div class="col-md-6 mb-3">
            <div class='card'>
                <div class='card-header'>
                    {{ now()->format('d-F') }} Top 10 Favorite Item 
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Quantity Order</th>
                                {{-- <th>Price</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp 
                            @foreach ($daily_favorite_item as $key => $item)    
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->item->items_name }}</td>
                                    <td>{{ $item->qty }}</td>
                                    {{-- <td>{{ $item->price }}</td> --}}
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
                    {{ now()->format('F') }} Top 10 Favorite Item
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Quantity Order</th>
                                {{-- <th>Price</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp 
                            @foreach ($monthly_favorite_item as $key => $item)    
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->item->items_name }}</td>
                                    <td>{{ $item->qty }}</td>
                                    {{-- <td>{{ $item->price }}</td> --}}
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

    {{-- NOT FAVORIT ITEM --}}
        <div class="col-md-6 mb-3">
            <div class='card'>
                <div class='card-header'>
                    {{ now()->format('d-F') }} NOT FAVORIT ITEM 
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Quantity Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp 
                            @foreach ($daily_not_favorite_item as $key => $items)    
                                <tr>
                                    <td>{{$no}}</td>
                                    <td>
                                        @foreach ($items as $qty => $item)
                                            @if (getData::item($item['item_id']))
                                                {{ getData::item($item['item_id'])->items_name. ", " }}
                                            @else
                                                <i>item tidak ditemukan</i>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{$key}}</td>
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
                    {{ now()->format('F') }} NOT FAVORIT ITEM
                </div>
                <div class='card-body'>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Quantity Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp 
                            @foreach ($monthly_not_favorite_item as $key => $items)    
                                <tr>
                                    <td>{{$no}}</td>
                                    <td>
                                        @foreach ($items as $item)
                                            @if (getData::item($item['item_id']))
                                                {{ getData::item($item['item_id'])->items_name. ", " }}
                                            @else
                                                <i>item tidak ditemukan</i>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{$key}}</td>
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