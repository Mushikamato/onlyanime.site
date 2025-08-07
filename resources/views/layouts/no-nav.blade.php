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

{{-- ADD THIS SCRIPT HERE --}}
<script>
// Universal Clickable Avatars Script
document.addEventListener('DOMContentLoaded', function() {
    function makeAvatarsClickable() {
        const avatarSelectors = [
            '.avatar:not(.clickable-avatar)',
            '.user-avatar:not(.clickable-avatar)', 
            '.rounded-circle:not(.clickable-avatar)',
            'img[src*="avatar"]:not(.clickable-avatar)',
            '.avatar-wrapper img:not(.clickable-avatar)',
            '.post-header img:not(.clickable-avatar)'
        ];
        
        avatarSelectors.forEach(selector => {
            const avatars = document.querySelectorAll(selector);
            
            avatars.forEach(avatar => {
                if (avatar.classList.contains('clickable-avatar') || 
                    avatar.closest('.side-menu')) {
                    return;
                }
                
                let username = null;
                
                const postBox = avatar.closest('.post-box, .post, .user-card');
                if (postBox) {
                    const profileLink = postBox.querySelector('a[href*="/profile/"]');
                    if (profileLink) {
                        const href = profileLink.getAttribute('href');
                        username = href.split('/profile/')[1];
                    }
                }
                
                if (username && username.trim()) {
                    avatar.classList.add('clickable-avatar');
                    avatar.style.cursor = 'pointer';
                    avatar.style.transition = 'transform 0.2s ease';
                    
                    avatar.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.location.href = `/profile/${username}`;
                    });
                    
                    avatar.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.05)';
                    });
                    
                    avatar.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                    });
                }
            });
        });
    }
    
    makeAvatarsClickable();
    
    const observer = new MutationObserver(function(mutations) {
        let shouldReprocess = false;
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1 && (
                    node.querySelector('.avatar') ||
                    node.classList.contains('post-box')
                )) {
                    shouldReprocess = true;
                }
            });
        });
        if (shouldReprocess) {
            setTimeout(makeAvatarsClickable, 100);
        }
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
</script>

</body>
</html>
