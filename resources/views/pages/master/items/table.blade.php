<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>#</th>
                <th class="pt-4 pb-4" nowrap>Item Name</th>
                <th class="pt-4 pb-4 text-right" nowrap>Price</th>
                <th class="pt-4 pb-4" nowrap>Catalog</th>
                <th class="pt-4 pb-4 text-center" nowrap>Font Color</th>
                <!-- <th class="pt-4 pb-4 text-center" nowrap>Available</th> -->
                <!-- <th class="pt-4 pb-4 text-center" nowrap>Stock</th> -->
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('items.destroy', $value['id']) }}" method="post">
                    @csrf
                    @method('delete')
                    <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                    <td nowrap>
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <div class="widget-content-left">
                                        <a href="javascript:void(0)" onclick="loadGallery('{{ $value['id'] }}')">
                                            <img width="40" class="rounded" src="{{ strpos($value['item_image_primary'], 'amazonaws.com') !== false ? $value['item_image_primary'] : (str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$value['item_image_primary']) }}{{ '?'.time() }}" alt="" />
                                        </a>
                                    </div>
                                </div>
                                <div class="widget-content-left flex2">
                                    <div class="widget-heading">{{ $value['items_name'] }} {{ $value['item_sku'] ? '['.$value['item_sku'].']' : '' }}</div>
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
                    <td nowrap>
	                    {!! implode('<br>', $value['catalog']->pluck('catalog_title')->toArray()) !!}
	                </td>
                    <td class="text-center" nowrap>
                        <div class="badge badge-warning" style="background: {{ $value['items_color'] }};color:{{ $value['items_color'] }}">.</div>
                    </td>
                    <!-- <td class="text-center" nowrap>
                        {{ $value['ready_stock'] }}
                    </td> -->
                    <!-- <td class="text-center" nowrap>
                        {{ $value['stock'] ? ceil($value['stock']) : 0 }}
                    </td> -->
                    <td class="text-center" nowrap>
                        <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                            <a href="{{ url('/items/ingredient/'.$value['id']) }}" class="btn btn-shadow btn-primary">Ingredient</a>
                            <a href="{{ url('/items/addons/'.$value['id']) }}" class="btn btn-shadow btn-primary">Add Ons</a>
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
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