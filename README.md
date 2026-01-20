# Hi-Hat Repeater

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

An Advanced Custom Fields (ACF) add-on that provides two repeater field types: **Hi-hat Repeater - WYSIWYG** for rich text editing and **Hi-hat Repeater - Textarea** for plain text entries.

## Features

- **Two Field Types**: Choose **Hi-hat Repeater - WYSIWYG** for rich text or **Hi-hat Repeater - Textarea** for plain text
- **Full WordPress WYSIWYG**: WYSIWYG field type provides the default WordPress editor with Visual/Text tabs, TinyMCE toolbar, and media buttons
- **Rich Text Editing**: Full TinyMCE toolbar with formatting, links, media, and all standard editor features
- **Media Upload Integration**: Built-in media library access for each WYSIWYG editor instance
- **WPGraphQL ACF Integration**: Automatically registers GraphQL fields that return arrays of HTML strings
- **Field Group Support**: Works seamlessly within ACF field groups
- **WordPress Standards**: Follows WordPress coding standards and best practices
- **ACF Integration**: Fully compatible with Advanced Custom Fields
- **Responsive Design**: Clean, responsive admin interface
- **Asset Optimization**: Minified CSS and JavaScript for production use
- **Development Tools**: Comprehensive linting, testing, and build tools

## Requirements

- WordPress 5.0 or higher (tested up to 6.4)
- PHP 7.4 or higher
- Advanced Custom Fields (ACF) 5.0.0 or higher
- WPGraphQL ACF 2.0.0 or higher (for GraphQL features)

## Installation

### From WordPress Plugin Directory

1. Go to **Plugins > Add New** in your WordPress admin
2. Search for "Hi-Hat Repeater"
3. Click **Install Now** and then **Activate**

### Manual Installation

1. Download the latest release from [GitHub](https://github.com/yourusername/hi-hat-repeater/releases)
2. Upload the `hi-hat-repeater` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress

### Development Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/hi-hat-repeater.git
cd hi-hat-repeater

# Install PHP dependencies
composer install

# Install Node.js dependencies and build assets
npm install

# Run development tools
composer run check      # Run lint, analyze, and test
npm run lint           # Lint CSS and JS files
```

## Usage

### Basic Usage

1. After activation, create or edit a Field Group in ACF
2. Add a new field and select either:
   - **Hi-hat Repeater - WYSIWYG** for rich text editing with full WordPress editor
   - **Hi-hat Repeater - Textarea** for plain text entries
3. Save the field group

### In Templates

Both field types return an array of strings. Use **Hi-hat Repeater - Textarea** for plain text and **Hi-hat Repeater - WYSIWYG** for HTML.

```php
<?php
$repeater_values = get_field('your_field_name');

if ($repeater_values && is_array($repeater_values)) {
    echo '<div class="content-blocks">';
    foreach ($repeater_values as $value) {
        // For Textarea field type: use esc_html() for plain text
        // For WYSIWYG field type: use wp_kses_post() for HTML
        echo '<div class="content-block">' . wp_kses_post($value) . '</div>';
    }
    echo '</div>';
}
?>
```

### GraphQL Usage

With WPGraphQL ACF, both field types are available in your GraphQL schema. They return an array of strings (plain text for **Hi-hat Repeater - Textarea**, HTML for **Hi-hat Repeater - WYSIWYG**):

```graphql
{
  post(id: 1) {
    yourFieldGroupName {
      yourFieldName
    }
  }
}
```

Example response:
```json
{
  "data": {
    "post": {
      "yourFieldGroupName": {
        "yourFieldName": ["<p>First <strong>rich text</strong> item</p>", "<p>Second item with <a href='#'>link</a></p>", "<p>Third item</p>"]
      }
    }
  }
}
```

## Development

### Project Structure

```
hi-hat-repeater/
├── hi-hat-repeater.php          # Main plugin file
├── fields/
│   ├── class-hi-hat-repeater-field-base.php      # Base field class
│   ├── class-hi-hat-repeater-field-wysiwyg.php   # WYSIWYG field type
│   └── class-hi-hat-repeater-field-textarea.php  # Textarea field type
├── css/
│   └── input.css                # Admin styles
├── js/
│   └── input.js                 # Admin JavaScript
├── tests/
│   ├── bootstrap.php            # Test bootstrap
│   └── HiHatRepeaterFieldTest.php # PHPUnit tests
├── composer.json                # PHP dependencies
├── composer.lock                # Composer lock file
├── package.json                 # Node.js dependencies
├── phpcs.xml                    # PHP CodeSniffer config
├── phpstan.neon                 # PHPStan config
├── phpunit.xml                  # PHPUnit config
├── postcss.config.js            # PostCSS config
├── LICENSE                      # License file
├── WARP.md                      # Development notes
└── README.md                    # This file
```

### Development Commands

```bash
# Install dependencies
composer install
npm install

# Run code quality checks
composer run lint       # PHP_CodeSniffer
composer run test       # PHPUnit tests
composer run analyze    # PHPStan static analysis
composer run check      # Run all checks

# Build assets
npm run build           # Build production assets
npm run dev             # Build development assets
npm run watch           # Watch for changes

# Lint and fix code
npm run lint            # Lint CSS and JS
npm run lint:fix        # Fix linting issues

# Clean build artifacts
npm run clean
```

### Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Make your changes and commit: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Submit a pull request

### Code Standards

This plugin follows WordPress coding standards. Before submitting a pull request:

- Run `composer run lint` to check PHP code standards
- Run `composer run analyze` for static analysis
- Ensure all tests pass with `composer run test`

## Changelog

### 1.2.1
- **Bug Fix**: Resolved deprecated function warning for editor IDs with brackets
- Manual editor HTML structure to avoid wp_editor() ID restrictions
- Proper ACF field submission with array naming while using clean editor IDs
- Enhanced editor initialization for both existing and cloned editors

### 1.2.0
- **Major Feature**: Complete WordPress WYSIWYG editor integration
- Each repeater item now has a full WordPress editor with Visual/Text tabs
- Proper TinyMCE initialization and cleanup for cloned editors
- Media upload integration for each editor instance
- Simplified and robust JavaScript for editor lifecycle management

### 1.1.0
- **Major Feature**: Upgraded from textarea to WYSIWYG editors
- Full TinyMCE integration with media upload support
- Enhanced JavaScript for proper WYSIWYG editor lifecycle management
- Updated field value processing to handle HTML content
- Improved GraphQL resolvers for HTML content filtering

### 1.0.0
- Initial release
- Basic repeater functionality with add/remove textareas
- Full WPGraphQL ACF integration
- Automatic GraphQL schema registration
- WordPress coding standards compliance
- PHPUnit test suite
- Asset build system with PostCSS and Terser
- PHPStan static analysis support
- Comprehensive development tooling

## Support

- **Documentation**: [GitHub Wiki](https://github.com/yourusername/hi-hat-repeater/wiki)
- **Issues**: [GitHub Issues](https://github.com/yourusername/hi-hat-repeater/issues)
- **WordPress.org**: [Support Forum](https://wordpress.org/support/plugin/hi-hat-repeater/)

## License

This plugin is licensed under the GPL-2.0-or-later License. See [LICENSE](LICENSE) for details.

## Credits

- Built for Advanced Custom Fields
- Compatible with WPGraphQL
- Developed with ❤️ for the WordPress community