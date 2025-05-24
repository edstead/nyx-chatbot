/**
 * Main JavaScript for the Nyx Chatbot
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/public/js
 */

(function($) {
    'use strict';

    // Store conversation history
    let conversationHistory = [];
    let sessionId = '';
    let messageCount = 0;
    let isThinking = false;

    $(document).ready(function() {
        // Initialize the chatbot
        initChatbot();

        // Set up event listeners
        setupEventListeners();
    });

    /**
     * Initialize the chatbot
     */
    function initChatbot() {
        // Generate or retrieve session ID
        sessionId = getSessionId();

        // Set CSS variables for customization
        setChatbotStyles();

        // Load previous conversation if history is enabled
        if (nyx_vars.enable_history === '1' && nyx_vars.user_logged_in) {
            loadConversationHistory();
        }
    }

    /**
     * Set up event listeners
     */
    function setupEventListeners() {
        // Send message on button click
        $('.nyx-send-button').on('click', function() {
            sendMessage();
        });

        // Send message on Enter key
        $('#user-input').on('keypress', function(e) {
            if (e.which === 13) {
                sendMessage();
                return false;
            }
        });

        // Floating button click
        $('.nyx-floating-button').on('click', function() {
            $('.nyx-floating-chatbox').fadeToggle();
        });

        // Close button click
        $('.nyx-close-button').on('click', function() {
            $('.nyx-floating-chatbox').fadeOut();
        });

        // Clear chat button
        $('.nyx-clear-button').on('click', function() {
            clearChat();
        });

        // View history button
        $('.nyx-history-button').on('click', function() {
            viewHistory();
        });
    }

    /**
     * Send message to the chatbot
     */
    function sendMessage() {
        // Get user input
        const userInput = $('#user-input').val().trim();
        
        // Don't send empty messages
        if (userInput === '' || isThinking) {
            return;
        }

        // Clear input field
        $('#user-input').val('');

        // Add user message to chat
        addMessageToChat('user', userInput);

        // Check if guest user has reached message limit
        if (!nyx_vars.user_logged_in && nyx_vars.guest_message_limit > 0) {
            messageCount++;
            
            if (messageCount >= nyx_vars.guest_message_limit) {
                showLoginPrompt();
                return;
            }
        }

        // Show thinking indicator
        showThinking();

        // Add to conversation history
        conversationHistory.push({
            role: 'user',
            content: userInput
        });

        // Send to server
        $.ajax({
            url: nyx_vars.rest_url + '/chat',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nyx_vars.nonce);
            },
            data: {
                message: userInput,
                session_id: sessionId,
                conversation_history: JSON.stringify(conversationHistory)
            },
            success: function(response) {
                // Hide thinking indicator
                hideThinking();

                // Add AI response to chat
                addMessageToChat('ai', response.reply);

                // Add to conversation history
                conversationHistory.push({
                    role: 'assistant',
                    content: response.reply
                });

                // Save conversation if user is logged in
                if (nyx_vars.user_logged_in) {
                    saveConversation();
                }
            },
            error: function(xhr, status, error) {
                // Hide thinking indicator
                hideThinking();

                // Show error message
                addMessageToChat('ai', nyx_vars.strings.error);
                console.error('Error:', error);
            }
        });
    }

    /**
     * Add message to chat
     */
    function addMessageToChat(role, content) {
        const chatLog = $('#chat-log');
        let messageClass = (role === 'user') ? 'user-message' : 'ai-message';
        let messageHtml = `<div class="${messageClass}">`;
        
        if (role === 'user') {
            messageHtml += `<strong>You:</strong> ${content}`;
        } else {
            messageHtml += `<strong>Nyx:</strong> ${content}`;
        }
        
        messageHtml += '</div>';
        
        chatLog.append(messageHtml);
        
        // Scroll to bottom
        chatLog.scrollTop(chatLog[0].scrollHeight);
    }

    /**
     * Show thinking indicator
     */
    function showThinking() {
        isThinking = true;
        const chatLog = $('#chat-log');
        chatLog.append(`
            <div class="nyx-thinking" id="nyx-thinking">
                ${nyx_vars.strings.thinking}
                <span class="nyx-thinking-dot"></span>
            </div>
        `);
        chatLog.scrollTop(chatLog[0].scrollHeight);
    }

    /**
     * Hide thinking indicator
     */
    function hideThinking() {
        isThinking = false;
        $('#nyx-thinking').remove();
    }

    /**
     * Show login prompt
     */
    function showLoginPrompt() {
        const chatLog = $('#chat-log');
        chatLog.append(`
            <div class="nyx-login-prompt">
                ${nyx_vars.strings.login_prompt}
                <div>
                    <a href="${nyx_vars.login_url}" target="_blank">Login</a> | 
                    <a href="${nyx_vars.register_url}" target="_blank">Register</a>
                </div>
            </div>
        `);
        chatLog.scrollTop(chatLog[0].scrollHeight);
    }

    /**
     * Get or generate session ID
     */
    function getSessionId() {
        let id = localStorage.getItem('nyx_session_id');
        
        if (!id) {
            id = 'nyx_' + Math.random().toString(36).substring(2, 15);
            localStorage.setItem('nyx_session_id', id);
        }
        
        return id;
    }

    /**
     * Set chatbot styles from options
     */
    function setChatbotStyles() {
        document.documentElement.style.setProperty('--nyx-primary-color', nyx_vars.primary_color);
        document.documentElement.style.setProperty('--nyx-background-color', nyx_vars.background_color);
        document.documentElement.style.setProperty('--nyx-text-color', nyx_vars.text_color);
        document.documentElement.style.setProperty('--nyx-font-size', nyx_vars.font_size + 'px');
    }

    /**
     * Load conversation history
     */
    function loadConversationHistory() {
        $.ajax({
            url: nyx_vars.rest_url + '/history',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nyx_vars.nonce);
            },
            success: function(response) {
                if (response.success && response.messages.length > 0) {
                    // Clear chat log
                    $('#chat-log').empty();
                    
                    // Add messages to chat
                    response.messages.forEach(function(message) {
                        addMessageToChat(message.role, message.content);
                        
                        // Add to conversation history
                        conversationHistory.push({
                            role: message.role === 'ai' ? 'assistant' : 'user',
                            content: message.content
                        });
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading history:', error);
            }
        });
    }

    /**
     * Save conversation
     */
    function saveConversation() {
        $.ajax({
            url: nyx_vars.rest_url + '/save',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nyx_vars.nonce);
            },
            data: {
                session_id: sessionId,
                conversation_history: JSON.stringify(conversationHistory)
            },
            error: function(xhr, status, error) {
                console.error('Error saving conversation:', error);
            }
        });
    }

    /**
     * Clear chat
     */
    function clearChat() {
        // Clear chat log
        $('#chat-log').empty();
        
        // Reset conversation history
        conversationHistory = [];
        
        // Reset message count
        messageCount = 0;
    }

    /**
     * View conversation history
     */
    function viewHistory() {
        $.ajax({
            url: nyx_vars.rest_url + '/conversations',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nyx_vars.nonce);
            },
            success: function(response) {
                if (response.success && response.conversations.length > 0) {
                    // Show history modal
                    showHistoryModal(response.conversations);
                } else {
                    alert('No conversation history found.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading conversations:', error);
                alert('Error loading conversation history.');
            }
        });
    }

    /**
     * Show history modal
     */
    function showHistoryModal(conversations) {
        // Create modal HTML
        let modalHtml = `
            <div class="nyx-history-modal">
                <div class="nyx-history-modal-content">
                    <span class="nyx-history-modal-close">&times;</span>
                    <h2>Conversation History</h2>
                    <div class="nyx-history-list">
        `;
        
        // Add conversations
        conversations.forEach(function(conversation) {
            modalHtml += `
                <div class="nyx-history-item" data-id="${conversation.id}">
                    <div class="nyx-history-date">${conversation.date}</div>
                    <div class="nyx-history-preview">${conversation.preview}</div>
                </div>
            `;
        });
        
        modalHtml += `
                    </div>
                </div>
            </div>
        `;
        
        // Add modal to page
        $('body').append(modalHtml);
        
        // Show modal
        $('.nyx-history-modal').fadeIn();
        
        // Close modal on click
        $('.nyx-history-modal-close').on('click', function() {
            $('.nyx-history-modal').fadeOut(function() {
                $(this).remove();
            });
        });
        
        // Load conversation on click
        $('.nyx-history-item').on('click', function() {
            const conversationId = $(this).data('id');
            loadSpecificConversation(conversationId);
            
            // Close modal
            $('.nyx-history-modal').fadeOut(function() {
                $(this).remove();
            });
        });
    }

    /**
     * Load specific conversation
     */
    function loadSpecificConversation(conversationId) {
        $.ajax({
            url: nyx_vars.rest_url + '/conversation/' + conversationId,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nyx_vars.nonce);
            },
            success: function(response) {
                if (response.success && response.messages.length > 0) {
                    // Clear chat log
                    $('#chat-log').empty();
                    
                    // Reset conversation history
                    conversationHistory = [];
                    
                    // Add messages to chat
                    response.messages.forEach(function(message) {
                        addMessageToChat(message.role, message.content);
                        
                        // Add to conversation history
                        conversationHistory.push({
                            role: message.role === 'ai' ? 'assistant' : 'user',
                            content: message.content
                        });
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading conversation:', error);
                alert('Error loading conversation.');
            }
        });
    }

})(jQuery);
