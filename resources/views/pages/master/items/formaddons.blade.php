<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
    	<div class="modal-body">
    		<div class="row">
    		    <div class="col-md-6">
    		    	<div class="position-relative form-group">
    		    		<label>Select Category</label>
    		    		<select id="category_id" name="category_id" class="custom-select">
    		    		    <option value="">Select</option>
    		    		    @foreach($category as $vcategory)
    		    		        <option value="{{ $vcategory['id'] }}">{{ $vcategory['category_name'] }}</option>
    		    		    @endforeach
    		    		</select>
    		    		<div v-if="formErrors['category_id']" class="errormsg alert alert-danger mt-1">
    		    		  @{{ formErrors['category_id'][0] }}
    		    		</div>
    		    	</div>
    		    </div>
    		    <div class="col-md-6">
    		    	<div class="position-relative form-group">
	    		        <label>Type</label>
	    		        <select id="check_type" name="check_type" class="custom-select">
	    		            <option value="Single">Single</option>
	    		            <option value="Multiple">Multiple</option>
	    		        </select>
	    		        <div v-if="formErrors['check_type']" class="errormsg alert alert-danger mt-1">
    		    		  @{{ formErrors['check_type'][0] }}
    		    		</div>
	    		    </div>
    		    </div>
    		</div>
    		<div class="position-relative form-group">
    		    <label>Select Addon(s)</label>
    		    <div class="row">
    		        @foreach($addons as $addons)
    		            <div class="col-md-2 mb-3">
    		                <img src="{{ strpos($addons['image'], 'amazonaws.com') !== false ? $addons['image'] : str_replace('scaneat.id', 'scaneat.id/liatmenu.id', myFunction::getProtocol()).$addons['image'].'?'.time() }}" class="img-fluid">
    		                <div class="custom-checkbox custom-control mt-2">
    		                    <input type="checkbox" id="item{{ $addons['id'] }}" name="addons_id[]" class="custom-control-input" value="{{ $addons['id'] }}" />
    		                    <label class="custom-control-label" for="item{{ $addons['id'] }}" style="font-size: 12px;">{{ $addons['name'] }}</label>
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
<script>
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
	            var action = "{{ url('/items/addaddons/'.$itemid) }}";
	            var put = '';
	            var csrfToken = "{{ csrf_token() }}";

	            var arraddons=[];
	            $("input:checkbox[name*=addons_id]:checked").each(function(){
	                arraddons.push($(this).val());
	            });

	            let datas = new FormData();
	            datas.append("item_id", "{{ $itemid }}");
	            datas.append("category_id", $("#category_id").val());
	            datas.append("addons_id", arraddons);
	            datas.append("check_type", $("#check_type").val());

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