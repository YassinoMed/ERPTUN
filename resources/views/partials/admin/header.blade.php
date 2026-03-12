@php
    $users=\Auth::user();
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
    $languages=\App\Models\Utility::languages();

    $lang = isset($users->lang)?$users->lang:'en';
    if ($lang == null) {
        $lang = 'en';
    }
    $LangName = cache()->remember('full_language_data_' . $lang, now()->addHours(24), function () use ($lang) {
    return \App\Models\Language::languageData($lang);
    });

    $setting = \App\Models\Utility::settings();

    $unseenCounter=App\Models\ChMessage::where('to_id', Auth::user()->id)->where('seen', 0)->count();
    $savedViews = \App\Models\SavedView::query()
        ->where('user_id', Auth::id())
        ->latest('is_default')
        ->latest('id')
        ->limit(6)
        ->get();
@endphp
@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <header class="dash-header transprent-bg erp-header-shell">
@else
    <header class="dash-header erp-header-shell">
@endif
    <div class="header-wrapper erp-header-wrapper">
        <div class="me-auto dash-mob-drp">
            <ul class="list-unstyled erp-header-cluster erp-header-cluster-start">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="dash-h-item d-none d-lg-inline-flex">
                    <a href="#!" class="dash-head-link" data-sidebar-pin-toggle="1" aria-label="{{ __('Toggle compact sidebar') }}">
                        <i class="ti ti-layout-sidebar-left-collapse"></i>
                    </a>
                </li>

                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="theme-avtar">
                             <img src="{{ !empty(\Auth::user()->avatar) ? $profile . \Auth::user()->avatar :  $profile.'avatar.png'}}" class="img-fluid rounded border-2 border border-primary">
                        </span>
                        <span class="hide-mob ms-2">{{__('Hi, ')}}{{\Auth::user()->name }}!</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">

                        <a href="{{route('profile')}}" class="dropdown-item">
                            <i class="ti ti-user text-dark"></i><span>{{__('Profile')}}</span>
                        </a>

                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" class="dropdown-item">
                            <i class="ti ti-power text-dark"></i><span>{{__('Logout')}}</span>
                        </a>

                        <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                            {{ csrf_field() }}
                        </form>

                    </div>
                </li>

            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled erp-header-cluster erp-header-cluster-end">
                @if(\Auth::user()->type == 'company' )
                @impersonating($guard = null)
                <li class="dropdown dash-h-item drp-company">
                    <a class="btn btn-danger btn-sm" href="{{ route('exit.company') }}"><i class="ti ti-ban"></i>
                        {{ __('Exit Company Login') }}
                    </a>
                </li>
                @endImpersonating
                @endif

                @if(\Auth::user()->type != 'client')
                    <li class="dropdown dash-h-item drp-create">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0" href="#" id="quickCreateToggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-plus"></i>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                            @can('create invoice')
                                <a href="{{ route('invoice.create', 0) }}" class="dropdown-item">
                                    <i class="ti ti-file-invoice text-dark"></i><span>{{ __('Invoice') }}</span>
                                </a>
                            @endcan

                            @can('create customer')
                                <a href="{{ route('customer.create') }}" class="dropdown-item">
                                    <i class="ti ti-users text-dark"></i><span>{{ __('Customer') }}</span>
                                </a>
                            @endcan

                            @can('create client')
                                <a href="{{ route('clients.create') }}" class="dropdown-item">
                                    <i class="ti ti-user-plus text-dark"></i><span>{{ __('Client') }}</span>
                                </a>
                            @endcan

                            @can('create project')
                                <a href="{{ route('projects.create') }}" class="dropdown-item">
                                    <i class="ti ti-briefcase text-dark"></i><span>{{ __('Project') }}</span>
                                </a>
                            @endcan

                            @can('create employee')
                                <a href="{{ route('employee.create') }}" class="dropdown-item">
                                    <i class="ti ti-id text-dark"></i><span>{{ __('Employee') }}</span>
                                </a>
                            @endcan
                        </div>
                    </li>
                @endif

                <li class="dropdown dash-h-item drp-workspace">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-layout-grid"></i>
                        <span class="drp-text hide-mob">{{ __('Workspace') }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        <span class="dropdown-item-text workspace-menu-label">{{ __('Navigate') }}</span>
                        <a href="{{ route('executive.dashboard') }}" class="dropdown-item">
                            <i class="ti ti-layout-dashboard text-dark"></i><span>{{ __('Executive Overview') }}</span>
                        </a>
                        @can('manage invoice')
                            <a href="{{ route('invoice.index') }}" class="dropdown-item">
                                <i class="ti ti-file-invoice text-dark"></i><span>{{ __('Finance Desk') }}</span>
                            </a>
                        @endcan
                        @can('manage lead')
                            <a href="{{ route('leads.index') }}" class="dropdown-item">
                                <i class="ti ti-users text-dark"></i><span>{{ __('CRM Pipeline') }}</span>
                            </a>
                        @endcan
                        @can('manage employee')
                            <a href="{{ route('employee.index') }}" class="dropdown-item">
                                <i class="ti ti-user-heart text-dark"></i><span>{{ __('People Hub') }}</span>
                            </a>
                        @endcan
                        @can('manage projects')
                            <a href="{{ route('projects.index') }}" class="dropdown-item">
                                <i class="ti ti-briefcase text-dark"></i><span>{{ __('Projects') }}</span>
                            </a>
                        @endcan
                        <div class="dropdown-divider"></div>
                        <span class="dropdown-item-text workspace-menu-label">{{ __('Shortcuts') }}</span>
                        <a href="{{ route('core.saved-views.index') }}" class="dropdown-item">
                            <i class="ti ti-bookmarks text-dark"></i><span>{{ __('Saved Views') }}</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" onclick="if(window.toggleNotifPanel){window.toggleNotifPanel();}">
                            <i class="ti ti-bell-ringing text-dark"></i><span>{{ __('Notifications') }}</span>
                        </a>
                        <a href="{{ route('core.onboarding') }}" class="dropdown-item">
                            <i class="ti ti-building-store text-dark"></i><span>{{ __('Tenant Cockpit') }}</span>
                        </a>
                        <a href="{{ route('core.security.index') }}" class="dropdown-item">
                            <i class="ti ti-shield-lock text-dark"></i><span>{{ __('Security Center') }}</span>
                        </a>
                        <a href="{{ route('core.help-center') }}" class="dropdown-item">
                            <i class="ti ti-lifebuoy text-dark"></i><span>{{ __('Help Center') }}</span>
                        </a>
                        @if($savedViews->isNotEmpty())
                            <div class="dropdown-divider"></div>
                            <span class="dropdown-item-text workspace-menu-label">{{ __('Recent Views') }}</span>
                            @foreach($savedViews as $savedView)
                                <a href="{{ route('core.saved-views.index') }}" class="dropdown-item">
                                    <i class="ti ti-arrow-forward-up text-dark"></i>
                                    <span>{{ $savedView->name }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </li>

                <li class="dropdown dash-h-item">
                    <a class="dash-head-link arrow-none me-0 dash-command-launcher" href="javascript:void(0)" onclick="openSearch()" data-global-search-trigger="1" aria-haspopup="false" aria-expanded="false">
                        <span class="command-icon">
                            <i class="ti ti-search"></i>
                        </span>
                        <span class="command-copy d-none d-xl-flex">
                            <span class="command-title">{{ __('Search, commands, clients...') }}</span>
                            <span class="command-shortcut">Ctrl K</span>
                        </span>
                    </a>
                </li>

                @if( \Auth::user()->type !='client' && \Auth::user()->type !='super admin' )
                    <li class="dropdown dash-h-item drp-notification">
                        <a class="dash-head-link arrow-none me-0" href="{{ url('chats') }}" aria-haspopup="false"
                           aria-expanded="false">
                            <i class="ti ti-brand-hipchat"></i>
                            <span class="bg-danger dash-h-badge message-toggle-msg  message-counter custom_messanger_counter beep"> {{ $unseenCounter }}<span
                                    class="sr-only"></span>
                            </span>
                        </a>
                    </li>
                @endif

                <li class="dropdown dash-h-item drp-language">
                    <a
                        class="dash-head-link dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false"
                    >
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob">{{ ucfirst(optional($LangName)->full_name ?? ($languages[$lang] ?? $lang ?? 'en')) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @foreach ($languages as $code => $language)
                            <a href="{{ route('change.language', $code) }}"
                               class="dropdown-item {{ $lang == $code ? 'text-primary' : '' }}">
                                <span>{{ucFirst($language)}}</span>
                            </a>
                        @endforeach

                        <h></h>
                            @if(\Auth::user()->type=='super admin')
                                <a data-url="{{ route('create.language') }}" class="dropdown-item text-primary" data-ajax-popup="true" data-title="{{__('Create New Language')}}" style="cursor: pointer">
                                    {{ __('Create Language') }}
                                </a>
                                <a class="dropdown-item text-primary" href="{{route('manage.language',[isset($lang)?$lang:'english'])}}">{{ __('Manage Language') }}</a>
                            @endif
                    </div>
                </li>
            </ul>
        </div>
    </div>
    </header>
