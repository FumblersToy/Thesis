<?php

if (!function_exists('getImageUrl')) {
    function getImageUrl($path) {
        if (!$path) {
            return null;
        }
        
        // If it's already a full URL (Cloudinary), return as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // Otherwise, it's a local storage path
        return \Illuminate\Support\Facades\Storage::url($path);
    }
}