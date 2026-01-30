<?php
/**
 * Base class for Hi-hat Repeater fields.
 */
abstract class Hi_Hat_Repeater_Field_Base extends acf_field {

	/**
	 * Update the field value.
	 *
	 * @param mixed $value   The value to be updated.
	 * @param int   $post_id The post ID.
	 * @param array $field   The field settings.
	 * @return mixed
	 */
	public function update_value( $value, $post_id, $field ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		// Filter out empty values, including empty HTML
		$value = array_filter( $value, function( $item ) {
			// Check if the value is empty or contains only whitespace/HTML entities
			if ( ! is_string( $item ) ) {
				return false;
			}

			// Strip HTML tags and check if there's actual content
			$content = wp_strip_all_tags( $item );
			$content = trim( $content );

			return strlen( $content ) > 0;
		} );

		return $value;
	}

	/**
	 * Get the GraphQL type for this field.
	 *
	 * @return string|array
	 */
	public function get_graphql_type() {
		return [ 'list_of' => 'String' ];
	}

	/**
	 * Resolve the field value for GraphQL.
	 *
	 * @param mixed $value The field value.
	 * @param mixed $post_id The post ID.
	 * @param array $field The field settings.
	 * @return mixed
	 */
	public function resolve_graphql_value( $value, $post_id, $field ) {
		if ( is_array( $value ) ) {
			return array_filter( $value, function( $item ) {
				if ( ! is_string( $item ) ) {
					return false;
				}

				// Strip HTML tags and check if there's actual content
				$content = wp_strip_all_tags( $item );
				$content = trim( $content );

				return strlen( $content ) > 0;
			} );
		}
		return [];
	}

	/**
	 * Render the field settings.
	 *
	 * @param array $field The field settings.
	 */
	public function render_field_settings( $field ) {
		// No settings needed for base field types.
	}
}
