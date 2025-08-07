<div class="swiper {{ $reelClass }}">
  <div class="swiper-wrapper">
    @foreach($reels[$reelClass] as $post)
      <div class="swiper-slide">
        <div class="hero-card">
          <img src="{{ asset('uploads/posts/' . $post->cover_image) }}" alt="{{ $post->title }}" class="hero-img">
          <div class="hero-info">
            <div class="hero-title">{{ Str::limit($post->title, 22) }}</div>
            <div class="hero-author">@{{ $post->user->username }}</div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  <div class="swiper-button-prev"></div>
  <div class="swiper-button-next"></div>
</div>