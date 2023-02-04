@extends('layouts.main') 
@section('content')
<style>
    /**
     * Nestable
     */

.dd {
  position: relative;
  display: block;
  margin: 0;
  padding: 0;
  /* max-width: 600px; */
  list-style: none;
  font-size: 13px;
  line-height: 20px;
}

.dd-edit-box input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13px;
  color: #444;
  text-shadow: 0 1px 0 #fff;
  width: 45%;
}

.dd-edit-box { position: relative; }

.dd-edit-box i {
  right: 0;
  overflow: hidden;
  cursor: pointer;
  position: absolute;
}

.dd-item-blueprint { display: none; }

.dd-list {
  display: block;
  position: relative;
  margin: 0;
  padding: 0;
  list-style: none;
}

.dd-list .dd-list { padding-left: 30px; }

.dd-collapsed .dd-list { display: none; }

.dd-item,  .dd-empty,  .dd-placeholder {
  text-shadow: 0 1px 0 #fff;
  display: block;
  position: relative;
  margin: 0;
  padding: 0;
  min-height: 20px;
  font-size: 13px;
  line-height: 20px;
}

.dd-handle {
  cursor: move;
  display: block;
  height: 30px;
  margin: 5px 0;
  padding: 5px 10px;
  color: #333;
  text-decoration: none;
  font-weight: bold;
  border: 1px solid #AAA;
  background: #E74C3C;
  background: -webkit-linear-gradient(top, #E74C3C 0%, #C0392B 100%);
  background: -moz-linear-gradient(top, #E74C3C 0%, #C0392B 100%);
  background: linear-gradient(top, #E74C3C 0%, #C0392B 100%);
  -webkit-border-radius: 3px;
  border-radius: 3px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

.dd-handle:hover {
  color: #2ea8e5;
  background: #fff;
}

.dd-item > button {
  display: inline-block;
  position: relative;
  cursor: pointer;
  float: left;
  width: 24px;
  height: 20px;
  margin: 5px 5px 5px 30px;
  padding: 0;
  white-space: nowrap;
  overflow: hidden;
  border: 0;
  background: transparent;
  font-size: 16px;
  line-height: 1;
  text-align: center;
  font-weight: bold;
  color: f black;
}

.dd-item .item-remove {
  position: absolute;
  right: 7px;
  height: 19px;
  padding: 0 5px;
  top: 4px;
  /* overflow: auto; */
}

.dd-item .item-edit {
  position: absolute;
  right: 27px;
  height: 19px;
  padding: 0 5px;
  top: 4px;
  overflow: auto;
}

.dd3-item > button:first-child { margin-left: 30px; }

.dd-item > button:before {
  display: block;
  position: absolute;
  width: 100%;
  text-align: center;
  text-indent: 0;
}

.dd-placeholder,  .dd-empty {
  margin: 5px 0;
  padding: 0;
  min-height: 30px;
  background: #f2fbff;
  border: 1px dashed #b6bcbf;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

.dd-empty {
  border: 1px dashed #bbb;
  min-height: 100px;
  background-color: #e5e5e5;
  background-image: -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),  -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
  background-image: -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),  -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
  background-image: linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),  linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
  background-size: 60px 60px;
  background-position: 0 0, 30px 30px;
}

.dd-dragel {
  height: 60px;
  position: absolute;
  pointer-events: none;
  z-index: 9999;
}

.dd-dragel > .dd-item .dd-handle { margin-top: 0; }

.dd-dragel .dd-handle {
  -webkit-box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
  box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
}

/**
     * Nestable Draggable Handles
     */

.dd3-content {
  display: block;
  height: 30px;
  margin: 5px 0;
  padding: 5px 10px 5px 40px;
  color: #333;
  text-decoration: none;
  font-weight: bold;
  border: 1px solid #ccc;
  border: 1px solid #898989;
  background: #fafafa;
  background: -webkit-linear-gradient(top, #F4F4F4 10%, #C9C9C9 100%);
  background: -moz-linear-gradient(top, #F4F4F4 10%, #C9C9C9 100%);
  background: linear-gradient(top, #F4F4F4 10%, #C9C9C9 100%);
  -webkit-border-radius: 3px;
  border-radius: 3px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

.dd3-content:hover {
  color: #2ea8e5;
  background: #E74C3C;
  background: -webkit-linear-gradient(top, #E5E5E5 10%, #FFFFFF 100%);
  background: -moz-linear-gradient(top, #E5E5E5 10%, #FFFFFF 100%);
  background: linear-gradient(top, #E5E5E5 10%, #FFFFFF 100%);
}

.dd-dragel > .dd3-item > .dd3-content { margin: 0; }

.dd3-handle {
  position: absolute;
  margin: 0;
  left: 0;
  top: 0;
  cursor: move;
  width: 30px;
  text-indent: 100%;
  white-space: nowrap;
  overflow: hidden;
 bold;
  border: 1px solid #807B7B;
  text-shadow: 0 1px 0 #807B7B;
  background: #E74C3C;
  background: -webkit-linear-gradient(top, #E74C3C 0%, #C0392B 100%);
  background: -moz-linear-gradient(top, #E74C3C 0%, #C0392B 100%);
  background: linear-gradient(top, #E74C3C 0%, #C0392B 100%);
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.dd3-handle:before {
  content: '≡';
  display: block;
  position: absolute;
  left: 0;
  top: 3px;
  width: 100%;
  text-align: center;
  text-indent: 0;
  color: #fff;
  font-size: 20px;
  font-weight: normal;
}

.dd3-handle:hover {
  background: #E74C3C;
  background: -webkit-linear-gradient(top, #E74C3C 0%, #C0392B 100%);
  background: -moz-linear-gradient(top, #E74C3C 0%, #C0392B 100%);
  background: linear-gradient(top, #E74C3C 0%, #C0392B 100%);
}
</style>
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-display2 icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }} ( {{ $catalog['catalog_title'] }} )
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
                        <li class="nav-item"><a data-toggle="tab" href="#tab-eg115-0" class="active nav-link">Item List</a></li>
                        <li class="nav-item"><a href="{{ url('/viewmenus/'.$catalog['id']) }}" class="nav-link">Item View</a></li>
                        <li class="nav-item"><a href="{{ url('/catalog/item_prices/'.$catalog['id']) }}" class="nav-link">Item Price</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-eg115-0" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-6" id="indexVue">
                                    <a href="javascript:void(0)" class="btn-shadow btn btn-dark btn-sm" v-on:click="showForm('create')"><i class="fa fa-plus"></i> Add Items</a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <div role="group" class="btn-group-sm btn-group">
                                        <!-- <button class="btn-shadow  btn btn-dark">Refresh</button>
                                        <button type="button" class="btn-shadow  btn btn-dark">Remove</button> -->
                                        {{-- <button id="btnReload" type="button" class="btn btn-warning"><i class="fas fa-sync"></i> Reset Data</button> --}}
                                        <button id="btnOutput" type="button" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="dd" id="domenu">
                                        <li class="dd-item-blueprint">
                                            <div class="dd-handle dd3-handle"></div>
                                            <div class="dd3-content"> <span>[item_name]</span>
                                                <button class="btn item-remove"><i class="fas fa-trash"></i></button>
                                                <!-- <button class="btn item-edit"><i class="fa fa-edit text-info"></i></button> -->
                                                <div class="dd-edit-box" style="display: none;">
                                                    <input type="text" name="title" placeholder="name">
                                                    <input type="hidden" name="http" placeholder="http://">
                                                    <input type="hidden" name="category_id" placeholder="category_id">
                                                    <input type="hidden" name="subcategory_id" placeholder="subcategory_id">
                                                    <input type="hidden" name="item_id" placeholder="item_id">
                                                    <i>&#x270e;</i>
                                                </div>
                                            </div>
                                        </li>
                                        <ol class="dd-list">
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-eg115-1" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    
                                </div>
                            </div>
                        </div>
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

<textarea id="tree_temp" class="d-none"></textarea>
@endsection 

@section('modal')
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div id="modalSize" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!-- Form Loader -->
            <div class="formLoader">
                <div class="jumper">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
            <!-- End -->
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="contentForm"></div>
        </div>
    </div>
</div>
@endsection 

@section('customjs')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript" src="{{ url('js/jquery.domenu-0.0.1.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        var domenu = $('#domenu').domenu();
        $('#domenu[disabled="disabled"]').next().find(".note-editable").attr("contenteditable", false);
        $('#tree_temp').val('{!! $tree !!}');
        var data_menu = $('#tree_temp').val();
        domenu.parseJson(data_menu);
        // console.log(data_menu);
        // saving data
        $('#btnOutput').on('click', function() {
            var temp_btn = $('#btnOutput').html();
            $('#btnOutput').html('<i class="fas fa-sync fa-pulse"></i> Processing...').prop('disabled', true);
            var str = domenu.toJson();
            // str = str.replace(/&/g,"dan");
            // console.log(str);
            $.ajax({
                method: 'POST',
                url: "{{ url('catalog/save_detail') }}",
                data: 'str=' + str + '&_token=' + "{{ csrf_token() }}" + '&id=' + "{{ $catalog['id'] }}",
                success: function(data) {
                    toastr.success('Data Updated')
                    data_menu = data.tree;
                    $('#tree_temp').val(data_menu)
                    // domenu.parseJson('{!! $tree !!}', data.tree);
                    $('#btnReload').click();
                    $('#btnOutput').html(temp_btn).prop('disabled', false);
                    location.reload();
                }
            })
        });

        // reset data
        $('#btnReload').on('click', function() {
            data_menu = $('#tree_temp').val();
            domenu.parseJson(data_menu,data_menu);
            // alert('new')
        });

        // $('body').on('click', '.item-edit', function() {
        //     console.log($(this).parent().parent().html());
        // });
        
        /*
            getLists(params)
            // parseJson(data, override)
            // toJson()
            expandAll()
            collapseAll()
            expand(callback)
            collapse(callback)
            // getListNodes()
        */
    })
</script>
<script type="text/javascript">
    new Vue({
        el: "#indexVue",
        data() {
            return {
                csrf: "",
                deletedata: "",
                formErrors: {},
                notif: [],
            };
        },
        mounted: function () {
            this.csrf = "{{ csrf_token() }}";
            let self = this;
            $(document).on("click", ".editlink", function (evetn, id) {
                var id = $(this).attr("data-id");
                self.showForm("edit", id);
            });
        },
        methods: {
            showForm: function (action, id = null) {
                // preloader();
                $("#modalSize").removeClass("modal-sm");
                $("#modalSize").addClass("modal-xl");
                if (action == "create") {
                    $("#titleModal").html("Create New");
                    $.ajax({
                        url: "{{ url('/catalog/additems/'.$catalog['id']) }}", 
                        type: "GET", 
                    })
                    .done(function(data) {
                        $("#modalForm").modal("show");
                        $("#contentForm").html(data);
                        afterpreloader();
                    })
                    .fail(function() {
                        Swal.fire("Ops!", "Load data failed.", "error");
                    });
                }
            },
        },
    });
</script>
<script type="text/javascript">
    function getPosition(){
        catalog = "{{ $catalog['id'] }}";
        category = $("#category_id").val();
        $.ajax({
            url: "{{ url('/catalog/position/category') }}"+'/'+catalog+'/'+category,
            type: 'GET',
        })
        .done(function(data) {
            $("#category_position").attr({
               "max" : data,
               "value" : data
            });
            getPositionSub();
        })
        .fail(function() {
            console.log("error");
        });
    }
    function getPositionSub(){
        catalog = "{{ $catalog['id'] }}";
        category = $("#category_id").val();
        subcategory = $("#subcategory_id").val();
        $.ajax({
            url: "{{ url('/catalog/position/subcategory') }}"+'/'+catalog+'/'+category+'/'+subcategory,
            type: 'GET',
        })
        .done(function(data) {
            $("#subcategory_position").attr({
               "max" : data,
               "value" : data
            });
        })
        .fail(function() {
            console.log("error");
        });
    }
</script>
@endsection
