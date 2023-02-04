<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" class="form-control"/>
        <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
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
                            <label>Material Name <sup class="text-danger">* (Required)</sup></label>
                            <input type="text" id="items_name" name="items_name" class="form-control"/>
                            <div v-if="formErrors['items_name']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['items_name'][0] }}
                            </div>
                        </div>
                        <div class="position-relative form-group">
                            <label>Unit</label>
                            <select id="item_unit" name="item_unit" class="form-control">
                              <option value="">Select</option>
                              <option value="Mililiter">Mililiter</option>
                              <option value="Miligram">Miligram</option>
                              <option value="Gram">Gram</option>
                              <option value="PCS">PCS</option>
                            </select>
                            <div v-if="formErrors['item_unit']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['item_unit'][0] }}
                            </div>
                        </div>
                        {{--
                        <div class="position-relative form-group">
                            <label style="font-weight: bold;">Add Material for Catalog</label>
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
</div>
<script type="text/javascript">
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
                  var action = "{{ route('material.update',0) }}";
                  var put = form.querySelector('input[name="_method"]').value;
                }else{
                  var action = "{{ route('material.store') }}";
                  var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("items_name", $("#items_name").val());
                datas.append("ready_stock", 'Y');
                datas.append("item_unit", $("#item_unit").val());
                datas.append("item_type", 'Material');

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