# Hi-Hat Repeater

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

An Advanced Custom Fields (ACF) add-on that provides custom repeater-style field types for structured content: **Hi-hat Repeater - Textarea**, **Hi-hat Repeater - Image**, and **Hi-hat Repeater - Group** (repeater with sub-fields).

## Features

- **Three Field Types**: **Hi-hat Repeater - Textarea**, **Hi-hat Repeater - Image**, and **Hi-hat Repeater - Group**
- **Textarea Repeater**: Stores an ordered list of plain-text entries
- **Image Repeater**: Stores an ordered list of media items
- **Group Repeater**: Repeater-like field with configurable sub-fields
- **WPGraphQL ACF Integration**: Registers GraphQL fields for textarea, image, and group types
- **Field Group Support**: Works within ACF field groups
- **WordPress Standards**: Follows WordPress coding standards and best practices
- **ACF Integration**: Fully compatible with Advanced Custom Fields
- **Responsive Design**: Clean, responsive admin interface
- **Development Tools**: Linting, testing, and build tooling

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
2. Add a new field and select one of the Hi-hat Repeater field types:
  - **Hi-hat Repeater - Textarea** for plain text entries
  - **Hi-hat Repeater - Image** for media items
  - **Hi-hat Repeater - Group** for repeater rows with sub-fields
3. Save the field group

### In Templates

Textarea fields return an array of strings. Image fields return an array of attachment IDs (or GraphQL media objects when using WPGraphQL). Group fields return an array of rows with sub-field values.

```php
<?php
$repeater_values = get_field('your_field_name');

if ($repeater_values && is_array($repeater_values)) {
    echo '<div class="content-blocks">';
    foreach ($repeater_values as $value) {
      echo '<div class="content-block">' . esc_html($value) . '</div>';
    }
    echo '</div>';
}
?>
```

### GraphQL Usage

With WPGraphQL ACF, textarea and image fields are available in your GraphQL schema.

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
        "yourFieldName": ["First item", "Second item", "Third item"]
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
│   ├── class-hi-hat-repeater-field-textarea.php  # Textarea field type
│   ├── class-hi-hat-repeater-field-image.php     # Image field type
│   └── class-acf-field-hi-hat-repeater-group.php # Group field type
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

### 1.2.2
- **Change**: Removed Hi-hat Repeater WYSIWYG field type
- **Compatibility**: Focus on Textarea, Image, and Group field types
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