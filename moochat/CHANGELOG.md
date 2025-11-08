# Changelog for block_moochat

All notable changes to this project will be documented in this file.

## [Version 1.2] - 2025-11-08

### Changed
- **Migrated from legacy AJAX to External Services API** (Moodle plugin requirement)
  - Created new External Service class (`classes/external/send_message.php`)
  - Added service definition file (`db/services.php`)
  - Updated JavaScript to use Moodle's `Ajax.call()` instead of jQuery `$.ajax()`
  - Removed deprecated `chat_service.php` file
  - Improved security and compatibility with Moodle standards

### Added
- **Privacy API Implementation** (GDPR compliance)
  - Created Privacy Provider class (`classes/privacy/provider.php`)
  - Implements metadata provider to describe stored user data
  - Implements plugin provider for data export and deletion
  - Implements userlist provider for bulk operations
  - Added privacy-related language strings

- **Language String Improvements**
  - Moved all hard-coded JavaScript strings to language files
  - Added `chatcleared` string for chat reset confirmation
  - Added `confirmclear` string for clear button dialog
  - Added six privacy metadata strings for GDPR compliance

### Fixed
- Hard-coded language strings in JavaScript replaced with proper `get_string()` calls
- JavaScript now properly receives language strings from PHP
- All user-facing text now translatable and follows Moodle coding standards

### Technical Details
- Updated `amd/src/chat.js` to accept language strings as parameter
- Modified `block_moochat.php` to pass language strings to JavaScript module
- External Service uses `core_external` namespace for Moodle 4.5 compatibility
- Privacy Provider handles user data in `block_moochat_usage` table

## [Version 1.1] - 2025-10-30

### Added
- Rate limiting functionality
- Avatar support with configurable sizes
- Enhanced configuration options

## [Version 1.0] - Initial Release

### Added
- Basic AI chatbot block functionality
- Integration with Moodle's core AI system
- Configurable system prompts
- Message history management
- Clear chat functionality
