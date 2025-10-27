import { Server } from 'socket.io';
import http from 'http';
import express from 'express';
import cors from 'cors';

const app = express();
app.use(cors({
    origin: ["http://localhost:8000", "http://127.0.0.1:8000"],
    credentials: true
}));

const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: ["http://localhost:8000", "http://127.0.0.1:8000"],
        methods: ["GET", "POST"],
        credentials: true
    }
});

// Store connected users
const connectedUsers = new Map();

io.on('connection', (socket) => {
    console.log(`User connected: ${socket.id}`);

    // Handle user authentication/identification
    socket.on('user_join', (userData) => {
        connectedUsers.set(socket.id, {
            id: userData.id,
            name: userData.name,
            type: userData.type,
            socketId: socket.id
        });
        
        console.log(`User ${userData.name} (${userData.type}) joined`);
        
        // Join user to their personal room
        socket.join(`user_${userData.id}`);
        
        // Broadcast to all clients that a user joined
        socket.broadcast.emit('user_online', {
            id: userData.id,
            name: userData.name,
            type: userData.type
        });
    });

    // Handle new post creation
    socket.on('new_post', (postData) => {
        console.log('New post created:', postData);
        
        // Broadcast to all connected users
        socket.broadcast.emit('post_created', {
            post: postData,
            author: connectedUsers.get(socket.id)
        });
    });

    // Handle post likes
    socket.on('post_liked', (data) => {
        console.log('Post liked:', data);
        
        // Broadcast to all users except the one who liked
        socket.broadcast.emit('post_like_update', {
            postId: data.postId,
            likeCount: data.likeCount,
            liked: data.liked,
            user: connectedUsers.get(socket.id)
        });
    });

    // Handle new comments
    socket.on('new_comment', (commentData) => {
        console.log('New comment:', commentData);
        
        // Broadcast to all users
        socket.broadcast.emit('comment_added', {
            comment: commentData,
            author: connectedUsers.get(socket.id)
        });
    });

    // Handle follow notifications
    socket.on('user_followed', (followData) => {
        console.log('User followed:', followData);
        
        // Notify the user being followed
        const targetUser = Array.from(connectedUsers.values())
            .find(user => user.id === followData.targetUserId);
        
        if (targetUser) {
            io.to(targetUser.socketId).emit('follow_notification', {
                follower: connectedUsers.get(socket.id),
                action: followData.action // 'follow' or 'unfollow'
            });
        }
    });

    // Handle real-time chat messages
    socket.on('send_message', (messageData) => {
        console.log('Message sent:', messageData);
        
        const sender = connectedUsers.get(socket.id);
        
        // Send to specific user if they're online
        const recipient = Array.from(connectedUsers.values())
            .find(user => user.id == messageData.receiver_id);
        
        if (recipient) {
            io.to(recipient.socketId).emit('new_message', {
                message: messageData,
                sender: sender
            });
        }
        
        // Also send back to sender for confirmation
        socket.emit('message_sent', {
            message: messageData,
            status: 'sent'
        });
    });

    // Handle typing indicators
    socket.on('typing_start', (data) => {
        const sender = connectedUsers.get(socket.id);
        const recipient = Array.from(connectedUsers.values())
            .find(user => user.id == data.receiver_id);
        
        if (recipient) {
            io.to(recipient.socketId).emit('user_typing', {
                sender_id: sender.id,
                sender_name: sender.name,
                receiver_id: data.receiver_id,
                isTyping: true
            });
        }
    });

    socket.on('typing_stop', (data) => {
        const sender = connectedUsers.get(socket.id);
        const recipient = Array.from(connectedUsers.values())
            .find(user => user.id == data.receiver_id);
        
        if (recipient) {
            io.to(recipient.socketId).emit('user_typing', {
                sender_id: sender.id,
                sender_name: sender.name,
                receiver_id: data.receiver_id,
                isTyping: false
            });
        }
    });

    // Handle message read status
    socket.on('message_read', (data) => {
        console.log('Message read:', data);
        
        const sender = Array.from(connectedUsers.values())
            .find(user => user.id == data.sender_id);
        
        if (sender) {
            io.to(sender.socketId).emit('message_read_confirmation', {
                message_id: data.message_id,
                read_by: connectedUsers.get(socket.id)
            });
        }
    });

    // Handle user disconnect
    socket.on('disconnect', () => {
        const user = connectedUsers.get(socket.id);
        if (user) {
            console.log(`User ${user.name} disconnected`);
            
            // Broadcast to all clients that user went offline
            socket.broadcast.emit('user_offline', {
                id: user.id,
                name: user.name,
                type: user.type
            });
            
            connectedUsers.delete(socket.id);
        }
    });
});

const PORT = process.env.SOCKET_PORT || 3001;
server.listen(PORT, () => {
    console.log(`Socket.IO server running on port ${PORT}`);
});
