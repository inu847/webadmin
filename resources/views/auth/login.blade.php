@extends('layouts.app')

@section('content')
<div id="wrapVue">
    <div class="modal-dialog mx-auto" style="width: 300px;">
        <div class="modal-content">
            <!-- Form Loader -->
            <div class="formLoader">
                <div class="jumper">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
            <!-- End -->

            <div class="row m-3 ml-auto">
                {{app()->setLocale(Session::get('locale'))}}
                @if (__('lang.idlang') == 'id')            
                    <form action="./lang" method="get">
                        <input id="bhs" name="bhs" type="text" value="id" hidden/>
                        <button type="submit" class="mr-1 btn btn-outline-primary">ID</button>
                    </form>
                    <form action="./lang" method="get">
                        <input id="bhs" name="bhs" type="text" value="en" hidden/>
                        <button disabled type="submit" class="btn btn-primary">EN</button>
                    </form>                
                @else
                    <form action="./lang" method="get">
                        <input id="bhs" name="bhs" type="text" value="id" hidden/>
                        <button disabled type="submit" class="mr-1 btn btn-primary">ID</button>
                    </form>
                    <form action="./lang" method="get">
                        <input id="bhs" name="bhs" type="text" value="en" hidden/>
                        <button type="submit" class="btn btn-outline-primary">EN</button>
                    </form> 
                @endif
            </div>
            
            <div>
                <form id="myForm" @submit.prevent="submitForm" method="post" onsubmit="return false;" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="h5 modal-title text-center">
                            <h4 class="mt-2">
                                <div>{{ __('auth.welcome')}}</div>
                                <span>{{ __('auth.title')}}</span>
                            </h4>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <input id="email" type="email" class="form-control" name="email" placeholder="{{ __('auth.email')}}" autocomplete="email" autofocus>
                                    <div v-if="formErrors['email']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['email'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <input id="password" type="password" class="form-control" name="password" placeholder="{{ __('auth.pass')}}" autocomplete="current-password">
                                    <div v-if="formErrors['password']" class="errormsg alert alert-danger mt-1">
                                      @{{ formErrors['password'][0] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative form-check text-right">
                            <div class="custom-checkbox custom-control">
                                <input type="checkbox" id="remember" name="remember" class="custom-control-input">
                                <label class="custom-control-label mr-1" for="remember">{{ __('auth.keeplogin')}}</label>
                            </div>
                        </div>
                        <div class="d-none">
                            <div class="divider"></div>
                            <p class="mb-0">No account? <a href="javascript:void(0);" class="text-primary">Sign up now</a></p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </form>
                        <button style="display: none" onclick="forgotForm()" type="button" class="btn btn-primary btn-lg">Forgot Password</button>
                    </div>

            </div>
            <div>
                <form id="forgotForm" action="" method="post">
                    <div class="modal-body">
                        <div class="h5 modal-title text-center">
                            <h4 class="mt-2">
                                <div>{{ __('lang.forgot')}} Password</div>
                                <span>{{ __('auth.title')}}</span>
                            </h4>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <input type="email" class="form-control" name="email" placeholder="{{ __('auth.pass')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body">
                            <button type="submit" class="btn btn-primary btn-lg">Send Recover</button>
                        </form>
                            <button onclick="loginForm()" type="button" class="btn btn-primary btn-lg">Cancel</button>
                        </div>
            </div>

            </div>
    </div>
</div>

<script type="text/javascript">
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
            this.loginForm();
        },

        methods: {

            loginForm: function () {
                document.getElementById("myForm").style.display = "block";
                document.getElementById("forgotForm").style.display = "none";
            },

            forgotForm: function () {
                document.getElementById("myForm").style.display = "none";
                document.getElementById("forgotForm").style.display = "block";
            },

            submitForm: function (e) {
                submitForm();
                var form = e.target || e.srcElement;
                var action = "{{ url('/auth/login') }}";
                var csrfToken = "{{ csrf_token() }}";

                if (document.getElementById("remember").checked == true) {
                    var remember = 1;
                } else {
                    var remember = 0
                }

                let datas = new FormData();
                datas.append("email", $("#email").val());
                datas.append("password", $("#password").val());
                datas.append("status", remember);

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
                            window.location.replace("{{ url('/') }}");
                            toastr.success(notif.message);
                        } else {
                            afterSubmitForm();
                            toastr.error(notif.message);
                        }
                    })
                    .catch((error) => {
                        afterSubmitForm();
                        $('.errormsg').css('visibility','visible');
                        this.formErrors = error.response.data.errors;
                    });
            },
        },
    });
</script>
@endsection
