<div class="slot-photo-card" style="background: #232e39; border-radius: 18px; padding: 12px 8px; text-align:center; height: 260px; display: flex; flex-direction: column; justify-content: flex-start;">
    @php
        $img = $post->attachments->first();
    @endphp
    @if($img)
        <img src="{{ asset('storage/posts/images/' . $img->filename) }}" 
             alt="image"
             style="max-width: 100%; max-height: 170px; border-radius:10px; margin-bottom: 12px; object-fit:cover; background: #111;">
    @else
        <div style="height:170px; background:#222; border-radius:10px; margin-bottom: 12px;"></div>
    @endif

    <div class="small" style="opacity:0.8; font-weight:bold;">
        @if($post->user)
            @ {{ $post->user->username }}
        @else
            @unknown
        @endif
    </div>
</div>