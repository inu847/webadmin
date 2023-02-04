<!DOCTYPE html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <title>{{ $titlepage }}</title>
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />

        <link rel="manifest" href="site.webmanifest" />
        <!-- <link rel="apple-touch-icon" href="icon.png" /> -->
        <!-- Place favicon.ico in the root directory -->
        <link rel="stylesheet" href="{{ asset('/css/main.css'.'?'.time()) }}" />
        <link rel="stylesheet" href="{{ asset('/css/custom.css'.'?'.time()) }}" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
        <link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css" type="text/css" rel="stylesheet" />
        <link href="https://unpkg.com/placeholder-loading@0.3.0/dist/css/placeholder-loading.min.css" type="text/css" rel="stylesheet" />
        @yield('customcss')
        <script src="{{ asset('/js/pusher.min.js') }}"></script>
        <script src="{{ asset('/js/echo.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.13/vue.min.js"></script>

        <style>
            .floatbell {
                position: fixed;
                background: #1dc905;;
                border-radius: 2px;
                padding: 10px;
                right: 20px;
                bottom: 20px;
                border: 3px solid #F7F7F7;
                box-shadow: 0 .25rem .125rem 0 rgba(0,0,0,0.07);
                z-index: 9999;
                cursor: pointer;
                color: white;
                font-size: .8rem;
                font-weight: bold;
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <!-- ***** Preloader Start ***** -->
        <!-- <div id="preloader"> -->
        <div id="">
            <div class="jumper">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="preloader">
            <div class="jumper">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <!-- ***** Preloader End ***** -->
        <div id="myContainer" class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar">
            @include('/blocks/topbar')

            <!-- <div class="floatbell">
                <div class="row">
                    <div class="col-12"><a href="https://wa.me/6285289535328?text=Hallo,%20Saya%20ingin%20menanyakan%20perihal%20" target="_blank" rel="noopener noreferrer" style="text-decoration: none;" class="text-white">Butuh Bantuan</a></div>
                </div>
            </div> -->

            <div class="app-main">
                @if(Auth::user()->level == 'Member' || Auth::user()->level == 'User')
                  @include('/blocks/sidebar')
                @else
                  @include('/blocks/sidebaradmin')
                @endif

                <div class="app-main__outer">
                    <div class="app-main__inner">
                        @yield('content')
                    </div>
                    <div class="app-wrapper-footer">
                        <div class="app-footer">
                            <div class="app-footer__inner">
                                <div class="app-footer-right">
                                    <ul class="header-megamenu nav">
                                        <li class="nav-item">
                                            <a data-placement="top" rel="popover-focus" data-offset="300" data-toggle="popover-custom" class="nav-link">
                                                Copyright © {{ date('Y') }} ScanEat. All right reserved
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <iframe id="prints" style="display: none"></iframe>
        @yield('modal')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.6.0/umd/popper.min.js" integrity="sha512-BmM0/BQlqh02wuK5Gz9yrbe7VyIVwOzD1o40yi1IsTjriX/NGF37NyXHfmFzIlMmoSIBXgqDiG1VNU6kB5dBbA==" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
        <script src='https://cdn.rawgit.com/admsev/jquery-play-sound/master/jquery.playSound.js'></script>
        <script type="text/javascript" src="{{ asset('/js/main.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/custom.js'.'?'.time()) }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-input-spinner@1.9.7/src/bootstrap-input-spinner.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script type="text/javascript">
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            $(document).ready(function() {
                $("input[type='number']").inputSpinner();
                //toggleFullscreen()

                // // set active menu
                // var _menu = $('a[href="{{-- (\Request::route()->getName() != '' && (strpos(\Request::url(),'edit') == 0 || strpos(\Request::url(),'show') == 0)) ? route(\Request::route()->getName()) : '' --}}"]'); 
                // _menu.addClass('mm-active');
                // if(_menu.parent().parent().hasClass('mm-collapse')){
                //     _menu.parent().parent().addClass('mm-show');
                //     _menu.parent().parent().css({ 'height' : '' });
                //     _menu.parent().parent().prev().prop('aria-expanded', true);
                //     _menu.parent().parent().parent().addClass('mm-active');
                // }
            });

            function toggleFullscreen() {
              let elem = document.getElementById("myContainer");

              if (!document.fullscreenElement) {
                elem.requestFullscreen().catch(err => {
                  alert(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
                });
              } else {
                document.exitFullscreen();
              }
            }
            $(window).on('load', function() {
                @if(Auth::user()->level != 'Super Admin' && getData::haveCatalog() == 'True')
                    @if(!Session::has('catalogsession') || Session::get('catalogsession') == 'null')
                        catalogSession();
                    @else
                        $("#catalogsession").val("{{ Session::get('catalogsession') }}")
                    @endif
                @endif
                afterpreloader();
            });
        </script>
        @toastr_js
        @toastr_render
        <script type="text/javascript">
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: "04c3b9d0c77c38d69025",
                cluster: "ap1",
                encrypted: true,
            });
            Echo.channel('pushernotif').listen('NotifEvent', function(e) {
                if(e.note == 'Bell'){
                    if(e.catalog == "{{ getData::getCatalogSession('id') }}"){
                        toastr.success("Panggilan dari nomor meja "+e.status, 'Informasi', {timeOut: 10000});
                        $.playSound("{{ asset('/sound.mp3?'.time()) }}")
                    }
                }
                else if(e.note == 'Front'){
                    if(e.catalog == "{{ getData::getCatalogSession('id') }}"){
                        toastr.success("Nomor Meja "+e.status, e.info+' '+e.invoice, {timeOut: 10000});
                        $.playSound("{{ asset('/swiftly.mp3?'.time()) }}");
                    }
                }else{
                    if(e.catalog == "{{ getData::getCatalogSession('id') }}"){
                        @if(!empty($status))
                        if(e.status == "{{ ucwords($status) }}"){
                            // window.location.reload();
                            window.location.href = window.location.href;
                        }
                        @endif
                    }
                }
            });
            function catalogSession(){
                $.ajax({
                    url: "{{ url('/catalog-session') }}"+"/"+$("#catalogsession").val(),
                    type: 'GET'
                })
                .done(function(data) {
                    // window.location.reload();
                    window.location.href = window.location.href;
                })
                .fail(function() {
                    console.log("error");
                });
            }
            function catalogSessionMenu(id){
                $.ajax({
                    url: "{{ url('/catalog-session') }}"+"/"+id,
                    type: 'GET'
                })
                .done(function(data) {
                    // window.location.reload();
                    window.location.href = window.location.href;
                })
                .fail(function() {
                    console.log("error");
                });
            }
        </script>
        @yield('customjs')
    </body>
</html>
