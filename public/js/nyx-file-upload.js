/**
 * File upload functionality for Nyx Chatbot
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/public/js
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize file upload functionality
        initFileUpload();
    });

    /**
     * Initialize file upload functionality
     */
    function initFileUpload() {
        // Add file upload button to controls
        if ($('.nyx-controls').length && !$('.nyx-file-button').length) {
            $('.nyx-controls').append(`
                <button class="nyx-control-button nyx-file-button">
                    ${nyx_vars.strings.file_upload}
                </button>
                <input type="file" class="nyx-file-upload" accept=".pdf,.doc,.docx,.txt" style="display:none;">
            `);
        }

        // File button click
        $('.nyx-file-button').on('click', function() {
            $('.nyx-file-upload').click();
        });

        // File selection
        $('.nyx-file-upload').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                uploadFile(file);
            }
        });
    }

    /**
     * Upload file to server
     */
    function uploadFile(file) {
        // Create FormData
        const formData = new FormData();
        formData.append('file', file);
        formData.append('session_id', getSessionId());

        // Show file preview
        const chatLog = $('#chat-log');
        chatLog.append(`
            <div class="nyx-file-preview">
                Uploading file: ${file.name}...
            </div>
        `);
        chatLog.scrollTop(chatLog[0].scrollHeight);

        // Upload file
        $.ajax({
            url: nyx_vars.rest_url + '/upload',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nyx_vars.nonce);
            },
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Update file preview
                $('.nyx-file-preview').html(`
                    File uploaded: ${file.name}
                `);

                // Add user message
                addMessageToChat('user', `I've uploaded a file: ${file.name}`);

                // Show thinking indicator
                showThinking();

                // Process file
                processFile(response.file_id);
            },
            error: function(xhr, status, error) {
                // Update file preview
                $('.nyx-file-preview').html(`
                    Error uploading file: ${error}
                `);
                console.error('Error uploading file:', error);
            }
        });
    }

    /**
     * Process uploaded file
     */
    function processFile(fileId) {
        $.ajax({
            url: nyx_vars.rest_url + '/process-file',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nyx_vars.nonce);
            },
            data: {
                file_id: fileId,
                session_id: getSessionId()
            },
            success: function(response) {
                // Hide thinking indicator
                hideThinking();

                // Add AI response
                addMessageToChat('ai', response.reply);

                // Add to conversation history
                if (typeof window.conversationHistory !== 'undefined') {
                    window.conversationHistory.push({
                        role: 'assistant',
                        content: response.reply
                    });

                    // Save conversation if user is logged in
                    if (nyx_vars.user_logged_in) {
                        saveConversation();
                    }
                }
            },
            error: function(xhr, status, error) {
                // Hide thinking indicator
                hideThinking();

                // Show error message
                addMessageToChat('ai', 'Error processing file: ' + error);
                console.error('Error processing file:', error);
            }
        });
    }

    /**
     * Get session ID from localStorage or main script
     */
    function getSessionId() {
        if (typeof window.sessionId !== 'undefined') {
            return window.sessionId;
        }
        
        let id = localStorage.getItem('nyx_session_id');
        
        if (!id) {
            id = 'nyx_' + Math.random().toString(36).substring(2, 15);
            localStorage.setItem('nyx_session_id', id);
        }
        
        return id;
    }

    /**
     * Add message to chat (if main script function is not available)
     */
    function addMessageToChat(role, content) {
        if (typeof window.addMessageToChat === 'function') {
            window.addMessageToChat(role, content);
            return;
        }

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
     * Show thinking indicator (if main script function is not available)
     */
    function showThinking() {
        if (typeof window.showThinking === 'function') {
            window.showThinking();
            return;
        }

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
     * Hide thinking indicator (if main script function is not available)
     */
    function hideThinking() {
        if (typeof window.hideThinking === 'function') {
            window.hideThinking();
            return;
        }

        $('#nyx-thinking').remove();
    }

    /**
     * Save conversation (if main script function is not available)
     */
    function saveConversation() {
        if (typeof window.saveConversation === 'function') {
            window.saveConversation();
            return;
        }

        $.ajax({
            url: nyx_vars.rest_url + '/save',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nyx_vars.nonce);
            },
            data: {
                session_id: getSessionId(),
                conversation_history: JSON.stringify(window.conversationHistory)
            },
            error: function(xhr, status, error) {
                console.error('Error saving conversation:', error);
            }
        });
    }

})(jQuery);
