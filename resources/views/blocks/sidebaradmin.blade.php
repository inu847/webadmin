<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">
                <li class="app-sidebar__heading">{{__('lang.mainmenu')}}</li>
                <li class="mm-active">
                    <a href="{{ url('/') }}">
                        <i class="metismenu-icon lnr-screen"></i>
                        Dashboard.
                    </a>
                </li>
                
                <li class="app-sidebar__heading">{{ __('lang.masterdata')}}</li>
                <li>
                    <a href="{{ route('menu.index') }}">
                        <i class="metismenu-icon pe-7s-display2"></i>
                        {{ __('lang.manage')}} Menu
                    </a>
                </li>
                <li>
                    <a href="{{ route('metode-pembayaran.index') }}">
                        <i class="metismenu-icon pe-7s-display2"></i>
                        {{ __('lang.manage')}} Metode Pembayaran
                    </a>
                </li> 
                <li>
                    <a href="{{ route('manage-user.index') }}">
                        <i class="metismenu-icon pe-7s-display2"></i>
                        {{ __('lang.manage')}} User
                    </a>
                </li>
                <li>
                    <a href="{{ route('mainfeatures.index') }}">
                        <i class="metismenu-icon lnr-star-half"></i>
                        Package Features
                    </a>
                </li>
                <li>
                    <a href="{{ route('package.index') }}">
                        <i class="metismenu-icon lnr-list"></i>
                        Package Registration
                    </a>
                </li>
                <li>
                    <a href="{{ route('voucher.index') }}">
                        <i class="metismenu-icon pe-7s-gift"></i>
                        Voucher
                    </a>
                </li>

                <li class="app-sidebar__heading">Customer Data</li>
                <li>
                    <a href="{{ route('member.index') }}">
                        <i class="metismenu-icon lnr-users"></i>
                        Members
                    </a>
                </li>

                <li class="app-sidebar__heading">Register Data</li>
                <li>
                    <a href="{{ url('/register/checkout') }}">
                        <i class="metismenu-icon lnr-enter-down"></i>
                        {{ __('lang.checkout')}}
                    </a>
                </li>
                <li>
                    <a href="{{ url('/register/confirmation') }}">
                        <i class="metismenu-icon pe-7s-exapnd2"></i>
                        {{ __('lang.confirmpayment')}}
                    </a>
                </li>
                <li>
                    <a href="{{ url('/register/approved') }}">
                        <i class="metismenu-icon pe-7s-check"></i>
                        {{ __('lang.approve')}}
                    </a>
                </li>
                <li>
                    <a href="{{ url('/register/rejected') }}">
                        <i class="metismenu-icon lnr-cross"></i>
                        {{ __('lang.rejected')}}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
