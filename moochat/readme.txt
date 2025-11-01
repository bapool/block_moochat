==============================================================================
MooChat Block for Moodle
==============================================================================

Author: Brian A. Pool
Organization: National Trail Local Schools
Email: bpool@nationaltrail.us
Version: 1.1
License: GNU GPL v3 or later
Moodle Required: 4.0 or higher

==============================================================================
DESCRIPTION
==============================================================================

MooChat Block transforms your Moodle course sidebar into an interactive AI 
assistant. Teachers can create multiple chatbot blocks, each with its own 
personality, avatar, and purpose - from subject tutors to historical figures.

Unlike the MooChat Activity module, the block version is designed for sidebar 
placement and provides quick, always-visible access to AI assistance without 
leaving the current page.

==============================================================================
KEY FEATURES
==============================================================================

- Custom AI Personalities - Define unique system prompts for each chatbot
- Avatar Support - Upload custom images with adjustable sizing (32-128px)
- Multiple Instances - Add different chatbots to the same course
- Compact Design - Optimized for sidebar placement
- Rate Limiting - Prevent AI resource abuse with configurable question limits 
  (per hour or per day)
- Server-Side Tracking - Students cannot bypass limits by clearing chat
- Auto-Cleanup - Usage records automatically purge after 7 days
- User-Friendly Interface - Clean chat interface with message history
- Message Formatting - Long AI responses formatted with paragraphs and bullet 
  points for readability
- Stacked or Inline Layout - Avatar displays inline for small sizes, stacked 
  for large sizes

==============================================================================
SYSTEM REQUIREMENTS
==============================================================================

- Moodle 4.0 or higher
- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.2+ or PostgreSQL 9.6+
- Moodle Core AI Subsystem configured with at least one AI provider

IMPORTANT: This plugin requires Moodle's core AI subsystem to be configured. 
You must have at least one AI provider enabled (OpenAI, Anthropic, Azure 
OpenAI, or local models via Ollama). No external API keys or services beyond 
what Moodle provides are required.

==============================================================================
INSTALLATION
==============================================================================

OPTION 1: Manual Installation
------------------------------
1. Download the plugin package
2. Extract the contents to: [moodleroot]/blocks/moochat
3. Navigate to: Site Administration > Notifications
4. Click "Upgrade Moodle database now"
5. Follow the on-screen instructions

OPTION 2: Via Moodle Plugin Installer
--------------------------------------
1. Go to: Site Administration > Plugins > Install plugins
2. Upload the plugin ZIP file
3. Click "Install plugin from the ZIP file"
4. Follow the on-screen instructions

POST-INSTALLATION:
------------------
1. Ensure Moodle's AI subsystem is configured:
   Site Administration > AI > AI providers
2. Enable and configure at least one AI provider
3. Test the configuration with a sample block

==============================================================================
CONFIGURATION
==============================================================================

ADDING A MOOCHAT BLOCK:

1. Navigate to a course
2. Turn editing on
3. Click "Add a block"
4. Select "MooChat" from the list
5. The block appears in the sidebar with default settings

BLOCK CONFIGURATION:

To configure a MooChat block:
1. Click the gear icon on the block
2. Select "Configure MooChat block"

Configuration Options:

General Settings:
- Chatbot Name - Custom name for your AI assistant
  Examples: "Math Tutor", "Benjamin Franklin", "Code Helper"

Avatar:
- Avatar Image - Upload an image to represent your chatbot
- Avatar Size - Choose from Small (32x32) to Extra Large (128x128)
  Note: Sizes 32-64px display inline with title, 128px displays stacked

AI Personality:
- System Prompt - Define how the AI should behave
  Examples:
  - "You are a helpful math tutor who explains concepts step-by-step"
  - "You are Shakespeare, speaking in poetic language about literature"
  - "You are a friendly science teacher who uses real-world examples"

Rate Limiting:
- Enable Rate Limiting - Turn on to prevent abuse
- Rate Limit Period - Choose "Per Hour" or "Per Day"
- Maximum Questions - Set number of questions allowed per period
  Example: "10 questions per day" means students get 10 questions, 
  then must wait until tomorrow

Additional Settings:
- Maximum Messages per Session - Legacy setting, use Rate Limiting instead
- Creativity Level (Temperature) - Control response variety (0.1-0.9)
  Lower = More focused, Higher = More creative

==============================================================================
USAGE
==============================================================================

FOR TEACHERS:

Creating Multiple Chatbots:
- Add multiple MooChat blocks to provide different AI assistants
- Example setup:
  - "Math Helper" - Explains math concepts
  - "Writing Coach" - Helps with essays and grammar
  - "Historical Figure" - Role-plays as a person from history

Block Visibility:
- Click the eye icon to hide/show blocks
- Configure block visibility by role if needed
- Move blocks to different regions using drag-and-drop

Best Practices:
- Write clear, specific system prompts
- Use descriptive names so students know the purpose
- Upload appropriate avatars for visual identification
- Set rate limits to manage AI usage
- Test chatbot responses before enabling for students

FOR STUDENTS:

