<div id="wrapVue">
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
                    <div class="modal-body">
                        <div class="position-relative form-group">
                            <label style="font-weight: bold;">Category Name <sup class="text-danger">* (Required)</sup></label>
                            <input type="text" id="category_name" name="category_name" class="form-control"/>
                            <div v-if="formErrors['category_name']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['category_name'][0] }}
                            </div>
                        </div>
                        <div class="position-relative form-group">
                            <label style="font-weight: bold;">Font Color <sup class="text-danger">* (Required)</sup></label>
                            <input id="category_color" name="category_color" type="text" class="colorpicker form-control" value='#000' readonly />
                            <div v-if="formErrors['category_color']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['category_color'][0] }}
                            </div>
                        </div>
                        <hr>
                        <div class="position-relative form-group">
                            <label style="font-weight: bold;">Show in Catalog</label>
                            <div class="row">
                                @foreach($catalog as $key => $value)
                                <div class="col-md-6">
                                    <div class="mt-2">
                                        <input type="checkbox" id="catalog{{ $key }}" name="catalogs[]" value="{{ $key }}" />
                                        <label style="font-weight:normal;" for="catalog{{ $key }}">{{ $value }}</label>
                                    </div>
                                </div>
                                @endforeach
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
                  var action = "{{ route('category.update',0) }}";
                  var put = form.querySelector('input[name="_method"]').value;
                }else{
                  var action = "{{ route('category.store') }}";
                  var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                var catalogs=[];
                $("input:checkbox[name*=catalogs]:checked").each(function(){
                    catalogs.push($(this).val());
                });

                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("category_name", $("#category_name").val());
                datas.append("category_color", $("#category_color").val());
                datas.append("category_type", 'Main');
                datas.append("catalogs", catalogs);

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