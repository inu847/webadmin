<form action='{{ route('saveMenuRole') }}' method='POST' enctype='multipart/form-data'>
    @csrf
    <input type="hidden" name="role_id" value="{{ $detail->id }}">
    <div class='card'>
        <div class='card-header d-none'>
            Manage Menu
        </div>
        <div class='card-body'>
            <div class="main-card mb-3 card">
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <thead>
                        <tr>
                            <th class="col-md-2">Nama Role</th>
                            <td>{{ $detail->name }}</td>
                        </tr>
                        <!-- <tr>
                            <th>Owner</th>
                            <td>{{ $detail->user->name }}</td>
                        </tr> -->
                    </table>
                </div>
            </div>
            <label><b>List Menu</b></label>
            <div class="position-relative form-group">
                <div class="row">
                    @foreach($menus as $key => $value)
                    <div class="col-md-4">
                        <div class="custom-checkbox custom-control mt-4">
                            <input type="checkbox" id="menu_category_id{{ $value->id }}" name="menu_category_id[]" class="custom-control-input" value="{{ $value->id }}" {{
                            (getData::checkCategoryMenuRole($value->id,$detail->id))?'checked':'' }} />
                            <label class="custom-control-label" for="menu_category_id{{ $value->id }}">{{ $value->name }}</label>
                        </div>
                        @if($value->menu)
                        <div class="row ml-2">
                            @foreach($value->menu as $mvalue)
                                <div class="col-md-12">
                                    <div class="custom-checkbox custom-control mt-2">
                                        <input type="checkbox" id="menu{{ $mvalue->id }}" name="menu[]" class="custom-control-input" value="{{ $mvalue->id }}" {{
                                        (getData::checkMenuRole($mvalue->id,$detail->id))?'checked':'' }} />
                                        <label class="custom-control-label" for="menu{{ $mvalue->id }}">{{ $mvalue->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class='card-footer'>
            <button type='submit' class='btn btn-primary'>Save</button>
        </div>
    </div>
</form>