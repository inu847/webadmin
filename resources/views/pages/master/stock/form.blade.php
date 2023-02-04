{{-- <div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" class="form-control"/>
        <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
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
                    <div id="modalContent" class="modal-body">
                        <div class="position-relative form-group">
                            <label>Item Name <sup class="text-danger">* (Required)</sup></label>
                            <select id="item_id" name="item_id" class="form-control select2" style="width:100%; height: calc(2.25rem + 2px);">
                                <option value="">Select Item</option>
                                @foreach($items_data as $value)
                                    <option value="{{ $value->id }}">{{  $value->items_name }}</option>
                                @endforeach
                            </select>
                            <div v-if="formErrors['item_id']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['item_id'][0] }}
                            </div>
                        </div>
                        <div class="position-relative form-group">
                            <label>Total Stock <sup class="text-danger">* (Required)</sup></label>
                            <input type="text" id="stock" name="stock" class="form-control"/>
                            <div v-if="formErrors['stock']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['stock'][0] }}
                            </div>
                        </div>
                        <div class="position-relative form-group">
                            <label>Notes</label>
                            <textarea id="notes" name="notes" class="form-control"></textarea>
                            <div v-if="formErrors['notes']" class="errormsg alert alert-danger mt-1">
                                @{{ formErrors['notes'][0] }}
                            </div>
                        </div>

                        {{--
                        <div class="position-relative form-group">
                            <label style="font-weight: bold;">Add Stock for Catalog</label>
                            <div class="row">
                                @foreach($catalog as $key => $value)
                                <div class="col-md-12">
                                    <div class="mt-1">
                                        <input type="checkbox" id="catalog{{ $key }}" name="catalogs[]" value="{{ $key }}" />
                                        <label style="font-weight:normal;" for="catalog{{ $key }}">{{ $value }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Data</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div> --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
    });

    new Vue({
        el: "#wrapVue",
        data() {
            return {
                csrf: "",
                formErrors: {},
                notif: [],
            };
        },
        mounted: function () {
            this.csrf = "{{ csrf_token() }}";
            let self = this;
        },
        methods: {
            submitForm: function (e) {
                submitForm();
                $('.errormsg').hide();
                var form = e.target || e.srcElement;
                if($("#id").val() > 0){
                  var action = "{{ route('stock.update',0) }}";
                  var put = form.querySelector('input[name="_method"]').value;
                }else{
                  var action = "{{ route('stock.store') }}";
                  var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("item_id", $("#item_id").val());
                datas.append("stock", $("#stock").val());
                datas.append("notes", $("#notes").val());
                
                // var arrcatalog=[];
                // $("input:checkbox[name*=catalogs]:checked").each(function(){
                //     arrcatalog.push($(this).val());
                // });

                axios.post(action, datas, {
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "X-HTTP-Method-Override": put,
                            Accept: "application/json",
                        },
                    })
                    .then((response) => {
                        let self = this;
                        var notif = response.data;
                        var getstatus = notif.status;
                        if (getstatus == "success") {
                            $("#modalForm").modal("hide");
                            afterSubmitForm();
                            loadView();
                        }else{
                            afterSubmitForm();
                            toastr.error(notif.message);
                        }
                    })
                    .catch((error) => {
                        afterSubmitForm();
                        $('.errormsg').css('visibility','visible');
                        this.formErrors = error.response.data.errors;
                    });
                
            },
        },
    });
</script>