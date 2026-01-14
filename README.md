# Hi-Hat Repeater

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/hi-hat-repeater.svg)](https://wordpress.org/plugins/hi-hat-repeater/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/hi-hat-repeater.svg)](https://wordpress.org/plugins/hi-hat-repeater/)
[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/r/hi-hat-repeater.svg)](https://wordpress.org/plugins/hi-hat-repeater/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![PHPStan](https://github.com/yourusername/hi-hat-repeater/actions/workflows/phpstan.yml/badge.svg)](https://github.com/yourusername/hi-hat-repeater/actions/workflows/phpstan.yml)

An Advanced Custom Fields (ACF) add-on that provides a repeater-like field with multiple text areas. Perfect for content that needs multiple text entries in a structured format.

## Features

- **Simple Repeater Field**: Add multiple text areas with easy add/remove functionality
- **GraphQL Support**: Automatically registers GraphQL fields for your custom field types
- **WordPress Standards**: Follows WordPress coding standards and best practices
- **ACF Integration**: Seamlessly integrates with Advanced Custom Fields
- **Responsive Design**: Clean, responsive interface that works across devices

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Advanced Custom Fields (ACF) 5.0.0 or higher
- WPGraphQL (optional, for GraphQL features)

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

# Install Node.js dependencies (if any)
npm install

# Run development tools
composer run lint
composer run test
```

## Usage

### Basic Usage

1. After activation, create or edit a Field Group in ACF
2. Add a new field and select "Hi-Hat Repeater" from the field type dropdown
3. Configure your field settings as needed
4. Save the field group

### In Templates

```php
<?php
// Get the repeater field values
$repeater_values = get_field('your_field_name');

// Check if values exist
if ($repeater_values) {
    echo '<ul>';
    foreach ($repeater_values as $value) {
        echo '<li>' . esc_html($value) . '</li>';
    }
    echo '</ul>';
}
?>
```

### GraphQL Usage

When using with WPGraphQL, the field is automatically available in your GraphQL schema:

```graphql
{
  post(id: 1) {
    yourFieldName
  }
}
```

## Development

### Project Structure

```
hi-hat-repeater/
├── hi-hat-repeater.php          # Main plugin file
├── fields/
│   └── class-hi-hat-repeater-field.php  # ACF field class
├── css/
│   └── input.css                # Admin styles
├── js/
│   └── input.js                 # Admin JavaScript
├── .github/
│   └── workflows/               # GitHub Actions
├── composer.json                # PHP dependencies
├── package.json                 # Node.js dependencies
├── phpcs.xml                    # Code standards
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

# Build assets (if applicable)
npm run build
npm run watch
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

### 1.0.0
- Initial release
- Basic repeater functionality
- GraphQL support
- WordPress coding standards compliance

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