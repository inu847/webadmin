<div id="wrapTheVue">
    <form id="myForm" @submit.prevent="submitEditForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" class="form-control"/>
        <div class="modal-body">
            <div class="position-relative form-group">
                <label>Price Name <sup class="text-danger">* (Required)</sup></label>
                <input type="text" id="price_name" name="price_name" class="form-control"/>
            </div>
            <div class="position-relative form-group">
                <label>Select Catalog</label>
                <div class="row">
                    @foreach($catalogs as $catalog)
                    <div class="col-md-6">
                        <div class="mt-2">
                            <input type="checkbox" id="catalog{{ $catalog['id'] }}" name="catalogs[]" value="{{ $catalog['id'] }}" {{
                            (getData::checkPriceCatalog($catalog['id'],(!empty($getData))?$getData['id']:0))?'checked':'' }} />
                            <label for="catalog{{ $catalog['id'] }}">{{ $catalog['catalog_title'] }}</label>
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
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        @if(!empty($getData))
            $("#contentForm #id").val("{{ $getData['id'] }}");
            $("#contentForm #price_name").val("{{ $getData['price_name'] }}");
        @endif
    });

    new Vue({
        el: "#wrapTheVue",
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
            submitEditForm: function (e) {
                submitForm();
                $('.errormsg').hide();
                var form = e.target || e.srcElement;
                if($("#contentForm #id").val() > 0){
                    $("#contentForm #myForm").append('<input type="hidden" name="_method" value="PUT" />');
                    var action = "{{ route('price_type.update',0) }}";
                    var put = form.querySelector('#contentForm input[name="_method"]').value;
                }else{
                    $("#contentForm input[name=_method]").remove();
                    var action = "{{ route('price_type.store') }}";
                    var put = '';
                }
                var csrfToken = "{{ csrf_token() }}";

                var arrsliders=[];
                $("#contentForm input:checkbox[name*=catalogs]:checked").each(function(){
                    arrsliders.push($(this).val());
                });

                let datas = new FormData();
                datas.append("id", $("#contentForm #id").val());
                datas.append("price_name", $("#contentForm #price_name").val());
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
                            $("#editForm").modal("hide");
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