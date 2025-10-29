<?php

if (!function_exists('getImageUrl')) {
    function getImageUrl($path) {
        if (!$path) {
            return null;
        }
        
        // If it's already a full URL (Cloudinary), return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        // If it already has /storage/, return as is
        if (str_starts_with($path, '/storage/')) {
            return $path;
        }
        
        // Otherwise, prepend /storage/ for local files
        return '/storage/' . $path;
    }
}