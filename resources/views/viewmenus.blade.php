@extends('layouts.main')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-menu icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }} ( {{ $catalog->catalog_title }} )
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ route('catalog.index') }}" class="btn-shadow btn btn-success btn-sm"><i class="icon lnr-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<div class="page-content browse container-fluid p-0">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3 card">
                <div class="card-header card-header-tab-animation">
                    <ul class="nav nav-justified">
                        <li class="nav-item"><a href="{{ url('/catalog/items/'.$catalog->id) }}" class="nav-link">Item List</a></li>
                        <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-1" class="active nav-link">Item View</a></li>
                        <li class="nav-item"><a href="{{ url('/catalog/item_prices/'.$catalog->id) }}" class="nav-link">Item Price</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-eg115-0" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    
                                </div>
                            </div>
                        </div>
                      <form action="{{url('viewmenus')}}/{{ $id }}" method="post">
                        <div class="tab-pane active" id="tab-eg115-1" role="tabpanel">
                              <div class="row mb-2">
                                  <div class="col-md-6">
                                      
                                  </div>
                                  <div class="col-md-6 text-right">
                                      <div role="group" class="btn-group-sm btn-group">
                                          
                                          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                                      </div>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="col-md-12 p-0" id="indexVue">
                                      @csrf
                                      <div class="row m-2" id="loadpage">
                                          <div id="SetingSubCat" class="col">
                                              <h5>Sub Category</h5>   
                  
                                              <div class="row m-2">
                                                  @if ($view_subcat == 'grid')
                                                    <div class="custom-control custom-radio custom-control-inline mr-3">
                                                      <input onclick="subcatgridsample()" value="grid" class="custom-control-input" type="radio" name="view_subcat" id="RadioSubCat1" checked>
                                                      <label class="custom-control-label" for="RadioSubCat1">
                                                        Grid View
                                                      </label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline mr-3">
                                                      <input onclick="subcatlistsample()" value="list" class="custom-control-input" type="radio" name="view_subcat" id="RadioSubCat2">
                                                      <label class="custom-control-label" for="RadioSubCat2">
                                                        List View
                                                      </label>
                                                    </div>
                                                  @endif
                                                  @if ($view_subcat == 'list')
                                                      <div class="custom-control custom-radio custom-control-inline mr-3">
                                                        <input onclick="subcatgridsample()" value="grid" class="custom-control-input" type="radio" name="view_subcat" id="RadioSubCat1">
                                                        <label class="custom-control-label" for="RadioSubCat1">
                                                          Grid View
                                                        </label>
                                                      </div>
                                                      <div class="custom-control custom-radio custom-control-inline mr-3">
                                                        <input onclick="subcatlistsample()" value="list" class="custom-control-input" type="radio" name="view_subcat" id="RadioSubCat2" checked>
                                                        <label class="custom-control-label" for="RadioSubCat2">
                                                          List View
                                                        </label>
                                                      </div>                                    
                                                  @endif
                                              </div>  
                                              <img id="subgridview" style="maxwidth: 380px" src="{{asset('images/viewmenusample/subgridview.png')}}" class="img-fluid mt-2">
                                              <img id="sublistview" style="maxwidth: 380px" src="{{asset('images/viewmenusample/sublistview.png')}}" class="img-fluid mt-2">
                                          </div>

                                          <div id="SetingMenus" class="col">
                                              <h5>List Item</h5>
                                              
                                              <div class="row m-2">
                                                  @if ($view_item == 'grid')
                                                    <div class="custom-control custom-radio custom-control-inline mr-3">
                                                      <input onclick="menugridsample()" value="grid" class="custom-control-input" type="radio" name="view_item" id="RadioItem1" checked>
                                                      <label class="custom-control-label" for="RadioItem1">
                                                        Grid View
                                                      </label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline mr-3">
                                                      <input onclick="menulistsample()" value="list" class="custom-control-input" type="radio" name="view_item" id="RadioItem2">
                                                      <label class="custom-control-label" for="RadioItem2">
                                                        List View
                                                      </label>
                                                    </div>
                                                  @endif
                                                  @if ($view_item == 'list')
                                                      <div class="custom-control custom-radio custom-control-inline mr-3">
                                                        <input onclick="menugridsample()" value="grid" class="custom-control-input" type="radio" name="view_item" id="RadioItem1">
                                                        <label class="custom-control-label" for="RadioItem1">
                                                          Grid View
                                                        </label>
                                                      </div>
                                                      <div class="custom-control custom-radio custom-control-inline mr-3">
                                                        <input onclick="menulistsample()" value="list" class="custom-control-input" type="radio" name="view_item" id="RadioItem2" checked>
                                                        <label class="custom-control-label" for="RadioItem2">
                                                          List View
                                                        </label>
                                                      </div>                                    
                                                  @endif
                                              </div>
                                              <img id="menugridview" style="maxwidth: 380px" src="{{asset('images/viewmenusample/itemgridview.png')}}" class="img-fluid mt-2">
                                              <img id="menulistview" style="maxwidth: 380px" src="{{asset('images/viewmenusample/itemlistview.png')}}" class="img-fluid mt-2">
                                          </div>
                                      </div>
                                      <!-- <button class="m-3 btn btn-primary btn-lg float-right mr-5" type="submit">Save</button> -->
                                  </div>
                              </div>
                        </div>

                        <div class="row">
                          <h5 class="col-md-12">Theme</h5>
                          <div class="col-md-3">
                              <div class="custom-control custom-checkbox image-checkbox">
                                  <input type="radio" name="theme" value="default" class="custom-control-input" id="ck1b" {{ ($theme == null) ? 'checked':'' }}>
                                  <label class="custom-control-label" for="ck1b"> Default
                                    <img id="menugridview" style="maxwidth: 380px" src="{{asset('images/viewmenusample/itemgridview.png')}}" class="img-fluid mt-2">
                                  </label>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="custom-control custom-checkbox image-checkbox">
                                  <input type="radio" value="1" name="theme" class="custom-control-input" id="ck1a" {{ ($theme == '1') ? 'checked':'' }}>
                                  <label class="custom-control-label" for="ck1a"> Hovered
                                    <img id="menugridview" style="maxwidth: 380px" src="{{asset('images/viewmenusample/1.png')}}" class="img-fluid mt-2">
                                  </label>
                              </div>
                          </div>
                      </div>
                    </form>

                        <div class="tab-pane" id="tab-eg115-2" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
  var x = document.getElementById("subgridview")
  var y = document.getElementById("sublistview")
  var z = document.getElementById("menugridview")
  var a = document.getElementById("menulistview")

  if({!! json_encode($view_subcat) !!} == 'grid') {
    subcatgridsample()
  }

  if({!! json_encode($view_subcat) !!} == 'list') {
    subcatlistsample()
  }

  if({!! json_encode($view_item) !!} == 'grid') {
    menugridsample()
  }

  if({!! json_encode($view_item) !!} == 'list') {
    menulistsample()
  }
  
  function subcatgridsample() {
    x.style.display = "block"
    y.style.display = "none"
  }

  function subcatlistsample() {
    y.style.display = "block"
    x.style.display = "none"
  }  

  function menugridsample() {
    z.style.display = "block"
    a.style.display = "none"
  }  

  function menulistsample() {
    z.style.display = "none"
    a.style.display = "block"
  }
</script>
@endsection
