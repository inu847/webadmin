@extends('layouts.main')
@section('customcss')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endsection
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-laptop-phone icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            <a href="{{ url('/pos') }}" class="btn-shadow btn btn-success btn-sm"><i class="icon lnr-arrow-left"></i> Back</a>
            <a href="{{ \Request::url() }}" class="btn-shadow btn btn-info btn-sm"><i class="icon lnr-sync"></i> Refresh</a>
        </div>
    </div>
</div>

<div class="tabs-animation">
    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-body" style="min-height: 500px;">
                    <div class="row">
                        <div class="col-lg-7 col-sm-6">
                            <input type="text" class="form-control" id="searchfield" name="searchfield" placeholder="Search item(s)..." autocomplete="off" />
                            <div class="preloadContent mt-3">
                                @for($i=0;$i<=4;$i++)
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="ph-row"><div class="ph-col-12 big"></div></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="ph-row"><div class="ph-col-12 big"></div></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="ph-row"><div class="ph-col-12 big"></div></div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                            <div id="tableData" class="mt-3"></div>
                        </div>
                        <div class="col-lg-5 col-sm-6">
                            <div style="position: relative; width: 100%; border: 1px dashed; background: #f9f9f9; min-height: 300px;">
                                <div id="loadContentPOS"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('modal')
    @include('pages.pos.regform')
    @include('pages.pos.customform')
    @include('pages.pos.groupaddon')