Using a MooChat Block:
1. Find the MooChat block in the course sidebar
2. Read the block title to understand its purpose
3. Type your question in the text box
4. Press Enter or click "Send"
5. Wait a few seconds for the AI response
6. Continue the conversation as needed
7. Click "Clear Chat" to start over (doesn't reset question limit!)

Understanding Rate Limits:
- If rate limiting is enabled, you'll see "Questions remaining: X"
- Each question you ask decreases the count
- When you reach 0, you must wait until the time period expires
- Clearing the chat does NOT give you more questions
- The counter resets after the configured time period (hour or day)

Tips for Better Responses:
- Ask clear, specific questions
- Provide context when needed
- Be patient - responses take a few seconds
- If unsure about something, ask for clarification

==============================================================================
TEACHER VIEW
==============================================================================

When a teacher edits a course, MooChat blocks display differently:

TEACHER EDIT MODE:
- Shows "Teacher Configuration" instead of chat interface
- Displays current system prompt
- Provides link to edit configuration
- Helps teachers review settings without chatting

NORMAL VIEW (not editing):
- Teachers see the same chat interface as students
- Can test and verify chatbot responses
- Can use the chat normally

==============================================================================
COMPARING BLOCK VS. ACTIVITY
==============================================================================

MOOCHAT BLOCK:
✓ Sidebar placement
✓ Always visible
✓ Quick access
✓ Multiple instances per course
✓ Compact design
✗ No section content integration
✗ Fixed size (sidebar width)

MOOCHAT ACTIVITY:
✓ Center of course page
✓ Inline or separate page options
✓ Adjustable sizes (small/medium/large)
✓ Section content integration
✓ Can reference course materials
✗ Only one per activity
✗ Requires click to access (if separate page)

RECOMMENDATION: Use blocks for quick Q&A and activities for comprehensive 
course tutoring with content integration.

==============================================================================
TROUBLESHOOTING
==============================================================================

AI Not Responding:
- Check that Moodle AI subsystem is configured
- Verify at least one AI provider is enabled and working
- Check PHP error logs for API connection issues
- Ensure network allows connections to AI provider

Rate Limit Not Working:
- Verify rate limiting is enabled in block settings
- Check database table exists: [prefix]_block_moochat_usage
- Students may need to wait until the time period expires
- Clear Moodle cache after configuration changes

Avatar Not Displaying:
- Verify image was uploaded successfully
- Check file permissions on moodledata directory
- Try re-uploading the image
- Ensure image is in a supported format (JPG, PNG, GIF)

Block Not Appearing:
- Verify block is installed: Site Administration > Plugins > Blocks
- Check course format supports blocks
- Ensure editing is turned on when adding
- Check block visibility settings

Formatting Issues:
- Clear Moodle cache: Site Administration > Development > Purge all caches
- Clear browser cache and refresh page
- Check that JavaScript is enabled in browser
- Try a different browser to isolate the issue

==============================================================================
DATABASE TABLES
==============================================================================

This plugin creates one table:

[prefix]_block_moochat_usage  
- Tracks student usage for rate limiting per block instance
- Links to block instance ID and user ID
- Automatically cleaned of records older than 7 days
- Stores: messagecount, firstmessage, lastmessage timestamps

==============================================================================
TECHNICAL NOTES
==============================================================================

Avatar Handling:
- Files stored in Moodle file system
- Context: CONTEXT_BLOCK
- File area: 'avatar'
- Maximum 1 file per block instance
- Supported formats: All image types

Rate Limiting:
- Server-side enforcement prevents bypass
- Uses timestamps to track time periods
- Automatic reset when period expires
- Cleanup runs on every chat request (minimal overhead)

JavaScript:
- AMD module: blocks/moochat/chat
- jQuery-based for compatibility
- AJAX calls to chat_service.php
- Maintains conversation history in browser session
- Formats responses dynamically

==============================================================================
SUPPORT & DEVELOPMENT
==============================================================================

Author: Brian A. Pool
Organization: National Trail Local Schools
Email: bpool@nationaltrail.us

For issues, feature requests, or contributions:
- Report bugs via email to the author
- Feature suggestions welcome
- Contributions and improvements appreciated

==============================================================================
LICENSE
==============================================================================

This program is free software: you can redistribute it and/or modify it under 
the terms of the GNU General Public License as published by the Free Software 
Foundation, either version 3 of the License, or (at your option) any later 
version.

This program is distributed in the hope that it will be useful, but WITHOUT 
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with 
this program. If not, see <https://www.gnu.org/licenses/>.

==============================================================================
ACKNOWLEDGMENTS
==============================================================================

Special thanks to:
- Anthropic for AI assistance in development
- The Moodle community for ongoing support and inspiration
- National Trail Local Schools for supporting innovative educational technology

==============================================================================
CHANGELOG
==============================================================================

Version 1.1 (2025-10-30)
- Removed unused settings (AI model selection, Ollama endpoint)
- Cleaned up settings page to only show per-instance configuration
- Enhanced message formatting for better readability
- Improved avatar display with size options

Version 1.0 (2025-10-28)
- Initial release
- Core chat functionality with AI integration
- Multiple block instances support
- Rate limiting with server-side tracking
- Avatar support with flexible sizing
- Message formatting for long responses
- Auto-cleanup of usage data
- Teacher configuration view

==============================================================================
RELATED PLUGINS
==============================================================================

MooChat Activity Module:
A companion plugin that provides full-featured AI chat as a course activity
with section content integration and flexible display modes. Search for
"MooChat Activity" by Brian A. Pool.

Both plugins can be used together or independently based on your needs.

==============================================================================