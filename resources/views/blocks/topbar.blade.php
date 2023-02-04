<div class="app-header header-shadow">
    <div class="app-header__logo">
        <div class="logo-src" style="width: 169px;height: 35px;"></div>
        <h6 class="d-lg-none d-xl-none ml-3 d-none"><b>{{ (empty(getData::getCatalogSession('catalog_title')))?'No':getData::getCatalogSession('catalog_title') }} ( Selected )</b></h6>
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
    <div class="app-header__content">
        @if(Auth::user()->level != 'Super Admin' || Auth::user()->level != 'User')
            <div class="app-header-left">
                <ul class="header-megamenu nav">
                    <li class="btn-group nav-item">
                        @if(getData::haveCatalog() == 'True')
                        <select id="catalogsession" class="custom-select" onchange="catalogSession()">
                            @if(getData::getCatalog()->count() > 1 || Auth::user()->owner == 1)
                                <option value="All">
                                    {{-- @if(!empty($page))
                                        @if($page == 'Stock')
                                            Warehouse ( Stock )
                                        @endif
                                    @else --}}
                                    {{ __('lang.select')}} Catalog
                                    {{-- @endif --}}
                                </option>
                            @endif
                            @foreach(getData::getCatalog() as $keycat => $catalogsession)
                                <option value="{{ $catalogsession['id'] }}" {{ ($keycat == 0)?'selected':'' }}>
                                    {{ $catalogsession['catalog_title'] }}
                                    {{-- @if(!empty($page))
                                        @if($page == 'Stock')
                                            ( Stock )
                                        @endif
                                    @endif --}}
                                </option>
                            @endforeach
                        </select>
                        @endif
                    </li>
                </ul>
            </div>
        @endif
        <div class="app-header-right">
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

            <div class="header-btn-lg pr-0">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="btn-group">
                                <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                    <img width="42" class="rounded-circle" src="{{ Auth::user()->photo ? asset(Auth::user()->photo) : asset('/images/template/avatars/user.png?'.time()) }}" alt="" />
                                    <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                </a>
                                <div tabindex="-1" role="menu" aria-hidden="true" class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-menu-header">
                                        <div class="dropdown-menu-header-inner bg-info">
                                            <div class="menu-header-image opacity-2" style="background-image: url({{ asset('/images/template/dropdown-header/city3.jpg')  }});"></div>
                                            <div class="menu-header-content text-left">
                                                <div class="widget-content p-0">
                                                    <div class="widget-content-wrapper">
                                                        <div class="widget-content-left mr-3">
                                                            <img width="42" class="rounded-circle" src="{{ Auth::user()->photo ? asset(Auth::user()->photo) : asset('/images/template/avatars/user.png?'.time()) }}" alt="" />
                                                        </div>
                                                        <div class="widget-content-left">
                                                            <div class="widget-heading">{{ Auth::user()->name }}</div>
                                                            <div class="widget-subheading opacity-8">{{ Auth::user()->email }}</div>
                                                        </div>
                                                        <div class="widget-content-right mr-2">
                                                          <a href="{{ url('/logout') }}" class="btn-pill btn-shadow btn-shine btn btn-focus">Logout</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="scroll-area-xs" style="height: 120px;">
                                        <div class="scrollbar-container ps">
                                            <ul class="nav flex-column">
                                                <li class="nav-item-header nav-item">My Account</li>
                                                <li class="nav-item">
                                                    <a href="{{ url('/profile') }}" class="nav-link">{{ __('lang.profileset')}} </a>
                                                </li>
                                                {{-- <li class="nav-item">
                                                    <a href="javascript:;" class="nav-link">{{ __('lang.recoverpass')}} </a>
                                                </li> --}}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left ml-3 header-user-info">
                            <div class="widget-heading">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="widget-subheading">
                                {{ Auth::user()->level }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
