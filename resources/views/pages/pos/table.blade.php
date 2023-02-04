<div class="table-responsive">
    @if(count($getData))
        <table class="align-middle mb-0 table table-borderless table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" nowrap>No</th>
                    <th nowrap>Item Name</th>
                    <th class="text-right" nowrap>Item Price</th>
                    <th class="text-center" nowrap>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($getData as $key=>$value)
                <tr>
                    <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                    <td nowrap>
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <div class="widget-content-left">
                                        <a href="javascript:void(0)" onclick="loadGallery('{{ $value['id'] }}')">
                                            <img width="40" class="rounded" src="{{ strpos($value['item_image_primary'], 'amazonaws.com') !== false ? $value['item_image_primary'] : str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$value['item_image_primary'].'?'.time() }}" alt="" />
                                        </a>
                                    </div>
                                </div>
                                <div class="widget-content-left flex2">
                                    <div class="widget-heading">{{ $value['items_name'] }}</div>
                                    <div class="widget-subheading opacity-7">
                                        {{ $value['category_name'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-right" nowrap>
                        Rp. 
                        @if($value['items_discount'] > 0)
                            <span class="text-danger" style="text-decoration: line-through;">{{ number_format($value['items_price']) }}</span>
                            {{ number_format($value['items_price']-$value['items_discount']) }}
                        @else
                            {{ number_format($value['items_price']) }}
                        @endif
                    </td>
                    <td class="text-center" nowrap>
                        @if((getData::getAddons($value['id'])->count() > 0))
                            <a href="javascript:void(0)" class="btn-icon btn-icon-only btn-hover-shine btn btn-primary btn-shadow" onclick="addItemCustom('{{ $value['item'] }}','{{ $value['category_name'] }}','{{ $value['items_price'] }}','{{ $value['items_discount'] }}','{{ $value['items_name'] }}')">
                                <i class="lnr-plus-circle btn-icon-wrapper"></i>
                            </a>
                        @else
                            <a href="javascript:void(0)" class="btn-icon btn-icon-only btn-hover-shine btn btn-primary btn-shadow" onclick="addItem('{{ $value['item'] }}','{{ $value['category_name'] }}','{{ $value['items_price'] }}','{{ $value['items_discount'] }}','{{ $value['items_name'] }}')">
                                <i class="lnr-plus-circle btn-icon-wrapper"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        Choose Catalog First.
    @endif
</div>
<nav class="mt-3">
    {!! $pagination !!}
</nav>