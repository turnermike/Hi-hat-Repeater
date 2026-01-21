<?php
/**
 * Class Hi_Hat_Repeater_Field_Textarea.
 */
class Hi_Hat_Repeater_Field_Textarea extends Hi_Hat_Repeater_Field_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = 'hi_hat_repeater_textarea';
		$this->label    = __( 'Hi-hat Repeater - Textarea', 'hi-hat-repeater' );
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
		$values = is_array( $field['value'] ) ? $field['value'] : array( '' );
		$data_attrs = sprintf(
			' data-name="%s" data-field-type="textarea"',
			esc_attr( $field['name'] )
		);
		?>
		<div class="acf-hi-hat-repeater"<?php echo $data_attrs; ?>>
			<div class="hi-hat-repeater-items-wrap">
				<?php foreach ( $values as $i => $value ) : ?>
					<div class="hi-hat-repeater-item">
						<?php $this->render_textarea_item( $field, $value, $i ); ?>
						<a href="#" class="hi-hat-repeater-remove-button button button-small" style="margin-top:14px"><?php esc_html_e( 'Remove', 'hi-hat-repeater' ); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
			<a href="#" class="hi-hat-repeater-add-button button button-primary" style="margin-top: 28px;"><?php esc_html_e( 'Add', 'hi-hat-repeater' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Render a textarea item for the repeater.
	 *
	 * @param array  $field The field settings.
	 * @param string $value The current value.
	 * @param int    $index The item index.
	 */
	private function render_textarea_item( $field, $value, $index ) {
		$textarea_id = 'hi_hat_textarea_' . $field['name'] . '_' . $index . '_' . uniqid();
		?>
		<textarea class="hi-hat-repeater-textarea large-text" rows="4" name="<?php echo esc_attr( $field['name'] ); ?>[]" id="<?php echo esc_attr( $textarea_id ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
		<?php
	}
}
