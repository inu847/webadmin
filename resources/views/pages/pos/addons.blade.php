@foreach($addons as $key => $vdetail)
<div class="text-primary mt-2 mb-1" style="font-size: 11px"><b>{{ $vdetail['category_name'] }}</b></div>
<table class="align-middle mt-0 mb-0 table table-borderless">
    <tbody>
        @foreach(getData::getItemAddons($item,$vdetail['category_id']) as $addons)
        <tr>
            <td style="padding: 5px 0">
                @if($addons['check_type'] == 'Multiple')
                    <input type="checkbox" name="addons[]" value="{{ $vdetail['category_id'].'-'.$addons['addon'].'-'.$addons['items_price'] }}" class="mr-2" {{ (getData::checkAddMultiple($group,$addons['category_id'],$addons['addon'],$addons['items_price']))?'checked':'' }}>
                @else
                    <input type="radio" name="addons-{{ $vdetail['category_id'] }}" value="{{ $vdetail['category_id'].'-'.$addons['addon'].'-'.$addons['items_price'] }}" class="mr-2" {{ (getData::checkAddSingle($group,$addons['category_id'],$addons['addon'],$addons['items_price']))?'checked':'' }}>
                @endif
                {{ $addons['items_name'] }} 
            </td>
            <td class="text-right" style="padding: 5px 0">
                @if($addons['items_price'] > 0)
                    {{ number_format($addons['items_price']) }}
                @else
                    Free
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
