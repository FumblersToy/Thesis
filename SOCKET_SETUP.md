# Socket.IO Setup for BandMate

This document explains how to run the Socket.IO server and messaging system.

## Features Implemented

### Real-time Features:
- ✅ Real-time messaging between users
- ✅ Typing indicators
- ✅ Online/offline status
- ✅ Message read receipts
- ✅ Real-time notifications for posts, likes, comments, and follows
- ✅ Live post updates
- ✅ Live like/comment updates

### Messaging System:
- ✅ Full messaging interface with conversation list
- ✅ User search for messaging
- ✅ Message history
- ✅ Unread message counts
- ✅ Real-time message delivery

## How to Run

### 1. Install Dependencies
```bash
npm install
```

### 2. Run the Socket.IO Server
```bash
npm run socket
```
This will start the Socket.IO server on port 3001.

### 3. Run Laravel Development Server
```bash
php artisan serve
```
This will start Laravel on port 8000.

### 4. Run Vite (for asset compilation)
```bash
npm run dev
```

### 5. Run Everything Together (Recommended)
```bash
npm run dev:all
```
This runs Laravel server, Socket.IO server, and Vite concurrently.

## Access Points

- **Main Feed**: http://localhost:8000/main/feed
- **Messages**: http://localhost:8000/messages
- **Socket.IO Server**: http://localhost:3001

## Database

The messaging system uses a `messages` table with the following structure:
- `id` - Primary key
- `sender_id` - User who sent the message
- `receiver_id` - User who receives the message
- `content` - Message content
- `is_read` - Read status
- `read_at` - Timestamp when read
- `created_at` / `updated_at` - Timestamps

## Socket.IO Events

### Client to Server:
- `user_join` - User connects and identifies themselves
- `send_message` - Send a message to another user
- `typing_start` - Start typing indicator
- `typing_stop` - Stop typing indicator
- `message_read` - Mark message as read
- `new_post` - Broadcast new post
- `post_liked` - Broadcast post like
- `new_comment` - Broadcast new comment
- `user_followed` - Broadcast follow action

### Server to Client:
- `new_message` - Receive new message
- `message_sent` - Confirmation message was sent
- `user_typing` - Typing indicator from another user
- `message_read_confirmation` - Message read confirmation
- `user_online` - User came online
- `user_offline` - User went offline
- `post_created` - New post notification
- `post_like_update` - Post like update
- `comment_added` - New comment notification
- `follow_notification` - Follow notification

## API Endpoints

### Messages API:
- `GET /api/messages` - Get conversations list
- `GET /api/messages/{userId}` - Get conversation with specific user
- `POST /api/messages` - Send a message
- `POST /api/messages/{userId}/read` - Mark messages as read
- `GET /api/messages/unread/count` - Get unread message count
- `GET /api/messages/search/users` - Search users for messaging

## Configuration

The Socket.IO server is configured to:
- Accept connections from `http://localhost:8000` and `http://127.0.0.1:8000`
- Use WebSocket and polling transports
- Store connected users in memory
- Handle CORS for Laravel integration

## Troubleshooting

1. **Socket.IO not connecting**: Make sure the Socket.IO server is running on port 3001
2. **Messages not sending**: Check browser console for errors and ensure user is authenticated
3. **Real-time updates not working**: Verify Socket.IO connection status in the top-left corner
4. **Database errors**: Run `php artisan migrate` to ensure the messages table exists

## Development Notes

- The Socket.IO server runs independently from Laravel
- User authentication is handled by Laravel sessions
- Messages are stored in the database and synced via Socket.IO
- The system supports both real-time and offline message delivery
- All Socket.IO events are logged to the console for debugging
