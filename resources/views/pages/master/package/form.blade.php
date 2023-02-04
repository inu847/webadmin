<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" class="form-control"/>
        <div class="modal-body">
            <div class="row">
                <div class="col">
                    <div class="position-relative form-group">
                        <label>Package Name</label>
                        <input type="text" id="package_name" name="package_name" class="form-control"/>
                        <div v-if="formErrors['package_name']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['package_name'][0] }}
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="position-relative form-group">
                        <label>Recommended</label>
                        <select id="recommended" name="recommended" class="form-control">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                        <div v-if="formErrors['recommended']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['recommended'][0] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="position-relative form-group">
                <label>Select Features</label>
                <div class="row">
                    @foreach($features as $vfeature)
                    <div class="col-md-6">
                        <div class="custom-checkbox custom-control mt-2">
                            <input type="checkbox" id="feature{{ $vfeature['id'] }}" name="features[]" class="custom-control-input" value="{{ $vfeature['id'] }}" {{
                            (getData::checkPackageFeature($vfeature['id'],(!empty($getData))?$getData['id']:0))?'checked':'' }} />
                            <label class="custom-control-label" for="feature{{ $vfeature['id'] }}">{{ $vfeature['feature_name'] }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="position-relative form-group">
                <label>Note</label>
                <textarea id="description" name="description" class="form-control">{{ $getData ? $getData['description'] :'' }}</textarea>
                <div v-if="formErrors['description']" class="errormsg alert alert-danger mt-1">
                  @{{ formErrors['description'][0] }}
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
                    var action = "{{ route('package.update',0) }}";
                    var put = form.querySelector('input[name="_method"]').value;
                }else{
                    $("input[name=_method]").remove();
                    var action = "{{ route('package.store') }}";
                    var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                var arrfeatures=[];
                $("input:checkbox[name*=features]:checked").each(function(){
                    arrfeatures.push($(this).val());
                });

                if(arrfeatures.length < 1){
                    Swal.fire("Ops!", "Please select features.", "error");
                    afterSubmitForm();
                    return false;
                }

                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("package_name", $("#package_name").val());
                datas.append("recommended", $("#recommended").val());
                datas.append("features", arrfeatures);
                datas.append("description", $("#description").val());

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
            $("#package_name").val("{{ $getData['package_name'] }}");
            $("#recommended").val("{{ $getData['recommended'] }}");
        @endif
    });
</script>
