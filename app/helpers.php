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

if (!function_exists('filterProfanity')) {
    /**
     * Filter profanity from text by replacing inappropriate words with asterisks
     * 
     * @param string $text
     * @return string
     */
    function filterProfanity($text)
    {
        if (empty($text)) {
            return $text;
        }

        // List of inappropriate words to filter
        $profanityList = [
            'fuck', 'shit', 'ass', 'bitch', 'damn', 'bastard', 'cunt', 'dick', 
            'pussy', 'cock', 'slut', 'whore', 'fag', 'nigger', 'nigga', 'retard',
            'rape', 'nazi', 'hitler', 'terrorist', 'kill yourself', 'kys',
            'motherfucker', 'asshole', 'douche', 'twat', 'prick', 'wanker',
            // Add variations and common misspellings
            'f*ck', 'sh*t', 'b*tch', 'fuk', 'fck', 'sht', 'btch', 'cnt',
            'dck', 'psy', 'cok', 'slt', 'whr', 'mthrfckr', 'ashl', 'dch',
            // Additional inappropriate terms
            'porn', 'sex', 'xxx', 'nude', 'nudes', 'cum', 'jizz', 'orgasm',
            'horny', 'sexy', 'dildo', 'vibrator', 'masturbate', 'blowjob',
        ];

        $filtered = $text;

        foreach ($profanityList as $word) {
            // Create a case-insensitive pattern that matches the word with word boundaries
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
            
            // Replace with asterisks of the same length
            $filtered = preg_replace_callback($pattern, function($matches) {
                return str_repeat('*', strlen($matches[0]));
            }, $filtered);
        }

        return $filtered;
    }
}