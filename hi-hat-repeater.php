<?php
/**
 * Plugin Name: Hi-Hat Repeater
 * Plugin URI:  https://github.com/yourusername/hi-hat-repeater
 * Description: An Advanced Custom Fields add-on for a repeater-like field with multiple text areas. Includes GraphQL support.
 * Version:     1.0.0
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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if ACF is active.
if ( ! class_exists( 'acf' ) ) {
	return;
}



// Define constants.
define( 'HI_HAT_REPEATER_URL', plugin_dir_url( __FILE__ ) );
define( 'HI_HAT_REPEATER_PATH', plugin_dir_path( __FILE__ ) );

// Register GraphQL support for WPGraphQL ACF as early as possible
add_action( 'wpgraphql/acf/init', function() {
	if ( function_exists( 'register_graphql_acf_field_type' ) ) {
		register_graphql_acf_field_type( 'hi_hat_repeater', [
			'graphql_type' => [ 'list_of' => 'String' ]
		] );
	}
} );

// Handle field value resolution using the standard WPGraphQL ACF filter
add_filter( 'wpgraphql/acf/field_value', function( $value, $field_config, $root, $node_id ) {
	if ( isset( $field_config['type'] ) && $field_config['type'] === 'hi_hat_repeater' ) {
		// Get the raw field value from ACF using the field name
		$raw_value = get_field( $field_config['name'], $node_id, false );
		if ( is_array( $raw_value ) ) {
			// Filter out empty strings and ensure all values are strings
			return array_map( 'strval', array_filter( $raw_value, 'strlen' ) );
		}
		return [];
	}
	return $value;
}, 10, 4 );

// Ensure ACF field groups show in GraphQL
add_filter( 'wpgraphql/acf/should_field_group_show_in_graphql', function( $should, $acf_field_group ) {
	// Force field groups to show in GraphQL if they contain hi_hat_repeater fields
	if ( isset( $acf_field_group['fields'] ) && is_array( $acf_field_group['fields'] ) ) {
		foreach ( $acf_field_group['fields'] as $field ) {
			if ( isset( $field['type'] ) && $field['type'] === 'hi_hat_repeater' ) {
				return true;
			}
		}
	}
	return $should;
}, 10, 2 );

/**
 * Include the new field type.
 *
 * @param int $version The ACF version.
 */
function include_field_types_hi_hat_repeater( $version ) {
	include_once 'fields/class-hi-hat-repeater-field.php';

	// Register the field type with ACF
	acf_register_field_type( new Hi_Hat_Repeater_Field() );

}

add_action( 'acf/include_field_types', 'include_field_types_hi_hat_repeater' );


