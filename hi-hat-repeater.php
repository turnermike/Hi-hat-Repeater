<?php

/**
 * Plugin Name: Hi-Hat Repeater
 * Plugin URI:  https://github.com/yourusername/hi-hat-repeater
 * Description: An Advanced Custom Fields add-on for a repeater-like field with multiple text areas. Includes GraphQL support.
 * Version:     1.2.1
 * Author:      Your Name
 * Author URI:  https://github.com/yourusername
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hi-hat-repeater
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * ACF: 5.0.0
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
	exit;
}

// Check if ACF is active.
if (! class_exists('acf')) {
	return;
}



// Define constants.
define('HI_HAT_REPEATER_URL', plugin_dir_url(__FILE__));
define('HI_HAT_REPEATER_PATH', plugin_dir_path(__FILE__));

// Register GraphQL support for WPGraphQL ACF as early as possible
add_action('wpgraphql/acf/init', function () {
	if (function_exists('register_graphql_acf_field_type')) {
		register_graphql_acf_field_type('hi_hat_repeater_wysiwyg', [
			'graphql_type' => ['list_of' => 'String']
		]);
		register_graphql_acf_field_type('hi_hat_repeater_textarea', [
			'graphql_type' => ['list_of' => 'String']
		]);
		register_graphql_acf_field_type('hi_hat_repeater_image', [
			'graphql_type' => ['list_of' => 'MediaItem']
		]);
		// Register the hi_hat_repeater_group field type
		// This behaves like ACF's repeater with sub_fields
		register_graphql_acf_field_type('hi_hat_repeater_group', [
			'graphql_type' => function ($field_config, $acf_field_type) {
				$sub_field_group = $field_config->get_acf_field();
				$parent_type     = $field_config->get_parent_graphql_type_name($sub_field_group);
				$field_name      = $field_config->get_graphql_field_name();

				// Use WPGraphQL's Utils class if available, otherwise format manually
				if (class_exists('\WPGraphQL\Utils\Utils')) {
					$type_name = \WPGraphQL\Utils\Utils::format_type_name($parent_type . ' ' . $field_name);
				} else {
					// Fallback: simple format conversion
					$type_name = ucfirst($parent_type) . ucfirst($field_name);
				}

				$sub_field_group['graphql_type_name']  = $type_name;
				$sub_field_group['graphql_field_name'] = $type_name;
				$sub_field_group['locations']          = null;

				// Register the sub fields as a GraphQL type
				$field_config->get_registry()->register_acf_field_groups_to_graphql(
					[$sub_field_group]
				);

				return ['list_of' => $type_name];
			}
		]);
	}
});

// Handle field value resolution using the standard WPGraphQL ACF filter
add_filter('wpgraphql/acf/field_value', function ($value, $field_config, $root, $node_id) {
	if (isset($field_config['type'])) {
		if ($field_config['type'] === 'hi_hat_repeater_wysiwyg' || $field_config['type'] === 'hi_hat_repeater_textarea') {
			// Get the raw field value from ACF using the field name
			$raw_value = get_field($field_config['name'], $node_id, false);
			if (is_array($raw_value)) {
				// Filter out empty values, including empty HTML
				return array_filter($raw_value, function ($item) {
					if (! is_string($item)) {
						return false;
					}

					// Strip HTML tags and check if there's actual content
					$content = wp_strip_all_tags($item);
					$content = trim($content);

					return strlen($content) > 0;
				});
			}
			return [];
		} elseif ($field_config['type'] === 'hi_hat_repeater_image') {
			// Get the raw field value from ACF using the field name
			$raw_value = get_field($field_config['name'], $node_id, false);
			if (! is_array($raw_value)) {
				return [];
			}

			$result = array();
			foreach ($raw_value as $attachment_id) {
				$attachment_id = absint($attachment_id);
				if (! $attachment_id) {
					continue;
				}

				$attachment = get_post($attachment_id);
				if (! $attachment || 'attachment' !== $attachment->post_type) {
					continue;
				}

				// Get image data
				$image_url = wp_get_attachment_image_url($attachment_id, 'full');
				$image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
				$image_title = get_the_title($attachment_id);

				// Get additional metadata
				$metadata = wp_get_attachment_metadata($attachment_id);
				$width = isset($metadata['width']) ? $metadata['width'] : null;
				$height = isset($metadata['height']) ? $metadata['height'] : null;

				$result[] = array(
					'id'     => $attachment_id,
					'url'    => $image_url ? $image_url : '',
					'alt'    => $image_alt ? $image_alt : '',
					'title'  => $image_title ? $image_title : '',
					'width'  => $width,
					'height' => $height,
				);
			}

			return $result;
		}
	}
	return $value;
}, 10, 4);

// Ensure ACF field groups show in GraphQL
add_filter('wpgraphql/acf/should_field_group_show_in_graphql', function ($should, $acf_field_group) {
	// Force field groups to show in GraphQL if they contain hi-hat repeater fields
	if (isset($acf_field_group['fields']) && is_array($acf_field_group['fields'])) {
		foreach ($acf_field_group['fields'] as $field) {
			if (isset($field['type']) && ($field['type'] === 'hi_hat_repeater_wysiwyg' || $field['type'] === 'hi_hat_repeater_textarea' || $field['type'] === 'hi_hat_repeater_image')) {
				return true;
			}
		}
	}
	return $should;
}, 10, 2);

/**
 * Include the new field types.
 *
 * @param int $version The ACF version.
 */
function include_field_types_hi_hat_repeater($version)
{
	include_once 'fields/class-hi-hat-repeater-field-base.php';
	include_once 'fields/class-hi-hat-repeater-field-wysiwyg.php';
	include_once 'fields/class-hi-hat-repeater-field-textarea.php';
	include_once 'fields/class-hi-hat-repeater-field-image.php';
	// include_once 'fields/class-hi-hat-repeater-field-group.php';
	include_once 'fields/class-acf-field-hi-hat-repeater-group.php';

	// Register the field types with ACF
	acf_register_field_type(new Hi_Hat_Repeater_Field_Wysiwyg());
	acf_register_field_type(new Hi_Hat_Repeater_Field_Textarea());
	acf_register_field_type(new Hi_Hat_Repeater_Field_Image());
	// acf_register_field_type( new Hi_Hat_Repeater_Field_Group() );
}

add_action('acf/include_field_types', 'include_field_types_hi_hat_repeater');
/**
 * Enqueue admin styles for the hi-hat repeater field.
 */
function enqueue_hi_hat_repeater_admin_styles()
{
	wp_enqueue_style(
		'hi-hat-repeater-admin',
		HI_HAT_REPEATER_URL . 'css/hi-hat-repeater-admin.css',
		array(),
		'1.0.0'
	);
}

add_action('admin_enqueue_scripts', 'enqueue_hi_hat_repeater_admin_styles');