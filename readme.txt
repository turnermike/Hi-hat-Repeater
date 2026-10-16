=== Hi-hat Repeater ===
Contributors: turnermike
Tags: advanced-custom-fields, repeater, textarea, image, wysiwyg
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom repeater fields for ACF: textareas, images, rich-text editors, and grouped content.

== Description ==

Hi-hat Repeater adds custom repeater-style fields to Advanced Custom Fields (ACF):

* Textarea repeater for an ordered list of text entries.
* Image repeater for an ordered list of media items.
* WYSIWYG repeater for rich-text content.
* Group repeater for rows with configurable sub-fields.

WPGraphQL ACF integration is available for supported field types.

== Installation ==

1. Install and activate Advanced Custom Fields.
2. Upload the `hi-hat-repeater` directory to `/wp-content/plugins/`, or install the plugin ZIP through the WordPress Plugins screen.
3. Activate Hi-hat Repeater through the Plugins screen.
4. Create or edit an ACF field group and add a Hi-hat Repeater field type.

If Advanced Custom Fields is not active, Hi-hat Repeater deactivates itself on the next admin request and displays a standard admin notice. This also applies if ACF is deactivated later.

== Frequently Asked Questions ==

= Where is the plugin settings page? =

Hi-hat Repeater does not add a separate WordPress admin settings page. After activation, its field types appear in the field type options when you add or edit a field in an ACF field group.

= Which field types are included? =

Textarea, image, WYSIWYG, and group repeater fields.

= Is WPGraphQL required? =

No. WPGraphQL ACF integration is optional.

== Changelog ==

= 1.3.0 =
* Added the WYSIWYG repeater field.
* Improved repeater editor initialization and cleanup.
* Register fields regardless of plugin load order and show a notice when ACF is missing.
* Limit group-row controls to Hi-hat Repeater fields to avoid interfering with native ACF repeaters.
* Remove duplicate GraphQL field type registrations.
* Show the missing ACF requirement notice in both site admin and Network Admin.
