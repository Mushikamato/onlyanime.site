<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand logo-wrapper" href="{{ route('home') }}">
            <img src="{{asset(getSetting('site.light_logo'))}}" alt="{{getSetting('site.name')}}" class="logo">
        </a>

        <!-- 18+ Toggle Button - COMPLETELY UNTOUCHED -->
        <div class="logo-style-18-btn-wrapper">
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
            <ul class="navbar-nav">
                @if(Auth::check())
                    @if(!getSetting('site.hide_create_post_menu'))
                        <li class="nav-item">
                            <a class="nav-link ml-0 ml-md-2 py-1" href="{{ route('posts.create') }}">{{ __('Create') }}</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link ml-0 ml-md-2 py-1" href="{{ route('feed') }}">{{ __('Feed') }}</a>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav ml-auto align-items-center">
                @guest
                    @if(Route::currentRouteName() !== 'profile')
                        <!-- NEW: Language Dropdown for Guests -->
                        @if(getSetting('site.allow_language_switch'))
                        <li class="nav-item dropdown">
                            <a class="nav-link py-1 dropdown-toggle d-flex align-items-center" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{ asset('img/language.png') }}" alt="Language" style="width: 20px; height: 20px; margin-right: 6px;">
                                {{ __("Language") }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                @foreach(LocalesHelper::getAvailableLanguages() as $languageCode)
                                    @if(LocalesHelper::getLanguageName($languageCode))
                                        <a class="dropdown-item" href="{{route('language',['locale' => $languageCode])}}" rel="nofollow">
                                            {{ucfirst(__(LocalesHelper::getLanguageName($languageCode)))}}
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
                    <!-- Language Dropdown for Logged In Users -->
                    @if(getSetting('site.allow_language_switch'))
                    <li class="nav-item dropdown">
                        <a class="nav-link py-1 dropdown-toggle d-flex align-items-center" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ asset('img/language.png') }}" alt="Language" style="width: 20px; height: 20px; margin-right: 6px;">
                            {{ __("Language") }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach(LocalesHelper::getAvailableLanguages() as $languageCode)
                                @if(LocalesHelper::getLanguageName($languageCode))
                                    <a class="dropdown-item" href="{{route('language',['locale' => $languageCode])}}" rel="nofollow">
                                        {{ucfirst(__(LocalesHelper::getLanguageName($languageCode)))}}
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

{{-- 18+ Button Script - COMPLETELY UNTOUCHED --}}
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
