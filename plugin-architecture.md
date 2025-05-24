# Nyx Chatbot WordPress Plugin Architecture

## Overview
The Nyx Chatbot WordPress plugin will provide an AI-powered chatbot that integrates with OpenAI for natural language processing and Pinecone for knowledge retrieval. The plugin will support both shortcode embedding and floating button options, with conversation memory for users.

## Plugin Structure

```
nyx-chatbot/
├── nyx-chatbot.php                  # Main plugin file
├── includes/                        # Core functionality
│   ├── class-nyx-chatbot.php        # Main plugin class
│   ├── class-nyx-activator.php      # Plugin activation hooks
│   ├── class-nyx-deactivator.php    # Plugin deactivation hooks
│   └── class-nyx-i18n.php           # Internationalization
├── admin/                           # Admin-related functionality
│   ├── class-nyx-admin.php          # Admin class
│   ├── js/                          # Admin JavaScript
│   │   └── nyx-admin.js             # Admin JavaScript file
│   ├── css/                         # Admin CSS
│   │   └── nyx-admin.css            # Admin CSS file
│   └── partials/                    # Admin page templates
│       ├── nyx-admin-display.php    # Main admin page
│       ├── nyx-settings-general.php # General settings
│       ├── nyx-settings-openai.php  # OpenAI settings
│       ├── nyx-settings-pinecone.php# Pinecone settings
│       ├── nyx-settings-appearance.php # Appearance settings
│       └── nyx-settings-features.php# Feature settings
├── public/                          # Public-facing functionality
│   ├── class-nyx-public.php         # Public class
│   ├── js/                          # Public JavaScript
│   │   ├── nyx-chatbot.js           # Main chatbot JS
│   │   ├── nyx-voice.js             # Voice functionality
│   │   └── nyx-file-upload.js       # File upload functionality
│   ├── css/                         # Public CSS
│   │   └── nyx-chatbot.css          # Chatbot CSS
│   └── partials/                    # Public templates
│       ├── nyx-chatbot-shortcode.php# Shortcode template
│       └── nyx-chatbot-floating.php # Floating button template
├── api/                             # API endpoints
│   ├── class-nyx-api.php            # API class
│   ├── class-nyx-openai.php         # OpenAI integration
│   ├── class-nyx-pinecone.php       # Pinecone integration
│   └── class-nyx-conversation.php   # Conversation handling
└── languages/                       # Internationalization files
    └── nyx-chatbot.pot              # POT file for translations
```

## Core Components

### 1. Plugin Base
- Main plugin class to initialize hooks, shortcodes, and settings
- Activation/deactivation hooks for database setup
- Internationalization support

### 2. Admin Interface
- Settings page with tabs for different configuration sections:
  - General settings (plugin activation, shortcode/floating button toggle)
  - OpenAI settings (API key, model selection, token limits)
  - Pinecone settings (API key, index name, environment)
  - Appearance settings (colors, fonts, sizes, custom CSS)
  - Features settings (file upload, voice, user message limits)
- AJAX handlers for settings updates
- Settings validation and sanitization

### 3. Database Structure
- Conversations table:
  ```sql
  CREATE TABLE {prefix}_nyx_conversations (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) DEFAULT NULL,
    session_id varchar(255) NOT NULL,
    created_at datetime NOT NULL,
    updated_at datetime NOT NULL,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    KEY session_id (session_id)
  )
  ```

- Messages table:
  ```sql
  CREATE TABLE {prefix}_nyx_messages (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    conversation_id bigint(20) NOT NULL,
    role varchar(50) NOT NULL,
    content text NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY (id),
    KEY conversation_id (conversation_id)
  )
  ```

- User settings table:
  ```sql
  CREATE TABLE {prefix}_nyx_user_settings (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) DEFAULT NULL,
    session_id varchar(255) DEFAULT NULL,
    setting_key varchar(255) NOT NULL,
    setting_value text NOT NULL,
    created_at datetime NOT NULL,
    updated_at datetime NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY user_setting (user_id, session_id, setting_key)
  )
  ```

### 4. OpenAI Integration
- API client for OpenAI
- Support for configurable models
- Token limit management
- Error handling and retry logic
- System message configuration for Nyx personality

### 5. Pinecone Integration
- API client for Pinecone
- Vector embedding generation using OpenAI
- Query and retrieval functionality
- Context augmentation for chat completions

### 6. Public Interface
- Shortcode implementation for embedding chatbot
- Floating button implementation with toggle
- Chat UI with styling based on provided code
- AJAX handlers for chat interactions
- File upload functionality
- Voice input/output functionality
- Chat history viewing

### 7. Authentication and Authorization
- Session management for non-logged-in users
- WordPress user integration for logged-in users
- Message limit enforcement for non-logged-in users
- Optional rate limiting

## Data Flow

1. **User Interaction**:
   - User sends message via chat interface
   - Message is sent to WordPress backend via AJAX

2. **Message Processing**:
   - Backend receives message and user/session information
   - Checks user authentication status and message limits
   - Retrieves conversation history

3. **Knowledge Retrieval**:
   - Message is converted to vector embedding via OpenAI
   - Vector is used to query Pinecone for relevant information
   - Retrieved information is formatted as context

4. **AI Response Generation**:
   - Conversation history, user message, and context are sent to OpenAI
   - OpenAI generates response
   - Response is processed and returned to frontend

5. **Conversation Storage**:
   - User message and AI response are stored in database
   - Linked to user ID (if logged in) or session ID (if not logged in)

6. **Response Display**:
   - Response is displayed in chat interface
   - Chat history is updated

## Extension Points

The architecture includes several extension points for future enhancements:

1. **Additional AI Providers**:
   - Structure allows for adding alternative AI providers beyond OpenAI

2. **Additional Vector Databases**:
   - Support for other vector databases beyond Pinecone

3. **Enhanced Analytics**:
   - Framework for tracking and analyzing chat interactions

4. **Custom Workflows**:
   - Ability to define custom conversation flows or triggers

5. **Integration with Other Plugins**:
   - Hooks for integration with other WordPress plugins

## Security Considerations

1. **API Key Storage**:
   - API keys stored securely using WordPress options API
   - Keys never exposed to frontend

2. **User Data Protection**:
   - Proper sanitization of all user inputs
   - Prepared statements for database operations
   - Capability checks for admin functions

3. **Rate Limiting**:
   - Optional rate limiting to prevent abuse
   - Configurable limits based on user type

4. **Error Handling**:
   - Graceful error handling for API failures
   - User-friendly error messages
   - Detailed logging for administrators
