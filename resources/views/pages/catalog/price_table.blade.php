<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>#</th>
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
                <th class="pt-4 pb-4" nowrap>Item Name</th>
                <th class="pt-4 pb-4 text-right" nowrap>Web Price</th>
                
                @foreach($prices as $val)
                    <th class="pt-4 pb-4 text-center" nowrap>{{ $val->price_name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('items.destroy', $value['id']) }}" method="post">
                    @csrf
                    @method('delete')
                    <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                    <td class="text-center" nowrap>
                        <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                        </div>
                    </td>

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
                                    <div class="widget-subheading opacity-7">Created : {{ Date::fullDate($value['created_at']) }}</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-right" nowrap>
                        @if($value['items_discount'] > 0)
                            <span class="text-danger" style="text-decoration: line-through;">{{ number_format($value['items_price']) }}</span>
                            {{ number_format($value['items_price']-$value['items_discount']) }}
                        @else
                            {{ number_format($value['items_price']) }}
                        @endif
                    </td>
                    
                    @foreach($prices as $val)
                        <td class="text-right" nowrap>
                            {{ getData::getPriceCatalogItem((!empty($catalog)?$catalog['id']:''),$val->price_type_id,$value['id']) }}
                        </td>
                    @endforeach
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