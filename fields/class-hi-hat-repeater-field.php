<?php
/**
 * Class Hi_Hat_Repeater_Field.
 */
class Hi_Hat_Repeater_Field extends acf_field {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = 'hi_hat_repeater';
		$this->label    = __( 'Hi-Hat Repeater', 'hi-hat-repeater' );
		$this->category = 'basic';
		$this->defaults = array(
			'sub_fields' => array(),
		);
		parent::__construct();
	}

	/**
	 * Render the field settings.
	 *
	 * @param array $field The field settings.
	 */
	public function render_field_settings( $field ) {
		// No settings needed.
	}

	/**
	 * Render the field.
	 *
	 * @param array $field The field settings.
	 */
	public function render_field( $field ) {
		$values = is_array( $field['value'] ) ? $field['value'] : array( '' );
		?>
		<div class="acf-hi-hat-repeater" data-name="<?php echo esc_attr( $field['name'] ); ?>">
			<div class="hi-hat-repeater-items-wrap">
				<?php foreach ( $values as $i => $value ) : ?>
					<div class="hi-hat-repeater-item">
						<textarea name="<?php echo esc_attr( $field['name'] ); ?>[]"><?php echo esc_textarea( $value ); ?></textarea>
						<a href="#" class="hi-hat-repeater-remove-button button button-small"><?php _e( 'Remove', 'hi-hat-repeater' ); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
			<a href="#" class="hi-hat-repeater-add-button button button-primary"><?php _e( 'Add', 'hi-hat-repeater' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function input_admin_enqueue_scripts() {
		$version = '1.0.0';

		wp_enqueue_script( 'hi-hat-repeater', HI_HAT_REPEATER_URL . 'js/input.js', array( 'jquery' ), $version, true );
		wp_enqueue_style( 'hi-hat-repeater', HI_HAT_REPEATER_URL . 'css/input.css', array(), $version );
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

		// Remove empty values.
		$value = array_filter( $value, 'strlen' );

		return $value;
	}
}
