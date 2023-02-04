<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
        <title>{{ $catalog['catalog_title'] }}</title>
        <link rel="stylesheet" href="{{ asset('/css/main.css'.'?'.time()) }}" />
    </head>
    <body>
        <div class="container">
            <div class="row mt-5">
                <div class="col-md-12 text-center">
                    <h2><b>{{ strtoupper($catalog['catalog_title']) }}</b></h2>
                    <h4>QRIS Code</h4>
                    <div class="mt-5">
                        @if($catalog['qr_string'])
                        <img src="data:image/png
                        $Also = ;base64, {!! base64_encode(\QrCode::format('png')->size(350)->generate($catalog['qr_string'])) !!}">
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script>
            $(document).ready(function(){
                window.print();
            })
        </script>
    </body>
</html>