@php
    // The map view has been removed. Redirect users back to the feed.
    header('Location: ' . route('feed'));
    exit;
@endphp
