<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="Content-Language" content="en" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
        <meta name="description" content="ScanEat" />

        <!-- Disable tap highlight on IE -->
        <meta name="msapplication-tap-highlight" content="no" />
        <link rel="stylesheet" href="{{ asset('/css/main.css'.'?'.time()) }}" />
        <link rel="stylesheet" href="{{ asset('/css/custom.css'.'?'.time()) }}" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.13/vue.min.js"></script>
    </head>
    <body>
        <div id="preloader" style="background: #754FA7">
            <div class="jumper">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="preloader" style="background: #754FA7">
            <div class="jumper">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="app-container app-theme-white body-tabs-shadow">
            <div class="app-container">
                <div class="h-100 bg-plum-plate bg-animation">
                    <div class="d-flex h-100 justify-content-center align-items-center">
                        <div class="mx-auto app-login-box col-md-8">
                            <div class="app-logo-inverse mx-auto mb-3" style="width: 169px;height: 45px;"></div>
                            @yield('content')
                            <div class="text-center text-white opacity-8 mt-3">Copyright © 2020-{{ date('Y') }} ScanEat. All right reserved </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="{{ asset('/js/main.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/custom.js'.'?'.time()) }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
        <script type="text/javascript">
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            $(window).on('load', function() {
                afterpreloader();
            });
        </script>
    </body>
</html>