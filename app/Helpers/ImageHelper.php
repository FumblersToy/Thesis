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
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }
            
            // Otherwise, use storage URL for local files
            return \Illuminate\Support\Facades\Storage::url($path);
        }
    }

