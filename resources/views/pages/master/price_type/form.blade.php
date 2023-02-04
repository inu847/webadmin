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
                            <label>Price Name <sup class="text-danger">* (Required)</sup></label>
                            <input type="text" id="price_name" name="price_name" class="form-control"/>
                            <div v-if="formErrors['price_name']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['price_name'][0] }}
                            </div>
                        </div>
                        <div class="position-relative form-group">
                            <label>Select Catalog</label>
                            <div class="row">
                                @foreach($catalogs as $catalog)
                                <div class="col-md-6">
                                    <div class="custom-checkbox custom-control mt-2">
                                        <input type="checkbox" id="catalog{{ $catalog['id'] }}" name="catalogs[]" class="custom-control-input" value="{{ $catalog['id'] }}" />
                                        <label class="custom-control-label" for="catalog{{ $catalog['id'] }}">{{ $catalog['catalog_title'] }}</label>
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
                  var action = "{{ route('price_type.update',0) }}";
                  var put = form.querySelector('input[name="_method"]').value;
                }else{
                  var action = "{{ route('price_type.store') }}";
                  var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                var arrsliders=[];
                $("input:checkbox[name*=catalogs]:checked").each(function(){
                    arrsliders.push($(this).val());
                });

                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("price_name", $("#price_name").val());
                datas.append("catalogs", arrsliders);

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