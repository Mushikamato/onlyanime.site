<nav class="navbar navbar-expand-md {{ Cookie::get('app_theme') ? (Cookie::get('app_theme') == 'dark' ? 'navbar-dark bg-dark' : 'navbar-light bg-white') : (getSetting('site.default_user_theme') == 'dark' ? 'navbar-dark bg-dark' : 'navbar-light bg-white') }} shadow-sm py-1">
    <style>
    .header-center-btn {
      position: absolute;
      left: 0; right: 0; top: 0;
      width: 100%;
      z-index: 10;
      pointer-events: none;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
    }
    .logo-style-18-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 90px;
      height: 44px;
      padding: 0 20px;
      border-radius: 20px;
      background: #fff;
      border: 4px solid #55a8f9;
      font-size: 1.17rem;
      font-weight: bold;
      color: #55a8f9;
      box-shadow: 0 4px 16px rgba(50,110,255,0.12);
      transition: box-shadow 0.25s, background-color 0.25s, color 0.25s;
      cursor: pointer;
      text-decoration: none;
      margin: 0 10px;
      user-select: none;
      position: relative;
      pointer-events: auto;
      animation: cosplay-pulse 1.6s infinite alternate;
    }
    .logo-style-18-btn.active-nsfw { background-color:#3483d6; color:#fff; border-color:#fff; }
    @keyframes cosplay-pulse {
      0% { transform: scale(1); box-shadow: 0 4px 16px rgba(50,110,255,0.12);}
      50% { transform: scale(1.08); box-shadow: 0 8px 32px rgba(50,110,255,0.17);}
      100% { transform: scale(1); box-shadow: 0 4px 16px rgba(50,110,255,0.12);}
    }
    .logo-style-18-btn:hover, .logo-style-18-btn:focus-visible {
      border-color:#3483d6; background:#55a8f9; color:#fff; transform:scale(1.11);
      box-shadow:0 8px 28px 4px rgba(85,168,249,0.16);
      animation-play-state: paused !important;
    }
    .logo-style-18-btn:active { filter:brightness(0.96); transform:scale(0.97); animation-play-state: paused !important; }
    .logo-style-18-btn .icon-18 { font-size:1.36em; margin-right:7px; line-height:1; }
    @media (max-width: 767.98px) {
      .header-center-btn { height:56px; align-items:flex-end; }
      .logo-style-18-btn { min-width:64px; height:36px; font-size:1em; padding:0 12px; border-radius:11px; margin-left:100px; }
      .logo-style-18-btn .icon-18 { font-size:1em; margin-right:2px; }
    }

    /* ===== Glass dropdown for Language (light & dark aware) ===== */
    .lang-dropdown{
      background: rgba(255,255,255,0.72) !important;
      backdrop-filter: blur(12px) saturate(160%) !important;
      -webkit-backdrop-filter: blur(12px) saturate(160%) !important;
      border-radius: 16px;
      border: 1px solid rgba(255,255,255,0.35);
      box-shadow: 0 12px 28px rgba(0,0,0,0.22);
      overflow: hidden;
      padding: 8px 0;
      min-width: 200px;
    }
    .lang-dropdown .dropdown-item{
      color:#1b1f28;
      font-weight:600;
      padding:10px 16px;
      transition: background .25s, color .25s;
    }
    .lang-dropdown .dropdown-item:hover{
      background: rgba(85,168,249,0.15);
      color:#3483d6;
    }
    /* dark theme overrides */
    .navbar-dark .lang-dropdown{
      background: rgba(20,22,30,0.55) !important;
      border-color: rgba(255,255,255,0.12);
    }
    .navbar-dark .lang-dropdown .dropdown-item{
      color:#ecf3ff;
    }
    .navbar-dark .lang-dropdown .dropdown-item:hover{
      background: rgba(85,168,249,0.22);
      color:#ffffff;
    }
    </style>

    <div class="container-fluid position-relative">

        <!-- ВСЕГДА СВЕТЛОЕ ЛОГО -->
        <a class="navbar-brand py-0 navbar-logo-large" href="{{ route('home') }}">
            <img src="{{ asset(getSetting('site.light_logo')) }}"
                 class="d-inline-block align-top"
                 alt="{{ __('Site logo') }}">
        </a>

        <div class="header-center-btn">
            @if(Auth::check())
                <a href="#" id="header-toggle-adult-content"
                   class="logo-style-18-btn {{ Auth::user()->show_adult_content ? 'active-nsfw' : '' }}"
                   data-is-nsfw-on="{{ Auth::user()->show_adult_content ? 'true' : 'false' }}">
                    <span class="icon-18">🔞</span>
                    <span>18+</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="logo-style-18-btn">
                    <span class="icon-18">🔞</span>
                    <span>18+</span>
                </a>
            @endif
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- ЛЕВАЯ СТОРОНА — Create и Feed убраны -->
            <ul class="navbar-nav text-center w-100"></ul>

            <ul class="navbar-nav ml-auto align-items-center text-center">
                @guest
                    @if(Route::currentRouteName() !== 'profile')
                        @if(getSetting('site.allow_language_switch'))
                        <li class="nav-item dropdown">
                            <a class="nav-link py-1 dropdown-toggle d-flex align-items-center"
                               href="#"
                               id="navbarDropdownLanguageGuest"
                               role="button"
                               data-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false">
                                <img src="{{ asset('img/language.png') }}" alt="Language" style="width: 20px; height: 20px; margin-right: 6px;">
                                {{ __("Language") }}
                            </a>
                            <div class="dropdown-menu lang-dropdown dropdown-menu-right" aria-labelledby="navbarDropdownLanguageGuest">
                                @foreach(LocalesHelper::getAvailableLanguages() as $languageCode)
                                    @if(LocalesHelper::getLanguageName($languageCode))
                                        <a class="dropdown-item" href="{{route('language',['locale' => $languageCode])}}" rel="nofollow">
                                            @if($languageCode == 'en')
                                                🇬🇧 English
                                            @elseif($languageCode == 'ro')
                                                🇷🇴 Romanian
                                            @else
                                                {{ ucfirst(__(LocalesHelper::getLanguageName($languageCode))) }}
                                            @endif
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link py-1" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @endif
                @else
                    @if(getSetting('site.allow_language_switch'))
                    <li class="nav-item dropdown">
                        <a class="nav-link py-1 dropdown-toggle d-flex align-items-center"
                           href="#"
                           id="navbarDropdownLanguageUser"
                           role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false">
                            <img src="{{ asset('img/language.png') }}" alt="Language" style="width: 20px; height: 20px; margin-right: 6px;">
                            {{ __("Language") }}
                        </a>
                        <div class="dropdown-menu lang-dropdown dropdown-menu-right" aria-labelledby="navbarDropdownLanguageUser">
                            @foreach(LocalesHelper::getAvailableLanguages() as $languageCode)
                                @if(LocalesHelper::getLanguageName($languageCode))
                                    <a class="dropdown-item" href="{{route('language',['locale' => $languageCode])}}" rel="nofollow">
                                        @if($languageCode == 'en')
                                            🇬🇧 English
                                        @elseif($languageCode == 'ro')
                                            🇷🇴 Romanian
                                        @else
                                            {{ ucfirst(__(LocalesHelper::getLanguageName($languageCode))) }}
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle text-right text-truncate d-flex align-items-center py-1" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="text-truncate max-width-150 mr-1">{{ Auth::user()->name }}</div>
                            <img src="{{Auth::user()->avatar}}" class="rounded-circle home-user-avatar" style="width: 38px; height: 38px;">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route('feed')}}">
                                {{__('Feed')}}
                            </a>
                            @if(!getSetting('site.hide_create_post_menu'))
                                <a class="dropdown-item" href="{{ route('posts.create') }}">
                                    {{ __('Create') }}
                                </a>
                            @endif
                            <a class="dropdown-item" href="{{route('my.messenger.get')}}">
                                {{__('Messenger')}}
                            </a>
                            <a class="dropdown-item" href="{{route('my.settings')}}">
                                {{__('Settings')}}
                            </a>
                            <a class="dropdown-item" href="{{route('profile',['username'=>Auth::user()->username])}}">
                                {{__('Profile')}}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

{{-- 18+ Button Script - COMPLEТELY UNTOUCHED --}}
@if(Auth::check())
@push('scripts')
<script>
    $(function() {
        $('#header-toggle-adult-content').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var isNsfwOn = button.data('is-nsfw-on');
            var newState = !isNsfwOn;

            button.css('pointer-events', 'none');

            $.ajax({
                type: 'POST',
                url: '{{ route('my.settings.flags.save') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'key': 'show_adult_content',
                    'value': newState
                },
                success: function(response) {
                    if(response.success) {
                        location.reload();
                    } else {
                        alert('Could not update setting.');
                        button.css('pointer-events', 'auto');
                    }
                },
                error: function() {
                    alert('An error occurred.');
                    button.css('pointer-events', 'auto');
                }
            });
        });
    });
</script>
@endpush
@endif