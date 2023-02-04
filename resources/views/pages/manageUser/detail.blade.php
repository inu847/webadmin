{{-- @extends('layouts.main')

@section('content')
@endsection --}}
<div class='card'>
    <div class="card-header card-header-tab-animation">
        <ul class="nav nav-justified">
            <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-0" class="active nav-link">Basic</a></li>
            <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-1" class="nav-link">Catalog</a></li>
            <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-2" class="nav-link">Member</a></li>
            <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-3" class="nav-link">Affilate</a></li>
        </ul>
    </div>
    <div class='card-body'>
        <div class="tab-content">
            <div class="tab-pane active" id="tab-eg115-0" role="tabpanel">
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <tbody>
                            <tr>
                                <tr>
                                    <th class="col-md-4">Photo</th>
                                    <td>
                                        @if ($detail->photo)
                                            <img class="img-thumbnail" src="{{ asset('storage/'.$detail->photo )}}" alt="" width="100px" width="100px">
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td>{{ $detail->username }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $detail->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $detail->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $detail->phone }}</td>
                                </tr>
                                <!-- <tr>
                                    <th>Kuota Catalog</th>
                                    <td>{{ $detail->number_catalog }}</td>
                                </tr> -->
                                <tr>
                                    <th>Total Catalog</th>
                                    <td>{{ optional($detail->catalog())->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Total Member</th>
                                    <td>{{ optional($detail->member())->count() }}</td>
                                </tr>
                                <!-- <tr>
                                    <th>Level</th>
                                    <td>{{ $detail->level }}</td>
                                </tr> -->
                                <tr>
                                    <th>Status</th>
                                    <td>{{ $detail->active == 'Y' ? 'Active' : 'Not Active' }}</td>
                                </tr>
                                <!-- <tr>
                                    <th>User Show</th>
                                    <td>{{ $detail->user_show }}</td>
                                </tr>
                                <tr>
                                    <th>Legitimate</th>
                                    <td>{{ $detail->legitimate }}</td>
                                </tr>
                                <tr>
                                    <th>Owner</th>
                                    <td>{{ $detail->owner }}</td>
                                </tr>
                                <tr>
                                    <th>Parent</th>
                                    <td>{{ $detail->parent_id }}</td>
                                </tr>
                                <tr>
                                    <th>Catalog</th>
                                    <td>{{ $detail->catalog }}</td>
                                </tr> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="tab-eg115-1" role="tabpanel">
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(optional($detail->catalog())->count())
                                @foreach($detail->catalog()->get() as $value)
                                    <tr>
                                        <td>
                                            <img width="40" class="rounded" src="{{ strpos($value['catalog_logo'], 'amazonaws.com') !== false ? $value['catalog_logo'] : str_replace('scaneat.id', 'scaneat.id', myFunction::getProtocol()).$value['catalog_logo'].'?'.time() }}" alt="" />
                                        </td>
                                        <td>{{ $value['catalog_title'] }}</td>
                                        <td>
                                            @php
                                                if($value['catalog_type'] == 1){
                                                    echo 'Resto';
                                                }
                                                elseif($value['catalog_type'] == 2){
                                                    echo 'Hotel';
                                                }
                                                elseif($value['catalog_type'] == 3){
                                                    echo 'Food Court';

                                                    if($value->food_court){
                                                        echo " [".$value->food_court->name."]";
                                                    }
                                                }

                                                if($value['advance_payment'] == "Y"){
                                                    echo ' [Pre Paid]';
                                                }
                                                elseif($value['advance_payment'] == "N"){
                                                    echo ' [Post Paid]';
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="tab-eg115-2" role="tabpanel">
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Catalog</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(optional($detail->member())->count())
                                @foreach($detail->member()->get() as $value)
                                    <tr>
                                        <td>{{ $value['name'] }}</td>
                                        <td>{{ $value['email'] }}</td>
                                        <td>{{ optional($value->catalogUser)->catalog_title }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="tab-eg115-3" role="tabpanel">
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Affiliate Percent</th>
                                <th>Total Transaksi</th>
                                <th>Affiliate Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_grand_total = 0;
                                $total_affiliate_income = 0;
                            @endphp
                            @foreach($affiliate as $value)
                                @php
                                    $total_grand_total += $value->grand_total;
                                    $total_affiliate_income += $value->affiliate_income;
                                @endphp
                                <tr>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->affiliate_percent }}</td>
                                    <td>Rp. {{ number_format($value->grand_total) }}</td>
                                    <td>Rp. {{ number_format($value->affiliate_income) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align: right;">Total :</td>
                                <td>Rp. {{ number_format($total_grand_total) }}</td>
                                <td>Rp. {{ number_format($total_affiliate_income) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>