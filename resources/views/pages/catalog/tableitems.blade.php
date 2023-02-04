<div class="card-body">
    <div id="accordion" class="accordion-wrapper mb-3">
        @foreach($detail as $key => $vdetail)
        <div class="card">
            <div id="headingTwo" class="b-radius-0 card-header">
                <button type="button" data-toggle="collapse" data-target="#collapse{{ $vdetail['id'] }}" aria-expanded="false" aria-controls="collapseTwo" class="text-left m-0 p-0 btn btn-link btn-block">
                    <p class="m-0 p-0" style="font-size:15px;color:#666"><b>{{ $vdetail['category_name'] }}</b></p>
                    <div class="mt-2">
                        <a href="javascript:void(0)" class="changeposition btn-hover-shine btn btn-primary btn-shadow btn-sm"
                            data-catalog="{{ $catalog['id'] }}" 
                            data-me="{{ $vdetail['category_id'] }}"
                            data-current="{{ $vdetail['category_position'] }}" 
                            data-status="Category">
                            Change Position
                        </a>
                        <a href="javascript:void(0)" data-catalog="{{ $catalog['id'] }}" data-me="{{ $vdetail['category_id'] }}" data-type="Category" class="deletelink btn-hover-shine btn btn-danger btn-shadow btn-sm">
                            Delete Category
                        </a>
                    </div>
                </button>
            </div>
            <div data-parent="#accordion" id="collapse{{ $vdetail['id'] }}" class="collapse">
                <div class="card-body">
                    @foreach(getData::getCatalogSubCategory($catalog['id'],$vdetail['category_id']) as $subcategory)
                    @if($subcategory['subcategory_id'] > 0)
                    <div class="text-warning mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <b>{{ $subcategory['subcategory_name'] }}</b>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="javascript:void(0)" class="changeposition btn-hover-shine btn btn-warning btn-shadow btn-sm"
                                    data-catalog="{{ $catalog['id'] }}" 
                                    data-me="{{ $subcategory['subcategory_id'] }}"
                                    data-current="{{ $subcategory['subcategory_position'] }}" 
                                    data-status="SubCategory" 
                                    style="width:40%">
                                    Change Position
                                </a>
                                <a href="javascript:void(0)" class="deletelink btn-hover-shine btn btn-danger btn-shadow btn-sm" data-catalog="{{ $catalog['id'] }}" data-me="{{ $subcategory['subcategory_id'] }}" data-type="SubCategory" style="width:40%">
                                    Delete Sub Category
                                </a>
                            </div>
                        </div>
                    </div>
                    @foreach(getData::getCatalogSubCategoryItems($catalog['id'],$vdetail['category_id'],$subcategory['subcategory_id']) as $subcategoryitem)
                    <div class="ml-3 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                {{ $subcategoryitem['items_name'] }}
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="javascript:void(0)" class="changeposition btn-hover-shine btn btn-secondary btn-shadow btn-sm"
                                    data-catalog="{{ $catalog['id'] }}" 
                                    data-me="{{ $subcategoryitem['id'] }}"
                                    data-current="{{ $subcategoryitem['item_position'] }}" 
                                    data-status="Item" 
                                    style="width:30%">
                                    Change Position
                                </a>
                                <a href="javascript:void(0)" class="deletelink btn-hover-shine btn btn-danger btn-shadow btn-sm" data-catalog="{{ $catalog['id'] }}" data-me="{{ $subcategoryitem['id'] }}" data-type="Item" style="width:20%">
                                    Delete Item
                                </a>
                                <a href="javascript:void(0)" class="availablelink btn-hover-shine btn btn-dark btn-shadow btn-sm" data-catalog="{{ $catalog['id'] }}" data-me="{{ $subcategoryitem['id'] }}" data-available="{{ $subcategoryitem['available_item'] }}" data-type="Item" style="width:20%">
                                    Hide Item
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    @foreach(getData::getCatalogItems($catalog['id'],$vdetail['category_id'],'0') as $item)
                    <div class="row">
                        <div class="col-md-7">
                            <b class="text-success">{{ $item['items_name'] }}</b>
                        </div>
                        <div class="col-md-5 text-right">
                            <div class="mb-3">
                                <a href="javascript:void(0)" class="changeposition btn-hover-shine btn btn-success btn-shadow btn-sm"
                                    data-catalog="{{ $catalog['id'] }}" 
                                    data-me="{{ $item['id'] }}"
                                    data-current="{{ $item['item_position'] }}" 
                                    data-status="Item" 
                                    style="width:30%">
                                    Change Position
                                </a>
                                <a href="javascript:void(0)" class="deletelink btn-hover-shine btn btn-danger btn-shadow btn-sm" data-catalog="{{ $catalog['id'] }}" data-me="{{ $item['id'] }}" data-type="Item" style="width:20%">
                                    Delete Item
                                </a>
                                <a href="javascript:void(0)" class="availablelink btn-hover-shine btn btn-dark btn-shadow btn-sm" data-catalog="{{ $catalog['id'] }}" data-me="{{ $item['id'] }}" data-available="{{ $item['available_item'] }}" data-type="Item" style="width:20%">
                                    Hide Item
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>