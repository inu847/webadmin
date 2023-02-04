@extends('layouts.main')

@section('content')
<div id="wrapVues">
    <div class='card'>
        <div class='card-header'>
            Data Pengeluaran
        </div>
        <div class='card-body'>
            <div class="main-card mb-1 card" style="">
                <div class="table-responsive">
                    <table class="align-middle mb-3 table table-borderless table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="col-md-2">Tanggal</th>
                                <td>{{ $detail->datetime ? \Carbon\Carbon::parse($detail->datetime)->format('d/m/Y') : '' }}</td>
                            </tr>
                            <tr>
                                <th>Catalog</th>
                                <td>{{ $detail->catalog->catalog_title }}</td>
                            </tr>
                            <tr>
                                <th>Judul</th>
                                <td>{{ $detail->judul }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $detail->keterangan }}</td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td class="total_pengeluaran"></td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class='card'>
        <div class='card-header'>
            <div class="row" style="width: 100%;">
                <div class="col-md-6">
                    Detail Pengeluaran
                </div>
                <div class="col-md-6 text-right">
                    <a href="javascript:;" class="btn-shadow btn btn-dark btn-sm pull-right" onClick="showForm()"><i class="fa fa-plus"></i> 
                        Add New 
                    </a>
                </div>
            </div>
        </div>
        <div class='card-body'>
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Keterangan</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Sub Total</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody id="list_detail">
                    
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">Total</th>
                        <th colspan="2" class="total_pengeluaran">12.500</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@section('modal')
<div id="wrapVue">
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div id="modalSize" class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModal">Add New Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
                    <div class="modal-body" id="contentForm">
                        @csrf
                        <div class='form-group'>
                            <label for='nama'>Nama</label>
                            <input type='text' class='form-control' name='nama' id='nama' placeholder='' required>
                        </div>
                        <div class='form-group'>
                            <label for='qty'>Quantiti</label>
                            <input type='text' class='form-control' name='qty' id='qty' placeholder='' required>
                        </div>
                        <div class='form-group'>
                            <label for='harga'>Harga</label>
                            <input type='text' class='form-control' name='harga' id='harga' placeholder='' required>
                        </div>
                        <div class='form-group'>
                            <label for='keterangan'>Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class='form-control'></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">CLose</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            loadView();
        });

        function showForm() {
            $("#modalForm").modal("show");
        }

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
                    var form = e.target || e.srcElement;
                    var action = "{{ url('/pengeluaran/detail/'.$detail->id) }}";
                    var csrfToken = "{{ csrf_token() }}";
                    let datas = new FormData($('#myForm')[0]);

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
                                $("#modalForm").find('input:text, textarea').val('');
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
                            // this.formErrors = error.response.data.errors;
                        });
                }
            },
        });

        function remove_list(val) {
            Swal.fire({
                title: "Are you sure ?",
                text: "Data will be permanently deleted",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.value) {
                    preloadContent();
                    var url = "{{ url('/pengeluaran/delete_detail/') }}/"+val;
                    var obj = new Object();
                    
                    axios.get(url, obj, {
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                Accept: "application/json",
                            },
                        })
                        .then((response) => {
                            afterPreloadContent();
                            loadView();
                        })
                        .catch((error) => {
                            afterpreloader();
                            this.formErrors = error.response.data.errors;
                        });
                }
            });
        }

        function loadView(page = null) {
            preloadContent();
            var url = "{{ url('/pengeluaran/detail/'.$detail->id) }}";
            var obj = new Object();
            
            axios.get(url, obj, {
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        Accept: "application/json",
                    },
                })
                .then((response) => {
                    $("#list_detail").html(response.data.data);
                    $(".total_pengeluaran").html(response.data.total);
                    afterPreloadContent();
                })
                .catch((error) => {
                    afterpreloader();
                    this.formErrors = error.response.data.errors;
                });
        }
    </script>
@endsection