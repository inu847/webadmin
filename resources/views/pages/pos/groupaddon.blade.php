<div id="wrapVueGroupAddon">
    <div class="modal fade" id="modalGroupAddon" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModal">Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="myFormGroupAddon" @submit.prevent="submitFormGroupAddon" method="post" onsubmit="return false;" enctype="multipart/form-data">
                    <input type="hidden" id="invoicedetailid" />
                    <input type="hidden" id="groupaddons" />
                    <div class="modal-body">
                        <div id="cartQtyModalCustom" class="position-relative form-group">
                            <label>Qty</label>
                            <input type="number" id="qtyaddons" min="1" max="100" readonly />
                            <span v-if="formErrors['qtycustom']" class="errormsg">@{{ formErrors['qtycustom'][0] }}</span>
                        </div>
                        <div class="position-relative form-group">
                            <label>Add-Ons</label>
                            <div id="addonsItemUpdate" style="font-size: 11px;">
                              
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    new Vue({
        el: "#wrapVueGroupAddon",
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
            submitFormGroupAddon: function (e) {
                var form = e.target || e.srcElement;
                var action = "{{ url('/pos/updateaddons') }}";
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
                datas.append("detailid", $("#invoicedetailid").val());
                datas.append("group", $("#groupaddons").val());
                datas.append("qty", $("#qtyaddons").val());
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
                            $("#modalGroupAddon").modal("hide");
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