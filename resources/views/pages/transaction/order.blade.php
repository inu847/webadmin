@extends('layouts.main')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="{{ $icon }} icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            @if(!empty($request['searchfield']))
                <a href="{{ url('/transaction/'.$status) }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
            @endif
        </div>
    </div>
</div>

<div class="tabs-animation">
    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <form action="{{ url('/transaction/'.$status) }}" method="GET">
                    <div class="card-header">
                        <input type="text" class="form-control" name="searchfield" placeholder="Search Here..." value="{{ (!empty($request['searchfield']))?$request['searchfield']:'' }}">
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" nowrap>#</th>
                                <th nowrap>Invoice Number</th>
                                <th class="text-center" nowrap>Transaction Via</th>
                                <th class="text-center" nowrap>Table/ Room/ Address</th>
                                <th class="text-center" nowrap>Payment Type & Method</th>
                                <th class="text-right" nowrap>Payment</th>
                                <th class="text-center" nowrap>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($getData as $key=>$value)
                            <tr>
                                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('category.destroy', $value['id']) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
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
                                    <td class="text-center" nowrap>
                                        @if($value['via'] == 'System')
                                            Web Order
                                        @else
                                            {{ $value['via'] }}
                                        @endif
                                    </td>
                                    <td class="text-center" nowrap>
                                        {{ $value['position'] }}
                                    </td>
                                    <td class="text-center" nowrap>
                                        {!! isset($invoice_type[$value['invoice_type_id']]) ? '<b>'.$invoice_type[$value['invoice_type_id']].'</b><br>' : '' !!}
                                        {{ myFunction::payment_type($value['payment_method']) }}
                                    </td>
                                    <td class="text-right" nowrap>
                                        {{ number_format($value['amount']) }}
                                    </td>
                                    <td class="text-center" nowrap>
                                        <a href="{{ url('/transaction/detail/'.$value['invoice_number']) }}" class=" btn-hover-shine btn btn-primary btn-shadow btn-sm">
                                            Detail
                                        </a>
                                    </td>
                                </form>
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
            </div>
        </div>
    </div>
</div>
@endsection


@section('customjs')
    <script src='https://cdn.rawgit.com/admsev/jquery-play-sound/master/jquery.playSound.js'></script>
    <script type="text/javascript">
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: "a01ada789ab34d372572",
            cluster: "ap1",
            encrypted: true,
        });
        Echo.channel('pushernotif').listen('NotifEvent', function(e) {
            if(e.status == "{{ ucwords($status) }}"){
                $.playSound("{{ asset('/swiftly.mp3?'.time()) }}")
                setTimeout(function() {
                    // window.location.reload();
                    window.location.href = window.location.href;
                }, 1500);
            }
        });
    </script>
@endsection