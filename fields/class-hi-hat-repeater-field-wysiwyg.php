<?php
/**
 * Class Hi_Hat_Repeater_Field_Wysiwyg.
 */
class Hi_Hat_Repeater_Field_Wysiwyg extends Hi_Hat_Repeater_Field_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = 'hi_hat_repeater_wysiwyg';
		$this->label    = __( 'Hi-hat Repeater - WYSIWYG', 'hi-hat-repeater' );
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
		if ( function_exists( 'wp_enqueue_editor' ) ) {
			wp_enqueue_editor();
		}
		acf_enqueue_uploader();

		$values     = is_array( $field['value'] ) ? $field['value'] : array( '' );
		$template_id = 'hi_hat_wysiwyg_template_' . sanitize_html_class( $field['name'] );
		$data_attrs = sprintf(
			' data-name="%s" data-field-type="wysiwyg" data-wysiwyg-template="%s"',
			esc_attr( $field['name'] ),
			esc_attr( $template_id )
		);
		?>
		<div class="acf-hi-hat-repeater"<?php echo $data_attrs; ?>>
			<div class="hi-hat-repeater-items-wrap">
				<?php foreach ( $values as $i => $value ) : ?>
					<div class="hi-hat-repeater-item">
						<?php $this->render_wysiwyg_item( $field, $value, $i ); ?>
						<a href="#" class="hi-hat-repeater-remove-button button button-small"><?php esc_html_e( 'Remove', 'hi-hat-repeater' ); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
			<a href="#" class="hi-hat-repeater-add-button button button-primary"><?php esc_html_e( 'Add', 'hi-hat-repeater' ); ?></a>
		</div>
		<script type="text/html" id="<?php echo esc_attr( $template_id ); ?>" class="hi-hat-repeater-template">
			<div class="hi-hat-repeater-item">
				<div id="wp-__EDITOR_ID__-wrap" class="wp-core-ui wp-editor-wrap tmce-active">
					<div id="wp-__EDITOR_ID__-editor-tools" class="wp-editor-tools hide-if-no-js">
						<?php if ( current_user_can( 'upload_files' ) ) : ?>
						<div id="wp-__EDITOR_ID__-media-buttons" class="wp-media-buttons">
							<?php do_action( 'media_buttons', '__EDITOR_ID__' ); ?>
						</div>
						<?php endif; ?>
						<div class="wp-editor-tabs">
							<button type="button" id="__EDITOR_ID__-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="__EDITOR_ID__"><?php esc_html_e( 'Visual', 'hi-hat-repeater' ); ?></button>
							<button type="button" id="__EDITOR_ID__-html" class="wp-switch-editor switch-html" data-wp-editor-id="__EDITOR_ID__"><?php esc_html_e( 'Text', 'hi-hat-repeater' ); ?></button>
						</div>
					</div>
					<div id="wp-__EDITOR_ID__-editor-container" class="wp-editor-container">
						<textarea class="wp-editor-area" rows="10" cols="40" name="<?php echo esc_attr( $field['name'] ); ?>[]" id="__EDITOR_ID__"></textarea>
					</div>
				</div>
				<a href="#" class="hi-hat-repeater-remove-button button button-small"><?php esc_html_e( 'Remove', 'hi-hat-repeater' ); ?></a>
			</div>
		</script>
		<?php
	}

	/**
	 * Render a WYSIWYG item for the repeater.
	 *
	 * @param array  $field The field settings.
	 * @param string $value The current value.
	 * @param int    $index The item index.
	 */
	private function render_wysiwyg_item( $field, $value, $index ) {
		$editor_id = 'hi_hat_editor_' . sanitize_html_class( $field['name'] ) . '_' . $index . '_' . uniqid();
		echo $this->get_wysiwyg_item_markup( $field, $value, $editor_id );
	}

	/**
	 * Build the editor markup string so we can reuse it for templates.
	 *
	 * @param array  $field     Field settings.
	 * @param string $value     Current value.
	 * @param string $editor_id Editor ID to render.
	 * @return string
	 */
	private function get_wysiwyg_item_markup( $field, $value, $editor_id ) {
		ob_start();
		wp_editor( $value, $editor_id, array(
			'textarea_name' => $field['name'] . '[]',
			'textarea_rows' => 10,
			'media_buttons' => true,
			'teeny'         => false,
			'tinymce'       => true,
			'quicktags'     => true,
		) );
		return ob_get_clean();
	}

}
