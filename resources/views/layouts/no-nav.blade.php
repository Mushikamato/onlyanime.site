<!doctype html>
<html class="h-100" dir="{{GenericHelper::getSiteDirection()}}" lang="{{session('locale')}}">
<head>
    @include('template.head')
</head>
<body class="d-flex flex-column">
@include('elements.impersonation-header')
@include('elements.global-announcement')
<div class="flex-fill">
    @yield('content')
</div>
@if(getSetting('compliance.enable_age_verification_dialog'))
    @include('elements.site-entry-approval-box')
@endif
@include('template.footer-compact',['compact'=>true])
@include('template.jsVars')
@include('template.jsAssets')
@include('elements.language-selector-box')

{{-- REPLACE THE OLD SCRIPT WITH THE NEW ENHANCED ONE --}}
<script>
// Enhanced Universal Clickable Avatars Script
document.addEventListener('DOMContentLoaded', function() {
    function makeAvatarsClickable() {
        const avatarSelectors = [
            '.avatar:not(.clickable-avatar)',
            '.user-avatar:not(.clickable-avatar)', 
            '.rounded-circle:not(.clickable-avatar)',
            'img[src*="avatar"]:not(.clickable-avatar)',
            '.avatar-wrapper img:not(.clickable-avatar)',
            '.post-header img:not(.clickable-avatar)',
            // NEW: Add selectors for small profile images
            '.profile-image:not(.clickable-avatar)',
            '.user-image:not(.clickable-avatar)',
            '.suggestion-avatar:not(.clickable-avatar)',
            '.user-card img:not(.clickable-avatar)',
            '.suggestions img:not(.clickable-avatar)',
            // Catch any small circular images that might be avatars
            'img[style*="border-radius"]:not(.clickable-avatar)',
            'img[class*="circle"]:not(.clickable-avatar)'
        ];
        
        // ... (rest of the enhanced script)
    }
    
    makeAvatarsClickable();
    
    // ... (rest of the script)
});
</script>

</body>
</html>
