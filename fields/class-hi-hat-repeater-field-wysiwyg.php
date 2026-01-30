<?php

/**
 * Class Hi_Hat_Repeater_Field_Wysiwyg.
 */
class Hi_Hat_Repeater_Field_Wysiwyg extends Hi_Hat_Repeater_Field_Base
{

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->name     = 'hi_hat_repeater_wysiwyg';
		$this->label    = __('Hi-hat Repeater - WYSIWYG', 'hi-hat-repeater');
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
	public function render_field($field)
	{
		if (function_exists('wp_enqueue_editor')) {
			wp_enqueue_editor();
		}
		acf_enqueue_uploader();

		$values = is_array($field['value']) ? $field['value'] : array('');
?>
		<div class="acf-hi-hat-repeater" data-name="<?php echo esc_attr($field['name']); ?>" data-field-type="wysiwyg">
			<div class="hi-hat-repeater-items-wrap">
				<?php foreach ($values as $i => $value) : ?>
					<div class="hi-hat-repeater-item" style="margin-bottom: 20px;">
						<?php $this->render_wysiwyg_item($field, $value, $i); ?>
						<a href="#" class="hi-hat-repeater-remove-button button button-small"><?php esc_html_e('Remove', 'hi-hat-repeater'); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
			<a href="#" class="hi-hat-repeater-add-button button button-primary" style="margin-top: 28px;"><?php esc_html_e('Add', 'hi-hat-repeater'); ?></a>
		</div>
<?php
	}

	/**
	 * Render a WYSIWYG item for the repeater.
	 *
	 * @param array  $field The field settings.
	 * @param string $value The current value.
	 * @param int    $index The item index.
	 */
	private function render_wysiwyg_item($field, $value, $index)
	{
		$editor_id = 'hi_hat_editor_' . sanitize_html_class($field['name']) . '_' . $index . '_' . uniqid();
		echo $this->get_wysiwyg_item_markup($field, $value, $editor_id, $index);
	}

	/**
	 * Build the editor markup string so we can reuse it for templates.
	 *
	 * @param array  $field     Field settings.
	 * @param string $value     Current value.
	 * @param string $editor_id Editor ID to render.
	 * @return string
	 */
	private function get_wysiwyg_item_markup($field, $value, $editor_id, $index)
	{
		ob_start();
		wp_editor($value, $editor_id, array(
			'textarea_name' => $field['name'] . '[' . $index . ']',
			'textarea_rows' => 10,
			'media_buttons' => true,
			'teeny'         => false,
			'tinymce'       => true,
			'quicktags'     => true,
		));
		return ob_get_clean();
	}
}
