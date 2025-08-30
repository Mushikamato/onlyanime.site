@extends('layouts.user-no-nav')
@section('page_title', __('Your feed'))

{{-- Page specific CSS --}}
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
            '/libs/dropzone/dist/dropzone.css',
            '/css/quick-post-composer.css',
         ])->withFullUrl()
    !!}
    @if(getSetting('feed.post_box_max_height'))
        @include('elements.feed.fixed-height-feed-posts', ['height' => getSetting('feed.post_box_max_height')])
    @endif
@stop

{{-- Page specific JS --}}
@section('scripts')
    {!!
        Minify::javascript([
            '/js/PostsPaginator.js',
            '/js/CommentsPaginator.js',
            '/js/Post.js',
            '/js/posts/create-helper.js',
            '/js/suggestions.js',
            '/libs/dropzone/dist/dropzone.js',
            '/js/FileUpload.js',
            '/js/QuickPostComposer.js',
            '/js/SuggestionsSlider.js',
            '/js/pages/lists.js',
            '/js/pages/feed.js',
            '/js/pages/checkout.js',
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
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-8 col-md-7 second p-0">
         <div class="d-flex d-md-none px-3 py-3 feed-mobile-search neutral-bg fixed-top-m search-and-filters">
         {{-- Mobile search input --}}
         @include('elements.search-box') 
        {{-- Filter buttons for Cosplay and Anime (25% width each) --}}
        <div class="filter-buttons">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'cosplay']) }}" class="btn-filter">Cosplay</a>
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'anime']) }}" class="btn-filter">Anime</a>
        </div>
