<?php

if (!function_exists('getImageUrl')) {
    /**
     * Get image URL - handles both Cloudinary URLs and local storage
     * 
     * @param string|null $path
     * @return string|null
     */
    function getImageUrl($path)
    {
        if (empty($path)) {
            return null;
        }
        
        // If it's already a full URL (Cloudinary), return as-is
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }
        
        // If it already has /storage/, return as is
        if (strpos($path, '/storage/') === 0) {
            return $path;
        }
        
        // Otherwise, prepend /storage/ for local files
        return '/storage/' . $path;
    }
}