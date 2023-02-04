<div class="row">
    <div class="col-md-12">
        <div class="position-relative form-group">
            <label><b>Service</b></label>
            <input type="text" class="form-control" value="{{ $service->title }}" readonly />
            <input type="hidden" id="service_id" name="service_id" class="form-control" value="{{ $service->id }}"/>
            <input type="hidden" id="id_detail" name="id_detail"/>
            <input type="hidden" id="item_image_detail" name="item_image_detail"/>
        </div>
        <hr>
        <div class="position-relative form-group list_detail">
            <div class="row">
                <div class="col-sm-6 text-left"><b>List Item</b></div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-sm btn-success btn_add_detail"><i class="fa fa-plus"></i> Add</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="pt-3 pb-4" nowrap>#</th>
                            <th class="pt-3 pb-4" nowrap>Title</th>
                            <th class="pt-3 pb-4" nowrap>Image</th>
                            <th class="pt-3 pb-4" nowrap>Price</th>
                            <th class="pt-3 pb-4 text-center" nowrap>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detail as $key => $value)
                            <tr>
                                <form class="mb-0" id="delete_form_{{ $value['id'] }}" action="{{ route('items.destroy', $value['id']) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <th class="text-center text-muted" nowrap>{{ $key+1 }}</th>
                                    <td nowrap>{{ $value['title'] }}</td>
                                    <td nowrap>
                                        <a target="_blank" href="{{ $value['image'] }}"><img style="max-width: 80px; max-height: 80px;" src="{{ $value['image'] }}" alt=""></a>
                                    </td>
                                    <td nowrap>{{ number_format($value['price']) }}</td>
                                    <td class="text-center" nowrap>
                                        <div role="group" class="btn-group-sm btn-group btn-group-toggle">
                                            <a href="javascript:void(0)" data-service_id="{{ $service->id }}" data-id="{{ $value['id'] }}" data-image="{{ $value['image'] }}" class="editdetail btn btn-shadow btn-info">Edit</a>
                                            <a href="javascript:void(0)" data-service_id="{{ $service->id }}" data-id="{{ $value['id'] }}" class="deletedetail btn btn-shadow btn-danger btn_delete_{{ $value['id'] }}">Delete</a>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="position-relative form-group add_edit_detail d-none">
            <div class="row">
                <div class="col-sm-6 text-left"><b>Form Data</b></div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-sm btn-warning btn_back_to_list"><i class="fa fa-reply"></i> Back to List</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="position-relative form-group">
                        <label>Title <sup class="text-danger">* (Required)</sup></label>
                        <input type="text" id="title_detail" name="title_detail" class="form-control" value="{{ $single_data ? $single_data->title : '' }}"/>
                    </div>
                </div>
            </div>
            <div class="position-relative form-group">
                <label for="myLabel" class="">Description</label>
                <textarea id="description_detail" name="description_detail" class="form-control">{!! $single_data ? $single_data->description : '' !!}</textarea>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="position-relative form-group">
                        <label>Price</label>
                        <input type="text" id="price" name="price" class="form-control" value="{{ $single_data ? $single_data->price : '' }}"/>
                    </div>
                </div>
            </div>
            @if($single_data && $single_data->image)
                <div class="row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label>Recent Image</label>
                            <br>
                            <img src="{{ asset($single_data->image) }}" alt="">
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="position-relative form-group">
                        <label>Image</label>
                        <input type="file" id="image_detail" name="image_detail" class="form-control"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save"></i> {{ $single_data ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    </div>
</div>