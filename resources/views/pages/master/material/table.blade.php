<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4  text-center" nowrap>#</th>
                <th class="pt-4 pb-4 " nowrap>Material Name</th>
                <th class="pt-4 pb-4  text-right" nowrap>Total Stock</th>
                {{--
                @if((Session::get('catalogsession')=='All'))
                    <th class="pt-4 pb-4  text-right" nowrap>Total Stock in Catalog</th>
                @endif
                --}}
                @if((Session::get('catalogsession')!='All'))
                    <th class="pt-4 pb-4  text-right" nowrap>Stock Used</th>
                @endif
                <th class="pt-4 pb-4  text-right" nowrap>Stock Available</th>
                
                <th class="pt-4 pb-4  text-center" nowrap>Unit</th>
                <th class="pt-4 pb-4  text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('material.destroy', $value['id']) }}" method="post">
                    @csrf
                    @method('delete')
                    <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>
                    <td nowrap>
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left flex2">
                                    <div class="widget-heading">{{ $value['name'] }}</div>
                                    <div class="widget-subheading opacity-7">Created : {{ Date::fullDate($value['created_at']) }}</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-right" nowrap>
                        {{ myFunction::formatNumber($value->total_stock, 1) }}
                    </td>
                    {{--
                    @if((Session::get('catalogsession')=='All'))
                        <td class="text-right" nowrap>
                            {{ myFunction::formatNumber(getData::countStockBranch($value['id']),1) }}
                        </td>
                    @endif
                    --}}
                    @if((Session::get('catalogsession')!='All'))
                        <td class="text-right" nowrap>{{ myFunction::formatNumber($value->stock_used) }}</td>
                    @endif
                    <td class="text-right" nowrap>
                        {{ myFunction::formatNumber($value->stock_available,1) }}
                    </td>
                    <td class="text-center" nowrap>
                        {{ $value['uom'] }}
                    </td>
                    <td class="text-center" nowrap>
                        <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                            @if((Session::get('catalogsession')!='All'))
                            <a href="{{ url('/material/stock/'.$value['id']) }}" class="btn btn-shadow btn-primary">
                                Manage Stock
                            </a>
                            @endif
                            {{--
                            @if((Session::get('catalogsession')=='All'))
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="editlink btn btn-shadow btn-info">Edit</a>
                            <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
                            @endif
                            --}}
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