<div id="wrapVueCustom">
    <div class="modal fade" id="modalFormCustom" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModal">Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="myFormCustom" @submit.prevent="submitFormCustom" method="post" onsubmit="return false;" enctype="multipart/form-data">
                    <input type="hidden" id="invoice_number_custom">
                    <input type="hidden" id="idcustom">
                    <input type="hidden" id="productcustom">
                    <input type="hidden" id="productidcustom">
                    <input type="hidden" id="categoryidcustom">
                    <input type="hidden" id="pricecustom">
                    <input type="hidden" id="discountcustom">
                    <div class="modal-body">
                        <div class="position-relative form-group">
                            <label>Qty</label>
                            <input type="number" id="qtycustom" min="1" max="100" value="1">
                            <span v-if="formErrors['qtycustom']" class="errormsg">@{{ formErrors['qtycustom'][0] }}</span>
                        </div>
                        <div class="position-relative form-group">
                            <label>Add-Ons ( Opsional )</label>
                            <div id="addonsItem" style="font-size: 11px;">
                              
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="sbmButtonCustom" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    new Vue({
        el: "#wrapVueCustom",
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
            submitFormCustom: function (e) {
                var form = e.target || e.srcElement;
                if($("#idcustom").val() > 0){
                    var action = "{{ url('/pos/update') }}";
                }else{
                    var action = "{{ url('/pos/data') }}";
                }
                var csrfToken = "{{ csrf_token() }}";

                var arraddonsmultiple=[];
                $("input:checkbox[name*=addons]:checked").each(function(){
                    arraddonsmultiple.push($(this).val());
                });
                var arraddonssingle=[];
                $("input[type='radio']:checked").each(function(){
                    arraddonssingle.push($(this).val());
                });

                let datas = new FormData();
                datas.append("id", $("#idcustom").val());
                datas.append("invoice_number", $("#invoice_number_custom").val());
                datas.append("catalog", "{{ getData::getCatalogSession('id') }}");
                datas.append("via", 'POS');
                datas.append("item_id", $("#productidcustom").val());
                datas.append("item", $("#productcustom").val());
                datas.append("category", $("#categoryidcustom").val());
                datas.append("price", $("#pricecustom").val());
                datas.append("discount", $("#discountcustom").val());
                datas.append("qty", $("#qtycustom").val());
                datas.append("note", '');
                datas.append("arraddonsmultiple", arraddonsmultiple);
                datas.append("arraddonssingle", arraddonssingle);
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
                            $('input[type=radio]').prop('checked',false);
                            $('input[type=checkbox]').prop('checked',false);
                            $("#modalFormCustom").modal("hide");
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