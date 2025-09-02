@extends('layouts.user-no-nav')
@section('page_title', __('Collections'))

@section('styles')
    {!!
        Minify::stylesheet([
            '/libs/swiper/swiper-bundle.min.css',
            '/libs/photoswipe/dist/photoswipe.css',
            '/libs/photoswipe/dist/default-skin/default-skin.css',
            '/css/pages/bookmarks.css',
            '/css/posts/post.css',
            '/css/pages/checkout.css',
            '/css/pages/lists.css'
         ])->withFullUrl()
    !!}
    @if(getSetting('feed.post_box_max_height'))
        @include('elements.feed.fixed-height-feed-posts', ['height' => getSetting('feed.post_box_max_height')])
    @endif
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/js/pages/checkout.js',
            '/js/PostsPaginator.js',
            '/js/CommentsPaginator.js',
            '/js/Post.js',
            '/js/pages/lists.js',
            '/js/pages/bookmarks.js',
            '/js/pages/collections.js',
            '/libs/swiper/swiper-bundle.min.js',
            '/js/plugins/media/photoswipe.js',
            '/libs/photoswipe/dist/photoswipe-ui-default.min.js',
            '/js/plugins/media/mediaswipe.js',
            '/js/plugins/media/mediaswipe-loader.js',
            '/libs/autolinker/dist/autolinker.min.js'
         ])->withFullUrl()
    !!}
@stop

