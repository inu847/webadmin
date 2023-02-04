<div class="table-responsive">
    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
        <thead>
            <tr>
                <th class="pt-4 pb-4 text-center" nowrap>#</th>
                <th class="pt-4 pb-4" nowrap>Sliders</th>
                <th class="pt-4 pb-4 text-center" nowrap>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($getData as $key=>$value)
            <tr>
                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('sliders.destroy', $value['id']) }}" method="post">
                    @csrf
                    @method('delete')
                    <th class="text-center text-muted">{{ $getData->firstItem() + $key }}</th>
                    <td nowrap>
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <div class="widget-content-left">
                                        <a href="javascript:void(0)" data-featherlight="{{ str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$value['sliders_image'].'?'.time() }}">
                                            <img width="40" class="rounded" src="{{ strpos($value['sliders_image'], 'amazonaws.com') !== false ? $value['sliders_image'] : str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$value['sliders_image'].'?'.time() }}" alt="" />
                                        </a>
                                    </div>
                                </div>
                                <div class="widget-content-left flex2">
                                    <div class="widget-heading">{{ $value['sliders_title'] }}</div>
                                    <div class="widget-subheading opacity-7">Created : {{ Date::fullDate($value['created_at']) }}</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center" nowrap>
                        <div role="group" class="btn-group-sm btn-group btn-group-toggle">
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