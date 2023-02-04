<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" class="form-control"/>
        <input type="hidden" id="item_image" name="item_image" class="form-control"/>
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Title <sup class="text-danger">* (Required)</sup></label>
                                    <input type="text" id="title" name="title" class="form-control"/>
                                    <div v-if="formErrors['title']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['title'][0] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Image</label>
                                    <input type="file" id="image" name="image" class="form-control"/>
                                    <div v-if="formErrors['image']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['image'][0] }}
                                    </div>
                                </div>
                            </div>
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
                  var action = "{{ route('service.update',0) }}";
                  var put = form.querySelector('input[name="_method"]').value;
                }else{
                  var action = "{{ route('service.store') }}";
                  var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("item_image", $("#item_image").val());
                datas.append('image', document.getElementById('image').files[0]);
                datas.append("title", $("#title").val());
                // datas.append("description", $("#description").val());
                datas.append("description", CKEDITOR.instances['description'].getData());

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
            isChecked:function(addons) {
                $(document).on('show.bs.modal', '#modalForm', function () {
                    setTimeout(function(){ 
                        $.ajax({
                            url: "{{ url('/service/checkaddons') }}"+'/'+$("#id").val()+'/'+addons,
                            type: 'GET',
                        })
                        .done(function(data) {
                            if(data == 1){
                                $("#item"+addons).prop('checked', true);
                            }else{
                                $("#item"+addons).prop('checked', false);
                            }
                        })
                        .fail(function() {
                            console.log("error");
                        });
                    }, 1000);
                });
            },
        },
    });
</script>