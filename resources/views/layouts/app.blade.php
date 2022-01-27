<!DOCTYPE html>
    <!--[if IE 9]>         <html class="no-js lt-ie10" lang="en"> <![endif]-->
    <!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">

        <title>{{ env('APP_NAME') }}</title>

        <meta name="description" content="ProUI is a Responsive Bootstrap Admin Template created by pixelcave and published on Themeforest.">
        <meta name="author" content="pixelcave">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0">

        <!-- Stylesheets -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />

        <!-- Related styles of various icon packs and plugins -->
        <link rel="stylesheet" href="{{ asset('css/plugins.css') }}">

        <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
        <link rel="stylesheet" href="{{ asset('css/main.css') }}">

        <!-- Default themes. -->
        @if (!empty(Cookie::get('default_theme')))
            <link id="theme-link" rel="stylesheet" href="{{ asset(Cookie::get('default_theme')) }}">
        @endif

        <link rel="stylesheet" href="{{ asset('css/themes.css') }}"/>
        <!-- END Stylesheets -->

        <!-- Modernizr (browser feature detection library) -->
        <script src="{{ asset('js/modernizr.min.js') }}"></script>

        <!-- Custom CSS. -->
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    </head>
    <body>
        <div id="page-wrapper">
            <div class="preloader themed-background">
                <h1 class="push-top-bottom text-light text-center">{{ env('APP_NAME') }}</h1>
                <div class="inner">
                    <h3 class="text-light visible-lt-ie9 visible-lt-ie10"><strong>Loading..</strong></h3>
                    <div class="preloader-spinner hidden-lt-ie9 hidden-lt-ie10"></div>
                </div>
            </div>

            @auth
                <div id="page-container" class="sidebar-partial sidebar-visible-lg sidebar-no-animations {{ Cookie::get('default_page_style', '') }}">
            @endauth
            @guest
                <div id="{{ request()->route()->getName() == 'privacy.policy' ? '' : 'page-container' }}" class="sidebar-partial sidebar-visible-lg sidebar-no-animations {{ Cookie::get('default_page_style', '') }}">
            @endguest
                @auth
                <!-- Main Sidebar -->
                <div id="sidebar">
                    <div id="sidebar-scroll">
                        <!-- Sidebar Content -->
                        <div class="sidebar-content">
                            <!-- Brand -->
                            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                                <i class="gi gi-flash"></i><span class="sidebar-nav-mini-hide">{{ env('APP_NAME') }}</span>
                            </a>
                            <!-- END Brand -->

                            <!-- User Info -->
                            <div class="sidebar-section sidebar-user clearfix sidebar-nav-mini-hide">
                                <div class="sidebar-user-avatar">
                                    <a href="{{ route('dashboard') }}">
                                        <img src="{{ asset('img/placeholders/avatars/avatar.jpeg') }}" alt="avatar">
                                    </a>
                                </div>
                                <div class="sidebar-user-name">{{ __('Administrator') }}</div>
                                <div class="sidebar-user-links">
                                    <a href="javascript:void(0)" data-toggle="modal" data-placement="bottom" title="{{ __('Settings') }}" data-target="#change-password"><i class="gi gi-cogwheel"></i></a>
                                    <a href="#" data-toggle="tooltip" data-placement="bottom" title="{{ __('Logout') }}" onclick="event.preventDefault();
                                                                                        document.getElementById('logout-form').submit();"><i class="gi gi-exit"></i></a>

                                    <a href="{{ route('download.application') }}" data-placement="bottom" title="{{ __('Download Application') }}"><i class="gi gi-download"></i></a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                            <!-- END User Info -->

                            <!-- Theme Colors -->
                            <ul class="sidebar-section sidebar-themes clearfix sidebar-nav-mini-hide">
                                <!-- You can also add the default color theme
                                <li class="active">
                                    <a href="javascript:void(0)" class="themed-background-dark-default themed-border-default" data-theme="default" data-toggle="tooltip" title="Default Blue"></a>
                                </li>
                                -->
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-night themed-border-night" data-theme="{{ asset('css/themes/night.css') }}" data-toggle="tooltip" title="Night"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-amethyst themed-border-amethyst" data-theme="{{ asset('css/themes/amethyst.css') }}" data-toggle="tooltip" title="Amethyst"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-modern themed-border-modern" data-theme="{{ asset('css/themes/modern.css') }}" data-toggle="tooltip" title="Modern"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-autumn themed-border-autumn" data-theme="{{ asset('css/themes/autumn.css') }}" data-toggle="tooltip" title="Autumn"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-flatie themed-border-flatie" data-theme="{{ asset('css/themes/flatie.css') }}" data-toggle="tooltip" title="Flatie"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-spring themed-border-spring" data-theme="{{ asset('css/themes/spring.css') }}" data-toggle="tooltip" title="Spring"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-fancy themed-border-fancy" data-theme="{{ asset('css/themes/fancy.css') }}" data-toggle="tooltip" title="Fancy"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-fire themed-border-fire" data-theme="{{ asset('css/themes/fire.css') }}" data-toggle="tooltip" title="Fire"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-coral themed-border-coral" data-theme="{{ asset('css/themes/coral.css') }}" data-toggle="tooltip" title="Coral"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-lake themed-border-lake" data-theme="{{ asset('css/themes/lake.css') }}" data-toggle="tooltip" title="Lake"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-forest themed-border-forest" data-theme="{{ asset('css/themes/forest.css') }}" data-toggle="tooltip" title="Forest"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-waterlily themed-border-waterlily" data-theme="{{ asset('css/themes/waterlily.css') }}" data-toggle="tooltip" title="Waterlily"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-emerald themed-border-emerald" data-theme="{{ asset('css/themes/emerald.css') }}" data-toggle="tooltip" title="Emerald"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-blackberry themed-border-blackberry" data-theme="{{ asset('css/themes/blackberry.css') }}" data-toggle="tooltip" title="Blackberry"></a>
                                </li>
                            </ul>
                            <!-- END Theme Colors -->

                            <!-- Sidebar Navigation -->
                            <ul class="sidebar-nav">
                                <li>
                                    <a href="{{ route('dashboard') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                                        <i class="fa fa-columns sidebar-nav-icon"></i>
                                        <span class="sidebar-nav-mini-hide">{{ __('Dashboard') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('ho.index') }}" class="{{ request()->is('finance/ho*') ? 'active' : '' }}">
                                        <i class="gi gi-header sidebar-nav-icon"></i>
                                        <span class="sidebar-nav-mini-hide">{{ __('Finance HO') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('company.index') }}" class="{{ request()->is('finance/company*') ? 'active' : '' }}">
                                        <i class="gi gi-building sidebar-nav-icon"></i>
                                        <span class="sidebar-nav-mini-hide">{{ __('Finance Company') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('group.index') }}" class="{{ request()->is('group*') ? 'active' : '' }}">
                                        <i class="gi gi-group sidebar-nav-icon"></i>
                                        <span class="sidebar-nav-mini-hide">{{ __('Group') }}</span>
                                    </a>
                                </li>
                                <li class="{{ (request()->is('subseizer*') || request()->is('activity*')) ? 'active' : '' }}">
                                    <a href="#" class="sidebar-nav-menu">
                                        <i class="fa fa-angle-left sidebar-nav-indicator sidebar-nav-mini-hide"></i>
                                        <i class="gi gi-user sidebar-nav-icon"></i>
                                        <span class="sidebar-nav-mini-hide">{{ __('Sub Seizers') }}</span>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="{{ route('subseizer.index') }}" class="{{ request()->is('subseizer*') && !request()->is('subseizer/activity') ? 'active' : '' }}">{{ __('Users') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('activity.index') }}" class="{{ request()->is('subseizer/activity*') ? 'active' : '' }}">{{ __('Activity') }}</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="{{ route('vehicle.index') }}" class="{{ request()->is('vehicle*') ? 'active' : '' }}">
                                        <i class="gi gi-car sidebar-nav-icon"></i>
                                        <span class="sidebar-nav-mini-hide">{{ __('Vehicles') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('report.index') }}" class="{{ request()->is('report*') ? 'active' : '' }}">
                                        <i class="gi gi-file sidebar-nav-icon"></i>
                                        <span class="sidebar-nav-mini-hide">{{ __('Reports') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('constant.index') }}" class="{{ request()->is('constant*') ? 'active' : '' }}">
                                        <i class="fa fa-database sidebar-nav-icon"></i>
                                        <span class="sidebar-nav-mini-hide">{{ __('Constants') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('privacy.policy') }}" class="{{ request()->is('privacy*') ? 'active' : '' }}">
                                        <i class="fa fa-lock sidebar-nav-icon"></i>
                                        <span class="sidebar-nav-mini-hide">{{ __('Privacy Policy') }}</span>
                                    </a>
                                </li>
                            </ul>
                            <!-- END Sidebar Navigation -->
                        </div>
                        <!-- END Sidebar Content -->
                    </div>
                </div>
                <!-- END Main Sidebar -->
                @endauth

                <!-- Main Container -->
                @auth
                    <div id="main-container">
                @endauth
                @guest
                    <div id="{{ request()->route()->getName() == 'privacy.policy' ? 'page-content' : 'login-container' }}" style="{{ request()->route()->getName() == 'privacy.policy' ? 'height: 100vh;' : '' }}" class="animation-fadeIn">
                @endguest
                    @auth
                    <!-- Header -->
                    <header class="navbar navbar-{{ Cookie::get('default_header_theme', 'default') }}">
                        <!-- Left Header Navigation -->
                        <ul class="nav navbar-nav-custom">
                            <!-- Main Sidebar Toggle Button -->
                            <li>
                                <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar');this.blur();">
                                    <i class="fa fa-bars fa-fw"></i>
                                </a>
                            </li>
                            <!-- END Main Sidebar Toggle Button -->

                            <!-- Template Options -->
                            <li class="dropdown">
                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="gi gi-settings"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-custom dropdown-options">
                                    <li class="dropdown-header text-center">Header Style</li>
                                    <li>
                                        <div class="btn-group btn-group-justified btn-group-sm">
                                            <a href="javascript:void(0)" class="btn btn-primary" id="options-header-default" data-type="default">Light</a>
                                            <a href="javascript:void(0)" class="btn btn-primary" id="options-header-inverse" data-type="inverse">Dark</a>
                                        </div>
                                    </li>
                                    <li class="dropdown-header text-center">Page Style</li>
                                    <li>
                                        <div class="btn-group btn-group-justified btn-group-sm">
                                            <a href="javascript:void(0)" class="btn btn-primary" id="options-main-style" data-type="default">Default</a>
                                            <a href="javascript:void(0)" class="btn btn-primary" id="options-main-style-alt" data-type="style-alt">Alternative</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- END Template Options -->
                        </ul>
                        <!-- END Left Header Navigation -->
                    </header>
                    <!-- END Header -->
                    @endauth

                    <!-- Page content -->
                    <div @auth id="page-content" @endauth>
                        @yield('content')
                    </div>
                    <!-- END Page content -->
                </div>
                <!-- END Main Container -->
            </div>
        </div>

        <!-- Change password modal. -->
        <div id="change-password" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">{{ __('Update Password') }}</h4>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('admin.password.update') }}" method="post" class="form-horizontal form-bordered" _lpchecked="1">
                            @csrf

                            <div class="form-group">
                                <label class="col-md-3 control-label" for="current_password">{{ __('Current Password') }}</label>
                                <div class="col-md-9">
                                    <input type="password" name="current_password" id="current_password" class="form-control" autofocus />
                                    <span class="help-block">{{ __("If you forget then you can add master password here.") }}</span>
                                    <em class="color-red error invalid-feedback d-none" role="alert">
                                        <strong id="error_current_password"></strong>
                                    </em>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="new_password">{{ __('New Password') }}</label>
                                <div class="col-md-9">
                                    <input type="password" name="new_password" id="new_password" class="form-control" />
                                    <em class="color-red error invalid-feedback d-none" role="alert">
                                        <strong id="error_new_password"></strong>
                                    </em>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="confirm_new_password">{{ __('Confirm New Password') }}</label>
                                <div class="col-md-9">
                                    <input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" />
                                    <em class="color-red error invalid-feedback d-none" role="alert">
                                        <strong id="error_confirm_new_password"></strong>
                                    </em>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <em class="color-red error invalid-feedback d-none" role="alert">
                                    <strong id="error_general"></strong>
                                </em>
                                <em class="color-green alert-success d-none" role="alert">
                                    <strong id="success_general"></strong>
                                </em>
                                <button type="button" class="btn btn-sm btn-primary" id="admin-password-update"><i class="fa fa-user"></i> {{ __('Update') }}</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <!-- Scroll to top link, initialized in js/app.js - scrollToTop() -->
    <a href="#" id="to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- jQuery, Bootstrap.js, jQuery plugins and Custom JS code -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script type="text/javascript">
        var setThemeCookieRoute  = "{{ route('set-theme-cookie') }}",
            _token               = "{{ csrf_token() }}",
            whatsAppMessageRoute = "{{ route('vehicle.confirm.message.whatsapp.send') }}";
    </script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>

    @stack('custom-scripts')
</html>
