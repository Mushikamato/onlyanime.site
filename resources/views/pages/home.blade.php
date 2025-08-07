@extends('layouts.generic')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css"/>
<style>
  .swiper-wrapper {
    transition-timing-function: linear !important;
  }
  .slot-container { width: 100%; margin: 10px auto; position: relative; padding: 0 10px; height: calc(100% - 20px); display: flex; flex-direction: column; }
  .slot-grid-container { display: flex; flex-direction: row; flex-wrap: wrap; justify-content: space-between; gap: 10px; width: 100%; flex-grow: 1; overflow: hidden; min-height: 0; }
  .slot-post-box-wrapper { height: 100%; display: none; flex-direction: column; align-items: stretch; justify-content: flex-start; overflow: hidden; transition: none !important; flex-grow: 1; flex-shrink: 1; box-sizing: border-box; }
  .slot-post-box-wrapper .swiper-wrapper { display: flex; flex-direction: column; height: 100%; }
  .slot-post-box-wrapper .swiper-slide { width: 100%; height: auto; min-height: 210px; display: flex; flex-direction: column; justify-content: flex-start; align-items: center; box-sizing: border-box; padding-bottom: 5px; }
  .slot-post-box-wrapper .swiper-slide img { width: 100%; height: 190px; object-fit: cover; border-radius: 12px; }
  .slot-post-box-wrapper.repeating-image-slot { flex-shrink: 0; flex-grow: 0; background-color: #1a1a1a; }
  
  /* --- CSS FIX FOR HOVER --- */
  .center-buttons-absolute {
    position: absolute;
    left: 50%;
    top: 50%;
    z-index: 9;
    transform: translate(-50%, -50%);
    display: flex;
    justify-content: center;
    gap: 500px;
    pointer-events: none;
  }
  .cosplay-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: #fff;
    border: 4px solid #55a8f9;
    font-size: 1.45rem;
    font-weight: bold;
    color: #3483d6;
    box-shadow: 0 4px 16px rgba(50,110,255,0.12);
    transition: box-shadow 0.25s;
    animation: cosplay-pulse 1.6s infinite alternate;
    cursor: pointer;
    text-decoration: none;
    margin: 0 12px;
    user-select: none;
    pointer-events: auto;
  }
  
  /* NEW: Active button styling */
  .cosplay-btn.active {
    background: #3483d6 !important;
    color: #fff !important;
    border-color: #fff !important;
    transform: scale(1.05);
  }
  /* --- END OF CSS FIX --- */

  @keyframes cosplay-pulse { 0% { transform: scale(1); box-shadow: 0 4px 16px rgba(50,110,255,0.12);} 50% { transform: scale(1.08); box-shadow: 0 8px 32px rgba(50,110,255,0.17);} 100% { transform: scale(1); box-shadow: 0 4px 16px rgba(50,110,255,0.12);} }
  .cosplay-btn:hover { color: #215ca8; background: #f4f9ff; box-shadow: 0 8px 32px rgba(50,110,255,0.22); }
  @media (min-width: 1101px) { .slot-post-box-wrapper { display: none; } .slot-post-box-wrapper:nth-child(-n+7) { display: flex; } .slot-post-box-wrapper.content-column { width: calc((100% - 15px - (6 * 10px)) / 6); min-width: 160px; max-width: 220px; } .slot-post-box-wrapper.repeating-image-slot { width: 15px !important; min-width: 15px !important; max-width: 15px !important; border: none !important; } }
  @media (min-width: 768px) and (max-width: 1100px) { .slot-post-box-wrapper { display: none; } .slot-post-box-wrapper:nth-child(-n+5) { display: flex; } .slot-post-box-wrapper.content-column { width: calc((100% - 15px - (4 * 10px)) / 4); min-width: 140px; max-width: 180px; } .slot-post-box-wrapper .swiper-slide img { height: 160px; } .slot-post-box-wrapper .swiper-slide { min-height: 175px; } .slot-post-box-wrapper.repeating-image-slot { width: 15px !important; min-width: 15px !important; max-width: 15px !important; border: none !important; } }
  @media (max-width: 767px) { .slot-grid-container { min-height: 300px; } .slot-post-box-wrapper { display: none; } .slot-post-box-wrapper:nth-child(-n+3) { display: flex; } .slot-post-box-wrapper.content-column { width: calc((100% - 5px - (2 * 10px)) / 2); min-width: 100px; max-width: 150px; } .slot-post-box-wrapper .swiper-slide img { height: 120px; } .slot-post-box-wrapper .swiper-slide { min-height: 135px; } .center-buttons-absolute { flex-direction: column; gap: 16px; } .center-slider-btn { width: 100%; } .slot-post-box-wrapper.repeating-image-slot { width: 5px !important; min-width: 5px !important; max-width: 5px !important; border: none !important; } }
  .post-link { display: contents; text-decoration: none; color: inherit; }
</style>
@stop

@section('content')
<div class="slot-container" style="position:relative;">
  <div class="center-buttons-absolute">
    <a href="#" class="cosplay-btn" data-category="cosplay">Cosplay</a>
    <a href="#" class="cosplay-btn" data-category="anime">Anime</a>
  </div>

  <div class="slot-grid-container">
    @for ($i = 0; $i < 7; $i++)
        <div class="slot-post-box-wrapper" data-col-index="{{$i}}">
        </div>
    @endfor
  </div>
</div>
@stop

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

@php
    // This block is now simplified. It trusts the controller to send only the posts
    // that the user is allowed to see. The filtering is done in HomeController.php.
    $jsCosplayPosts = [];
    if (isset($cosplayPosts)) {
        foreach ($cosplayPosts as $post) {
            $img = $post->attachments->first();
            if ($img) {
                $jsCosplayPosts[] = [
                    'id' => $post->id,
                    'username' => $post->user->username ?? 'no-user',
                    'filename' => asset('storage/posts/images/' . ltrim(str_replace('posts/images/', '', $img->filename), '/\\')),
                    'is_adult' => $post->is_adult_content
                ];
            }
        }
    }

    $jsAnimePosts = [];
    if (isset($animePosts)) {
        foreach ($animePosts as $post) {
            $img = $post->attachments->first();
            if ($img) {
                $jsAnimePosts[] = [
                    'id' => $post->id,
                    'username' => $post->user->username ?? 'no-user',
                    'filename' => asset('storage/posts/images/' . ltrim(str_replace('posts/images/', '', $img->filename), '/\\')),
                    'is_adult' => $post->is_adult_content
                ];
            }
        }
    }
@endphp

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const cosplayData = @json($jsCosplayPosts);
    const animeData = @json($jsAnimePosts);
    const IS_LOGGED_IN = @json(auth()->check());

    const allColumnElements = document.querySelectorAll('.slot-post-box-wrapper');
    const categoryButtons = document.querySelectorAll('.cosplay-btn');
    
    // NEW: Add state for current selected category
    let currentCategory = 'mixed'; // 'mixed', 'cosplay', 'anime'

    function createSlideHtml(item) {
        const postUrl = `/posts/${item.id}/${item.username}`;
        const loginUrl = '{{ url('/login') }}';
        const finalUrl = IS_LOGGED_IN ? postUrl : loginUrl;
        return `
            <div class="swiper-slide">
                <a href="${finalUrl}" class="post-link">
                    <img src="${item.filename}" alt="photo" data-is-adult="${item.is_adult}">
                    <div class="mt-2" style="text-align:center; color:#aaa; font-size:15px;">@${item.username}</div>
                </a>
            </div>`;
    }

    function setupLayout() {
        const viewportWidth = window.innerWidth;
        let layoutConfig;
        if (viewportWidth >= 1101) { layoutConfig = { cosplay: 3, divider: 1, anime: 3 }; }
        else if (viewportWidth >= 768) { layoutConfig = { cosplay: 2, divider: 1, anime: 2 }; }
        else { layoutConfig = { cosplay: 1, divider: 1, anime: 1 }; }

        let visibleContentColumn = 0;

        allColumnElements.forEach((el, index) => {
            el.style.display = 'none';

            const dividerStartIndex = layoutConfig.cosplay;
            const animeStartIndex = layoutConfig.cosplay + layoutConfig.divider;
            const totalVisibleColumns = layoutConfig.cosplay + layoutConfig.divider + layoutConfig.anime;

            if (index >= totalVisibleColumns) return;

            el.style.display = 'flex';
            el.classList.remove('swiper', 'content-column', 'repeating-image-slot');
            if (el.swiper) {
                el.swiper.destroy(true, true);
                el.swiper = null;
            }

            if (index === dividerStartIndex) {
                el.classList.add('repeating-image-slot');
                el.innerHTML = '';
            }
            else {
                el.classList.add('swiper', 'content-column');
                el.innerHTML = '<div class="swiper-wrapper"></div>';
                
                // MODIFIED: New data selection logic based on current category
                let postData;
                if (currentCategory === 'cosplay') {
                    // ALL slots use cosplay data
                    postData = cosplayData;
                } else if (currentCategory === 'anime') {
                    // ALL slots use anime data
                    postData = animeData;
                } else {
                    // Mixed mode (original logic)
                    postData = (index < dividerStartIndex) ? cosplayData : animeData;
                }

                if (postData && postData.length > 0) {
                    let finalData = [...postData];
                    
                    while (finalData.length > 0 && finalData.length < 10) {
                        finalData.push(...finalData);
                    }
                    
                    let slidesHtml = '';
                    finalData.forEach(item => slidesHtml += createSlideHtml(item));
                    el.querySelector('.swiper-wrapper').innerHTML = slidesHtml;

                    const swiper = new Swiper(el, {
                        direction: 'vertical',
                        slidesPerView: 'auto',
                        loop: true,
                        allowTouchMove: false,
                        initialSlide: Math.floor(Math.random() * finalData.length),
                        autoplay: {
                            delay: 0,
                            disableOnInteraction: false,
                            reverseDirection: visibleContentColumn % 2 !== 0,
                        },
                        speed: 15000,
                    });
                    
                    el.addEventListener('mouseenter', () => swiper.autoplay.stop());
                    el.addEventListener('mouseleave', () => swiper.autoplay.start());
                    visibleContentColumn++;
                } else {
                    el.innerHTML = '<div style="display:flex;justify-content:center;align-items:center;height:100%;color:#555;">No Content</div>';
                }
            }
        });
    }

    // NEW: Add click handlers to category buttons
    categoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get category from data attribute
            const selectedCategory = this.dataset.category;
            
            // Update current category
            currentCategory = selectedCategory;
            
            // Update button visual states
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Rebuild layout with new category
            setupLayout();
        });
    });

    setupLayout();
    window.addEventListener('resize', setupLayout);
  });
</script>
@stop
