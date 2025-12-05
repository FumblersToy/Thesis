<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Post;
use App\Models\Musician;
use App\Models\Business;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class DeleteExpiredAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete user accounts that have passed their deletion deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired accounts...');

        // Find users scheduled for deletion whose deadline has passed
        // and whose appeal was not approved
        $expiredUsers = User::whereNotNull('deletion_scheduled_at')
            ->where('deletion_scheduled_at', '<=', now())
            ->whereIn('appeal_status', ['none', 'denied', 'pending'])
            ->get();

        if ($expiredUsers->isEmpty()) {
            $this->info('No expired accounts found.');
            return 0;
        }

        $this->info("Found {$expiredUsers->count()} expired accounts to delete.");

        $cloudinaryUrl = config('cloudinary.cloud_url');
        $cloudinary = $cloudinaryUrl ? new Cloudinary($cloudinaryUrl) : null;

        foreach ($expiredUsers as $user) {
            $this->info("Deleting user: {$user->name} (ID: {$user->id})");

            try {
                // Delete Cloudinary assets
                if ($cloudinary) {
                    // Delete user's post images
                    $posts = Post::where('user_id', $user->id)->get();
                    foreach ($posts as $post) {
                        if ($post->image_public_id) {
                            try {
                                $cloudinary->uploadApi()->destroy($post->image_public_id);
                            } catch (\Exception $e) {
                                Log::error("Failed to delete post image for user {$user->id}: " . $e->getMessage());
                            }
                        }
                    }

                    // Delete profile pictures
                    $musician = Musician::where('user_id', $user->id)->first();
                    if ($musician && $musician->profile_picture_public_id) {
                        try {
                            $cloudinary->uploadApi()->destroy($musician->profile_picture_public_id);
                        } catch (\Exception $e) {
                            Log::error("Failed to delete musician profile for user {$user->id}: " . $e->getMessage());
                        }
                    }

                    $business = Business::where('user_id', $user->id)->first();
                    if ($business && $business->profile_picture_public_id) {
                        try {
                            $cloudinary->uploadApi()->destroy($business->profile_picture_public_id);
                        } catch (\Exception $e) {
                            Log::error("Failed to delete business profile for user {$user->id}: " . $e->getMessage());
                        }
                    }
                }

                // Permanently delete user (cascade handles related records)
                $user->delete();
                
                $this->info("✓ Successfully deleted user: {$user->name}");
                Log::info("Expired account deleted: {$user->name} (ID: {$user->id})");
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to delete user {$user->id}: " . $e->getMessage());
                Log::error("Failed to delete expired account {$user->id}: " . $e->getMessage());
            }
        }

        $this->info('Cleanup complete.');
        return 0;
    }
}
