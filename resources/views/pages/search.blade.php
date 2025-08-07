@extends('layouts.user-no-nav')

@section('page_title', __('Discover'))
@section('share_url', route('home'))
@section('share_title', getSetting('site.name') . ' - ' . getSetting('site.slogan'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@section('meta')
    <meta name="robots" content="noindex">
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/js/PostsPaginator.js',
            '/js/UsersPaginator.js',
            '/js/StreamsPaginator.js',
            '/js/CommentsPaginator.js',
            '/js/Post.js',
            '/js/SuggestionsSlider.js',
            '/js/pages/lists.js',
            '/js/pages/checkout.js',
            '/libs/swiper/swiper-bundle.min.js',
            '/js/plugins/media/photoswipe.js',
            '/libs/photoswipe/dist/photoswipe-ui-default.min.js',
            '/js/plugins/media/mediaswipe.js',
            '/js/plugins/media/mediaswipe-loader.js',
            '/libs/autolinker/dist/autolinker.min.js',
            '/js/pages/search.js',
         ])->withFullUrl()
    !!}
@stop

@section('styles')
    {!!
        Minify::stylesheet([
            '/libs/swiper/swiper-bundle.min.css',
            '/libs/photoswipe/dist/photoswipe.css',
            '/css/pages/checkout.css',
            '/libs/photoswipe/dist/default-skin/default-skin.css',
            '/css/pages/feed.css',
            '/css/posts/post.css',
            '/css/pages/search.css',
         ])->withFullUrl()
    !!}
    <style>
        /* Fansly-style Discovery Feed */
        .discovery-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }
        
        .search-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 0 1rem;
        }
        
        .back-button {
            background: var(--dark-color-100);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .search-input-container {
            flex: 1;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--dark-color-200);
            border-radius: 25px;
            background: var(--dark-color-100);
            font-size: 1rem;
        }
        
        /* Category Tags (like Fansly) */
        .category-tags {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 2rem;
            padding-left: 2rem;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .category-tags::-webkit-scrollbar {
            display: none;
        }
        
        .category-tag {
            background: var(--dark-color-100);
            border: 1px solid var(--dark-color-200);
            color: var(--text-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            white-space: nowrap;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
       .category-tag:hover,
.category-tag.active {
    background: #007bff;
    border-color: #007bff;
    color: white;
    text-decoration: none;
}
        
        /* Discovery Sections */
        .discovery-section {
            margin-bottom: 3rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-left: 1rem;
            margin-left: 1rem;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 500;
            margin: 0;
        }
        
        .view-all-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .view-all-link:hover {
            text-decoration: underline;
        }
        
        /* Made For You Grid */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
            padding: 0 1rem;
        }
        
        .content-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 16/9;
            background: var(--dark-color-100);
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .content-card:hover {
            transform: scale(1.02);
        }
        
        .content-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .content-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            padding: 1rem;
            color: white;
        }
        
        .content-title {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        .content-meta {
            font-size: 0.8rem;
            opacity: 0.9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .content-author {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .content-stats {
            display: flex;
            gap: 0.75rem;
        }
        
        /* Live Streams */
        .live-indicator {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            background: #ff4757;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 2;
        }
        
        .live-indicator::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            margin-right: 0.25rem;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Accounts/Streams Toggle */
        .featured-section {
            margin-bottom: 3rem;
        }
        
        .toggle-header {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-left: 1rem;
        }
        
        .content-toggle {
            display: flex;
            background: var(--dark-color-100);
            border-radius: 25px;
            padding: 0.25rem;
            gap: 0.25rem;
        }
        
        .toggle-btn {
            background: transparent;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 20px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-muted);
            white-space: nowrap;
        }
        
        .toggle-btn.active {
            background: var(--primary-color);
            color: white;
        }
        
        /* User Cards */
        .users-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            padding: 0 1rem;
        }
        
        .user-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 12px;
            background: var(--dark-color-100);
            border: 1px solid var(--dark-color-200);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            object-fit: cover;
        }
        
        .user-name {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .follow-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease;
            width: 100%;
        }
        
        .follow-button:hover {
            background: var(--primary-color-dark);
        }
        
        /* Hide the old user list elements completely */
        .user-search-box-item {
            display: none !important;
        }
        
        .user-search-box-item.mb-4 {
            display: none !important;
        }
        
        [class*="user-search-box"] {
            display: none !important;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .discovery-container {
                padding: 0.5rem;
            }
            
            .content-grid,
            .users-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                padding: 0 0.5rem;
            }
            
            .section-header,
            .toggle-header {
                padding: 0 0.5rem;
            }
            
            .category-tags {
                padding: 0 0.5rem;
            }
            
            .search-header {
                padding: 0 0.5rem;
            }
        }
        
        /* Hide the old tab navigation */
        .inline-border-tabs {
            display: none !important;
        }
    </style>
