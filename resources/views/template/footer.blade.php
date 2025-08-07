{{-- resources/views/partials/footer.blade.php --}}
<footer class="site-footer">
  <div class="footer-content">
    {{-- Левый блок: копирайт --}}
    <div class="footer-left">
      &copy; {{ date('Y') }} {{ getSetting('site.name') }}. {{ __('All rights reserved.') }}
    </div>

    {{-- Правый блок: соцсети + переключатели --}}
    <div class="footer-right">
      {{-- Социальные иконки --}}
      <div class="footer-social-links">
        @if(getSetting('social.facebook_url'))
          <a href="{{ getSetting('social.facebook_url') }}" target="_blank" rel="noopener">
            @include('elements.icon', [
              'icon'    => 'logo-facebook',
              'variant' => 'medium',
              'classes' => 'opacity-8'
            ])
          </a>
        @endif

        @if(getSetting('social.twitter_url'))
          <a href="{{ getSetting('social.twitter_url') }}" target="_blank" rel="noopener">
            @include('elements.icon', [
              'icon'    => 'x-logo',
              'variant' => 'medium',
              'classes' => 'opacity-8'
            ])
          </a>
        @endif

        @if(getSetting('social.instagram_url'))
          <a href="{{ getSetting('social.instagram_url') }}" target="_blank" rel="noopener">
            @include('elements.icon', [
              'icon'    => 'logo-instagram',
              'variant' => 'medium',
              'classes' => 'opacity-8'
            ])
          </a>
        @endif

        @if(getSetting('social.youtube_url'))
          <a href="{{ getSetting('social.youtube_url') }}" target="_blank" rel="noopener">
            @include('elements.icon', [
              'icon'    => 'logo-youtube',
              'variant' => 'medium',
              'classes' => 'opacity-8'
            ])
          </a>
        @endif
      </div>

      {{-- Переключатели темы, направления и языка --}}
      <div class="footer-switchers">
        @include('elements.footer.dark-mode-switcher')
        @include('elements.footer.direction-switcher')
      </div>
    </div>
  </div>
</footer>