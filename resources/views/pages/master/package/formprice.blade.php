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
            <div id="wrapVue">
                <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id" class="form-control"/>
                    <input type="hidden" id="package_id" name="package_id" value="{{ $package['id'] }}" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label>Price</label>
                                    <input type="text" id="price" name="price" class="form-control"/>
                                    <div v-if="formErrors['price']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['price'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="position-relative form-group">
                                    <label>Number of Period</label>
                                    <input type="number" id="period" min="1" max="100" value="1">
                                    <div v-if="formErrors['number']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['number'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label>Period</label>
                                    <select id="unit" name="unit" class="form-control">
                                        <option value="Hari">Day(s)</option>
                                        <option value="Bulan">Month(s)</option>
                                    </select>
                                    <div v-if="formErrors['unit']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['unit'][0] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative form-group">
                            <label>Notes</label>
                            <input type="text" id="notes" name="notes" class="form-control"/>
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
	            	var action = "{{ url('/package/updateprice') }}";
	            }else{
	                var action = "{{ url('/package/addprice') }}";
	            }
	            var csrfToken = "{{ csrf_token() }}";

	            let datas = new FormData();
	            datas.append("id", $("#id").val());
	            datas.append("package_id", $("#package_id").val());
	            datas.append("price", $("#price").val());
	            datas.append("period", $("#period").val());
	            datas.append("unit", $("#unit").val());
	            datas.append("notes", $("#notes").val());

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
	    },
	});
</script>