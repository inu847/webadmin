<div id="wrapVue">
	<form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
		<div class="modal-body">
			<input type="hidden" id="me" name="me" value="{{ $me }}">
			<input type="hidden" id="status" name="status" value="{{ $status }}">
			<div class="position-relative form-group">
				<label>Current Position</label>
				<input type="text" id="current" name="current" value="{{ $current }}" class="form-control" readonly>
			</div>
			<div class="position-relative form-group">
				<label>Change Position</label>
				<input type="number" id="change_position" name="change_position" class="form-control" min="1" max="{{ $position }}" value="1">
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
            $(document).ready(function() {
                $("#myForm")[0].reset();
            });
        },
        methods: {
            submitForm: function (e) {
                submitForm();
                $('.errormsg').hide();
                var form = e.target || e.srcElement;
                var action = "{{ url('/catalog/change/position/'.$catalog.'/'.$me.'/'.$current.'/'.$status) }}";
                var csrfToken = "{{ csrf_token() }}";

                let datas = new FormData();
                datas.append("catalog_id", "{{ $catalog }}");
                datas.append("me", "{{ $me }}");
                datas.append("status", "{{ $status }}");
                datas.append("current", "{{ $current }}");
                datas.append("change_position", $("#change_position").val());

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