@section('content')
    <div class="row no-gutters">
        {{-- Left Column - Navigation/Sidebar --}}
        <div class="col-12 col-md-6 col-lg-4 border-right">
            {{-- Collections Header --}}
            <div class="pt-4 pl-4 px-3 d-flex justify-content-between pb-3 border-bottom">
                <h5 class="mb-0 text-truncate text-bold {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">
                    {{__('Collections')}}
                </h5>
                <div class="mr-2">
                    {{-- Search button --}}
                    <button class="btn btn-outline-primary btn-sm" onclick="Collections.showSearchDialog()">
                        @include('elements.icon',['icon'=>'search-outline'])
                    </button>
                </div>
            </div>

            {{-- Main Tabs Navigation --}}
            <div class="collections-tabs border-bottom">
                <nav class="nav nav-pills nav-justified text-bold">
                    <a class="nav-item nav-link collections-tab {{$activeTab == 'bookmarks' ? 'active' : ''}}" 
                       href="{{route('my.collections.index', ['tab' => 'bookmarks'])}}" 
                       data-tab="bookmarks">
                        <div class="d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'bookmarks-outline','centered'=>'false','variant'=>'medium'])
                            <span class="ml-2 d-none d-sm-inline">{{__('Bookmarks')}}</span>
                        </div>
                    </a>
                    <a class="nav-item nav-link collections-tab {{$activeTab == 'lists' ? 'active' : ''}}" 
                       href="{{route('my.collections.index', ['tab' => 'lists'])}}" 
                       data-tab="lists">
                        <div class="d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'list-outline','centered'=>'false','variant'=>'medium'])
                            <span class="ml-2 d-none d-sm-inline">{{__('Lists')}}</span>
                        </div>
                    </a>
                </nav>
            </div>

            {{-- Sidebar Content - Only filters and lists for navigation --}}
            <div class="collections-sidebar">
                @if($activeTab == 'bookmarks')
                    {{-- Bookmark Type Filters (Left Sidebar) - Inline Collections Menu --}}
                    <div class="card-settings border-bottom">
                        <div class="list-group list-group-sm list-group-flush">
                            @foreach($bookmarkTypes as $route => $setting)
                                <a href="{{route('my.collections.index', ['tab' => 'bookmarks', 'type' => $route])}}" class="{{($activeBookmarkType ?? 'all') == $route ? 'active' : ''}} list-group-item list-group-item-action d-flex justify-content-between">
                                    <div class="d-flex align-items-center">
                                        @include('elements.icon',['icon'=>$setting['icon'].'-outline','centered'=>'false','classes'=>'mr-3','variant'=>'medium'])
                                        <span>{{__(ucfirst($route))}}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @include('elements.icon',['icon'=>'chevron-forward-outline'])
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @elseif($activeTab == 'lists')
                    {{-- Lists Navigation (Left Sidebar) --}}
                    <div class="lists-sidebar">
                        {{-- Create New List Button --}}
                        <div class="p-3 border-bottom">
                            <button class="btn btn-primary btn-block btn-sm" onclick="Lists.showListEditDialog('create')">
                                @include('elements.icon',['icon'=>'add-outline','centered'=>'false'])
                                <span class="ml-2">{{__('Create New List')}}</span>
                            </button>
                        </div>

                        {{-- User Lists Navigation --}}
                        <div class="lists-navigation">
                            @if(isset($lists) && count($lists))
                                @foreach($lists as $list)
                                    @if(isset($list->id))
                                    <div class="list-item border-bottom p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="list-info">
                                                <a href="{{route('my.collections.index', ['tab' => 'lists', 'list_id' => $list->id])}}" class="list-name text-decoration-none">
                                                    <h6 class="mb-1 text-bold">{{$list->name}}</h6>
                                                </a>
                                                <div class="list-meta text-muted small">
                                                    <span>{{count($list->members)}} {{__('members')}}</span>
                                                    @if(isset($list->posts_count))
                                                        <span class="ml-2">{{$list->posts_count}} {{__('posts')}}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="list-actions">
                                                <a href="{{route('my.collections.index', ['tab' => 'lists', 'list_id' => $list->id])}}" class="btn btn-outline-primary btn-sm">
                                                    {{__('View')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="empty-state text-center p-4">
                                    @include('elements.icon',['icon'=>'list-outline','classes'=>'mb-3 text-muted','variant'=>'xlarge'])
                                    <h6 class="text-muted">{{__('No lists yet')}}</h6>
                                    <p class="text-muted small">{{__('Create lists to organize your content')}}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column - MAIN CONTENT AREA --}}
        <div class="col-12 col-md-6 col-lg-8">
            <div class="collections-main-content">
                @if($activeTab == 'bookmarks')
                    {{-- BOOKMARKS MAIN CONTENT (Right Column) --}}
                    <div class="bookmarks-main-content p-3">
                        @if(isset($posts) && count($posts))
                            <div class="posts-wrapper" id="posts-wrapper">
                                @foreach($posts as $post)
                                    @include('elements.feed.post-box', ['post' => $post])
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state text-center p-4">
                                @include('elements.icon',['icon'=>'bookmarks-outline','classes'=>'mb-3 text-muted','variant'=>'xlarge'])
                                <h6 class="text-muted">{{__('No bookmarks yet')}}</h6>
                                <p class="text-muted small">{{__('Save posts to see them here')}}</p>
                            </div>
                        @endif
                    </div>
                @elseif($activeTab == 'lists')
                    {{-- LISTS MAIN CONTENT (Right Column) --}}
                    <div class="lists-main-content p-3">
                        <div class="collections-preview-area">
                            <div class="empty-preview text-center p-5">
                                @include('elements.icon',['icon'=>'albums-outline','classes'=>'mb-3 text-muted','variant'=>'xlarge'])
                                <h5 class="text-muted">{{__('Select a List')}}</h5>
                                <p class="text-muted">{{__('Click on a list from the sidebar to view its contents')}}</p>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Default empty state --}}
                    <div class="collections-preview-area">
                        <div class="empty-preview text-center p-5">
                            @include('elements.icon',['icon'=>'albums-outline','classes'=>'mb-3 text-muted','variant'=>'xlarge'])
                            <h5 class="text-muted">{{__('Your Collections')}}</h5>
                            <p class="text-muted">{{__('Manage your bookmarks and lists in one place')}}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Search Modal --}}
    <div class="modal fade" id="collections-search-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Search Collections')}}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" id="collections-search-input" placeholder="{{__('Search in bookmarks and lists...')}}">
                    </div>
                    <div id="collections-search-results"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include existing modals for lists functionality --}}
    {{-- @include('template.list-edit-dialog') --}}
@stop