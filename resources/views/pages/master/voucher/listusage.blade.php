<div class="modal-body">
    <div class="table-responsive">
        <table class="table">
            <tr>
                <th style="border-top: none">Checkout</th><td style="border-top: none" class="text-right">: {{ $checkout->count() }} Transaction</td>
            </tr>
            @foreach($checkout as $vcheckout)
                <tr class="text-muted">
                    <td style="border-top: none">{{ $vcheckout['invoice'] }}</td><td style="border-top: none" class="text-right">{{ Date::fullDate($vcheckout['created_at']) }}</td>
                </tr>
            @endforeach
            <tr>
                <th style="border-top: none">Confirmation</th><td style="border-top: none" class="text-right">: {{ $confirmation->count() }} Transaction</td>
            </tr>
            @foreach($confirmation as $vconfirmation)
                <tr class="text-muted">
                    <td style="border-top: none">{{ $vconfirmation['invoice'] }}</td><td style="border-top: none" class="text-right">{{ Date::fullDate($vconfirmation['created_at']) }}</td>
                </tr>
            @endforeach
            <tr>
                <th style="border-top: none">Approved</th><td style="border-top: none" class="text-right">: {{ $approved->count() }} Transaction</td>
            </tr>
            @foreach($approved as $vapproved)
                <tr class="text-muted">
                    <td style="border-top: none">{{ $vapproved['invoice'] }}</td><td style="border-top: none" class="text-right">{{ Date::fullDate($vapproved['created_at']) }}</td>
                </tr>
            @endforeach
            <tr>
                <th style="border-top: none">Rejected</th><td style="border-top: none" class="text-right">: {{ $rejected->count() }} Transaction</td>
            </tr>
            @foreach($rejected as $vrejected)
                <tr class="text-muted">
                    <td style="border-top: none">{{ $vrejected['invoice'] }}</td><td style="border-top: none" class="text-right">{{ Date::fullDate($vrejected['created_at']) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>