<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Musician;
use App\Models\Business;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;
use Exception;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $users = User::with(['musician', 'business'])
            ->withCount(['posts', 'likes', 'comments', 'followers', 'following'])
            ->paginate(20);

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();

        $stats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_musicians' => Musician::count(),
            'total_businesses' => Business::count(),
            
            // Today's stats
            'posts_today' => Post::where('created_at', '>=', $todayStart)->count(),
            'likes_today' => DB::table('likes')->where('created_at', '>=', $todayStart)->count(),
            'comments_today' => DB::table('comments')->where('created_at', '>=', $todayStart)->count(),
            
            // This week's stats
            'posts_week' => Post::where('created_at', '>=', $weekStart)->count(),
            'likes_week' => DB::table('likes')->where('created_at', '>=', $weekStart)->count(),
            'comments_week' => DB::table('comments')->where('created_at', '>=', $weekStart)->count(),
            
            // This month's stats
            'posts_month' => Post::where('created_at', '>=', $monthStart)->count(),
            'likes_month' => DB::table('likes')->where('created_at', '>=', $monthStart)->count(),
            'comments_month' => DB::table('comments')->where('created_at', '>=', $monthStart)->count(),
        ];

        return view('admin.dashboard', compact('users', 'stats'));
    }

    public function deletePost(Request $request, $postId)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            Log::info('Starting post deletion', ['post_id' => $postId, 'admin_id' => auth('admin')->id()]);
            
            $post = Post::findOrFail($postId);
            $user = $post->user;
            
            Log::info('Post found, user:', ['user_id' => $user->id, 'post_id' => $post->id]);
            
            // Soft delete with reason
            $post->deletion_reason = $request->reason;
            $post->deleted_by = auth('admin')->id();
            $post->save();
            $post->delete();
            
            Log::info('Post soft deleted, creating notification');
            
            // Create notification (notifier_id is null for admin actions)
            $notification = Notification::create([
                'user_id' => $user->id,
                'notifier_id' => null,
                'type' => 'post_deleted',
                'post_id' => $post->id,
                'message' => 'Your post has been removed by an admin. Reason: ' . $request->reason,
                'read' => false,
            ]);
            
            Log::info('Notification created', ['notification_id' => $notification->id]);

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully. User has been notified.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting post', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the post.'
            ], 500);
        }
    }    public function userPosts($userId)
    {
        $user = User::with(['musician', 'business'])->findOrFail($userId);
        $posts = Post::where('user_id', $userId)
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at')
            ->paginate(12);

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();

        // User activity stats
        $userStats = [
            // Posts
            'posts_today' => Post::where('user_id', $userId)
                ->where('created_at', '>=', $todayStart)->count(),
            'posts_week' => Post::where('user_id', $userId)
                ->where('created_at', '>=', $weekStart)->count(),
            'posts_month' => Post::where('user_id', $userId)
                ->where('created_at', '>=', $monthStart)->count(),
            
            // Likes received
            'likes_today' => DB::table('likes')
                ->join('posts', 'likes.post_id', '=', 'posts.id')
                ->where('posts.user_id', $userId)
                ->where('likes.created_at', '>=', $todayStart)->count(),
            'likes_week' => DB::table('likes')
                ->join('posts', 'likes.post_id', '=', 'posts.id')
                ->where('posts.user_id', $userId)
                ->where('likes.created_at', '>=', $weekStart)->count(),
            'likes_month' => DB::table('likes')
                ->join('posts', 'likes.post_id', '=', 'posts.id')
                ->where('posts.user_id', $userId)
                ->where('likes.created_at', '>=', $monthStart)->count(),
            
            // Comments received
            'comments_today' => DB::table('comments')
                ->join('posts', 'comments.post_id', '=', 'posts.id')
                ->where('posts.user_id', $userId)
                ->where('comments.created_at', '>=', $todayStart)->count(),
            'comments_week' => DB::table('comments')
                ->join('posts', 'comments.post_id', '=', 'posts.id')
                ->where('posts.user_id', $userId)
                ->where('comments.created_at', '>=', $weekStart)->count(),
            'comments_month' => DB::table('comments')
                ->join('posts', 'comments.post_id', '=', 'posts.id')
                ->where('posts.user_id', $userId)
                ->where('comments.created_at', '>=', $monthStart)->count(),
        ];

        return view('admin.user-posts', compact('user', 'posts', 'userStats'));
    }

    public function deleteUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        try {
            $cloudinaryUrl = config('cloudinary.cloud_url');
            $cloudinary = $cloudinaryUrl ? new Cloudinary($cloudinaryUrl) : null;

            // Delete all user's posts and their images from Cloudinary
            $posts = Post::where('user_id', $userId)->get();
            foreach ($posts as $post) {
                if ($post->image_public_id && $cloudinary) {
                    try {
                        $cloudinary->uploadApi()->destroy($post->image_public_id);
                    } catch (Exception $e) {
                        Log::error('Cloudinary delete error for post: ' . $e->getMessage());
                        // Continue deletion even if Cloudinary deletion fails
                    }
                }
            }

            // Delete profile pictures from Cloudinary if they exist
            if ($user->musician && $user->musician->profile_picture_public_id && $cloudinary) {
                try {
                    $cloudinary->uploadApi()->destroy($user->musician->profile_picture_public_id);
                } catch (Exception $e) {
                    Log::error('Cloudinary delete error for musician profile: ' . $e->getMessage());
                }
            }

            if ($user->business && $user->business->profile_picture_public_id && $cloudinary) {
                try {
                    $cloudinary->uploadApi()->destroy($user->business->profile_picture_public_id);
                } catch (Exception $e) {
                    Log::error('Cloudinary delete error for business profile: ' . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            Log::error('Error deleting user assets from Cloudinary: ' . $e->getMessage());
        }
        
        // Delete user (cascade will handle related records)
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function toggleVerification(Request $request, $businessId)
    {
        $business = Business::findOrFail($businessId);
        $verified = $request->input('verified', false);
        
        $business->verified = $verified;
        $business->save();

        return response()->json([
            'success' => true,
            'message' => $verified ? 'Business verified successfully' : 'Business unverified successfully'
        ]);
    }

    public function toggleMusicianVerification(Request $request, $musicianId)
    {
        $musician = \App\Models\Musician::findOrFail($musicianId);
        $verified = $request->input('verified', false);
        
        $musician->verified = $verified;
        $musician->save();

        return response()->json([
            'success' => true,
            'message' => $verified ? 'Musician verified successfully' : 'Musician unverified successfully'
        ]);
    }

    public function userConversations($userId)
    {
        $user = User::with(['musician', 'business'])->findOrFail($userId);
        
        // Get all conversations where this user is involved
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($message) use ($userId) {
                // Group by the other user's ID
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            })
            ->map(function($messages) use ($userId) {
                $otherUserId = $messages->first()->sender_id == $userId 
                    ? $messages->first()->receiver_id 
                    : $messages->first()->sender_id;
                
                $otherUser = User::with(['musician', 'business'])->find($otherUserId);
                
                return [
                    'other_user' => $otherUser,
                    'messages' => $messages->sortBy('created_at'),
                    'message_count' => $messages->count(),
                    'last_message' => $messages->sortByDesc('created_at')->first()
                ];
            })
            ->sortByDesc(function($conversation) {
                return $conversation['last_message']->created_at;
            });

        return view('admin.user-conversations', compact('user', 'conversations'));
    }

    public function appeals()
    {
        $appeals = Post::onlyTrashed()
            ->where('appeal_status', 'pending')
            ->with(['user.musician', 'user.business', 'deletedBy'])
            ->orderByDesc('appeal_at')
            ->get();

        return view('admin.appeals', compact('appeals'));
    }

    public function respondToAppeal(Request $request, $postId)
    {
        $request->validate([
            'decision' => 'required|in:approved,denied',
            'response' => 'nullable|string|max:500'
        ]);

        $post = Post::onlyTrashed()
            ->where('id', $postId)
            ->where('appeal_status', 'pending')
            ->firstOrFail();

        if ($request->decision === 'approved') {
            $post->restore();
            $post->appeal_status = 'approved';
            $post->deletion_reason = null;
            $post->deleted_by = null;
            $post->save();

            Notification::create([
                'user_id' => $post->user_id,
                'notifier_id' => null,
                'type' => 'appeal_approved',
                'post_id' => $post->id,
                'message' => 'Your appeal has been approved! Your post has been restored.',
                'read' => false,
            ]);
        } else {
            $post->appeal_status = 'denied';
            $post->save();

            $message = 'Your appeal has been denied.';
            if ($request->response) {
                $message .= ' Admin response: ' . $request->response;
            }
            
            Notification::create([
                'user_id' => $post->user_id,
                'notifier_id' => null,
                'type' => 'appeal_denied',
                'post_id' => $post->id,
                'message' => $message,
                'read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Appeal ' . $request->decision . ' successfully.'
        ]);
    }
}