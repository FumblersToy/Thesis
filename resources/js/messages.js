document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Elements
    const conversationsList = document.getElementById('conversationsList');
    const chatHeader = document.getElementById('chatHeader');
    const messagesArea = document.getElementById('messagesArea');
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const messageText = document.getElementById('messageText');
    const sendMessageBtn = document.getElementById('sendMessageBtn');
    const emptyState = document.getElementById('emptyState');
    const typingIndicator = document.getElementById('typingIndicator');
    const typingText = document.getElementById('typingText');
    const searchUsersInput = document.getElementById('searchUsers');
    const searchResults = document.getElementById('searchResults');
    const newMessageBtn = document.getElementById('newMessageBtn');
    const newMessageModal = document.getElementById('newMessageModal');
    const newMessageSearch = document.getElementById('newMessageSearch');
    const newMessageResults = document.getElementById('newMessageResults');
    const closeNewMessageModal = document.getElementById('closeNewMessageModal');
    const closeChatBtn = document.getElementById('closeChatBtn');
    
    // State
    let currentChatUserId = null;
    let conversations = [];
    let messages = [];
    let typingTimeout = null;
    
    // Initialize
    loadConversations();
    setupEventListeners();
    
    // Check for user parameter in URL
    const urlParams = new URLSearchParams(window.location.search);
    const targetUserId = urlParams.get('user');
    if (targetUserId) {
        // Open chat with the target user after conversations are loaded
        setTimeout(async () => {
            await openChat(targetUserId);
            // Clear the URL parameter to avoid confusion
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }, 1500);
    }
    
    function setupEventListeners() {
        // Send message
        if (sendMessageBtn) {
            sendMessageBtn.addEventListener('click', sendMessage);
        }
        if (messageText) {
            messageText.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        }
        
        // Typing indicator
        if (messageText) {
            messageText.addEventListener('input', handleTyping);
        }
        
        // Search users
        if (searchUsersInput) {
            searchUsersInput.addEventListener('input', debounce(searchUsersHandler, 300));
        }
        if (newMessageSearch) {
            newMessageSearch.addEventListener('input', debounce(searchUsersForNewMessage, 300));
        }

        // Pressing Enter in search bar opens first result
        if (searchUsersInput) {
            searchUsersInput.addEventListener('keypress', async function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = searchUsersInput.value.trim();
                    if (query.length < 2) return;
                    try {
                        const response = await fetch(`/api/messages/search/users?query=${encodeURIComponent(query)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        const data = await response.json();
                        if (data.success && Array.isArray(data.users) && data.users.length > 0) {
                            openChat(data.users[0].id);
                            searchResults.classList.add('hidden');
                        }
                    } catch (error) {
                        // Optionally show error
                    }
                }
            });
        }
        
        // Modal controls
        if (newMessageBtn && newMessageModal) {
            newMessageBtn.addEventListener('click', () => newMessageModal.classList.remove('hidden'));
        }
        if (closeNewMessageModal && newMessageModal) {
            closeNewMessageModal.addEventListener('click', () => newMessageModal.classList.add('hidden'));
        }
        if (closeChatBtn) {
            closeChatBtn.addEventListener('click', closeChat);
        }
        
        // Close modal on outside click
        if (newMessageModal) {
            newMessageModal.addEventListener('click', function(e) {
                if (e.target === newMessageModal) {
                    newMessageModal.classList.add('hidden');
                }
            });
        }
    }
    
    // Load conversations
    async function loadConversations() {
        try {
            const response = await fetch('/api/messages', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                conversations = data.conversations;
                renderConversations();
            }
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    }
    
    // Render conversations list
    function renderConversations() {
        conversationsList.innerHTML = '';
        
        if (conversations.length === 0) {
            conversationsList.innerHTML = `
                <div class="p-4 text-center text-white/70">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                        <span class="text-2xl">💬</span>
                    </div>
                    <p class="text-lg font-semibold text-white mb-2">No conversations yet</p>
                    <p class="text-sm">Start a new conversation!</p>
                </div>
            `;
            return;
        }
        
        conversations.forEach(conversation => {
            const conversationElement = createConversationElement(conversation);
            conversationsList.appendChild(conversationElement);
        });
    }
    
    // Create conversation element
    function createConversationElement(conversation) {
        const user = conversation.user;
        const displayName = user.musician?.stage_name || user.business?.business_name || user.name;
        const userType = user.musician ? 'musician' : user.business ? 'business' : 'member';
        const avatar = user.musician?.profile_picture || user.business?.profile_picture || null;
        
        const div = document.createElement('div');
        div.className = `p-4 border-b border-white/20 cursor-pointer hover:bg-white/10 transition-colors rounded-xl m-2 ${currentChatUserId === user.id ? 'bg-white/20' : ''}`;
        div.setAttribute('data-user-id', user.id);
        
        div.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="relative">
                    ${avatar ? 
                        `<img src="${getImageUrl(avatar)}" alt="${displayName}" class="w-12 h-12 rounded-full object-cover">` :
                        `<div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold">${displayName.charAt(0).toUpperCase()}</div>`
                    }
                    <div class="online-status absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-white truncate">${displayName}</h3>
                        <span class="text-xs text-white/50">${formatTime(conversation.last_message_at)}</span>
                    </div>
                    <p class="text-sm text-white/70 truncate">${userType}</p>
                    ${conversation.unread_count > 0 ? 
                        `<div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-white/50">${conversation.message_count} messages</span>
                            <span class="bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs px-2 py-1 rounded-full">${conversation.unread_count}</span>
                        </div>` :
                        `<span class="text-xs text-white/50">${conversation.message_count} messages</span>`
                    }
                </div>
            </div>
        `;
        
        div.addEventListener('click', () => openChat(user.id));
        
        return div;
    }
    
    // Open chat with user
    async function openChat(userId) {
        console.log('Opening chat with user:', userId);
        currentChatUserId = userId;
        
        // Update UI
        chatHeader.classList.remove('hidden');
        messagesArea.classList.remove('hidden');
        messageInput.classList.remove('hidden');
        emptyState.classList.add('hidden');
        
        // Update conversation selection
        document.querySelectorAll('[data-user-id]').forEach(el => {
            el.classList.remove('bg-blue-50', 'bg-white/20');
        });
        const conversationElement = document.querySelector(`[data-user-id="${userId}"]`);
        if (conversationElement) {
            conversationElement.classList.add('bg-white/20');
        }
        
        // Load messages and user info
        await loadMessages(userId);
        
        // Mark messages as read if there are any
        if (messages.length > 0) {
            await markMessagesAsRead(userId);
        }
        
        // Update conversations list
        loadConversations();
    }
    
    // Load messages for a conversation
    async function loadMessages(userId) {
        try {
            const response = await fetch(`/api/messages/${userId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                messages = data.messages;
                renderMessages();
                updateChatHeader(data.user);
                scrollToBottom();
            } else {
                // If no conversation exists yet, we still need to load user info
                // This will happen when opening a new conversation
                console.log('No existing conversation, ready for new messages');
                // Load user info for new conversation
                await loadUserInfo(userId);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }
    
    // Load user info for new conversations
    async function loadUserInfo(userId) {
        try {
            const response = await fetch(`/api/messages/${userId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.user) {
                updateChatHeader(data.user);
                messages = []; // Empty messages for new conversation
                renderMessages();
            } else {
                // If we can't get user info from messages API, try to get it from search
                await searchAndLoadUser(userId);
            }
        } catch (error) {
            console.error('Error loading user info:', error);
            // Try alternative method
            await searchAndLoadUser(userId);
        }
    }

    // Search for user and load their info
    async function searchAndLoadUser(userId) {
        try {
            const response = await fetch(`/api/messages/search/users?query=${userId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.users && data.users.length > 0) {
                const user = data.users.find(u => u.id == userId);
                if (user) {
                    updateChatHeader(user);
                    messages = [];
                    renderMessages();
                }
            }
        } catch (error) {
            console.error('Error searching for user:', error);
        }
    }
    
    // Render messages
    function renderMessages() {
        messagesContainer.innerHTML = '';
        
        if (messages.length === 0) {
            // Show empty state for new conversation
            messagesContainer.innerHTML = `
                <div class="text-center py-8 text-white/70">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                        <span class="text-3xl">💬</span>
                    </div>
                    <p class="text-xl font-semibold text-white mb-2">Start a conversation</p>
                    <p class="text-sm">Send a message to begin chatting!</p>
                </div>
            `;
            return;
        }
        
        messages.forEach(message => {
            const messageElement = createMessageElement(message);
            messagesContainer.appendChild(messageElement);
        });
    }
    
    // Create message element
    function createMessageElement(message) {
    const isOwn = message.sender_id === window.currentUserId;
        const senderName = message.sender?.musician?.stage_name || message.sender?.business?.business_name || message.sender?.name || 'Unknown';
        
        const div = document.createElement('div');
        div.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;
        div.setAttribute('data-message-id', message.id);
        
        div.innerHTML = `
            <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl ${isOwn ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : 'bg-white/20 backdrop-blur-xl text-white border border-white/20'}">
                ${!isOwn ? `<div class="text-xs text-white/70 mb-1">${senderName}</div>` : ''}
                <p class="text-sm">${message.content}</p>
                <div class="text-xs ${isOwn ? 'text-white/80' : 'text-white/50'} mt-1">
                    ${formatTime(message.created_at)}
                    ${isOwn && message.is_read ? ' ✓✓' : isOwn ? ' ✓' : ''}
                </div>
            </div>
        `;
        
        return div;
    }
    
    // Update chat header
    function updateChatHeader(user) {
        const displayName = user.musician?.stage_name || user.business?.business_name || user.name;
        const userType = user.musician ? 'musician' : user.business ? 'business' : 'member';
        const avatar = user.musician?.profile_picture || user.business?.profile_picture || null;
        
        document.getElementById('chatUserName').textContent = displayName;
        document.getElementById('chatUserStatus').textContent = 'Online';
        
        const avatarElement = document.getElementById('chatUserAvatar');
        if (avatar) {
            avatarElement.innerHTML = `<img src="${getImageUrl(avatar)}" alt="${displayName}" class="w-full h-full rounded-full object-cover">`;
        } else {
            avatarElement.innerHTML = displayName.charAt(0).toUpperCase();
        }
    }
    
    // Send message
    async function sendMessage() {
        const content = messageText.value.trim();
        
        if (!content || !currentChatUserId) return;
        
        try {
            const response = await fetch('/api/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    receiver_id: currentChatUserId,
                    content: content
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                messageText.value = '';
                
                // Add message to UI
                const messageElement = createMessageElement(data.message);
                messagesContainer.appendChild(messageElement);
                messages.push(data.message);
                
                scrollToBottom();
                
                // Emit to Socket.IO
                if (window.socketManager && window.socketManager.isConnected) {
                    window.socketManager.emitMessage({
                        receiver_id: currentChatUserId,
                        content: content,
                        id: data.message.id,
                        created_at: data.message.created_at
                    });
                }
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }
    
    // Handle typing indicator
    function handleTyping() {
        if (!currentChatUserId) return;
        
        // Start typing indicator
        if (window.socketManager && window.socketManager.isConnected) {
            window.socketManager.startTyping(currentChatUserId);
        }
        
        // Clear existing timeout
        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }
        
        // Set timeout to stop typing
        typingTimeout = setTimeout(() => {
            if (window.socketManager && window.socketManager.isConnected) {
                window.socketManager.stopTyping(currentChatUserId);
            }
        }, 1000);
    }
    
    // Mark messages as read
    async function markMessagesAsRead(userId) {
        try {
            await fetch(`/api/messages/${userId}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    }
    
    // Search users
    async function searchUsersHandler() {
        const query = searchUsersInput.value.trim();
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
            return;
        }
        try {
            const response = await fetch(`/api/messages/search/users?query=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            const data = await response.json();
            if (data.success && Array.isArray(data.users)) {
                renderSearchResults(data.users, searchResults, false);
                searchResults.classList.remove('hidden');
            } else {
                searchResults.innerHTML = '<div class="p-4 text-gray-500">No users found.</div>';
                searchResults.classList.remove('hidden');
            }
        } catch (error) {
            searchResults.innerHTML = '<div class="p-4 text-red-500">Error searching users.</div>';
            searchResults.classList.remove('hidden');
        }
    }
    
    // Search users for new message
    async function searchUsersForNewMessage() {
        const query = newMessageSearch.value.trim();
        
        if (query.length < 2) {
            newMessageResults.classList.add('hidden');
            return;
        }
        
        try {
            const response = await fetch(`/api/messages/search/users?query=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                renderSearchResults(data.users, newMessageResults, true);
                newMessageResults.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error searching users:', error);
        }
    }
    
    // Render search results
    function renderSearchResults(users, container, isNewMessage = false) {
        container.innerHTML = '';
        if (users.length === 0) {
            container.innerHTML = '<div class="p-4 text-gray-500">No users found.</div>';
            return;
        }
        users.forEach(user => {
            const displayName = user.musician?.stage_name || user.business?.business_name || user.name;
            const userType = user.musician ? 'musician' : user.business ? 'business' : 'member';
            const avatar = user.musician?.profile_picture || user.business?.profile_picture || null;
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 p-3 cursor-pointer hover:bg-gray-100 rounded-xl transition-colors';
            div.innerHTML = `
                <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold">
                    ${avatar ? `<img src="${getImageUrl(avatar)}" alt="${displayName}" class="w-full h-full object-cover">` : displayName.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-800 truncate">${displayName}</div>
                    <div class="text-xs text-gray-500">${userType}</div>
                </div>
            `;
            div.addEventListener('click', () => {
                // Hide search results and open chat
                container.classList.add('hidden');
                if (!isNewMessage) {
                    openChat(user.id);
                } else {
                    // For new message modal, open chat and close modal
                    openChat(user.id);
                    if (typeof newMessageModal !== 'undefined') {
                        newMessageModal.classList.add('hidden');
                    }
                }
            });
            container.appendChild(div);
        });
    }
    
    // Close chat
    function closeChat() {
        currentChatUserId = null;
        chatHeader.classList.add('hidden');
        messagesArea.classList.add('hidden');
        messageInput.classList.add('hidden');
        emptyState.classList.remove('hidden');
        
        // Clear selection
        document.querySelectorAll('[data-user-id]').forEach(el => {
            el.classList.remove('bg-blue-50');
        });
    }
    
    // Scroll to bottom of the messages container
    function scrollToBottom() {
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    // Format time
    function formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) { // Less than 1 minute
            return 'Just now';
        } else if (diff < 3600000) { // Less than 1 hour
            return Math.floor(diff / 60000) + 'm ago';
        } else if (diff < 86400000) { // Less than 1 day
            return Math.floor(diff / 3600000) + 'h ago';
        } else {
            return date.toLocaleDateString();
        }
    }
    
    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Global functions for Socket.IO integration
    window.addMessageToChat = function(message, sender) {
        if (currentChatUserId && sender.id === currentChatUserId) {
            const messageElement = createMessageElement({
                ...message,
                sender: sender
            });
            messagesContainer.appendChild(messageElement);
            messages.push({...message, sender: sender});
            scrollToBottom();
        }
    };

    // Expose openChat globally so other scripts (e.g., Blade inline) can trigger it
    window.openChat = openChat;
    
    window.showTypingIndicator = function(data) {
        if (currentChatUserId && data.sender_id === currentChatUserId) {
            if (data.isTyping) {
                typingText.textContent = `${data.sender_name} is typing...`;
                typingIndicator.classList.remove('hidden');
            } else {
                typingIndicator.classList.add('hidden');
            }
        }
    };
    
    window.updateMessageStatus = function(messageId, status) {
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            const statusElement = messageElement.querySelector('.text-xs');
            if (statusElement) {
                statusElement.textContent = statusElement.textContent.replace(/✓+/, status === 'read' ? '✓✓' : '✓');
            }
        }
    };
    
    window.updateUnreadMessageCount = function() {
        loadConversations();
    };
});
