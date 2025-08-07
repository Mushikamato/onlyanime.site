@php
    // Get the full public URL from the stored filename
    $fileUrl = Storage::url($attachment->filename);
    $thumbnailUrl = $attachment->thumbnail ? Storage::url($attachment->thumbnail) : '';

    // Determine the type based on the file extension for reliability
    $fileExtension = strtolower(pathinfo($attachment->filename, PATHINFO_EXTENSION));
    $attachmentType = 'unknown';

    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    $videoExtensions = ['mp4', 'mov', 'webm', 'ogv', 'm3u8'];
    $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a'];

    if (in_array($fileExtension, $imageExtensions)) {
        $attachmentType = 'image';
    } elseif (in_array($fileExtension, $videoExtensions)) {
        $attachmentType = 'video';
    } elseif (in_array($fileExtension, $audioExtensions)) {
        $attachmentType = 'audio';
    }
@endphp

{{-- This outer link is for opening images in a lightbox --}}
@if($attachmentType === 'image')
    <a href="{{$fileUrl}}" rel="mswp" class="no-long-press">
@endif

    @if($isGallery)
        {{-- Gallery view (slider) --}}
        @if($attachmentType === 'image')
            <div class="post-media-image" style="background-image: url('{{$fileUrl}}');"></div>
        @elseif($attachmentType === 'video')
            <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                <video class="video-preview w-100" src="{{$fileUrl}}#t=0.001" controls controlsList="nodownload" preload="metadata" @if($thumbnailUrl) poster="{{$thumbnailUrl}}" @endif></video>
            </div>
        @elseif($attachmentType === 'audio')
            <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                <audio class="video-preview w-75" src="{{$fileUrl}}#t=0.001" controls controlsList="nodownload" preload="metadata"></audio>
            </div>
        @else
            <div class="p-3 text-center text-muted small"><span>{{__('Unsupported file type.')}}</span></div>
        @endif
    @else
        {{-- Single media item view --}}
        @if($attachmentType === 'image')
            <img src="{{$fileUrl}}" draggable="false" alt="" class="img-fluid rounded-0 w-100">
        @elseif($attachmentType === 'video')
            <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                <video class="video-preview w-100" src="{{$fileUrl}}#t=0.001" controls controlsList="nodownload" preload="metadata" @if($thumbnailUrl) poster="{{$thumbnailUrl}}" @endif></video>
            </div>
        @elseif($attachmentType === 'audio')
            <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                <audio class="video-preview w-75" src="{{$fileUrl}}#t=0.001" controls controlsList="nodownload" preload="metadata"></audio>
            </div>
        @else
            <div class="p-3 text-center text-muted small"><span>{{__('Unsupported file type.')}}</span></div>
        @endif
    @endif

@if($attachmentType === 'image')
    </a>
@endif