</div>

                <div class="m-pt-70"></div>

                {{-- @include('elements.user-stories-box')--}}

                <div class="">
                    @include('elements.message-alert',['classes'=>'pt-4 pb-4 px-2'])

                    {{-- ENHANCED Quick Post Composer - Uses SAME backend as normal post creation --}}
                    @if(Auth::check())
                        <div class="quick-post-composer-wrapper mb-4 px-3">
                            
                            {{-- Include ALL required templates (SAME as normal post) --}}
                            @include('elements.uploaded-file-preview-template')
                            @include('elements.post-price-setup',['postPrice'=>0])
                            @include('elements.post-poll-setup',['postPrice'=>0])
                            @include('elements.attachments-uploading-dialog')
                            @include('elements.messenger.locked-message-no-attachments-dialog',['type' => trans_choice('posts',2,['number' => ''])])
                            @include('elements.post-schedule-setup', [])

                            <div class="card quick-post-composer collapsed" id="quickPostComposer">
                                {{-- Collapsed State --}}
                                <div class="composer-collapsed" onclick="QuickPost.expand()">
                                    <div class="d-flex align-items-center p-3">
                                        <div class="composer-placeholder flex-grow-1">
                                            <span class="text-muted">{{__('Compose new post...')}}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Expanded State --}}
                                <div class="composer-expanded d-none">
                                    <div class="composer-header p-3 border-bottom">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0 font-weight-bold" style="cursor: pointer;" onclick="QuickPost.collapse()">{{__('Create Post')}}</h6>
                                            <button type="button" class="btn btn-link text-muted p-0" onclick="QuickPost.collapse()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- SAME pending posts warning as normal post --}}
                                    @if(!PostsHelper::getDefaultPostStatus(Auth::user()->id))
                                        <div class="pl-3 pr-3 pt-3">
                                            @include('elements.pending-posts-warning-box')
                                        </div>
                                    @endif

                                    {{-- SAME verification warning as normal post --}}
                                    @if(!GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks'))
                                        <div class="alert alert-warning text-white font-weight-bold mt-2 mb-0 mx-3" role="alert">
                                            {{__("Before being able to publish an item, you need to complete your")}} <a class="text-white" href="{{route('my.settings',['type'=>'verify'])}}">{{__("profile verification")}}</a>.
                                        </div>
                                    @endif

                                    <form id="quickPostForm" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="type" value="create">
                                        
                                        {{-- Text Content --}}
                                        <div class="composer-content p-3">
                                            <div class="flex-grow-1">
                                                <textarea 
                                                    id="quickPostText" 
                                                    name="text" 
                                                    class="form-control composer-textarea border-0" 
                                                    placeholder="{{__('What\'s on your mind?')}}"
                                                    rows="3"
                                                ></textarea>
                                            </div>

                                            {{-- Content Type Selection (SAME as normal post) --}}
                                            <div class="form-group mt-3 mb-3">
                                                <label for="quick_content_type" class="form-label small">{{__('Content Type')}} <span class="text-danger">*</span></label>
                                                <select name="content_type" id="quick_content_type" class="form-control form-control-sm {{ $errors->has('content_type') ? 'is-invalid' : '' }}" required>
                                                    <option value="">{{__('None')}}</option>
                                                    <option value="cosplay">{{__('Cosplay')}}</option>
                                                    <option value="anime">{{__('Anime')}}</option>
                                                </select>
                                                @error('content_type')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            {{-- Adult Content Checkbox (SAME as normal post) --}}
                                            <div class="form-group mb-3">
                                                <div class="form-check">
                                                    <input type="hidden" name="is_adult_content" value="0">
                                                    <input class="form-check-input" type="checkbox" name="is_adult_content" value="1" id="quick_is_adult_content">
                                                    <label class="form-check-label small" for="quick_is_adult_content">
                                                        {{__('18+ Content (Mark if content is for adults only)')}}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Action Buttons (FIXED layout for mobile) --}}
                                        <div class="px-3 mb-3">
                                            {{-- Post Create Actions (SAME as normal post) --}}
                                            <div class="mb-3">
                                                @include('elements.post-create-actions')
                                            </div>
                                            
                                            {{-- Bottom row with clear and save buttons --}}
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <a href="#" class="draft-clear-button text-muted">{{__('Clear draft')}}</a>
                                                </div>
                                                <div class="ml-3">
                                                    @if(!GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks'))
                                                        <button class="btn btn-primary disabled">{{__('Save')}}</button>
                                                    @else
                                                        <button class="btn btn-primary post-create-button" type="submit">{{__('Save')}}</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Simple File Upload (No Dropzone) --}}
                                        <div class="mb-3">
                                            <input type="file" id="quickPostFiles" class="d-none" multiple accept="image/*,video/*,audio/*">
                                            <div class="uploaded-files-preview"></div>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @include('elements.feed.posts-load-more')
                    <div class="feed-box mt-0 pt-4 posts-wrapper">
                        @include('elements.feed.posts-wrapper',['posts'=>$posts])
                    </div>
                    @include('elements.feed.posts-loading-spinner')
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-5 col-lg-4 first border-left order-0 pt-4 pb-5 min-vh-100 suggestions-wrapper d-none d-md-block">

                <div class="feed-widgets">
                    @if(!getSetting('feed.search_widget_hide'))
                        <div class="mb-3">
                            @include('elements.search-box')
                        </div>
                    @endif
                    @if(!getSetting('feed.hide_suggestions_slider'))
                        @include('elements.feed.suggestions-box',[
                             'id' => 'suggestions-box',
                             'profiles' => $suggestions,
                             'isMobile' => false,
                             'hideControls' => false,
                             'title' => __('Suggestions'),
                             'perPage' => (int)getSetting('feed.feed_suggestions_card_per_page'),
                        ])
                    @endif

                    @if(!getSetting('feed.expired_subs_widget_hide'))
                        @if($expiredSubscriptions->count())
                            <div class="mt-3">
                                @include('elements.feed.suggestions-box',[
                                    'id' => 'suggestions-box-expired',
                                    'profiles' => $expiredSubscriptions,
                                    'isMobile' => false,
                                    'hideControls' => true,
                                    'title' => __('Expired subscriptions'),
                                    'perPage' => (int)getSetting('feed.expired_subs_widget_card_per_page'),
                                ])
                            </div>
                        @endif
                    @endif

                    @if(getSetting('custom-code-ads.sidebar_ad_spot'))
                        <div class="mt-3">
                            {!! getSetting('custom-code-ads.sidebar_ad_spot') !!}
                        </div>
                    @endif

                    @include('template.footer-feed')

                </div>

            </div>
        </div>
        @include('elements.checkout.checkout-box')
    </div>

    <div class="d-none">
        <ion-icon name="heart"></ion-icon>
        <ion-icon name="heart-outline"></ion-icon>
    </div>

    {{-- Minimal JavaScript for debugging --}}
    <script>
        window.Post = {};
        console.log('Basic script loaded');
    </script>

    @include('elements.standard-dialog',[
        'dialogName' => 'comment-delete-dialog',
        'title' => __('Delete comment'),
        'content' => __('Are you sure you want to delete this comment?'),
        'actionLabel' => __('Delete'),
        'actionFunction' => 'Post.deleteComment();',
    ])

@stop