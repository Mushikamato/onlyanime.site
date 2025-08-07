<div class="slot-photo-card" style="background: #232e39; border-radius: 18px; padding: 12px 8px; text-align:center; height: 260px; display: flex; flex-direction: column; justify-content: flex-start;">
    @php
        $img = $post->attachments->first();
        // Гарантируем что filename не содержит поддиректорий
        $filename = $img ? $img->filename : null;
        $cleaned = $filename ? ltrim(str_replace('posts/images/', '', $filename), '/\\') : null;
    @endphp
    @if($cleaned)
        <img src="{{ asset('storage/posts/images/' . $cleaned) }}" 
             alt="image"
             style="max-width: 100%; max-height: 300px; object-fit:cover; background: #111;">
    @else
        <div style="height:3000px; background:#222;"></div>
    @endif

    <div class="small" style="opacity:0.8; font-weight:bold;">
        @if($post->user)
            @ {{ $post->user->username }}
        @else
            @unknown
        @endif
    </div>
</div>