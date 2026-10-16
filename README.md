# Hi-hat Repeater

Custom ACF repeater fields for text, images, WYSIWYG, and grouped content.

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

This README is the GitHub project documentation. WordPress.org plugin metadata,
installation instructions, and release notes are maintained in [readme.txt](./readme.txt).

## Features

- **Four Field Types**: **Hi-hat Repeater - Textarea**, **Hi-hat Repeater - Image**, **Hi-hat Repeater - WYSIWYG**, and **Hi-hat Repeater - Group**
- **Textarea Repeater**: Stores an ordered list of plain-text entries
- **Image Repeater**: Stores an ordered list of media items
- **WYSIWYG Repeater**: Stores an ordered list of rich-text entries
- **Group Repeater**: Repeater-like field with configurable sub-fields
- **WPGraphQL ACF Integration**: Registers GraphQL fields for textarea, image, and group types
- **Field Group Support**: Works within ACF field groups
- **WordPress Standards**: Follows WordPress coding standards and best practices
- **ACF Integration**: Fully compatible with Advanced Custom Fields
- **Responsive Design**: Clean, responsive admin interface
- **Development Tools**: Linting and build tooling

## Requirements

- WordPress 5.0 or higher (tested up to 7.1)
- PHP 7.4 or higher
- Advanced Custom Fields (ACF) 5.0.0 or higher
- WPGraphQL ACF 2.0.0 or higher (for GraphQL features)

## Installation

### From WordPress Plugin Directory

1. Go to **Plugins > Add New** in your WordPress admin
2. Search for "Hi-Hat Repeater"
3. Click **Install Now** and then **Activate**

### Manual Installation

Keep only one active installation. The distribution ZIP uses the lowercase
`hi-hat-repeater` directory. On case-sensitive servers, an existing
`Hi-hat-Repeater` directory is a separate plugin copy; deactivate it before
activating the lowercase installation.

1. Download the attached `hi-hat-repeater.zip` from the latest [GitHub release](https://github.com/turnermike/hi-hat-repeater/releases), not the automatically generated source archives
2. Upload the `hi-hat-repeater` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress

### Development Installation

```bash
# Clone the repository
git clone https://github.com/turnermike/hi-hat-repeater.git
cd hi-hat-repeater

# Install PHP dependencies
composer install

# Install Node.js dependencies and build assets
npm install

# Run development tools
npm run lint           # Lint CSS and JS files
```

## Usage

Hi-hat Repeater does not add a separate WordPress admin settings page. After
activation, its field types appear in the field type options when you add or edit
a field in an ACF field group.

### Basic Usage

1. After activation, create or edit a Field Group in ACF
2. Add a new field and select one of the Hi-hat Repeater field types:
  - **Hi-hat Repeater - Textarea** for plain text entries
  - **Hi-hat Repeater - Image** for media items
  - **Hi-hat Repeater - WYSIWYG** for rich-text entries
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
├── composer.json                # PHP dependencies
├── composer.lock                # Composer lock file
├── package.json                 # Node.js dependencies
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

- Verify plugin behavior manually in WordPress with ACF

## Creating a GitHub Release

### 1. Prepare the release

1. Update `Version` in [hi-hat-repeater.php](./hi-hat-repeater.php) and `Stable tag`
   in [readme.txt](./readme.txt) to the same version. Keep the version in
   [package.json](./package.json) in sync as well.
2. Add release notes to the changelogs in this README and
   [readme.txt](./readme.txt).
3. Update compatibility metadata only to versions actually tested.
4. Commit and push the release changes. Use `git status` to confirm the working
   tree is clean before tagging.

### 2. Build and test the installable ZIP

From the project root, run:

```bash
bash ./build.sh
unzip -t dist/hi-hat-repeater.zip
unzip -l dist/hi-hat-repeater.zip
```

The build requires Bash, `rsync`, and `zip`. It **clears the contents of `dist/`**
before writing `dist/hi-hat-repeater.zip`. Keep anything you need to preserve
outside that directory.

The ZIP should contain one top-level `hi-hat-repeater/` directory with the main
plugin file, field classes, CSS, JavaScript, [readme.txt](./readme.txt), and
[LICENSE](./LICENSE). Development files are excluded using
[.distignore](./.distignore).

Install the ZIP on a test WordPress site, run Plugin Check on that installation,
and verify adding, removing, saving, and reloading each field type. Check both
ACF-present and ACF-missing behavior and compatibility with native ACF repeaters.

### 3. Tag and publish

For example, for version `1.3.0` (replace it with the release version):

```bash
git tag -a v1.3.0 -m "Release 1.3.0"
git push origin v1.3.0
```

Do not recreate or move a tag that has already been published.

1. Open [GitHub Releases](https://github.com/turnermike/hi-hat-repeater/releases)
   and choose **Draft a new release**.
2. Select the tag you pushed and title the release `Hi-hat Repeater 1.3.0`.
3. Write release notes describing changes, fixes, and any upgrade instructions.
4. Attach `dist/hi-hat-repeater.zip` as a release asset.
5. Publish the release, then download the attached ZIP to confirm it is the
   intended installable package.

Alternatively, with the GitHub CLI authenticated:

```bash
gh release create v1.3.0 dist/hi-hat-repeater.zip \
  --repo turnermike/hi-hat-repeater \
  --verify-tag \
  --title "Hi-hat Repeater 1.3.0" \
  --notes "Describe the changes and any upgrade instructions here."
```

GitHub's automatic **Source code** archives are not the distribution package.
Publishing a GitHub release also does not publish to WordPress.org: submit the
tested ZIP separately for initial review, and follow WordPress.org's SVN release
process once the plugin is approved.

## Changelog

### 1.3.0
- Added WYSIWYG repeater fields and improved editor lifecycle handling.
- Fixed ACF load-order handling and duplicate GraphQL registrations.
- Scoped group-row controls to avoid interfering with native ACF repeaters.
- Added standard admin notices and automatic deactivation when ACF is missing.
- Added distribution packaging for WordPress plugin releases.

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
- Asset build system with PostCSS and Terser
- Comprehensive development tooling

## Support

- **Documentation**: [Project README](https://github.com/turnermike/hi-hat-repeater#readme)
- **Issues**: [GitHub Issues](https://github.com/turnermike/hi-hat-repeater/issues)
- **WordPress.org**: [Support Forum](https://wordpress.org/support/plugin/hi-hat-repeater/)

## License

This plugin is licensed under the GPL-2.0-or-later License. See [LICENSE](LICENSE) for details.

## Credits

- Built for Advanced Custom Fields
- Compatible with WPGraphQL
- Developed with ❤️ for the WordPress community
