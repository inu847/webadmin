@extends('layouts.main')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-screen icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
        <div class="page-title-actions">
            
        </div>
    </div>
</div>

<div id="loadContent" class="tabs-animation">
</div>
@endsection

@section('modal')
<div class="modal fade" id="modalData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="modalContent" class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="printInvoice()">Print</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url: "{{ url('/dahsboard-data') }}",
            type: 'GET',
        })
        .done(function(data) {
            $("#loadContent").html(data);
            //afterpreloader();
        })
        .fail(function() {
            console.log("error");
        });
    });
    function loadDetail(invoice){
        $("#modalData").modal('show');
        $("#titleModal").html("Detail order : "+invoice);
        $.ajax({
            url: "{{ url('/transaction/detailpopup') }}"+'/'+invoice,
            type: 'GET',
        })
        .done(function(data) {
            $("#modalContent").html(data)
        })
        .fail(function() {
            Swal.fire("Ops!", "Load data failed.", "error");
        });
    }
</script>
@endsection