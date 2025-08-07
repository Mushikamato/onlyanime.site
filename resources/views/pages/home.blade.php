@extends('layouts.user-no-nav')

@section('page_title', __('Home'))

@section('styles')
<style>
  .slot-container { min-height: 100vh; padding: 16px; background: linear-gradient(45deg, #f3f7ff 0%, #e3efff 100%); }
  .slot-grid-container { display: flex; flex-wrap: nowrap; gap: 10px; min-height: 500px; justify-content: center; position: relative; }
  .slot-post-box-wrapper { border-radius: 8px; overflow: hidden; background: rgba(255, 255, 255, 0.95); border: 1px solid rgba(85, 168, 249, 0.1); box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
  .slot-post-box-wrapper.swiper { height: 100%; }
  .slot-post-box-wrapper .swiper-slide { border-radius: 6px; overflow: hidden; margin-bottom: 8px; }
  .slot-post-box-wrapper .swiper-slide img { width: 100%; border-radius: 6px; object-fit: cover; transition: transform 0.25s ease; }
  .slot-post-box-wrapper .swiper-slide:hover img { transform: scale(1.05); }
  .slot-post-box-wrapper.repeating-image-slot { background: linear-gradient(to bottom, #55a8f9, #3483d6); opacity: 0.6; border: none; }
  .center-buttons-absolute {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 20;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 24px;
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
    transition: all 0.25s;
    animation: cosplay-pulse 1.6s infinite alternate;
    cursor: pointer;
    text-decoration: none;
    margin: 0 12px;
    user-select: none;
    pointer-events: auto;
  }
  
  /* Active state for selected category */
  .cosplay-btn.active {
    background: #3483d6;
    color: #fff;
    border-color: #fff;
    transform: scale(1.1);
  }
  
  @keyframes cosplay-pulse { 
    0% { transform: scale(1); box-shadow: 0 4px 16px rgba(50,110,255,0.12);} 
    50% { transform: scale(1.08); box-shadow: 0 8px 32px rgba(50,110,255,0.17);} 
    100% { transform: scale(1); box-shadow: 0 4px 16px rgba(50,110,255,0.12);} 
  }
  
  .cosplay-btn:hover { 
    color: #215ca8; 
    background: #f4f9ff; 
    box-shadow: 0 8px 32px rgba(50,110,255,0.22); 
  }
  
  .cosplay-btn.active:hover {
    background: #2d6bc4;
    color: #fff;
  }
  
  @media (min-width: 1101px) { 
    .slot-post-box-wrapper { display: none; } 
    .slot-post-box-wrapper:nth-child(-n+7) { display: flex; } 
    .slot-post-box-wrapper.content-column { width: calc((100% - 15px - (6 * 10px)) / 6); min-width: 160px; max-width: 220px; } 
    .slot-post-box-wrapper .swiper-slide img { height: 160px; } 
    .slot-post-box-wrapper .swiper-slide { min-height: 175px; } 
    .slot-post-box-wrapper.repeating-image-slot { width: 15px !important; min-width: 15px !important; max-width: 15px !important; border: none !important; } 
  }
  
  @media (min-width: 768px) and (max-width: 1100px) { 
    .slot-post-box-wrapper { display: none; } 
    .slot-post-box-wrapper:nth-child(-n+5) { display: flex; } 
    .slot-post-box-wrapper.content-column { width: calc((100% - 15px - (4 * 10px)) / 4); min-width: 140px; max-width: 180px; } 
    .slot-post-box-wrapper .swiper-slide img { height: 160px; } 
    .slot-post-box-wrapper .swiper-slide { min-height: 175px; } 
    .slot-post-box-wrapper.repeating-image-slot { width: 15px !important; min-width: 15px !important; max-width: 15px !important; border: none !important; } 
  }
  
  @media (max-width: 767px) { 
    .slot-grid-container { min-height: 300px; } 
    .slot-post-box-wrapper { display: none; } 
    .slot-post-box-wrapper:nth-child(-n+3) { display: flex; } 
    .slot-post-box-wrapper.content-column { width: calc((100% - 5px - (2 * 10px)) / 2); min-width: 100px; max-width: 150px; } 
    .slot-post-box-wrapper .swiper-slide img { height: 120px; } 
    .slot-post-box-wrapper .swiper-slide { min-height: 135px; } 
    .center-buttons-absolute { flex-direction: column; gap: 16px; } 
    .center-slider-btn { width: 100%; } 
    .slot-post-box-wrapper.repeating-image-slot { width: 5px !important; min-width: 5px !important; max-width: 5px !important; border: none !important; } 
  }
  
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
    
    // State for current selected category
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
                
                // *** MODIFIED LOGIC - Choose data based on current category ***
                let postData;
                if (currentCategory === 'cosplay') {
                    postData = cosplayData; // All columns use cosplay data
                } else if (currentCategory === 'anime') {
                    postData = animeData; // All columns use anime data
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

    // *** NEW: Category button click handlers ***
    categoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
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

    // Initial setup
    setupLayout();
    window.addEventListener('resize', setupLayout);
  });
</script>
@stop
