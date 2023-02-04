<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>#</th>
                <th class="pt-4 pb-4" nowrap>Catalog</th>
                <th class="pt-4 pb-4" nowrap>Item Name</th>
                <th class="pt-4 pb-4 text-right" nowrap>Stock</th>
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('stock.destroy', $value['id']) }}" method="post">
                    @csrf
                    @method('delete')
                    <th class="text-center text-muted" nowrap>{{ $getData->firstItem() + $key }}</th>

                    <td class="" nowrap>
                        {{ $value['catalog_title'] }}
                    </td>

                    <td nowrap>
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left flex2">
                                    <div class="widget-heading">{{ $value['items_name'] }}</div>
                                    <div class="widget-subheading opacity-7">Created : {{ Date::fullDate($value['created_at']) }}</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-right" nowrap>
                        {{ myFunction::formatNumber($value['stock']) }}
                    </td>
                    <td class="text-center" nowrap>
                        <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                            <a href="{{ url('/stock/stock/'.$value['id'].'/'.$value['catalog']) }}" class="btn btn-shadow btn-primary">
                                Manage Stock
                            </a>
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