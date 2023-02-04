@extends('layouts.main')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-calendar-full icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ url('/transaction/'.strtolower($invoice['status'])) }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="tabs-animation">
    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">
                    Status : {{ $invoice['status'] }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th style="border-top: 0">No.Order</th><th style="border-top: 0">: {{ $invoice['invoice_number'] }}</th>
                                    </tr>
                                    <tr>
                                        <td>Date Order</td><td>: {{ Date::fullDate($invoice['created_at']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Type</td><td>: {{ isset($invoice_type[$invoice['invoice_type_id']]) ? $invoice_type[$invoice['invoice_type_id']] : '-' }}</td>
                                    </tr>
                                    @if($invoice['address'])
                                    <tr>
                                        <td>Address</td><td>: {{ $invoice['address'] }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice['phone'])
                                    <tr>
                                        <td>Phone</td><td>: {{ $invoice['phone'] }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>{{ $invoice['invoice_type_id'] == 3 ? 'Table/ Room' : 'Info' }}</td>
                                        <td>: {{ $invoice['position'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td><td>: Rp. <span id="total" style="font-weight: bold;"></span></td>
                                    </tr>
                                    <tr>
                                        <td>(Extra) PPN</td><td>: {{ $invoice['tax'].' %' }}</td>
                                    </tr>
                                    <tr>
                                        <td>(Extra) Service Charge</td><td>: {{ $invoice['charge'].' %' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Payment</td><td>: Rp. <span id="payment" style="font-weight: bold;"></span></td>
                                    </tr>

                                    <tr>
                                        <td>Payment Status</td><td>: {{ $btn_bayar ? 'Belum Lunas' : 'Lunas' }}</td>
                                    </tr>

                                    <tr>
                                        <td>Payment Type</td><td>: {{ $invoice['advance_payment'] == "Y" ? 'Pre Paid' : 'Post Paid' }}</td>
                                    </tr>
                                    
                                    @if($invoice['payment_method'] > 0)
                                    <tr>
                                        <td>Payment Method</td><td>: {{ myFunction::payment_type($invoice['payment_method']) }}</td>
                                    </tr>
                                    @endif

                                    @if(!empty($invoice['transfer_image']))
                                        <tr>
                                            <td></td>
                                            <td>
                                                <a href="javascript:void(0)" data-featherlight="{{ $invoice['transfer_image'].'?'.time() }}">
                                                    <img src="{{ $invoice['transfer_image'].'?'.time() }}" width="150">
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-7">
                            @php
                                $grand = 0;
                                $totaladdons = 0;
                            @endphp
                            @foreach($item as $item)
                                <p class="text-primary"><b>{{ $item['category'] }}</b></p>
                                @php
                                    $total = 0;
                                    $totaladdons = 0;
                                @endphp
                                <ul class="list-group list-group-flush mb-3">
                                    @foreach(getData::getItemCart($invoice['invoice_number'],$item['category']) as $listitem)
                                        @php
                                            $price = ($listitem['price']-$listitem['discount']) * $listitem['qty'];
                                            $total = $total + $price;
                                            $itemgroup[]= $listitem['item'].' x '.$listitem['qty'];
                                        @endphp
                                        <li class="list-group-item">
                                            <div class="widget-content p-0">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">
                                                            @if(getData::getCatalogSession('advance_payment') == 'N')
                                                                @if($invoice['status'] == 'Checkout' or $invoice['status'] == 'Approve' or $invoice['status'] == 'Process')
                                                                    <button type="button" class="d-none btn-shadow btn btn-danger btn-sm mr-2" onclick="deleteItem({{ $listitem['id'] }})">Delete</button>
                                                                @endif
                                                            @endif
                                                            {{ $listitem['item'] }}
                                                        </div>
                                                        @if(!empty($listitem['note']))
                                                        <div class="widget-subheading">*Note : {{ $listitem['note'] }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="widget-content-right">
                                                        <div class="ml-auto badge badge-pill badge-info" style="min-width: 90px;text-align: right;">{{ $listitem['qty'] }} x {{ number_format($listitem['price']-$listitem['discount']) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        @if(getData::getInvoiceAddons($listitem['id'])->count() > 0)
                                            <li class="list-group-item" style="background: #F7F7F7">
                                                <div class="widget-content p-0">
                                                    <div class="text-muted" style="font-size: .7rem">
                                                        <b>AddOns :</b>
                                                    </div>
                                                    @foreach(getData::getInvoiceAddons($listitem['id']) as $addondata)
                                                        @php
                                                            $priceaddons = getData::addonsPrice($addondata['single_addon'],$addondata['multiple_addon']);
                                                        @endphp
                                                        <div class="widget-content-wrapper" style="font-size: .7rem">
                                                            <div class="widget-content-left">
                                                                <div class="widget-subheading" style="line-height: 25px;">
                                                                    {{ getData::decodeAddons($addondata['single_addon']) }} 
                                                                    {{ (!empty($addondata['multiple_addon']) && !empty(getData::decodeAddons($addondata['single_addon'])))?'|':'' }} 
                                                                    {{ getData::decodeAddons($addondata['multiple_addon']) }}
                                                                </div>
                                                            </div>
                                                            <div class="widget-content-right">
                                                                <div class="ml-auto badge badge-pill badge-warning" style="min-width: 90px;text-align: right; font-size: .7rem">{{ $addondata['addon_qty'] }} x {{ number_format($priceaddons) }}</div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $totaladdons = $totaladdons+($addondata['addon_qty']*$priceaddons);
                                                        @endphp
                                                    @endforeach
                                                </div>
                                            </li>
                                        @endif

                                    @endforeach

                                </ul>
                                @php
                                    $grand = $grand +  $total + $totaladdons;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
                
                @if($invoice['status'] != 'Completed' && $invoice['status'] != 'Cancel')
                    <div class="d-block text-right card-footer">
                        @if($btn_bayar)
                            <b class="float-left d-none">Customer Belum Melunasi Pembayaran.</b>
                        @endif

                        @if($invoice['status'] == 'Checkout')
                            <button class="mr-2 btn btn-danger btn-lg" onclick="cancelModal('{{ $invoice['invoice_number'] }}')">Cancel Order</button>
                        @endif

                        @if($invoice['status'] == 'Checkout')
                            @if(getData::checkStepTransaction('Approve',Session::get('catalogsession')))
                                @php
                                    $label = 'Approve Order';
                                    $next = 'Approve';
                                @endphp
                            @elseif(getData::checkStepTransaction('Process',Session::get('catalogsession')))
                                @php
                                    $label = 'Process Order';
                                    $next = 'Process';
                                @endphp
                            @elseif(getData::checkStepTransaction('Delivered',Session::get('catalogsession')))
                                @php
                                    $label = 'Delivered Order';
                                    $next = 'Delivered';
                                @endphp
                            @else
                                @php
                                    $label = 'Complete Order';
                                    $next = 'Completed';
                                @endphp
                            @endif
                        @elseif($invoice['status'] == 'Approve')
                            @if(getData::checkStepTransaction('Process',Session::get('catalogsession')))
                                @php
                                    $label = 'Process Order';
                                    $next = 'Process';
                                @endphp
                            @elseif(getData::checkStepTransaction('Delivered',Session::get('catalogsession')))
                                @php
                                    $label = 'Delivered Order';
                                    $next = 'Delivered';
                                @endphp
                            @else
                                @php
                                    $label = 'Complete Order';
                                    $next = 'Completed';
                                @endphp
                            @endif
                        @elseif($invoice['status'] == 'Process')
                            @if(getData::checkStepTransaction('Delivered',Session::get('catalogsession')))
                                @php
                                    $label = $invoice['invoice_type_id'] == 4 ? 'Ready to Pick Up' : 'Deliver to Customer';
                                    $next = 'Delivered';
                                @endphp
                            @else
                                @php
                                    $label = $invoice['invoice_type_id'] == 4 ? 'Ready to Pick Up' : 'Deliver to Customer';
                                    $next = 'Completed';
                                @endphp
                            @endif
                        @elseif($invoice['status'] == 'Delivered')
                            @php
                                $label = 'Complete Order';
                                $next = 'Completed';
                            @endphp
                        @endif

                        @if($btn_bayar)
                            <button class="btn btn-warning btn-lg" onclick="lunas('{{ $invoice['invoice_number'] }}')">Lunas</button>
                        @else
                            <button class="btn btn-success btn-lg" onclick="changeStatus('{{ $invoice['invoice_number'] }}','{{ $next }}','{{ $invoice['status'] }}')">{{ $label }}</button>
                            <!-- <button class="btn btn-success btn-lg" onclick="" disabled>{{ $label }}</button> -->
                        @endif
                    </div>
                @else
                    @if(getData::getCatalogSession('advance_payment') == 'N')
                        @if($btn_bayar)
                            <div class="row m-4">
                                <div class="col-md-12">
                                    <button class="btn btn-success btn-lg btn-block" onclick="setLunas('{{ $invoice['invoice_number'] }}','{{ $invoice['status'] }}',1)">Lunaskan</button>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('modal')
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal">Cancel Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="position-relative form-group">
                    <label>Invoice</label>
                    <input type="text" id="invoice_number" name="invoice_number" readonly class="form-control"/>
                </div>
                <div class="position-relative form-group">
                    <label>Note</label>
                    <input type="text" id="note_order" name="note_order" class="form-control"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="cancelOrder()">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('customjs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            total = "{{ $grand }}";
            tax = "{{ ($grand*$invoice['tax'])/100 }}";
            charge = "{{ ($grand*$invoice['charge'])/100 }}";
            payment = parseInt(total)+parseInt(tax)+parseInt(charge);
            $("#total").html(formatCurrency(total));
            $("#payment").html(formatCurrency(payment));
        });
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: "a01ada789ab34d372572",
            cluster: "ap1",
            encrypted: true,
        });
        Echo.channel('pushernotif').listen('NotifEvent', function(e) {
            if(e.invoice == "{{ $invoice['invoice_number'] }}"){
                // window.location.reload();
                window.location.href = window.location.href;
            }
        });
        function deleteItem(id){
            Swal.fire({
              title: "Confirmation",
              text: "Do you want to remove this row?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Yes",
              cancelButtonText: "Cancel"
            }).then((result) => {
              if (result.value) {
                $('.preloader').css('display','block');
                $.ajax({
                    url: "{{ url('/transaction/delete/item') }}"+'/'+id,
                    type: 'GET',
                })
                .done(function() {
                    $('.preloader').css('display','none');
                    // window.location.reload();
                    window.location.href = window.location.href;
                })
                .fail(function() {
                    console.log("error");
                });
              }
            })
        }
        function cancelModal(invoice){
            $("#modalForm").modal('show');
            $("#invoice_number").val(invoice);
        }
        function cancelOrder(){
            $('.preloader').css('display','block');
            obj = new Object;
            obj.invoice_number = $("#invoice_number").val();
            obj.note_order = $("#note_order").val();
            $.ajax({
                url: "{{ url('/transaction/cancel/order') }}",
                type: 'POST',
                data: obj,
            })
            .done(function(data) {
                window.location.replace("{{ url('/transaction/cancel') }}");
                $('.preloader').css('display','none');
            })
            .fail(function() {
                console.log("error");
            });
        }
        function changeStatus(invoice,status,current){
            $('.preloader').css('display','block');
            $.ajax({
                url: "{{ url('/transaction/status') }}"+'/'+invoice+'/'+status,
                type: 'GET',
            })
            .done(function() {
                window.location.replace("{{ url('/transaction') }}"+'/'+current.toLowerCase());
                $('.preloader').css('display','none');
            })
            .fail(function() {
                console.log("error");
            });
        }
        function lunas(invoice){
            $('.preloader').css('display','block');
            $.ajax({
                url: "{{ url('/transaction/lunas') }}"+'/'+invoice,
                type: 'GET',
            })
            .done(function() {
                window.location.replace("{{ url('/transaction') }}"+'/'+current.toLowerCase());
                $('.preloader').css('display','none');
            })
            .fail(function() {
                console.log("error");
            });
        }
        function setLunas(invoice,status,lunas){
            $('.preloader').css('display','block');
            $.ajax({
                url: "{{ url('/transaction/status') }}"+'/'+invoice+'/'+status+'/'+lunas,
                type: 'GET',
            })
            .done(function() {
                window.location.replace("{{ url('/transaction') }}"+'/detail/'+invoice.toLowerCase());
                $('.preloader').css('display','none');
            })
            .fail(function() {
                console.log("error");
            });
        }

        function closeTab(){
            var win = window.open("about:blank", "_self");
            win.close();
        }
    </script>
@endsection