@stop

@section('content')
    <div class="discovery-container">
        {{-- Search Header --}}
        <div class="search-header">
            <button class="back-button" onclick="window.history.back()">
                @include('elements.icon',['icon'=>'arrow-back-outline','variant'=>'medium','centered'=>true])
            </button>
            <div class="search-input-container">
                <form method="GET" action="{{ route('search.get') }}">
                    <input 
                        type="text" 
                        name="query" 
                        class="search-input" 
                        placeholder="{{ __('Search creators, content...') }}" 
                        value="{{ $searchTerm }}"
                        autocomplete="off"
                    >
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                </form>
            </div>
        </div>

        {{-- Category Tags (like Fansly) - FIXED ALIGNMENT --}}
        <div style="padding-left: 1rem; margin-bottom: 2rem; display: flex; gap: 0.75rem; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none;">
            <a href="{{ route('search.get', array_filter(['query' => $searchTerm])) }}" 
               class="category-tag {{ !$activeCategory ? 'active' : '' }}">
                {{ __('All') }}
            </a>
            @foreach($categoryTags as $key => $label)
                <a href="{{ route('search.get', array_filter(['query' => $searchTerm, 'category' => $key])) }}" 
                   class="category-tag {{ $activeCategory === $key ? 'active' : '' }}">
                    #{{ $label }}
                </a>
            @endforeach
        </div>

        @include('elements.message-alert',['classes'=>'p-2'])

        {{-- Made For You Section - FIXED: Removed template code --}}
        @if(isset($madeForYouPosts) && $madeForYouPosts->count() > 0)
            <div class="discovery-section">
                <div class="section-header">
                    <h2 class="section-title">{{ __('Made For You') }}</h2>
                </div>
                <div class="content-grid">
                    @foreach($madeForYouPosts->take(6) as $post)
                        <div class="content-card" onclick="window.location.href='{{ route('posts.get', ['post_id' => $post->id, 'username' => $post->user->username]) }}'">
                            @if($post->attachments->first())
                                @if(in_array($post->attachments->first()->type, ['mp4', 'mov', 'avi']))
                                    <video class="content-image" poster="{{ $post->attachments->first()->path }}">
                                        <source src="{{ $post->attachments->first()->path }}" type="video/mp4">
                                    </video>
                                @else
                                    <img src="{{ $post->attachments->first()->path }}" alt="{{ $post->text }}" class="content-image">
                                @endif
                            @endif
                            <div class="content-overlay">
                                <div class="content-title">{{ Str::limit($post->text ?: 'Untitled', 60) }}</div>
                                <div class="content-meta">
                                    <div class="content-stats">
                                        <span>❤️ {{ $post->reactions_count ?? 0 }}</span>
                                        <span>💬 {{ $post->comments_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Toggle Section with Dynamic Content --}}
        <div class="featured-section">
            {{-- Toggle Buttons --}}
            <div style="padding-left: 1rem; margin-bottom: 1.5rem;">
                <div style="display: inline-flex; background: #f0f0f0; border-radius: 25px; padding: 0.25rem;">
                    <button class="toggle-btn active" data-type="accounts" style="background: var(--primary-color); color: black; border: none; padding: 0.75rem 1.5rem; border-radius: 20px; font-size: 1.1rem; font-weight: 600; cursor: pointer; margin-right: 0.25rem;">
                        {{ __('Accounts') }}
                    </button>
                    <button class="toggle-btn" data-type="streams" style="background: transparent; color: black; border: none; padding: 0.75rem 1.5rem; border-radius: 20px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
                        {{ __('Live Streams') }}
                    </button>
                </div>
            </div>
            
            {{-- Accounts Content: "Who To Follow" - FIXED: Removed username template code --}}
            <div id="accounts-content" class="toggle-content">
                <div class="section-header">
                    <h2 class="section-title">{{ __('Who To Follow') }}</h2>
                </div>
                @if(isset($randomUsers) && $randomUsers->count() > 0)
                    <div class="users-grid">
                        @foreach($randomUsers->take(6) as $user)
                            <div class="user-card">
                                <img src="{{ $user->avatar ?? '/img/no-avatar.png' }}" alt="{{ $user->name }}" class="user-avatar">
                                <div class="user-name">{{ $user->name }}</div>
                                <button class="follow-button" onclick="window.location.href='{{ route('profile', ['username' => $user->username]) }}'">
                                    {{ __('View Profile') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            {{-- Live Streams Content: "Streams You Might Like" --}}
            <div id="streams-content" class="toggle-content" style="display: none;">
                <div class="section-header">
                    <h2 class="section-title">{{ __('Streams You Might Like') }}</h2>
                </div>
                @if(isset($liveStreams) && $liveStreams->count() > 0)
                    <div class="content-grid">
                        @foreach($liveStreams->take(6) as $stream)
                            <div class="content-card" onclick="window.location.href='{{ route('public.stream.get', ['streamID' => $stream->id, 'slug' => $stream->slug]) }}'">
                                <div class="live-indicator">{{ __('LIVE') }}</div>
                                @if($stream->poster)
                                    <img src="{{ $stream->poster }}" alt="{{ $stream->name }}" class="content-image">
                                @else
                                    <div class="content-image" style="background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);"></div>
                                @endif
                                <div class="content-overlay">
                                    <div class="content-title">{{ $stream->name }}</div>
                                    <div class="content-meta">
                                        <div class="content-stats">
                                            <span>👥 {{ __('Live') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        {{ __('No live streams available') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Trending Videos Section - FIXED: Removed template code --}}
        @if(isset($trendingVideos) && $trendingVideos->count() > 0)
            <div class="discovery-section">
                <div class="section-header">
                    <h2 class="section-title">{{ __('Trending Videos') }}</h2>
                    <a href="#" class="view-all-link">{{ __('View All') }}</a>
                </div>
                <div class="content-grid">
                    @foreach($trendingVideos->take(6) as $video)
                        <div class="content-card" onclick="window.location.href='{{ route('posts.get', ['post_id' => $video->id, 'username' => $video->user->username]) }}'">
                            @if($video->attachments->first())
                                <img src="{{ $video->attachments->first()->path }}" alt="{{ $video->text }}" class="content-image">
                                <div class="content-overlay">
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.8;">
                                        @include('elements.icon',['icon'=>'play-circle','variant'=>'large','centered'=>true])
                                    </div>
                                </div>
                            @endif
                            <div class="content-overlay">
                                <div class="content-title">{{ Str::limit($video->text ?: 'Video', 60) }}</div>
                                <div class="content-meta">
                                    <div class="content-stats">
                                        <span>❤️ {{ $video->reactions_count ?? 0 }}</span>
                                        <span>💬 {{ $video->comments_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Include checkout modal --}}
    @include('elements.checkout.checkout-box')

    {{-- JavaScript for toggle functionality --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle between Accounts and Live Streams
            const toggleBtns = document.querySelectorAll('.toggle-btn');
            const accountsContent = document.getElementById('accounts-content');
            const streamsContent = document.getElementById('streams-content');

            if (toggleBtns.length > 0 && accountsContent && streamsContent) {
                toggleBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const type = this.getAttribute('data-type');
                        
                        // Update active state
                        toggleBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Show/hide content
                        if (type === 'accounts') {
                            accountsContent.style.display = 'block';
                            streamsContent.style.display = 'none';
                        } else {
                            accountsContent.style.display = 'none';
                            streamsContent.style.display = 'block';
                        }
                    });
                });
            }

            // Auto-submit search form on input
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                let searchTimeout;
                
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.form.submit();
                    }, 500); // 500ms delay
                });
            }
        });
    </script>
@stop