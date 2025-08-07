<!doctype html>
<html class="h-100" dir="{{GenericHelper::getSiteDirection()}}" lang="{{session('locale')}}">
<head>
    @include('template.head')
    <style>
        /* CRITICAL: Ensure html and body take full viewport height and body is a flex container */
        html {
            height: 100%; /* Ensures html element takes full viewport height */
        }
        body {
            height: 100%; /* Ensures body element takes full viewport height */
            min-height: 100vh; /* Fallback to ensure body is at least viewport height */
            margin: 0;
            padding: 0;
            display: flex; /* Makes body a flex container */
            flex-direction: column; /* Stacks header, content, footer vertically */
            /* Do NOT add overflow: hidden here, as it would clip the entire page. */
        }
        /* Ensure the .flex-fill div (which holds your @yield('content')) correctly fills available space */
        .flex-fill {
            flex: 1; /* Allows this div to grow and shrink, taking all available space */
            display: flex; /* Makes it a flex container for its children (like slot-container) */
            flex-direction: column; /* Allows children to stack vertically and grow */
            min-height: 0; /* Allows this flex item to shrink properly */
        }
        /* Basic styles for header/footer if they don't have explicit heights */
        /* You might already have these defined elsewhere, this is just for certainty. */
        header, footer {
            flex-shrink: 0; /* Prevents header/footer from shrinking */
        }
    </style>
</head>
<body class="d-flex flex-column">
@include('elements.impersonation-header')
@include('elements.global-announcement')
@include('template.header')
<div class="flex-fill">
    @yield('content')
</div>

@if(getSetting('compliance.enable_age_verification_dialog'))
    @include('elements.site-entry-approval-box')
@endif
@include('template.footer')
@include('template.jsVars')
@include('template.jsAssets')
@stack('scripts') {{-- This line is added to correctly load scripts --}}
</body>
</html>