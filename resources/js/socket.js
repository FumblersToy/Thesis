import { io } from 'socket.io-client';

class SocketManager {
    constructor() {
        this.socket = null;
        this.isConnected = false;
        this.user = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.typingTimeout = null;
    }

    // Initialize socket connection
    init(userData) {
        if (this.socket) {
            this.disconnect();
        }

        this.user = userData;
        
        // Connect to Socket.IO server (try localhost and 127.0.0.1)
        const endpoints = ['http://localhost:3001', 'http://127.0.0.1:3001'];
        const endpoint = endpoints[this.reconnectAttempts % endpoints.length];
        this.socket = io(endpoint, {
            transports: ['websocket', 'polling'],
            timeout: 20000,
            forceNew: true
        });

        this.setupEventListeners();
    }

    // Setup all event listeners
    setupEventListeners() {
        if (!this.socket) return;

        // Connection events
        this.socket.on('connect', () => {
            console.log('Connected to Socket.IO server');
            this.isConnected = true;
            this.reconnectAttempts = 0;
            
            // Join as user
            if (this.user) {
                this.socket.emit('user_join', this.user);
            }
            
            this.showConnectionStatus('Connected', 'success');
        });

        this.socket.on('disconnect', (reason) => {
            console.log('Disconnected from Socket.IO server:', reason);
            this.isConnected = false;
            this.showConnectionStatus('Disconnected', 'error');
        });

        this.socket.on('connect_error', (error) => {
            console.error('Socket connection error:', error);
            this.reconnectAttempts++;
            
            if (this.reconnectAttempts <= this.maxReconnectAttempts) {
                this.showConnectionStatus(`Reconnecting... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`, 'warning');
            } else {
                this.showConnectionStatus('Connection failed', 'error');
            }
        });

        // Real-time events
        this.socket.on('post_created', (data) => {
            this.handleNewPost(data);
        });

        this.socket.on('post_like_update', (data) => {
            this.handlePostLikeUpdate(data);
        });

        this.socket.on('comment_added', (data) => {
            this.handleNewComment(data);
        });

        this.socket.on('follow_notification', (data) => {
            this.handleFollowNotification(data);
        });

        this.socket.on('new_message', (data) => {
            this.handleNewMessage(data);
        });

        this.socket.on('message_sent', (data) => {
            this.handleMessageSent(data);
        });

        this.socket.on('user_online', (data) => {
            this.handleUserOnline(data);
        });

        this.socket.on('user_offline', (data) => {
            this.handleUserOffline(data);
        });

        this.socket.on('user_typing', (data) => {
            this.handleTypingIndicator(data);
        });

        this.socket.on('message_read_confirmation', (data) => {
            this.handleMessageReadConfirmation(data);
        });

        this.socket.on('notification_received', (data) => {
            this.handleNotificationReceived(data);
        });
    }

    // Emit events
    emitNewPost(postData) {
        if (this.socket && this.isConnected) {
            this.socket.emit('new_post', postData);
        }
    }

    emitPostLike(postId, likeCount, liked, postOwnerId = null) {
        if (this.socket && this.isConnected) {
            this.socket.emit('post_liked', {
                postId,
                likeCount,
                liked,
                postOwnerId
            });
        }
    }

    emitNewComment(commentData) {
        if (this.socket && this.isConnected) {
            this.socket.emit('new_comment', {
                ...commentData,
                postOwnerId: commentData.postOwnerId || null
            });
        }
    }

    emitUserFollow(targetUserId, action) {
        if (this.socket && this.isConnected) {
            this.socket.emit('user_followed', {
                targetUserId,
                action
            });
        }
    }

    emitMessage(messageData) {
        if (this.socket && this.isConnected) {
            this.socket.emit('send_message', messageData);
        }
    }

    emitTypingStart(receiverId) {
        if (this.socket && this.isConnected) {
            this.socket.emit('typing_start', { receiver_id: receiverId });
        }
    }

    emitTypingStop(receiverId) {
        if (this.socket && this.isConnected) {
            this.socket.emit('typing_stop', { receiver_id: receiverId });
        }
    }

    emitMessageRead(messageId, senderId) {
        if (this.socket && this.isConnected) {
            this.socket.emit('message_read', {
                message_id: messageId,
                sender_id: senderId
            });
        }
    }

    // Event handlers
    handleNewPost(data) {
        console.log('New post received:', data);
        
        // Show notification
        this.showNotification(
            `New post by ${data.author.name}`,
            'info'
        );

        // Add post to feed if on feed page
        if (typeof window.addPostToFeed === 'function') {
            window.addPostToFeed(data.post);
        }
    }

