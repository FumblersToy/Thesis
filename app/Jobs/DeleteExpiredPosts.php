<?php

namespace App\Jobs;

use App\Models\Post;
use Cloudinary\Cloudinary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DeleteExpiredPosts implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find posts that were soft deleted more than 15 days ago
        // and have no appeal or denied appeal
        $expiredPosts = Post::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(15))
            ->whereIn('appeal_status', ['none', 'denied'])
            ->get();

        $cloudinary = null;
        $cloudinaryUrl = config('cloudinary.cloud_url');
        if ($cloudinaryUrl) {
            $cloudinary = new Cloudinary($cloudinaryUrl);
        }

        foreach ($expiredPosts as $post) {
            // Delete images from Cloudinary
            if ($cloudinary) {
                try {
                    if ($post->image_public_id) {
                        $cloudinary->uploadApi()->destroy($post->image_public_id);
                    }
                    if ($post->image_public_id_2) {
                        $cloudinary->uploadApi()->destroy($post->image_public_id_2);
                    }
                    if ($post->image_public_id_3) {
                        $cloudinary->uploadApi()->destroy($post->image_public_id_3);
                    }
                } catch (\Exception $e) {
                    Log::error('Cloudinary delete error for post ' . $post->id . ': ' . $e->getMessage());
                }
            }

            // Permanently delete the post
            $post->forceDelete();
            
            Log::info('Permanently deleted expired post: ' . $post->id);
        }

        Log::info('Deleted ' . $expiredPosts->count() . ' expired posts.');
    }
}
