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

{{-- Targeted Clickable Profile Pictures Script --}}
<script>
// Targeted Clickable Profile Pictures Script
// Specifically targets the small circular profile images in posts
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Initializing targeted clickable profile pictures...');
    
    function makeProfilePicturesClickable() {
        // Target the specific small profile images in posts
        const profileImageSelectors = [
            '.post-box .rounded-circle:not(.clickable-avatar)',
            '.post .rounded-circle:not(.clickable-avatar)', 
            '.post-header .rounded-circle:not(.clickable-avatar)',
            '.post-box .avatar:not(.clickable-avatar)',
            '.post .avatar:not(.clickable-avatar)',
            '.post-header .avatar:not(.clickable-avatar)',
            // Target any small circular images in post areas
            '.post-box img[style*="border-radius"]:not(.clickable-avatar)',
            '.post img[style*="border-radius"]:not(.clickable-avatar)',
            // Target user avatar images specifically
            '.post-box .user-avatar:not(.clickable-avatar)',
            '.post .user-avatar:not(.clickable-avatar)'
        ];
        
        profileImageSelectors.forEach(selector => {
            const profileImages = document.querySelectorAll(selector);
            
            profileImages.forEach(image => {
                // Skip if already processed, or if it's in side menu/navbar
                if (image.classList.contains('clickable-avatar') || 
                    image.closest('.side-menu') ||
                    image.closest('.navbar') ||
                    image.closest('.dropdown')) {
                    return;
                }
                
                let username = null;
                
                // Method 1: Look for nearby profile link in the same post
                const postContainer = image.closest('.post-box, .post, .feed-item');
                if (postContainer) {
                    // Look for profile links like href="/profile/username"
                    const profileLink = postContainer.querySelector('a[href*="/profile/"]');
                    if (profileLink) {
                        const href = profileLink.getAttribute('href');
                        if (href.includes('/profile/')) {
                            username = href.split('/profile/')[1].split('?')[0]; // Remove query params
                        }
                    }
                }
                
                // Method 2: Look for @username text near the image
                if (!username && postContainer) {
                    const usernameElements = postContainer.querySelectorAll('*');
                    for (let element of usernameElements) {
                        const text = element.textContent.trim();
                        if (text.startsWith('@') && text.length > 1 && text.length < 20) {
                            username = text.substring(1).split(' ')[0]; // Remove @ and get first word
                            break;
                        }
                    }
                }
                
                // Method 3: Check for data attributes
                if (!username && postContainer) {
                    username = postContainer.getAttribute('data-username') || 
                             postContainer.getAttribute('data-user') ||
                             image.getAttribute('data-username') ||
                             image.getAttribute('data-user');
                }
                
                // If we found a username, make the image clickable
                if (username && username.trim()) {
                    username = username.trim();
                    
                    // Mark as processed
                    image.classList.add('clickable-avatar');
                    
                    // Add cursor pointer
                    image.style.cursor = 'pointer';
                    image.style.transition = 'transform 0.2s ease, filter 0.2s ease';
                    
                    // Add click handler
                    image.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Navigate to profile
                        const profileUrl = `/profile/${username}`;
                        window.location.href = profileUrl;
                        
                        console.log(`🎯 Clicked profile picture - navigating to: ${profileUrl}`);
                    });
                    
                    // Add hover effects
                    image.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.1)';
                        this.style.filter = 'brightness(0.9)';
                    });
                    
                    image.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                        this.style.filter = 'brightness(1)';
                    });
                    
                    console.log(`✅ Made profile picture clickable for user: ${username}`);
                }
            });
        });
    }
    
    // Initial run
    makeProfilePicturesClickable();
    
    // Re-run when new content is loaded (for infinite scroll, etc.)
    const observer = new MutationObserver(function(mutations) {
        let shouldReprocess = false;
        
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    // Check if new content contains posts or profile images
                    if (node.querySelector && (
                        node.querySelector('.post-box') ||
                        node.querySelector('.post') ||
                        node.querySelector('.rounded-circle') ||
                        node.querySelector('.avatar') ||
                        node.classList.contains('post-box') ||
                        node.classList.contains('post')
                    )) {
                        shouldReprocess = true;
                    }
                }
            });
        });
        
        if (shouldReprocess) {
            setTimeout(makeProfilePicturesClickable, 200); // Small delay to ensure DOM is ready
        }
    });
    
    // Observe the document for changes
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    console.log('✅ Targeted clickable profile pictures system initialized');
});
</script>

</body>
</html>
