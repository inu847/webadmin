<div id="wrapVue">
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModal">Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
                    <input type="hidden" id="invoice_number">
                    <input type="hidden" id="id">
                    <input type="hidden" id="product">
                    <input type="hidden" id="productid">
                    <input type="hidden" id="categoryid">
                    <input type="hidden" id="price">
                    <input type="hidden" id="discount">
                    <div class="modal-body">
                        <div id="cartQtyModal" class="position-relative form-group">
                            <label>Qty</label>
                            <input type="number" id="qty" min="1" max="100" value="1">
                            <span v-if="formErrors['qty']" class="errormsg">@{{ formErrors['qty'][0] }}</span>
                        </div>
                        <div class="position-relative form-group">
                            <label>Note</label>
                            <input type="text" id="note" class="form-control">
                            <span v-if="formErrors['note']" class="errormsg">@{{ formErrors['note'][0] }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="sbmButton" class="btn btn-primary">Add Item</button>
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
                var form = e.target || e.srcElement;
                if($("#id").val() > 0){
                    var action = "{{ url('/pos/update') }}";
                }else{
                    var action = "{{ url('/pos/data') }}";
                }
                var csrfToken = "{{ csrf_token() }}";

                let datas = new FormData();
                datas.append("invoice_number", $("#invoice_number").val());
                datas.append("id", $("#id").val());
                datas.append("catalog", "{{ getData::getCatalogSession('id') }}");
                datas.append("via", 'POS');
                datas.append("item_id", $("#productid").val());
                datas.append("item", $("#product").val());
                datas.append("category", $("#categoryid").val());
                datas.append("price", $("#price").val());
                datas.append("discount", $("#discount").val());
                datas.append("qty", $("#qty").val());
                datas.append("note", $("#note").val());

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
                            loadTransaction();
                        }else{
                            afterpreloader();
                            toastr.error(notif.message);
                        }
                    })
                    .catch((error) => {
                        afterpreloader();
                        $('.errormsg').css('visibility','visible');
                        this.formErrors = error.response.data.errors;
                    });
            },
        },
    });
</script>