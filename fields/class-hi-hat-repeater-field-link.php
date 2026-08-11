<?php
/**
 * Class Hi_Hat_Repeater_Field_Link.
 */
class Hi_Hat_Repeater_Field_Link extends Hi_Hat_Repeater_Field_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = 'hi_hat_repeater_link';
		$this->label    = __( 'Hi-hat Repeater - Link', 'hi-hat-repeater' );
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
		$values = is_array( $field['value'] ) ? $field['value'] : array();
		if ( empty( $values ) ) {
			$values = array( array( 'title' => '', 'url' => '', 'target' => '_self' ) );
		}

		$data_attrs = sprintf(
			' data-name="%s" data-field-type="link"',
			esc_attr( $field['name'] )
		);
		?>
		<div class="acf-hi-hat-repeater"<?php echo $data_attrs; ?> >
			<div class="hi-hat-repeater-items-wrap">
				<?php foreach ( $values as $i => $value ) : ?>
					<div class="hi-hat-repeater-item">
						<?php $this->render_link_item( $field, $value, $i ); ?>
						<a href="#" class="hi-hat-repeater-remove-button button button-small" style="margin-top:14px"><?php esc_html_e( 'Remove', 'hi-hat-repeater' ); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
			<a href="#" class="hi-hat-repeater-add-button button button-primary" style="margin-top: 28px;"><?php esc_html_e( 'Add', 'hi-hat-repeater' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Render a link item for the repeater.
	 *
	 * @param array $field The field settings.
	 * @param array $value The current value.
	 * @param int   $index The item index.
	 */
	private function render_link_item( $field, $value, $index ) {
		$link_field = array(
			'type'         => 'link',
			'name'         => $field['name'] . '[' . $index . ']',
			'value'        => $value,
			'prefix'       => '',
			'parent'       => $field,
			'key'          => isset( $field['key'] ) ? $field['key'] . '_link_' . $index : 'hi_hat_repeater_link_' . $index,
			'label'        => '',
			'instructions' => '',
		);

		acf_render_field( $link_field );
	}

	/**
	 * Update the field value.
	 *
	 * @param mixed $value   The value to update.
	 * @param int   $post_id The post ID.
	 * @param array $field   The field settings.
	 * @return mixed
	 */
	public function update_value( $value, $post_id, $field ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$result = array();
		foreach ( $value as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$url = isset( $item['url'] ) ? trim( $item['url'] ) : '';
			if ( empty( $url ) ) {
				continue;
			}

			$sanitized_url = esc_url_raw( $url );
			if ( empty( $sanitized_url ) ) {
				continue;
			}

			$result[] = array(
				'title'  => isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '',
				'url'    => $sanitized_url,
				'target' => isset( $item['target'] ) && $item['target'] === '_blank' ? '_blank' : '_self',
			);
		}

		return $result;
	}
}
