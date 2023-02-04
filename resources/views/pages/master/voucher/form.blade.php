<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" class="form-control"/>
        <div class="modal-body">
            <div class="row">
                <div class="col">
                    <div class="position-relative form-group">
                        <label>Voucher Code</label>
                        <input type="text" id="voucher_code" name="voucher_code" class="form-control" maxlength="20" style="text-transform: uppercase;" />
                        <div v-if="formErrors['voucher_code']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['voucher_code'][0] }}
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="position-relative form-group">
                        <label>Voucher Type</label>
                        <select id="voucher_type" class="custom-select">
                            <option value="">Select</option>
                            <option value="Percent">Percent</option>
                            <option value="Nominal">Nominal</option>
                        </select>
                        <div v-if="formErrors['voucher_type']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['voucher_type'][0] }}
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="position-relative form-group">
                        <label>Voucher Value</label>
                        <input type="text" id="voucher_nominal" name="voucher_nominal" class="form-control"/>
                        <div v-if="formErrors['voucher_nominal']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['voucher_nominal'][0] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="position-relative form-group">
                <label>Voucher Owner</label>
                <input type="text" id="voucher_owner" name="voucher_owner" class="form-control"/>
                <div v-if="formErrors['voucher_owner']" class="errormsg alert alert-danger mt-1">
                  @{{ formErrors['voucher_owner'][0] }}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Data</button>
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
                var form = e.target || e.srcElement;
                if($("#id").val() > 0){
                    $("#myForm").append('<input type="hidden" name="_method" value="PUT" />');
                    var action = "{{ route('voucher.update',0) }}";
                    var put = form.querySelector('input[name="_method"]').value;
                }else{
                    $("input[name=_method]").remove();
                    var action = "{{ route('voucher.store') }}";
                    var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";
                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("voucher_code", $("#voucher_code").val());
                datas.append("voucher_type", $("#voucher_type").val());
                datas.append("voucher_nominal", $("#voucher_nominal").val());
                datas.append("voucher_owner", $("#voucher_owner").val());

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
    $(document).ready(function() {
        $("#myForm")[0].reset();
        @if(!empty($getData))
            $("#id").val("{{ $getData['id'] }}");
            $("#voucher_code").val("{{ $getData['voucher_code'] }}");
            $("#voucher_type").val("{{ $getData['voucher_type'] }}");
            $("#voucher_nominal").val("{{ $getData['voucher_nominal'] }}");
            $("#voucher_owner").val("{{ $getData['voucher_owner'] }}");
        @endif
    });
</script>
