<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" class="form-control"/>
        <div class="modal-body">
            <div class="position-relative form-group">
                <label>Loyalty Name</label>
                <input type="text" id="name" name="name" class="form-control" required />
                <div v-if="formErrors['name']" class="errormsg alert alert-danger mt-1">
                    @{{ formErrors['name'][0] }}
                </div>
            </div>
            <div class="position-relative form-group">
                <label>Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
                <div v-if="formErrors['description']" class="errormsg alert alert-danger mt-1">
                  @{{ formErrors['description'][0] }}
                </div>
            </div>
            <div class="position-relative form-group">
                <label>Image</label>
                <input type="file" name="photo" id="photo" class="form-control">
                <div v-if="formErrors['photo']" class="errormsg alert alert-danger mt-1">
                  @{{ formErrors['photo'][0] }}
                </div>
            </div>
            <div class="position-relative form-group">
                <label>Minimal Order</label>
                <input type="number" id="min_order" name="min_order" class="form-control" min="0" value="0" required/>
                <div v-if="formErrors['min_order']" class="errormsg alert alert-danger mt-1">
                  @{{ formErrors['min_order'][0] }}
                </div>
            </div>
            <div class="position-relative form-group">
                <label>Maximal Order</label>
                <input type="number" id="max_order" name="max_order" class="form-control" min="0" value="0" required/>
                <div v-if="formErrors['max_order']" class="errormsg alert alert-danger mt-1">
                  @{{ formErrors['max_order'][0] }}
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
                    var action = "{{ route('loyalty.update',0) }}";
                    var put = form.querySelector('input[name="_method"]').value;
                }else{
                    $("input[name=_method]").remove();
                    var action = "{{ route('loyalty.store') }}";
                    var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";
                let datas = new FormData($("#myForm")[0]);

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
            $("#name").val("{{ $getData['name'] }}");
            $("#description").val("{{ $getData['description'] }}");
            $("#min_order").val("{{ $getData['min_order'] }}");
            $("#max_order").val("{{ $getData['max_order'] }}");
        @endif
    });
</script>
