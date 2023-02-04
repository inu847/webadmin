<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        {{-- <input type="hidden" id="item_image_one" name="item_image_one" class="form-control"/>
        <input type="hidden" id="item_image_two" name="item_image_two" class="form-control"/>
        <input type="hidden" id="item_image_three" name="item_image_three" class="form-control"/>
        <input type="hidden" id="item_image_four" name="item_image_four" class="form-control"/>
        <input type="hidden" id="item_image_primary" name="item_image_primary" class="form-control"/> --}}
        <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
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
                        <div class="row">
                            <input type="hidden" id="id" name="id" class="form-control"/>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Add On Name <sup class="text-danger">* (Required)</sup></label>
                                    <input type="text" id="name" name="name" class="form-control"/>
                                    <div v-if="formErrors['name']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['name'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Price <sup class="text-danger">* (Required)</sup></label>
                                    <input type="text" id="price" name="price" class="form-control"/>
                                    <span class="text-danger">{{ $errors->first('price') }}</span>
                                    <div v-if="formErrors['price']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['price'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Satuan</label>
                                    <select id="uom" class="custom-select">
                                      <option value="">Pilih</option>
                                      <option value="Gram">Gram</option>
                                      <option value="PCS">PCS</option>
                                      <option value="Milliliter">Milliliter</option>
                                      <option value="Cup">Cup</option>
                                    </select>
                                    <div v-if="formErrors['uom']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['uom'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Image</label>
                                    <input type="file" id="image" name="image" class="form-control"/>
                                    <div v-if="formErrors['image']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['image'][0] }}
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-4 d-none">
                                <div class="position-relative form-group">
                                    <label>Available</label>
                                    <select id="ready_stock" name="ready_stock" class="form-control">
                                      <option value="Y">
                                        Yes
                                      </option>
                                      <option value="N">
                                        No
                                      </option>
                                    </select>
                                    <span v-if="formErrors['ready_stock']" class="errormsg">@{{ formErrors['ready_stock'][0] }}</span>
                                </div>
                            </div> --}}
                        </div>
                        <div class="position-relative form-group">
                            <label for="myLabel" class="">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="10"></textarea>
                            <div v-if="formErrors['description']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['description'][0] }}
                            </div>
                        </div>
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
                  var action = "{{ route('addons.update',0) }}";
                  var put = form.querySelector('input[name="_method"]').value;
                }else{
                  var action = "{{ route('addons.store') }}";
                  var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("name", $("#name").val());
                datas.append("price", $("#price").val());
                datas.append('image', document.getElementById('image').files[0]);
                datas.append("description", $("#description").val());
                datas.append("uom", $("#uom").val());
                
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
                        $('.errormsg').show();
                        this.formErrors = error.response.data.errors;
                    });

            },
        },
    });
</script>
