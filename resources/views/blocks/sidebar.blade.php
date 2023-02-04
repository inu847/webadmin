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
                <?php
                    // $menus = \App\Models\MenuRoles::join('menus', 'menus.id', '=', 'menu_roles.menu_id')
                    //     ->select('url')
                    //     ->where('role_id', Auth::user()->role_id)->pluck('url')->toArray();

                    $pre_menus = \App\Models\MenuRoles::leftJoin('menus', 'menus.id', '=', 'menu_roles.menu_id')
                        ->select('menu_roles.menu_id','menu_roles.menu_category_id', 'url')
                        ->where('role_id', Auth::user()->role_id)->get();

                    $menus = $pre_menus->whereNotNull('url')->pluck('url')->toArray();
                    $cat_menus = $pre_menus->where('menu_category_id', '>', 0)->pluck('menu_category_id')->toArray();

                    // dd($menus->toArray());
                    // dd($menus->whereNotNull('url')->pluck('url')->toArray());
                    // dd($menus->where('menu_id', '>', 0)->pluck('menu_id')->toArray());
                    // dd($menus->where('menu_category_id', '>', 0)->pluck('menu_category_id')->toArray());
                ?>

                @if(Auth::user()->level != 'Super Admin' || Auth::user()->level != 'User')
                <li class="d-lg-none d-xl-none">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-keypad"></i>{{__('lang.select')}}
                        @if(!empty($page))
                            @if($page == 'Stock')
                                Warehouse
                            @endif
                        @else
                            Catalog
                        @endif
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse" style="height: 7.04px;">
                        @if(getData::haveCatalog() == 'True')
                            @if(getData::getCatalog()->count() > 1 || Auth::user()->owner == 1)
                                <li>
                                    <a href="javascript:void(0)" onclick="catalogSessionMenu('All')">
                                        @if(!empty($page))
                                            @if($page == 'Stock')
                                                Warehouse ( Stock )
                                            @endif
                                        @else
                                            {{__('lang.all')}} Catalog
                                        @endif
                                    </a>
                                </li>
                            @endif
                            @foreach(getData::getCatalog() as $keycat => $catalogsession)
                                <li>
                                    <a href="javascript:void(0)" onclick="catalogSessionMenu('{{ $catalogsession['id'] }}')">
                                        {{ $catalogsession['catalog_title'] }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </li>
                @endif

                <li class="mm-active mt-3">
                    <a href="{{ url('/') }}">
                        <i class="metismenu-icon lnr-screen"></i>
                        Dashboard
                    </a>
                </li>

                <li class="">
                    <a href="https://wa.me/6281211199251?text=Hallo,%20Saya%20ingin%20menanyakan%20perihal%20" target="_blank">
                        <i class="metismenu-icon lnr-smile"></i>
                        Help
                    </a>
                </li>

                <li class="">
                    <a href="https://play.google.com/store/apps/details?id=com.scaneat.scaneat_second" target="_blank">
                        <img src="{{ asset('assets/img/playstore 1.svg') }}" alt="">
                    </a>
                </li>

                {{--
                @if(getData::haveCatalog() == 'True')
                    @if(Session::get('catalogsession') != 'null')
                        @if (Auth::user()->owner == 1 || in_array(url('/pos'), $menus))
                            <li>
                                <a href="{{ url('/pos') }}">
                                    <i class="metismenu-icon lnr-laptop-phone"></i>
                                    Point of Sales
                                </a>
                            </li>
                        @endif
                        @if (Auth::user()->owner == 1 || in_array(route('bell.index'), $menus))
                            <li>
                                <a href="{{ route('bell.index') }}">
                                    <i class="metismenu-icon pe-7s-bell"></i>
                                    {{ __('lang.bellnotif')}}
                                </a>
                            </li>
                        @endif
                    @endif
                @endif
                --}}

                <li class="app-sidebar__heading">{{ __('lang.masterdata')}}</li>

                @if (Auth::user()->owner == 1 || in_array(1, $cat_menus))
                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon pe-7s-display2"></i>{{ __('lang.manage')}} User
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            @if (Auth::user()->owner == 1 || in_array(route('user.index'), $menus))
                                <li>
                                    <a href="{{ route('user.index') }}">
                                        {{ __('lang.users')}} List
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->owner == 1 || in_array(route('role.index'), $menus))
                                <li>
                                    <a href="{{ route('role.index') }}">
                                        {{ __('lang.manage')}} Roles
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->owner == 1)
                                <li>
                                    <a href="{{ route('menu_password.index') }}">
                                        Menu Password
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif


                <li class="">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-display2"></i>{{ __('lang.manage')}} Foodcourt
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse" style="height: 7.04px;">
                        @if (Auth::user()->owner == 1 || in_array(2, $cat_menus))
                            @if (Auth::user()->owner == 1 || in_array(route('foodcourt.index'), $menus))
                                <li>
                                    <a href="{{ route('foodcourt.index') }}">
                                        List Foodcourt
                                    </a>
                                </li>
                            @endif
                        @endif
                        @if (Auth::user()->owner == 1 || in_array(3, $cat_menus))
                            @if (Auth::user()->owner == 1 || in_array(route('catalog-approval.index'), $menus))
                                <li>
                                    <a href="{{ route('catalog-approval.index') }}">
                                        Approval Catalog Foodcourt
                                    </a>
                                </li>
                            @endif
                        @endif
                    </ul>
                </li>

                <li class="">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-display2"></i>{{ __('lang.manage')}} Catalog
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse" style="height: 7.04px;">
                        @if (Auth::user()->owner == 1 || in_array(4, $cat_menus))
                            @if (Auth::user()->owner == 1 || in_array(route('catalog.index'), $menus))
                                <li>
                                    <a href="{{ route('catalog.index') }}">
                                        List Catalog
                                    </a>
                                </li>
                            @endif
                        @endif
                        @if (Auth::user()->owner == 1 || in_array(8, $cat_menus))
                            <li>
                                <a href="{{ route('sliders.index') }}">
                                    Sliders
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                @if (Auth::user()->owner == 1 || in_array(5, $cat_menus))
                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon lnr-layers"></i>{{ __('lang.manage')}} Items
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            @if (Auth::user()->owner == 1 || in_array(route('items.index'), $menus))
                                <li>
                                    <a href="{{ route('items.index') }}">
                                    {{ __('lang.items')}}
                                    </a>
                                </li>
                            @endif

                            @if (Auth::user()->owner == 1 || in_array(route('price_type.index'), $menus))
                                <li>
                                    <a href="{{ route('price_type.index') }}">
                                        Manage Price Type
                                    </a>
                                </li>
                            @endif

                            @if (Auth::user()->owner == 1 || in_array(route('category.index'), $menus))
                                <li>
                                    <a href="{{ route('category.index') }}">
                                        {{ __('lang.category')}}
                                    </a>
                                </li>
                            @endif

                            @if (Auth::user()->owner == 1 || in_array(route('subcategory.index'), $menus))
                                <li>
                                    <a href="{{ route('subcategory.index') }}">
                                    {{ __('lang.subcategory')}}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif



                @if (Auth::user()->owner == 1 || in_array(8, $cat_menus))
                    @if (Auth::user()->owner == 1 || in_array(route('sliders.index'), $menus))
                        <li>
                            <a href="{{ route('sliders.index') }}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Sliders
                            </a>
                        </li>
                    @endif
                @endif

                <li class="">
                    <a href="{{ route('ingredient.index') }}" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-display2"></i>{{ __('lang.manage')}} Ingredient
                    </a>
                </li>

                {{-- ADD ONS --}}
                @if (Auth::user()->owner == 1 || in_array(6, $cat_menus))
                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon lnr-plus-circle"></i>{{ __('lang.manage')}} Add Ons
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            @if (Auth::user()->owner == 1 || in_array(route('addons.index'), $menus))
                                <li>
                                    <a href="{{ route('addons.index') }}">
                                        {{ __('lang.addonlist')}}
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->owner == 1 || in_array(route('categoryadd.index'), $menus))
                                <li>
                                    <a href="{{ route('categoryadd.index') }}">
                                        {{ __('lang.category')}}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif


                {{-- MASTER DATA CATALOG --}}
                <li class="app-sidebar__heading">Data Catalog ( {{ (Session::get('catalogsession')=='All')?'Warehouse':getData::getCatalogSession('catalog_title') }} )</li>

                @if (Auth::user()->owner == 1 || in_array(7, $cat_menus))
                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon pe-7s-display2"></i>{{ __('lang.manage')}} Stock
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            @if (Auth::user()->owner == 1 || in_array(route('stock.index'), $menus))
                                <li>
                                    <a href="{{ route('stock.index') }}">
                                        {{ __('lang.manage')}} Stock Item
                                    </a>
                                </li>
                            @endif

                            @if (Auth::user()->owner == 1 || in_array(route('material.index'), $menus))
                                <li>
                                    <a href="{{ route('material.index') }}">
                                        {{--
                                        {{ (Auth::user()->owner == 1)?'Warehouse':'Material' }} Stock
                                        --}}
                                        {{ __('lang.manage')}} Stock Ingredient
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('stock-addons.index') }}">
                                    {{ __('lang.manage')}} Stock Addons
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (Auth::user()->owner == 1 || in_array(13, $cat_menus))
                <li class="">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-display2"></i>{{ __('lang.manage')}} Table
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse" style="height: 7.04px;">
                        @if (Auth::user()->owner == 1 || in_array(route('table.index'), $menus))
                            <li>
                                <a href="{{ route('table.index') }}">
                                    Table List
                                </a>
                            </li>
                        @endif
                        <!-- <li>
                            <a href="">
                                Transaction <small>(progress)</small>
                            </a>
                        </li> -->
                    </ul>
                </li>
                @endif

                <li>
                    <a href="{{ route('pengeluaran.index') }}">
                        <i class="metismenu-icon pe-7s-display2"></i>
                        Pengeluaran
                    </a>
                </li>


                @if(Auth::user()->id == 1)
                    <li class="app-sidebar__heading">Yo Hotel</li>
                    <li>
                        <a href="{{ route('tutorial.index') }}">
                            <i class="metismenu-icon lnr-book"></i>
                            Tutorial
                        </a>
                    </li>

                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon lnr-hand"></i>Service
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            <li>
                                <a href="{{ route('service.index') }}">
                                    List Data
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user_service.index') }}">
                                    User Service
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon lnr-sad"></i>Complaint
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            <li>
                                <a href="{{ route('complaint.index') }}">
                                    List Data
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user_complaint.index') }}">
                                    User Complaint
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if(Session::get('catalogsession') != 'null' && getData::getCatalogSession('feature') == 'Full')
                    @if (Auth::user()->owner == 1 || in_array(9, $cat_menus))
                        <li class="app-sidebar__heading">Resto Order Tracking</li>

                        @if (Auth::user()->owner == 1 || in_array(url('/transaction/checkout'), $menus))
                            <li>
                                <a href="{{ url('/transaction/checkout') }}">
                                    <i class="metismenu-icon pe-7s-next-2"></i>
                                    {{ __('lang.checkout')}}
                                </a>
                            </li>
                        @endif

                        @if(getData::checkStepTransaction('Approve',Session::get('catalogsession')))
                            @if (Auth::user()->owner == 1 || in_array(url('/transaction/approve'), $menus))
                                <li>
                                    <a href="{{ url('/transaction/approve') }}">
                                        <i class="metismenu-icon lnr-select"></i>
                                        {{ __('lang.approve')}}
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if(getData::checkStepTransaction('Process',Session::get('catalogsession')))
                            @if (Auth::user()->owner == 1 || in_array(url('/transaction/process'), $menus))
                                <li>
                                    <a href="{{ url('/transaction/process') }}">
                                        <i class="metismenu-icon lnr-hourglass"></i>
                                        {{ __('lang.process')}}
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if(getData::checkStepTransaction('Delivered',Session::get('catalogsession')))
                            @if (Auth::user()->owner == 1 || in_array(url('/transaction/delivered'), $menus))
                                <li>
                                    <a href="{{ url('/transaction/delivered') }}">
                                        <i class="metismenu-icon lnr-location"></i>
                                        {{ __('lang.devlivered')}}/ Ready to Pick Up
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if (Auth::user()->owner == 1 || in_array(url('/transaction/completed'), $menus))
                            <li>
                                <a href="{{ url('/transaction/completed') }}">
                                    <i class="metismenu-icon lnr-checkmark-circle"></i>
                                    {{ __('lang.completed')}}
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->owner == 1 || in_array(url('/transaction/cancel'), $menus))
                            <li>
                                <a href="{{ url('/transaction/cancel') }}">
                                    <i class="metismenu-icon lnr-cross-circle"></i>
                                    {{ __('lang.cancel')}}
                                </a>
                            </li>
                        @endif
                    @endif
                @endif

                @if (Auth::user()->owner == 1 || in_array(10, $cat_menus))
                    <li class="app-sidebar__heading">Report</li>

                    @if(Session::get('catalogsession') != 'null' || Session::get('catalogsession') == 'All')
                        @if (Auth::user()->owner == 1 || in_array(url('/transaction/report'), $menus))
                            <li>
                                <!-- <a href="{{ url('/transaction/report/All/'.date('Y-m-d').'/'.date('Y-m-d')) }}"> -->
                                <a href="{{ url('/transaction/report/All?start='.date('Y-m-d').'&end='.date('Y-m-d')) }}">
                                    <i class="metismenu-icon lnr-printer"></i>
                                    {{ __('lang.transaction')}}
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->owner == 1 || in_array(url('/transaction/income/report'), $menus))
                            <li>
                                <a href="{{ url('/transaction/income/report/All?start='.date('Y-m-d').'&end='.date('Y-m-d')) }}">
                                    <i class="metismenu-icon lnr-printer"></i>
                                        Income Statement
                                </a>
                            </li>
                        @endif

                    @endif

                    @if (Auth::user()->owner == 1 || in_array(route('customer.index'), $menus))
                        <li>
                            <a href="{{ route('customer.index') }}">
                                <i class="metismenu-icon lnr-heart"></i>
                                Customer
                            </a>
                        </li>
                    @endif
                @endif

                @if (Auth::user()->admin_menu == 1)
                    <li class="app-sidebar__heading">Super Admin Menu</li>

                    <li>
                        <a href="{{ route('catalog.monitoringMerchant') }}">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            Manage & Monitoring Account
                        </a>
                    </li>

                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon lnr-plus-circle"></i>{{ __('lang.masterdata')}}
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            <li>
                                <li class="">
                                    <a href="#" aria-expanded="false">
                                        <i class="metismenu-icon lnr-hand"></i>{{ __('lang.manage')}} Affiliate
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul class="mm-collapse" style="height: 7.04px;">
                                        <li>
                                            <a href="{{ route('member.index') }}">
                                                Affiliate Merchant
                                            </a>
                                        </li>
                                        <li>
                                            <li class="">
                                                <a href="#" aria-expanded="false">
                                                    <i class="metismenu-icon lnr-hand"></i>{{ __('lang.manage')}} Foodcourt
                                                    <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                                </a>
                                                <ul class="mm-collapse" style="height: 7.04px;">
                                                    <li>
                                                        <a href="{{ route('account-foodcourt.index') }}">
                                                            Account Foodcourt
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('affiliate-foodcourt.index') }}">
                                                            Affiliate Foodcourt
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </li>
                                    </ul>
                                </li>
                            </li>
                            <!-- <li class="">
                                <a href="{{ route('manage-user.index') }}">
                                    User List
                                </a>
                            </li> -->
                            <li>
                                <a href="{{ route('menu.index') }}">
                                    {{ __('lang.manage')}} Menu
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('metode-pembayaran.index') }}">
                                    {{ __('lang.manage')}} Metode Pembayaran
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('mainfeatures.index') }}">
                                    Package Features
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('package.index') }}">
                                    Package Registration
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('voucher.index') }}">
                                    Voucher
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon lnr-plus-circle"></i>Food Court
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            <li>
                                <a href="{{ route('request-catalog.index') }}">
                                    Request Catalog Foodcourt
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon lnr-plus-circle"></i>Customer Data
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            <li>
                                <a href="{{ route('member.index') }}">
                                    Members
                                </a>
                            </li>
                        </ul>
                    </li> -->

                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon lnr-plus-circle"></i>Register Data
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            <li>
                                <a href="{{ url('/register/checkout') }}">
                                    {{ __('lang.checkout')}}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/register/confirmation') }}">
                                    {{ __('lang.confirmpayment')}}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/register/approved') }}">
                                    {{ __('lang.approve')}}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/register/rejected') }}">
                                    {{ __('lang.rejected')}}
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- <li class="">
                        <a href="{{ route('menu-roles.index') }}">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            Menu Roles
                        </a>
                    </li> -->

                    <!-- <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon pe-7s-display2"></i>{{ __('lang.manage')}} Menu
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            <li>
                                <a href="{{ route('menu.index') }}">
                                    Master Menu
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('menu-roles.index') }}">
                                    Menu Roles
                                </a>
                            </li>
                        </ul>
                    </li> -->

                    <li class="">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon lnr-plus-circle"></i>Loyalty
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse" style="height: 7.04px;">
                            <li>
                                <a href="{{ route('loyalty.index') }}">
                                    Loyalty List
                                </a>
                            </li>
                        </ul>
                    </li>

                @endif
            </ul>
        </div>
    </div>
</div>
