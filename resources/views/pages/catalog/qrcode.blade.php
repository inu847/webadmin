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
                    <h5>https://{{ $catalog['catalog_username'].'.'.$catalog['domain'] }}</h5>
                    <div class="mt-5">
                        {!! \QrCode::format('svg')->size(350)->color(40,40,40)->generate($catalog['catalog_username'].'.'.$catalog['domain']) !!}
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