<style>
.dropdown-menu{
    position: absolute !important;
}
</style>
<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>#</th>
                <th class="pt-4 pb-4" nowrap>Catalog Title</th>
                <th class="pt-4 pb-4" nowrap>URL</th>
                <th class="pt-4 pb-4" nowrap>Key</th>
                <th class="pt-4 pb-4" nowrap>Type</th>
                {{--
                <th class="pt-4 pb-4 text-center" nowrap>Feature</th>
                <th class="pt-4 pb-4" nowrap>Layout Items</th>
                --}}
                <th class="pt-4 pb-4 text-center" nowrap>Status</th>
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('catalog.destroy', $value['id']) }}" method="post">
                    @csrf
                    @method('delete')
                    <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                    <td nowrap>
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <div class="widget-content-left">
                                        <a href="javascript:void(0)" data-featherlight="{{ str_replace('scaneat.id', 'scaneat.id', myFunction::getProtocol()).$value['catalog_logo'].'?'.time() }}">
                                            <img width="40" class="rounded" src="{{ strpos($value['catalog_logo'], 'amazonaws.com') !== false ? $value['catalog_logo'] : str_replace('scaneat.id', 'scaneat.id', myFunction::getProtocol()).$value['catalog_logo'].'?'.time() }}" alt="" />
                                        </a>
                                    </div>
                                </div>
                                <div class="widget-content-left flex2">
                                    <div class="widget-heading">{{ $value['catalog_title'] }}</div>
                                    <div class="widget-subheading opacity-7">Created : {{ Date::fullDate($value['created_at']) }}</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td nowrap>
                        <a href="https://{{ $value['catalog_username'].'.'.$value['domain'] }}" target="_blank">{{ $value['catalog_username'].'.'.$value['domain'] }}</a>
                    </td>
                    <td nowrap>
                        {{ $value['catalog_key'] }}
                    </td>
                    <td nowrap>
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
                                    echo "<br>";
                                    echo "[".$value->food_court->name."]";
                                }
                            }

                            if($value['advance_payment'] == "Y"){
                                echo '<br>[Pre Paid]';
                            }
                            elseif($value['advance_payment'] == "N"){
                                echo '<br>[Post Paid]';
                            }
                        @endphp
                    </td>
                    {{--
                    <td class="text-center" nowrap>
                        {{ $value['feature'] }}
                    </td>
                    <td nowrap>
                        {{ $value['layout'] }}
                    </td>
                    --}}
                    <td class="text-center" nowrap>
                        @php
                            $to = \Carbon\Carbon::now('Asia/Jakarta');
                            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value['created_at'], 'Asia/Jakarta');
                            $diff_in_minutes = $to->diffInMinutes($from);
                            
                            if($diff_in_minutes > 10){
                                echo "Ready";
                                $editable = true;
                            }
                            else{
                                echo "Verif Process";
                                $editable = false;
                            }

                            // check must filled otp and otp_expired 
                            $show_menu = false;
                            if($value['otp'] && $value['otp_validation']){
                                $to = \Carbon\Carbon::now('Asia/Jakarta');
                                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value['otp_expired'], 'Asia/Jakarta');
                                $diff_in_minutes = $to->diffInMinutes($from);
                                
                                if($diff_in_minutes < 60){
                                    // echo "Ready";
                                    $show_menu = true;
                                }
                            }

                            if(!$value['use_otp']){
                                $show_menu = true;
                            }
                        @endphp
                    </td>
                    <td class="text-center" nowrap>
                        <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="mr-2 dropdown-toggle btn btn-outline-link"><i class="fa fa-fw fa-wrench"></i></button>

                        <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu">
                            <ul class="nav flex-column  {{ $show_menu ? 'd-none' : '' }}">
                                <li class="nav-item"><a href="javascript:void(0);" data-id="{{ $value['id'] }}" class="btn-otp nav-link">Manage Items</a></li>
                                <!-- <li class="nav-item"><a href="javascript:void(0);" data-id="{{ $value['id'] }}" class="btn-otp nav-link">OTP Catalog</a></li> -->
                                <li class="nav-item"><a href="{{ url('/catalog/qrcode/'.$value['id']) }}" class="nav-link" target="_blank">QRCode Service</a></li>

                                @if($editable)
                                    <li class="nav-item"><a href="javascript:void(0);" data-id="{{ $value['id'] }}" class="btn-otp nav-link">Balance</a></li>
                                    <li class="nav-item"><a href="javascript:void(0);" data-id="{{ $value['id'] }}" class="btn-otp nav-link">Edit Catalog</a></li>
                                    <li class="nav-item"><a href="javascript:void(0);" data-id="{{ $value['id'] }}" class="btn-otp nav-link">Delete Catalog</a></li>
                                @endif
                            </ul>
                            <ul class="nav flex-column {{ $show_menu ? '' : 'd-none' }}">
                                <li class="nav-item"><a href="{{ url('/catalog/items/'.$value['id']) }}" class="nav-link">Manage Items</a></li>
                                <!-- <li class="nav-item"><a href="{{ url('/viewmenus/'.$value['id']) }}" class="nav-link">Items Layout</a></li> -->

                                <!-- <li class="nav-item"><a href="javascript:void(0);" data-id="{{ $value['id'] }}" class="btn-otp nav-link">OTP Catalog</a></li> -->

                                <li class="nav-item"><a href="{{ url('/catalog/qrcode/'.$value['id']) }}" class="nav-link" target="_blank">QRCode Service</a></li>
                                <!-- <li class="nav-item"><a href="{{ url('/catalog/qrcode/'.$value['id']) }}" class="nav-link" target="_blank">Web QRCode</a></li> -->
                                <!-- <li class="nav-item"><a href="{{ url('/catalog/qriscode/'.$value['id']) }}" class="nav-link" target="_blank">QRIS Code</a></li> -->

                                @if($editable)
                                    <li class="nav-item"><a href="{{ url('/catalog/balance/'.$value['id']) }}" class="nav-link">Balance</a></li>
                                    <li class="nav-item"><a href="javascript:void(0);" data-id="{{ $value['id'] }}" class="editlink nav-link">Edit Catalog</a></li>
                                    <!-- <li class="nav-item"><a href="javascript:void(0);" data-id="{{ $value['id'] }}" class="edittype nav-link">Change Type</a></li> -->
                                    <li class="nav-item"><a href="javascript:void(0);" data-id="{{ $value['id'] }}" class="deletelink nav-link">Delete Catalog</a></li>
                                @endif
                            </ul>
                        </div>
                        
                        <!-- price_type -->

                        <div role="group" class="btn-group-sm btn-group btn-group-toggle d-none">
                            <a href="{{ url('/catalog/items/'.$value['id']) }}" class="btn btn-shadow btn-primary">Manage Items</a>
                            <!-- <a href="{{ url('/viewmenus/'.$value['id']) }}" class="btn btn-shadow btn-primary">View Items</a> -->
                            <a href="{{ url('/catalog/qrcode/'.$value['id']) }}" class="btn btn-shadow btn-primary" target="_blank">QRCode Service</a>
                            <!-- <a href="{{ url('/catalog/qrcode/'.$value['id']) }}" class="btn btn-shadow btn-primary" target="_blank">Web QRCode</a> -->
                            <!-- <a href="{{ url('/catalog/qriscode/'.$value['id']) }}" class="btn btn-shadow btn-primary" target="_blank">QRIS Code</a> -->
                            <a href="{{ url('/catalog/balance/'.$value['id']) }}" class="btn btn-shadow btn-primary">Balance</a>
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit Catalog</a>
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete Catalog</a>
                        </div>

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