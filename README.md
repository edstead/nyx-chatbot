# Nyx Chatbot WordPress Plugin

An AI-powered chatbot plugin for WordPress with OpenAI and Pinecone integration.

![WordPress](https://img.shields.io/badge/WordPress-5.0+-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)
![License](https://img.shields.io/badge/License-GPL--2.0+-green.svg)

## Features

- 🤖 **OpenAI GPT Integration** - Powered by GPT-3.5 Turbo, GPT-4, and other OpenAI models
- 🧠 **Pinecone Vector Database** - Long-term conversation memory and context awareness
- 📁 **File Upload Support** - Users can upload and analyze PDF, DOC, and TXT files
- 🎤 **Voice Input/Output** - Speech-to-text and text-to-speech capabilities
- 🎨 **Customizable Appearance** - Full control over colors, fonts, and styling
- 📱 **Multiple Display Options** - Shortcode embedding or floating button
- 👥 **User Management** - Guest message limits and user-specific conversations
- ⚡ **Rate Limiting** - Control API usage and costs
- 🔧 **Easy Configuration** - User-friendly admin interface

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- OpenAI API key
- Pinecone API key and index

## Installation

### From GitHub

1. Download the latest release from the [Releases page](https://github.com/yourusername/nyx-chatbot/releases)
2. Upload the plugin files to your `/wp-content/plugins/nyx-chatbot/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure your API keys in **Nyx Chatbot** settings

### Automatic Updates

This plugin supports automatic updates from GitHub releases. Once installed, you'll receive update notifications in your WordPress admin when new versions are available.

## Configuration

### 1. OpenAI Setup
Navigate to **Nyx Chatbot > OpenAI** in your WordPress admin:
- Enter your OpenAI API key
- Select your preferred model (GPT-3.5 Turbo, GPT-4, etc.)
- Set maximum token limits
- Customize the AI personality with system messages

### 2. Pinecone Setup
Navigate to **Nyx Chatbot > Pinecone**:
- Enter your Pinecone API key
- Set your index name (default: "nyx")
- Configure vector dimensions (default: 1536 for OpenAI embeddings)

### 3. General Settings
Configure display options and user limits:
- Choose between shortcode only, floating button only, or both
- Set guest message limits
- Enable/disable various features

### 4. Appearance Customization
Personalize the chatbot's look:
- Primary and background colors
- Font size and text colors
- Custom CSS for advanced styling
- Upload custom floating button images

### 5. Feature Management
Enable or disable specific features:
- File upload functionality (PDF, DOC, TXT)
- Voice input and output
- Chat history viewing
- Rate limiting controls

## Usage

### Shortcode Method
Add the chatbot to any page or post using:
```
[nyx_chatbot]
```

You can also customize the height:
```
[nyx_chatbot height="400px"]
```

### Floating Button
Enable the floating button in settings to display the chatbot on all pages with a convenient floating chat icon.

## API Keys Setup

### OpenAI API Key
1. Visit [OpenAI Platform](https://platform.openai.com/api-keys)
2. Sign in or create an account
3. Navigate to API Keys section
4. Create a new secret key
5. Copy and paste into the plugin settings

### Pinecone API Key
1. Visit [Pinecone.io](https://pinecone.io)
2. Sign up for an account
3. Create a new index with:
   - Dimension: 1536 (for OpenAI embeddings)
   - Metric: cosine
   - Name: nyx (or your preferred name)
4. Get your API key from the dashboard
5. Enter both the API key and index name in plugin settings

## Development

### File Structure
```
nyx-chatbot/
├── admin/                  # Admin interface files
│   ├── css/               # Admin styles
│   ├── js/                # Admin scripts
│   └── partials/          # Admin templates
├── api/                   # API integration classes
├── includes/              # Core plugin classes
├── public/                # Frontend files
│   ├── css/              # Frontend styles
│   ├── js/               # Frontend scripts
│   └── partials/         # Frontend templates
└── languages/            # Translation files
```

### Hooks and Filters

The plugin provides several hooks for customization:

#### Actions
- `nyx_chatbot_before_message` - Before sending message to OpenAI
- `nyx_chatbot_after_message` - After receiving response from OpenAI
- `nyx_chatbot_file_uploaded` - After file upload is processed

#### Filters
- `nyx_chatbot_system_message` - Modify the system message
- `nyx_chatbot_user_message` - Modify user input before processing
- `nyx_chatbot_ai_response` - Modify AI response before display
- `nyx_chatbot_file_types` - Modify allowed file types

### Custom CSS Classes

The plugin uses structured CSS classes for easy styling:

```css
.nyx-chatbot-container    /* Main container */
.nyx-chatbot-messages     /* Messages area */
.nyx-chatbot-input        /* Input area */
.nyx-chatbot-button       /* Send button */
.nyx-message-user         /* User messages */
.nyx-message-ai           /* AI messages */
.nyx-floating-button      /* Floating chat button */
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Setup

```bash
# Clone the repository
git clone https://github.com/yourusername/nyx-chatbot.git

# Install dependencies (if using composer)
composer install

# Set up your WordPress development environment
# Copy plugin to wp-content/plugins/nyx-chatbot/
```

## Troubleshooting

### Common Issues

**Settings not saving:**
- Ensure you have proper WordPress permissions
- Check for PHP errors in WordPress debug log
- Verify all required fields are filled

**API connection errors:**
- Verify your API keys are correct
- Check your server's ability to make outbound HTTPS requests
- Ensure your OpenAI account has sufficient credits

**Chatbot not displaying:**
- Check if shortcode is properly placed
- Verify plugin is activated
- Check for JavaScript errors in browser console

**File uploads not working:**
- Check WordPress upload limits
- Verify file types are enabled in settings
- Ensure proper file permissions

### Debug Mode

Enable WordPress debug mode to troubleshoot:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Getting Help

- Check the [Issues](https://github.com/yourusername/nyx-chatbot/issues) page
- Review the [Discussions](https://github.com/yourusername/nyx-chatbot/discussions) section
- Contact support through your preferred method

## Changelog

See [Releases](https://github.com/yourusername/nyx-chatbot/releases) for detailed version history.

### Version 1.0.0
- Initial release
- OpenAI GPT integration
- Pinecone vector database support
- File upload functionality
- Voice input/output
- Customizable appearance
- WordPress admin interface

## Roadmap

- [ ] Multi-language support
- [ ] Advanced conversation analytics
- [ ] Integration with more AI providers
- [ ] Enhanced file processing capabilities
- [ ] Mobile app integration
- [ ] Webhook support for external integrations

## License

This project is licensed under the GPL-2.0+ License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- OpenAI for providing the GPT API
- Pinecone for vector database services
- WordPress community for the excellent framework
- Contributors and beta testers

## Support

If you find this plugin helpful, please consider:
- ⭐ Starring the repository
- 🐛 Reporting bugs and issues
- 💡 Suggesting new features
- 🔄 Contributing code improvements

---

**Note:** This plugin requires paid API services from OpenAI and Pinecone. Please review their pricing before deployment.
