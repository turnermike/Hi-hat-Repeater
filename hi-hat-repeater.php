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

/**
 * Include the new field type.
 *
 * @param int $version The ACF version.
 */
function include_field_types_hi_hat_repeater( $version ) {
	include_once 'fields/class-hi-hat-repeater-field.php';
	new Hi_Hat_Repeater_Field();
}

add_action( 'acf/include_field_types', 'include_field_types_hi_hat_repeater' );

add_action( 'graphql_register_types', 'hi_hat_repeater_manual_graphql_registration' );

function hi_hat_repeater_manual_graphql_registration() {
    if ( ! function_exists( 'acf_get_field_groups' ) ) {
        return;
    }

    $field_groups = acf_get_field_groups();

    foreach ( $field_groups as $field_group ) {
        $fields = acf_get_fields( $field_group['ID'] );

        foreach ( $fields as $field ) {
            if ( $field['type'] === 'hi_hat_repeater' ) {
                $locations = $field_group['location'];

                foreach ( $locations as $location_group ) {
                    foreach ( $location_group as $location_rule ) {
                        if ( $location_rule['param'] === 'post_type' && $location_rule['operator'] === '==' ) {
                            $post_type = get_post_type_object( $location_rule['value'] );
                            if ( $post_type && ! empty( $post_type->graphql_single_name ) ) {
                                $type_name = $post_type->graphql_single_name;
                                $field_name = $field['name'];

                                register_graphql_field( $type_name, $field_name, [
                                    'type'        => ['list_of' => 'String'],
                                    'description' => $field['instructions'],
                                    'resolve'     => function( $post ) use ( $field_name ) {
                                        $value = get_field( $field_name, $post->ID, false );
                                        return is_array( $value ) ? $value : [];
                                    },
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
