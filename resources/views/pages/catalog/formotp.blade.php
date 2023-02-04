<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <div class="modal-body">
            <input type="hidden" id="id" name="id" class="form-control" value="{{$catalog->id}}" />
            <div class="position-relative form-group">
                <div class="col-md-12">
                    <label>Catalog Name</label>
                    <input type="text" id="catalog_title" name="catalog_title" class="form-control" value="{{$catalog->catalog_title}}" readonly />
                </div>
            </div>
            <div class="position-relative form-group">
                <div class="col-md-12">
                    <label>Catalog Email</label>
                    <input type="text" id="email_contact" name="email_contact" class="form-control" value="{{$catalog->email_contact}}" readonly />
                </div>
            </div>
            <hr>
            <!-- <p>
                <b>
                    Insert OTP Code that sent to this email for accessing catalog menu.
                </b>
            </p> -->
            <div class="position-relative form-group">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary" id="btn_send_otp">Send OTP Code to Catalog Email</button>
                </div>
            </div>

            @if(isset($invalid_otp) && $invalid_otp)
            <div class="position-relative form-group">
                <div class="col-md-12 text-danger">
                    <b>Invalid OTP Code.</b>
                </div>
            </div>
            @endif

            <div class="position-relative form-group div_loading_otp d-none">
                <div class="col-md-12 text-success">
                    <b>Sending Email, Please Wait...</b>
                </div>
            </div>

            @if(isset($sent_otp) && $sent_otp)
            <div class="position-relative form-group">
                <div class="col-md-12 text-success">
                    <b>OTP Code already sent to Catalog Email. Check your inbox or spam.</b>
                </div>
            </div>
            @endif

            <hr>
            <div class="position-relative form-group">
                <div class="col-md-12">
                    <label>Input OTP Code from email</label>
                    <input type="text" id="otp_code" name="otp_code" class="form-control" value="{{ (isset($otp_code) && $otp_code) ? $otp_code : '' }}" />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="btn_save_catalog">Continue</button>
        </div>
    </form>
</div>
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
                $("#btn_save_catalog").html('Processing data...')
                $("#btn_save_catalog").prop('disabled', true)                
                $("input[name=_method]").remove();

                var action = "{{ url('/catalog/process_otp') }}";
                var csrfToken = "{{ csrf_token() }}";
                
                let datas = new FormData();
                datas.append("otp_code", $("#myForm #otp_code").val());
                datas.append("id", $("#myForm #id").val());

                axios.post(action, datas, {
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            // "X-HTTP-Method-Override": put,
                            Accept: "application/json",
                        },
                    })
                    .then((response) => {
                        let self = this;
                        var notif = response.data;
                        var getstatus = notif.status;
                        
                        // $("#contentForm").html(notif.data);
                        // loadView();
                        // $("#modalForm").modal("hide");
                        // afterSubmitForm();

                        $("#btn_save_catalog").html('Continue')
                        $("#btn_save_catalog").prop('disabled', false)
                        if (getstatus == "success") {
                            $("#modalForm").modal("hide");
                            toastr.success(notif.message);
                            afterSubmitForm();
                            loadView();
                        }else{
                            afterSubmitForm();
                            toastr.error(notif.message);
                        }
                    })
                    .catch((error) => {
                        $("#btn_save_catalog").html('Continue')
                        $("#btn_save_catalog").prop('disabled', false)

                        var formErrors = error.response.data.errors;
                        let [first] = Object.keys(formErrors)
                        toastr.error(formErrors[first][0]);

                        afterSubmitForm();
                    });
                
            },
        },
    });
</script>
