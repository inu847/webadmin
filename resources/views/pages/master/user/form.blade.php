<div id="formVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" class="form-control"/>
        <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" style="max-width: 500px">
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
                    <div class="modal-body">
                        <div class="row" id="biodata1">
                            <div class="col">
                                <div class="position-relative form-group">
                                    <label>Name <sup class="text-danger">* (Required)</sup></label>
                                    <input type="text" id="name" name="name" class="form-control"/>
                                    <div v-if="formErrors['name']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['name'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="position-relative form-group">
                                    <label>Catalog <sup class="text-danger">* (Required)</sup></label>
                                    <select id="catalogid" name="catalogid" class="form-control">
                                        <option value="">Select</option>
                                        @foreach($catalog as $catalog)
                                            <option value="{{ $catalog['id'] }}">{{ $catalog['catalog_title'] }}</option>
                                        @endforeach
                                    </select>
                                    <div v-if="formErrors['catalogid']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['catalogid'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="position-relative form-group">
                                    <label>Role <sup class="text-danger">* (Required)</sup></label>
                                    <select id="role_id" name="role_id" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($role as $rls)
                                            <option value="{{ $rls->id }}">{{ $rls->name }}</option>
                                        @endforeach
                                    </select>
                                    <div v-if="formErrors['role_id']" class="errormsg alert alert-danger mt-1">
                                        @{{ formErrors['role_id'][0] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="biodata2">
                            <div class="col">
                                <div class="position-relative form-group">
                                    <label>Email <sup class="text-danger">* (Required)</sup></label>
                                    <input id="email" name="email" type="text" class="colorpicker form-control"/>
                                    <div v-if="formErrors['email']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['email'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="position-relative form-group">
                                    <label>Phone <sup class="text-danger">* (Required)</sup></label>
                                    <input id="phone" name="phone" type="text" class="colorpicker form-control"/>
                                    <div v-if="formErrors['phone']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['phone'][0] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="wrappassword">
                            <div class="col">
                                <div class="position-relative form-group">
                                    <label>Password <sup class="text-danger">* (Required)</sup></label>
                                    <input id="password" name="password" type="password" class="colorpicker form-control"/>
                                    <div v-if="formErrors['password']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['password'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="position-relative form-group">
                                    <label>Re Password <sup class="text-danger">* (Required)</sup></label>
                                    <input id="repassword" name="repassword" type="password" class="colorpicker form-control"/>
                                    <div v-if="formErrors['repassword']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['repassword'][0] }}
                                    </div>
                                </div>
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
        el: "#formVue",
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
                if($("#id").val() > 0 && $("#password").val() == ""){
                  var action = "{{ route('user.update',0) }}";
                  var put = form.querySelector('input[name="_method"]').value;
                } else if($("#id").val() > 0 && $("#password").val() != ""){
                  var action = "{{ route('user.update',1) }}";
                  var put = form.querySelector('input[name="_method"]').value;
                } else {
                  var action = "{{ route('user.store') }}";
                  var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("name", $("#name").val());
                datas.append("catalogid", $("#catalogid").val());
                datas.append("role_id", $("#role_id").val());
                datas.append("email", $("#email").val());
                datas.append("phone", $("#phone").val());
                datas.append("password", $("#password").val());
                datas.append("repassword", $("#repassword").val());

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
                            toastr.success(notif.message);
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