    handlePostLikeUpdate(data) {
        console.log('Post like update:', data);
        
        // Update like count in UI
        const likeElements = document.querySelectorAll(`[data-post-id="${data.postId}"] .like-count`);
        likeElements.forEach(element => {
            element.textContent = data.likeCount;
        });

        // Update like button state
        const likeButtons = document.querySelectorAll(`[data-post-id="${data.postId}"] .like-btn`);
        likeButtons.forEach(button => {
            const svg = button.querySelector('svg');
            if (data.liked) {
                svg.setAttribute('class', 'w-6 h-6 fill-red-500 text-red-500');
                button.setAttribute('data-liked', 'true');
            } else {
                svg.setAttribute('class', 'w-6 h-6 fill-none text-gray-600 hover:text-red-500');
                button.setAttribute('data-liked', 'false');
            }
        });
    }

    handleNewComment(data) {
        console.log('New comment received:', data);
        
        // Show notification
        this.showNotification(
            `New comment by ${data.author.name}`,
            'info'
        );

        // Add comment to UI if post modal is open
        if (typeof window.addCommentToModal === 'function') {
            window.addCommentToModal(data.comment);
        }
    }

    handleFollowNotification(data) {
        console.log('Follow notification:', data);
        
        const action = data.action === 'follow' ? 'followed' : 'unfollowed';
        this.showNotification(
            `${data.follower.name} ${action} you`,
            'success'
        );
    }

    handleNewMessage(data) {
        console.log('New message received:', data);
        
        // Show notification
        this.showNotification(
            `New message from ${data.sender.name}`,
            'info'
        );

        // Add message to chat if chat is open
        if (typeof window.addMessageToChat === 'function') {
            window.addMessageToChat(data.message, data.sender);
        }

        // Update unread count
        this.updateUnreadCount();
    }

    handleMessageSent(data) {
        console.log('Message sent confirmation:', data);
        
        // Add message to chat UI
        if (typeof window.addMessageToChat === 'function') {
            window.addMessageToChat(data.message, this.user);
        }
    }

    handleUserOnline(data) {
        console.log('User online:', data);
        
        // Update online status indicator
        const userElements = document.querySelectorAll(`[data-user-id="${data.id}"]`);
        userElements.forEach(element => {
            const statusIndicator = element.querySelector('.online-status');
            if (statusIndicator) {
                statusIndicator.classList.remove('bg-gray-400');
                statusIndicator.classList.add('bg-green-500');
                statusIndicator.title = 'Online';
            }
        });
    }

    handleUserOffline(data) {
        console.log('User offline:', data);
        
        // Update offline status indicator
        const userElements = document.querySelectorAll(`[data-user-id="${data.id}"]`);
        userElements.forEach(element => {
            const statusIndicator = element.querySelector('.online-status');
            if (statusIndicator) {
                statusIndicator.classList.remove('bg-green-500');
                statusIndicator.classList.add('bg-gray-400');
                statusIndicator.title = 'Offline';
            }
        });
    }

    handleTypingIndicator(data) {
        console.log('Typing indicator:', data);
        
        // Show typing indicator in chat
        if (typeof window.showTypingIndicator === 'function') {
            window.showTypingIndicator(data);
        }
    }

    handleMessageReadConfirmation(data) {
        console.log('Message read confirmation:', data);
        
        // Update message status in UI
        if (typeof window.updateMessageStatus === 'function') {
            window.updateMessageStatus(data.message_id, 'read');
        }
    }

    handleNotificationReceived(data) {
        console.log('Notification received:', data);
        
        // Show notification toast
        if (typeof window.showNotificationToast === 'function') {
            window.showNotificationToast(data.message, data.type);
        } else {
            this.showNotification(data.message, data.type === 'like' ? 'success' : 'info');
        }
        
        // Update notification badge count
        if (typeof window.updateNotificationCount === 'function') {
            window.updateNotificationCount();
        }
    }

    // Utility methods
    showConnectionStatus(message, type) {
        const statusElement = document.getElementById('socket-status');
        if (statusElement) {
            statusElement.textContent = message;
            statusElement.className = `socket-status socket-status-${type}`;
        }
    }

    showNotification(message, type = 'info') {
        // Use existing notification system if available
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
        } else {
            // Fallback notification
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                type === 'warning' ? 'bg-yellow-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }

    updateUnreadCount() {
        // Update unread message count in UI
        if (typeof window.updateUnreadMessageCount === 'function') {
            window.updateUnreadMessageCount();
        }
    }

    // Typing indicator management
    startTyping(receiverId) {
        this.emitTypingStart(receiverId);
        
        // Clear existing timeout
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }
        
        // Set timeout to stop typing indicator
        this.typingTimeout = setTimeout(() => {
            this.stopTyping(receiverId);
        }, 3000);
    }

    stopTyping(receiverId) {
        this.emitTypingStop(receiverId);
        
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
            this.typingTimeout = null;
        }
    }

    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
            this.socket = null;
            this.isConnected = false;
        }
    }

    // Get connection status
    getConnectionStatus() {
        return {
            connected: this.isConnected,
            socketId: this.socket?.id,
            user: this.user
        };
    }
}

// Create global instance
window.socketManager = new SocketManager();

export default window.socketManager;
