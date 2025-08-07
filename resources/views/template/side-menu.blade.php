<div class="side-menu px-1 px-md-2 px-lg-3">
    
    {{-- NEW: Animated Logo Button at Top --}}
    <div class="logo-button-container mb-0 d-flex justify-content-start pl-2">
        <a href="#" id="side-menu-logo-btn" class="side-menu-logo-btn">
            <img src="{{ asset(getSetting('site.light_logo')) }}" alt="{{ __('Site logo') }}">
        </a>
    </div>

    <style>
    /* Ultra compact menu items - another 10% smaller */
    .side-menu {
        font-size: 0.65rem; /* Even tinier text */
    }
    
    .side-menu .nav-link {
        padding: 0.18rem 0.18rem; /* Ultra small padding */
        margin-bottom: 0.03rem; /* Minimal spacing */
    }
    
    .side-menu .icon-wrapper {
        width: 25px; /* Ultra small icons */
        height: 25px;
    }
    
    .side-menu .btn {
        padding: 0.25rem 0.35rem; /* Ultra small button padding */
        font-size: 0.58rem; /* Tiny button text */
        margin-bottom: 0.1rem; /* Minimal button spacing */
    }
    
    .side-menu .user-details {
        margin-bottom: 0.4rem; /* Ultra minimal spacing */
    }
    
    .side-menu .nav-item {
        margin-bottom: 0.03rem; /* Ultra tight spacing between items */
    }
    
    .side-menu .user-avatar {
        width: 27px !important; /* Ultra small avatar */
        height: 27px !important;
    }
    
    .side-menu .side-menu-label {
        font-size: 0.65rem; /* Tiny labels */
        line-height: 1.1; /* Ultra tight line height */
    }
    
    .side-menu-logo-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px; /* 20% bigger than 30px */
        height: 36px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #55a8f9; /* Thicker border for prominence */
        box-shadow: 0 2px 8px rgba(50,110,255,0.12);
        transition: box-shadow 0.25s, transform 0.25s;
        cursor: pointer;
        text-decoration: none;
        animation: cosplay-pulse 1.6s infinite alternate;
        padding: 2px; /* Better padding for bigger button */
    }
    
    .side-menu-logo-btn img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 50%;
    }
    
    @keyframes cosplay-pulse {
        0% { transform: scale(1); box-shadow: 0 2px 8px rgba(50,110,255,0.12); }
        50% { transform: scale(1.05); box-shadow: 0 4px 16px rgba(50,110,255,0.18); }
        100% { transform: scale(1); box-shadow: 0 2px 8px rgba(50,110,255,0.12); }
    }
    
    .side-menu-logo-btn:hover {
        border-color: #3483d6;
        background: #f4f9ff;
        box-shadow: 0 4px 20px rgba(50,110,255,0.22);
        animation-play-state: paused !important;
        transform: scale(1.08);
        text-decoration: none;
    }
    
    .side-menu-logo-btn:active {
        filter: brightness(0.96);
        transform: scale(0.95);
        animation-play-state: paused !important;
    }
    
    /* Mobile adjustments */
    @media (max-width: 767.98px) {
        .side-menu-logo-btn {
            width: 33px; /* 20% bigger than 28px */
            height: 33px;
            border-width: 2px;
            padding: 2px;
        }
    }
    
    /* Hide on very small left panel */
    @media (max-width: 575.98px) {
        .logo-button-container {
            display: none !important;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoBtn = document.getElementById('side-menu-logo-btn');
        if (logoBtn) {
            logoBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Scroll to top of feed/content
                const contentWrapper = document.querySelector('.content-wrapper');
                const feedContainer = document.querySelector('[data-feed-container]') || 
                                    document.querySelector('.posts-wrapper') || 
                                    document.querySelector('.feed-posts') ||
                                    contentWrapper;
                
                if (feedContainer) {
                    feedContainer.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } else {
                    // Fallback: scroll window to top
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
                
                console.log('🚀 Logo clicked - scrolling to top');
            });
        }
    });
    </script>

    <div class="user-details mb-0 d-flex open-menu pointer-cursor flex-row-no-rtl">
        <div class="ml-0 ml-md-2">
            @if(Auth::check())
                <img src="{{Auth::user()->avatar}}" class="rounded-circle user-avatar">
            @else
                <div class="avatar-placeholder">
                    @include('elements.icon',['icon'=>'person-circle','variant'=>'xlarge text-muted'])
                </div>
            @endif
        </div>
        @if(Auth::check())
            <div class="d-none d-lg-block overflow-hidden">
                <div class="pl-2 d-flex justify-content-center flex-column overflow-hidden">
                    <div class="ml-2 d-flex flex-column overflow-hidden">
                        <span class="text-bold text-truncate {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{Auth::user()->name}}</span>
                        <span class="text-muted"><span>@</span>{{Auth::user()->username}}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <ul class="nav flex-column user-side-menu">
        <li class="nav-item ">
            <a href="{{Auth::check() ? route('feed') : route('home')}}" class="h-pill h-pill-primary nav-link {{Route::currentRouteName() == 'feed' ? 'active' : ''}} d-flex justify-content-between">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        @include('elements.icon',['icon'=>'home-outline','variant'=>'large'])
                    </div>
                    <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Home')}}</span>
                </div>
            </a>
        </li>
        @if(GenericHelper::isEmailEnforcedAndValidated())
            <li class="nav-item">
                <a href="{{route('my.notifications')}}" class="nav-link h-pill h-pill-primary {{Route::currentRouteName() == 'my.notifications' ? 'active' : ''}} d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center position-relative">
                            @include('elements.icon',['icon'=>'notifications-outline','variant'=>'large'])
                            <div class="menu-notification-badge notifications-menu-count {{(isset($notificationsCountOverride) && $notificationsCountOverride->total > 0 ) || (NotificationsHelper::getUnreadNotifications()->total > 0) ? '' : 'd-none'}}">
                                {{!isset($notificationsCountOverride) ? NotificationsHelper::getUnreadNotifications()->total : $notificationsCountOverride->total}}
                            </div>
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Notifications')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('my.messenger.get')}}" class="nav-link h-pill h-pill-primary {{Route::currentRouteName() == 'my.messenger.get' ? 'active' : ''}} d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center position-relative">
                            @include('elements.icon',['icon'=>'chatbubbles-outline','variant'=>'large'])
                            <div class="menu-notification-badge notifications-menu-count {{(isset($messagesCountOverride) && $messagesCountOverride > 0) || (class_exists('MessengerHelper') && MessengerHelper::getUnreadMessagesCount() > 0) ? '' : 'd-none'}}">
                                {{!isset($messagesCountOverride) ? (class_exists('MessengerHelper') ? MessengerHelper::getUnreadMessagesCount() : 0) : $messagesCountOverride}}
                            </div>
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Messages')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('search.get')}}" class="nav-link {{Route::currentRouteName() == 'search.get' ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'compass-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Explore')}}</span>
                    </div>
                </a>
            </li>
            @if(getSetting('streams.allow_streams'))
                <li class="nav-item">
                    <a href="{{route('public.streams.get')}}" class="nav-link {{Route::currentRouteName() == 'public.streams.get' ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="icon-wrapper d-flex justify-content-center align-items-center position-relative">
                                @include('elements.icon',['icon'=>'videocam-outline','variant'=>'large'])
                                <div class="menu-notification-badge live-streams-menu-count {{StreamsHelper::getPublicLiveStreamsCount() > 0 ? '' : 'd-none'}}">
                                    {{StreamsHelper::getPublicLiveStreamsCount()}}
                                </div>
                            </div>
                            <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Streams')}}</span>
                        </div>

                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a href="{{route('my.bookmarks')}}" class="nav-link {{Route::currentRouteName() == 'my.bookmarks' ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'bookmark-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Bookmarks')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('my.lists.all')}}" class="nav-link {{Route::currentRouteName() == 'my.lists.all' ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'list-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Lists')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('my.settings',['type'=>'subscriptions'])}}" class="nav-link {{Route::currentRouteName() == 'my.settings' &&  is_int(strpos(Request::path(),'subscriptions')) ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'people-circle-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Subscriptions')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('profile',['username'=>Auth::user()->username])}}" class="nav-link {{Route::currentRouteName() == 'profile' && (request()->route("username") == Auth::user()->username) ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'person-circle-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('My profile')}}</span>
                    </div>
                </a>
            </li>
        @endif

        @if(!Auth::check())
            <li class="nav-item">
                <a href="{{route('search.get')}}" class="nav-link {{Route::currentRouteName() == 'search.get' ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'compass-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Explore')}}</span>
                    </div>
                </a>
            </li>
        @endif

        <li class="nav-item">
            <a href="#" role="button" class="open-menu nav-link h-pill h-pill-primary text-muted d-flex justify-content-between">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        @include('elements.icon',['icon'=>'ellipsis-horizontal-circle-outline','variant'=>'large'])
                    </div>
                    <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('More')}}</span>
                </div>
            </a>
        </li>

        @if(GenericHelper::isEmailEnforcedAndValidated())
            @if(getSetting('streams.streaming_driver') !== 'none' && !getSetting('site.hide_stream_create_menu'))
                <li class="nav-item-live mt-2 mb-0">
                    <a role="button" class="btn btn-round btn-outline-danger btn-block px-3" href="{{route('my.streams.get')}}{{StreamsHelper::getUserInProgressStream() ? '' : ( !GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks') ? '' : '?action=create')}}">
                        <div class="d-none d-md-flex d-xl-flex d-lg-flex justify-content-center align-items-center ml-1 text-truncate new-post-label">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div class="stream-on-label w-100 {{StreamsHelper::getUserInProgressStream() ? '' : 'd-none'}}">
                                    <div class="d-flex align-items-center w-100">
                                        <div class="mr-4"><div class="blob red"></div></div>
                                        <div class="ml-1">{{__('On air')}} </div>
                                    </div>
                                </div>
                                <div class="stream-off-label w-100 {{StreamsHelper::getUserInProgressStream() ? 'd-none' : ''}}">
                                    <div class="d-flex  align-items-center w-100">
                                        <div class="mr-3"> @include('elements.icon',['icon'=>'ellipse','variant'=>'','classes'=>'flex-shrink-0 text-danger'])</div>
                                        <div class="ml-1">{{__('Go live')}} </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="d-block d-md-none d-flex align-items-center justify-content-center">@include('elements.icon',['icon'=>'add-circle-outline','variant'=>'medium','classes'=>'flex-shrink-0'])</div>
                    </a>
                </li>
            @endif
        @endif

        @if(!getSetting('site.hide_create_post_menu'))
            @if(GenericHelper::isEmailEnforcedAndValidated())
                <li class="nav-item">
                    <a role="button" class="btn btn-round btn-primary btn-block " href="{{route('posts.create')}}">
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate new-post-label">{{__('New post')}}</span>
                        <span class="d-block d-md-none d-flex align-items-center justify-content-center">@include('elements.icon',['icon'=>'add-circle-outline','variant'=>'medium','classes'=>'flex-shrink-0'])</span>
                    </a>
                </li>
            @endif
        @endif

        {{-- ================= START: MODIFIED CODE ================= --}}
        <div class="d-flex mt-2 justify-center feed-filter-buttons">
            <a href="{{ route('feed', ['filter' => 'cosplay']) }}"
                class="feed-btn {{ request()->get('filter') == 'cosplay' ? 'active' : '' }}">Cosplay</a>
            <a href="{{ route('feed', ['filter' => 'anime']) }}"
                class="feed-btn {{ request()->get('filter') == 'anime' ? 'active' : '' }}">Anime</a>
        </div>
        {{-- ================= END: MODIFIED CODE ================= --}}
    </ul>
</div>
