<div id="formVue">
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
	                        <div class="row">
	                            <div class="col-md-8">
	                                <div class="position-relative form-group">
	                                    <label>Add QTY</label>
	                                    <input type="text" id="stock" name="stock" class="form-control">
	                                </div>
	                            </div>
	                            <div class="col-md-4">
	                                <div class="position-relative form-group">
	                                    <label>Unit</label>
	                                    <input type="text" class="form-control" disabled value="{{ $item['item_unit'] }}">
	                                </div>
	                            </div>
	                        </div>
	                        <div v-if="formErrors['stock']" class="errormsg alert alert-danger">
	                          @{{ formErrors['stock'][0] }}
	                        </div>
	                        <div class="position-relative form-group">
	                            <label>Notes (Optional)</label>
	                            <textarea id="notes" name="notes" class="form-control"></textarea>
	                            <div v-if="formErrors['notes']" class="errormsg alert alert-danger mt-1">
	                              @{{ formErrors['notes'][0] }}
	                            </div>
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
</div>
<script type="text/javascript">
	new Vue({
	    el: "#formVue",
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
	                 var action = "{{ url('/material/updatestock/'.$item['id']) }}";
	            }else{
	                var action = "{{ url('/material/addstock/'.$item['id']) }}";
	            }
	            var csrfToken = "{{ csrf_token() }}";
	            var catalog = "{{ (Session::get('catalogsession')=='All')?0:Session::get('catalogsession') }}";
	            let datas = new FormData();
	            datas.append("id", $("#id").val());
	            datas.append("ingredient_id", "{{ $item['id'] }}");
	            datas.append("catalog", catalog);
	            datas.append("stock", $("#stock").val());
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