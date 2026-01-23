<?php
/**
 * Class Hi_Hat_Repeater_Field_Image.
 */
class Hi_Hat_Repeater_Field_Image extends Hi_Hat_Repeater_Field_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = 'hi_hat_repeater_image';
		$this->label    = __( 'Hi-hat Repeater - Image', 'hi-hat-repeater' );
		$this->category = 'content';
		$this->defaults = array(
			'sub_fields' => array(),
		);
		parent::__construct();
	}

	/**
	 * Render the field.
	 *
	 * @param array $field The field settings.
	 */
	public function render_field( $field ) {
		acf_enqueue_uploader();
		
		// Ensure media library scripts are enqueued
		wp_enqueue_media();

		$values = is_array( $field['value'] ) ? $field['value'] : array();
		// Always ensure at least one empty item exists
		if ( empty( $values ) ) {
			$values = array( '' );
		}
		$data_attrs = sprintf(
			' data-name="%s" data-field-type="image"',
			esc_attr( $field['name'] )
		);
		?>
		<div class="acf-hi-hat-repeater"<?php echo $data_attrs; ?>>
			<div class="hi-hat-repeater-items-wrap">
				<?php foreach ( $values as $i => $value ) : ?>
					<div class="hi-hat-repeater-item">
						<?php $this->render_image_item( $field, $value, $i ); ?>
						<a href="#" class="hi-hat-repeater-remove-button button button-small" style="margin-top:14px"><?php esc_html_e( 'Remove', 'hi-hat-repeater' ); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
			<a href="#" class="hi-hat-repeater-add-button button button-primary" style="margin-top: 28px;"><?php esc_html_e( 'Add', 'hi-hat-repeater' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Render an image item for the repeater.
	 *
	 * @param array  $field The field settings.
	 * @param string $value The current value (attachment ID).
	 * @param int    $index The item index.
	 */
	private function render_image_item( $field, $value, $index ) {
		$input_id = 'hi_hat_image_' . $field['name'] . '_' . $index . '_' . uniqid();
		$attachment_id = ! empty( $value ) ? absint( $value ) : 0;
		$image_url = '';
		$image_alt = '';

		if ( $attachment_id ) {
			$image_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
			$image_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		}
		?>
		<div class="hi-hat-repeater-image-wrapper">
			<input type="hidden" class="hi-hat-repeater-image-input" name="<?php echo esc_attr( $field['name'] ); ?>[]" value="<?php echo esc_attr( $attachment_id ); ?>" id="<?php echo esc_attr( $input_id ); ?>" />
			<div class="hi-hat-repeater-image-preview" style="<?php echo $image_url ? '' : 'display:none;'; ?>">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px;" />
				<?php endif; ?>
			</div>
			<button type="button" class="button hi-hat-repeater-image-select-button">
				<?php echo $attachment_id ? esc_html__( 'Change Image', 'hi-hat-repeater' ) : esc_html__( 'Select Image', 'hi-hat-repeater' ); ?>
			</button>
			<button type="button" class="button hi-hat-repeater-image-remove-button" style="<?php echo $attachment_id ? '' : 'display:none;'; ?>">
				<?php esc_html_e( 'Remove Image', 'hi-hat-repeater' ); ?>
			</button>
		</div>
		<?php
	}

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

		// Filter out empty values and validate attachment IDs
		$value = array_filter( $value, function( $item ) {
			if ( empty( $item ) ) {
				return false;
			}

			$attachment_id = absint( $item );
			if ( ! $attachment_id ) {
				return false;
			}

			// Verify the attachment exists and is an image
			$attachment = get_post( $attachment_id );
			if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
				return false;
			}

			// Check if it's an image
			if ( ! wp_attachment_is_image( $attachment_id ) ) {
				return false;
			}

			return true;
		} );

		// Convert to integers
		return array_map( 'absint', $value );
	}

	/**
	 * Get the GraphQL type for this field.
	 *
	 * @return string|array
	 */
	public function get_graphql_type() {
		// Return a list of MediaItem objects
		return [ 'list_of' => 'MediaItem' ];
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
		if ( ! is_array( $value ) ) {
			return [];
		}

		$result = array();
		foreach ( $value as $attachment_id ) {
			$attachment_id = absint( $attachment_id );
			if ( ! $attachment_id ) {
				continue;
			}

			$attachment = get_post( $attachment_id );
			if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
				continue;
			}

			// Get image data
			$image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
			$image_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			$image_title = get_the_title( $attachment_id );

			// Get additional metadata
			$metadata = wp_get_attachment_metadata( $attachment_id );
			$width = isset( $metadata['width'] ) ? $metadata['width'] : null;
			$height = isset( $metadata['height'] ) ? $metadata['height'] : null;

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