@endsection
@section('customjs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            //$("#searchfield").val('');
            // $("#searchfield").val("{{ (!empty($request['searchfield']))?$request['searchfield']:'' }}");
            $("#searchfield").focus();
        });
        $("#searchfield").bind("keyup", function () {
            loadTable();
        });
    </script>
    <script type="text/javascript">
        $(window).on("load", function () {
            loadTable();
            loadTransaction();
        });
        $(document).on("click", ".pagination a", function (event) {
            $("li").removeClass("active");
            $(this).parent("li").addClass("active");
            event.preventDefault();
            var myurl = $(this).attr("href");
            var page = myurl.match(/([^\/]*)\/*$/)[1];
            loadTable(page);
        });
        function loadTable(page = null) {
            preloadContent();
            if (page == null) {
                if ($("#searchfield").val() != "") {
                    url = "{{ url('/pos/table') }}" + "?searchfield=" + $("#searchfield").val();
                } else {
                    url = "{{ url('/pos/table') }}";
                }
            } else {
                url = "{{ url('/pos') }}" + "/" + page;
            }
            $.ajax({
                url: url,
                type: "GET",
            })
            .done(function (data) {
                $("#tableData").html(data);
                afterPreloadContent();
            })
            .fail(function () {
                Swal.fire("Ops!", "Load data failed.", "error");
                afterPreloadContent();
            });
        }
        function loadTransaction() {
            $.ajax({
                url: "{{ url('/pos/editdata/'.$invoice['id']) }}",
                type: "GET",
            })
            .done(function (data) {
                $("#loadContentPOS").html(data);
            })
            .fail(function () {
                Swal.fire("Ops!", "Load data failed.", "error");
            });
        }

        function addItem(id, category, price, discount, product) {
            $("#myForm")[0].reset();
            $("#modalForm").modal("show");
            $("#invoice_number").val("{{ $invoice['invoice_number'] }}");
            $("#id").val('');
            $("#productid").val(id);
            $("#product").val(product);
            $("#categoryid").val(category);
            $("#price").val(price);
            $("#discount").val(discount);
            $("#qty").val(1);
            $("#cartQtyModal").removeClass('d-none');
            $("#sbmButton").html("Add Item");
        }
        function addItemCustom(id, category, price, discount, product) {
            $("#myFormCustom")[0].reset();
            $("#modalFormCustom").modal("show");
            $("#invoice_number_custom").val("{{ $invoice['invoice_number'] }}");
            $("#idcustom").val('');
            $("#productidcustom").val(id);
            $("#productcustom").val(product);
            $("#categoryidcustom").val(category);
            $("#pricecustom").val(price);
            $("#discountcustom").val(discount);
            $("#qtycustom").val(1);
            $("#sbmButtonCustom").html("Add Item");
            //Addons
            $.ajax({
                url: "{{ url('/pos/addons') }}" + "/" + id + "/0",
                type: "GET",
            })
            .done(function (data) {
                $("input[type=radio]").prop("checked", false);
                $("input[type=checkbox]").prop("checked", false);
                $("#addonsItem").html(data);
            })
            .fail(function () {
                console.log("error");
            });
            //End
        }
        function editItem(id) {
            $("#myForm")[0].reset();
            $("#modalForm").modal("show");
            $("#sbmButton").html("Save Changes");
            @if(getData::getCatalogSession('advance_payment') == 'Y')
                $("#cartQtyModal").removeClass('d-none');
            @else
                $("#cartQtyModal").addClass('d-none');
            @endif
            $("#id").val('');
            $.ajax({
                url: "{{ url('/pos/detail') }}" + "/" + id,
                type: "GET",
            })
            .done(function (data) {
                $("#id").val(id);
                $("#productid").val(data.item_id);
                $("#product").val(data.item);
                $("#categoryid").val(data.category);
                $("#price").val(data.price);
                $("#discount").val(data.discount);
                $("#qty").val(data.qty);
                $("#note").val(data.note);
            })
            .fail(function () {
                Swal.fire("Ops!", "Load data failed.", "error");
            });
        }
        function editNote(id) {
            $("#myForm")[0].reset();
            $("#modalForm").modal("show");
            $("#sbmButton").html("Save Changes");
            $("#cartQtyModal").addClass('d-none');
            $("#idcustom").val('');
            $.ajax({
                url: "{{ url('/pos/detail') }}" + "/" + id,
                type: "GET",
            })
            .done(function (data) {
                $("#id").val(id);
                $("#productid").val(data.item_id);
                $("#product").val(data.item);
                $("#categoryid").val(data.category);
                $("#price").val(data.price);
                $("#discount").val(data.discount);
                $("#qty").val(data.qty);
                $("#note").val(data.note);
            })
            .fail(function () {
                Swal.fire("Ops!", "Load data failed.", "error");
            });
        }
        function removeItem(id) {
            Swal.fire({
                title: "Confirmation",
                text: "Do you want to remove this item?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ url('/pos/delete') }}" + "/" + id,
                        type: "GET",
                        dataType: "html",
                    })
                    .done(function (data) {
                        loadTransaction();
                    })
                    .fail(function () {
                        Swal.fire("Ops!", "Load data failed.", "error");
                    });
                }
            });
        }
        function removeAdd(detail, group) {
            Swal.fire({
                title: "Confirmation",
                text: "Do you want to remove this item?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ url('/pos/delete-addon') }}" + "/" + detail + "/" + group,
                        type: "GET",
                    })
                    .done(function (data) {
                        loadTransaction();
                    })
                    .fail(function () {
                        Swal.fire("Ops!", "Load data failed.", "error");
                    });
                }
            });
        }
        function editAddon(item, detail, group, qty) {
            $("#myFormGroupAddon")[0].reset();
            $("#modalGroupAddon").modal("show");
            $("#qtyaddons").val(qty);
            $("#invoicedetailid").val(detail);
            $("#groupaddons").val(group);
            $("input[type=radio]").prop("checked", false);
            $("input[type=checkbox]").prop("checked", false);
            @if(getData::getCatalogSession('advance_payment') == 'Y')
                $("#cartQtyModalCustom").removeClass('d-none');
            @else
                $("#cartQtyModalCustom").addClass('d-none');
            @endif
            //Addons
            $.ajax({
                url: "{{ url('/pos/addons') }}" + "/" + item + "/" + group,
                type: "GET",
            })
            .done(function (data) {
                $("#addonsItemUpdate").html(data);
            })
            .fail(function () {
                console.log("error");
            });
            //End
        }
        function paymentMethod() {
            if ($("#position").val() == "") {
                Swal.fire("Ops!", "Mohon untuk memasukan nomor meja / nomor kamar.", "error");
                return false;
            }
            payment = $("#paymentmethod").val();
            if (payment == 1) {
                checkout();
            } else if (payment == 2) {
                confirmation();
            } else if (payment == 3) {
                paymentGateway();
            }
        }
        function paymentAction() {
            payment = $("#paymentmethod").val();
            if (payment == 2) {
                $("#transferinfo").removeClass("d-none");
                $("#btnChk").text("Confirmation");
                $("#payment_slip").removeClass("d-none");
            } else {
                $("#transferinfo").addClass("d-none");
                $("#btnChk").text("Checkout");
                $("#payment_slip").addClass("d-none");
            }
        }
        function detailPending(id) {
            $.ajax({
                url: "{{ url('/pos/detailinvoicepending') }}" + "/" + id,
                type: "GET",
            })
            .done(function (data) {
                $("#loadContentpending").html(data);
            })
            .fail(function () {
                Swal.fire("Ops!", "Load data failed.", "error");
            });
        }
    </script>
@endsection
