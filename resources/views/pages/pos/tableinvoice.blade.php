<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped">
        <thead>
            <tr>
                <th class="text-center" nowrap>Action</th>
                <th nowrap>Order Number</th>
                <th nowrap>Status</th>
                @if($via == 'System')
                <th nowrap>Catalog</th>
                @endif
                <!-- <th class="text-center" nowrap>Table</th> -->
                <!-- <th class="text-center" nowrap>Transaction Via</th> -->
                <th class="text-center" nowrap>Payment Method</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach($getData as $key=>$value)
            <tr style="cursor: pointer;" class="{{ (getData::checkCloneDetail($value['id']) > 0)?'':'d-none' }}">
                <!-- <th class="text-center" nowrap>
                    <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                        <a href="javascript:void(0)" class=" btn-hover-shine btn btn-dark btn-shadow btn-sm" onclick="viewDetail({{ $value['id'] }})">
                            Detail
                        </a>
                        <a href="{{ url('/pos/edit/'.$value['id']) }}" class=" btn-hover-shine btn btn-info btn-shadow btn-sm">
                            Edit
                        </a>
                        <a href="javascript:void(0)" class=" btn-hover-shine btn btn-primary btn-shadow btn-sm" onclick="detailPending({{ $value['id'] }})">
                            Pay Invoice
                        </a>
                    </div>
                </th> -->

                <td class="text-center" nowrap>
                    <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="mr-2 dropdown-toggle btn btn-outline-link"><i class="fa fa-fw fa-cogs"></i></button>

                    <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="javascript:void(0)" class="nav-link" onclick="viewDetail({{ $value['id'] }})">Detail</a></li>
                            @if($value['via'] == 'System')
                            <li class="nav-item">
                                <a href="javascript:void(0)" class="nav-link" onclick="editStatus({{ $value['id'] }})">Edit</a></li>
                            @else
                            <li class="nav-item">
                                <a href="{{ url('/pos/edit/'.$value['id']) }}" class="nav-link">Edit</a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a href="javascript:void(0)" class="nav-link" onclick="detailPending({{ $value['id'] }})">Pay Invoice</a>
                            </li>
                        </ul>
                    </div>
                </td>

                <td nowrap>
                    <div class="widget-content p-0">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left flex2">
                                <div class="widget-heading">{{ $value['invoice_number'] }}</div>
                                <div class="widget-subheading opacity-7">{{ Date::fullDate($value['created_at']) }}</div>
                            </div>
                        </div>
                    </div>
                </td>
                <td nowrap>
                    {{ $value['status'] }}
                </td>
                @if($via == 'System')
                <td nowrap>
                    {{ optional($value->catalog)->catalog_title }}
                </td>
                @endif
                
                {{--
                <td class="text-center" nowrap>
                    {{ $value['position'] }}
                </td>
                <td class="text-center" nowrap>
                    @if($value['via'] == 'System')
                        Regular
                    @else
                        {{ $value['via'] }}
                    @endif
                </td>
                --}}
                <td class="text-center">
                    @if($value['payment_method'] == 1)
                        Tunai
                    @elseif($value['payment_method'] == 2)
                        Bank Transfer
                    @elseif($value['payment_method'] == 4)
                        QRIS
                    @endif
                </td>
                <td class="text-right" nowrap>
                    @php
                        $tax = (getData::getTotal($value['id'])*$value['tax'])/100;
                    @endphp
                    {{ number_format(getData::getTotal($value['id'])+$tax) }}
                </td>
            </tr>
            <tr id="detail{{$value['id']}}" style="display: none" class="{{ (getData::checkCloneDetail($value['id']) > 0)?'':'d-none' }}">
                <td colspan="6">
                    <div style="position: relative; width: 100%; border: 1px dashed; min-height: 300px;">
                        <div id="loadDetail{{$value['id']}}" class="p-3"></div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<nav class="mt-3">
    {!! $pagination !!}
</nav>
<script type="text/javascript">
    function viewDetail(id){
        $("#detail"+id).toggle();
        $("#loadDetail"+id).empty();
        $("#loadDetail"+id).html('Loading Data, Please Wait...');
        $.ajax({
            url: "{{ url('/pos/selectitem') }}" + "/" + id,
            type: "GET",
        })
        .done(function (data) {
            $("#loadDetail"+id).html(data);
            $("#loadContentpending").empty();
        })
        .fail(function () {
            Swal.fire("Ops!", "Load data failed.", "error");
        });
    }
    function editStatus(id){
        $("#detail"+id).toggle();
        $("#loadDetail"+id).empty();
        $("#loadDetail"+id).html('Loading Data, Please Wait...');
        $.ajax({
            url: "{{ url('/pos/selectitem') }}" + "/" + id + '?edit_status=1',
            type: "GET",
        })
        .done(function (data) {
            $("#loadDetail"+id).html(data);
            $("#loadContentpending").empty();
        })
        .fail(function () {
            Swal.fire("Ops!", "Load data failed.", "error");
        });
    }

</script>