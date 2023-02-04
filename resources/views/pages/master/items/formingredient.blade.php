<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
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
            <div id="wrapVue">
                <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id">
                    <div class="modal-body">
                        <div class="position-relative form-group">
                            <label>Select Item</label>
                            <select id="ingredient_id" name="ingredient_id" class="custom-select">
                                <option value="">Select</option>
                                @foreach($material as $vmaterial)
                                    <option value="{{ $vmaterial['id'] }}">{{ $vmaterial['name'] }}</option>
                                @endforeach
                            </select>
                            <div v-if="formErrors['ingredient_id']" class="errormsg alert alert-danger mt-1">
                              @{{ formErrors['ingredient_id'][0] }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Serving Size</label>
                                    <input type="text" id="serving_size" name="serving_size" class="form-control">
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label>Unit</label>
                                    <input type="text" class="form-control" id="unit" name="unit" readonly>
                                </div>
                            </div> --}}
                        </div>
                        <div v-if="formErrors['serving_size']" class="errormsg alert alert-danger mt-1">
                          @{{ formErrors['serving_size'][0] }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                    var action = "{{ url('/items/updateingredient/'.$item['id']) }}";
                }else{
                    var action = "{{ url('/items/addingredient/'.$item['id']) }}";
                }
                var csrfToken = "{{ csrf_token() }}";

                let datas = new FormData();
                datas.append("id", $("#id").val());
                datas.append("parent_id", "{{ $item['id'] }}");
                datas.append("ingredient_id", $("#ingredient_id").val());
                datas.append("serving_size", $("#serving_size").val());
                axios.post(action, datas, {
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
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
            // loadItem: function (e) {
            //     item = $("#ingredient_id").val();
            //     $.ajax({
            //         url: "{{ url('/items') }}"+'/'+item,
            //         type: 'GET',
            //     })
            //     .done(function(data) {
            //         console.log(data);
            //         $("#unit").val(data.item_unit);
            //     })
            //     .fail(function() {
            //         console.log("error");
            //     });
            // }
        },
    });
</script>
