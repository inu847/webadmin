@foreach($detail as $key => $vdetail)
<div class="text-warning mt-3 mb-3"><b>{{ $vdetail['category_name'] }}</b></div>
<table class="align-middle mb-0 table table-borderless table-striped table-hover">
    <thead>
        <tr>
            <th nowrap>AddOn Name</th>
            <th class="text-right" nowrap>Category</th>
            <th class="text-right" nowrap>Type</th>
            <th class="text-right" nowrap>Price</th>
            <th class="text-center" nowrap>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach(getData::getItemAddons($item['id'],$vdetail['category_id']) as $addons)
        <tr>
            <td nowrap>{{ $addons->addons->name ?? null }}</td>
            <td class="text-right" nowrap>{{ $addons->category_id ?? null }}</td>
            <td class="text-right" nowrap>{{ $addons->check_type ?? null }}</td>
            <td class="text-right" nowrap>{{ number_format($addons->addons->price) ?? null }}</td>
            <td class="text-center" nowrap>
                <a href="javascript:void(0)" data-id="{{ $addons['id'] }}" data-item="{{ $item['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach