<div id="wrapVue">
    <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" value="{{ $item->id }}">
        <input type="hidden" id="catalog_id" name="catalog_id" value="{{ $catalog_id }}">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="position-relative form-group">
                        <label>Item Name</label>
                        <input type="text" id="item_name" name="item_name" class="form-control" value="{{ $item->items_name }}" readonly>
                    </div>
                </div>
            </div>
            @foreach($prices as $val)
                <div class="row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label>{{ $val->price_name }}</label>
                            <input type="text" class="form-control" name="price_type_id[{{ $val->price_type_id }}]" value="{{ getData::getPriceCatalogItem($catalog_id,$val->price_type_id,$item->id) }}">
                        </div>
                    </div>
                </div>
            @endforeach
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
                $('.errormsg').hide();
                var form = e.target || e.srcElement;
                var action = "{{ url('/catalog/manage_item_prices/'.$catalog_id.'/'.$item->id) }}";
                var csrfToken = "{{ csrf_token() }}";

                let datas = new FormData($('#myForm')[0]);
                
                // datas.append("id", $("#id").val());
                // datas.append("catalog_id", $("#catalog_id").val());
                // datas.append("item_id", $("#item_id").val());
                // datas.append("serving_size", $("#serving_size").val());

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
                            // afterSubmitForm();
                            afterPreloadContent();
                            afterpreloader();
                            loadView();
                        }else{
                            // afterSubmitForm();
                            afterPreloadContent();
                            afterpreloader();
                            toastr.error(notif.message);
                        }
                    })
                    .catch((error) => {
                        // afterSubmitForm();
                        afterpreloader();
                        afterPreloadContent();
                        $('.errormsg').show();
                        // this.formErrors = error.response.data.errors;
                    });
            }
        },
    });

    function loadView(page = null) {
        preloadContent();
        if (page == null) {
            var url = "{{ url('/catalog/dataPrice') }}";
        } else {
            var url = "{{ url('/catalog') }}" + "/" + page;
        }
        var obj = new Object();
        obj.searchCatalog = '{{ $catalog_id }}';
        axios.post(url, obj, {
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    Accept: "application/json",
                },
            })
            .then((response) => {
                $("#loadpage").html(response.data);
                afterPreloadContent();
            })
            .catch((error) => {
                afterpreloader();
                $(".errormsg").css("visibility", "visible");
                this.formErrors = error.response.data.errors;
            });
    }
</script>