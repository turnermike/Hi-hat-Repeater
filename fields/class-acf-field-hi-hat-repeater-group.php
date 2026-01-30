<?php

if (! class_exists('acf_field_hi_hat_repeater_group')) :

	class acf_field_hi_hat_repeater_group extends acf_field
	{


		/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

		function __construct()
		{

			// vars
			$this->name = 'hi_hat_repeater_group';
			$this->label = __('Hi-hat Repeater - Group', 'acf');
			$this->category = 'layout';
			$this->defaults = array(
				'sub_fields'	=> array(),
				'min'			=> 0,
				'max'			=> 0,
				'layout' 		=> 'table',
				'button_label'	=> __('Add Row', 'acf'),
				'collapsed'		=> ''
			);


			// actions
			add_action('wp_ajax_acf/fields/repeater/add_row',			array($this, 'ajax_add_row'));
			add_action('wp_ajax_nopriv_acf/fields/repeater/add_row',	array($this, 'ajax_add_row'));


			// filters
			add_filter('acf/validate_field',						array($this, 'validate_any_field'));


			// do not delete!
			parent::__construct();
		}


		/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/12
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/

		function load_field($field)
		{

			// min / max
			$field['min'] = (int) $field['min'];
			$field['max'] = (int) $field['max'];


			// sub_fields
			$field['sub_fields'] = acf_get_fields($field);


			// return
			return $field;
		}


		/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/12
	*/

		function render_field($field)
		{

			// vars
			$sub_fields = $field['sub_fields'];
			$show_order = true;
			$show_add = true;
			$show_remove = true;


			// bail early if no sub fields
			if (empty($sub_fields)) {

				return;
			}


			// value
			$value = is_array($field['value']) ? $field['value'] : array();


			// div
			$div = array(
				'class'		=> 'acf-repeater',
				'data-min'	=> $field['min'],
				'data-max'	=> $field['max']
			);


			// empty?
			if (empty($value)) {

				$div['class'] .= ' -empty';
			}


			// If there are less values than min, populate the extra values
			if ($field['min'] > count($value)) {

				$value = array_pad($value, $field['min'], array());
			}


			// If there are more values than max, remove the extra values
			if ($field['max'] > 0 && count($value) > $field['max']) {

				$value = array_slice($value, 0, $field['max']);
			}


			// setup values for row generation
			acf_setup_meta($value, 'hi_hat_repeater_group', true);

?>
			<div <?php acf_esc_attr_e($div); ?>>

				<div class="acf-input">

					<table class="acf-table">

						<thead>
							<tr>
								<?php if ($show_order): ?>
									<th class="acf-row-handle"></th>
								<?php endif; ?>

								<?php foreach ($sub_fields as $sub_field):

									// prepare field (allow sub fields to be loaded)
									$sub_field = acf_prepare_field($sub_field);

									// vars
									$atts = array();
									$atts['class'] = 'acf-th';
									$atts['data-key'] = $sub_field['key'];
									$atts['data-type'] = $sub_field['type'];
									$atts['data-name'] = $sub_field['name'];

									if ($sub_field['wrapper']['width']) {

										$atts['data-width'] = $sub_field['wrapper']['width'];
										$atts['style'] = 'width: ' . $sub_field['wrapper']['width'] . '%;';
									}

								?>
									<th <?php acf_esc_attr_e($atts); ?>>
										<?php acf_render_field_label($sub_field); ?>
										<?php acf_render_field_instructions($sub_field); ?>
									</th>
								<?php endforeach; ?>

								<?php if ($show_remove): ?>
									<th class="acf-row-handle"></th>
								<?php endif; ?>
							</tr>
						</thead>

						<tbody>
							<?php if (!empty($value)): ?>
								<?php foreach ($value as $i => $row): ?>
									<?php $this->render_row($field, $row, $i); ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>

					</table>

					<?php if ($show_add): ?>

						<div class="acf-actions">
							<a class="acf-button button button-primary" href="#" data-event="add-row"><?php echo acf_esc_html($field['button_label']); ?></a>
						</div>

					<?php endif; ?>

				</div>

				<script type="text-html" class="acf-clone">
					<?php $this->render_row($field, array(), 'acfcloneindex'); ?>
	</script>

			</div>
		<?php

		}


		/*
	*  render_row
	*
	*  This function will render a row of the repeater field
	*
	*  @type	function
	*  @date	13/04/2016
	*  @since	5.3.8
	*
	*  @param	$field (array)
	*  @param	$row (array)
	*  @param	$i (int)
	*  @return	n/a
	*/

		function render_row($field, $row, $i)
		{

			// vars
			$sub_fields = $field['sub_fields'];
			$show_order = true;
			$show_remove = true;


			// add row
			acf_add_meta(array(
				'type'	=> 'row',
				'key'	=> $field['key'],
				'i'		=> $i
			));

		?>
			<tr class="acf-row<?php if ($i === 'acfcloneindex') {
													echo ' acf-clone';
												} ?>">

				<?php if ($show_order): ?>
					<td class="acf-row-handle order" title="<?php _e('Drag to reorder', 'acf'); ?>">
						<span><?php echo $i + 1; ?></span>
					</td>
				<?php endif; ?>

				<?php foreach ($sub_fields as $sub_field): ?>
					<?php acf_render_field_wrap($sub_field); ?>
				<?php endforeach; ?>

				<?php if ($show_remove): ?>
					<td class="acf-row-handle remove">
						<a href="#" class="acf-icon -minus" data-event="remove-row"></a>
					</td>
				<?php endif; ?>

			</tr>
		<?php

			// remove row
			acf_remove_meta();
		}


		/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/12
	*
	*  @param	$field	- an array holding all the field's data
	*/

		function render_field_settings($field)
		{

			// vars
			$args = array(
				'fields'	 => $field['sub_fields'],
				'parent'	 => $field['ID'],
				'is_subfield' => true,
			);

		?>
			<div class="acf-field acf-field-setting-sub_fields" data-setting="<?php echo esc_attr($this->name); ?>" data-name="sub_fields">
				<div class="acf-label">
					<label><?php esc_html_e('Sub Fields', 'acf'); ?></label>
				</div>
				<div class="acf-input acf-input-sub">
					<?php

					acf_get_view('acf-field-group/fields', $args);

					?>
				</div>
			</div>
<?php


			// min
			acf_render_field_setting($field, array(
				'label'			=> __('Minimum Rows', 'acf'),
				'instructions'	=> '',
				'type'			=> 'number',
				'name'			=> 'min',
				'placeholder'	=> '0',
			));


			// max
			acf_render_field_setting($field, array(
				'label'			=> __('Maximum Rows', 'acf'),
				'instructions'	=> '',
				'type'			=> 'number',
				'name'			=> 'max',
				'placeholder'	=> '0',
			));


			// layout
			acf_render_field_setting($field, array(
				'label'			=> __('Layout', 'acf'),
				'instructions'	=> '',
				'type'			=> 'radio',
				'name'			=> 'layout',
				'layout'		=> 'horizontal',
				'choices'		=> array(
					'table'			=> __('Table', 'acf'),
					'block'			=> __('Block', 'acf'),
					'row'			=> __('Row', 'acf')
				)
			));


			// button_label
			acf_render_field_setting($field, array(
				'label'			=> __('Button Label', 'acf'),
				'instructions'	=> '',
				'type'			=> 'text',
				'name'			=> 'button_label',
				'placeholder'	=> __('Add Row', 'acf')
			));
		}


		/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/12
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in the database
	*/

		function load_value($value, $post_id, $field)
		{

			// bail early if no value
			if (empty($value)) {

				return $value;
			}


			// bail ealry if not numeric
			if (!is_numeric($value)) {

				return $value;
			}


			// vars
			$value = intval($value);
			$rows = array();


			// loop
			for ($i = 0; $i < $value; $i++) {

				// create empty row
				$rows[$i] = array();


				// loop through sub fields
				if (!empty($field['sub_fields'])) {

					foreach ($field['sub_fields'] as $sub_field) {

						// get sub field name
						$sub_field_name = $sub_field['name'];


						// get value
						$sub_value = acf_get_value($post_id, $field['name'] . '_' . $i . '_' . $sub_field_name);


						// add value
						$rows[$i][$sub_field['key']] = $sub_value;
					}
				}
			}


			// return
			return $rows;
		}


		/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/12
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the formatted value
	*/

		function format_value($value, $post_id, $field)
		{

			// bail early if no value
			if (empty($value)) {

				return false;
			}


			// bail ealry if not array
			if (!is_array($value)) {

				return false;
			}


			// loop over rows
			foreach ($value as $i => &$row) {

				// loop over sub fields
				foreach ($field['sub_fields'] as $sub_field) {

					// get sub field key
					$sub_field_key = $sub_field['key'];


					// bail ealry if not set
					if (!isset($row[$sub_field_key])) {

						continue;
					}


					// get sub field value
					$sub_value = $row[$sub_field_key];


					// format value
					$sub_value = acf_format_value($sub_value, $post_id, $sub_field);


					// update value
					$row[$sub_field['name']] = $sub_value;
				}
			}


			// return
			return $value;
		}


		/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/12
	*
	*  @param	$value - the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in the database
	*/

		function update_value($value, $post_id, $field)
		{

			// vars
			$sub_fields = $field['sub_fields'];
			$old_value = acf_get_value($post_id, $field['key'], true);
			$old_value = is_numeric($old_value) ? intval($old_value) : 0;
			$new_value = 0;


			// update sub fields
			if (!empty($value)) {

				// remove dummy field
				unset($value['acfcloneindex']);

				// loop through rows
				$i = -1;
				foreach ($value as $row) {
					$i++;

					// loop through sub fields
					foreach ($sub_fields as $sub_field) {

						// get sub field key
						$sub_field_key = $sub_field['key'];


						// bail early if value doesnt exist
						if (!isset($row[$sub_field_key])) {

							continue;
						}


						// get sub field value
						$sub_value = $row[$sub_field_key];


						// update sub field value
						acf_update_value($sub_value, $field['name'] . '_' . $i . '_' . $sub_field['name'], $post_id);
					}
				}

				$new_value = $i + 1;
			}


			// remove old rows
			if ($old_value > $new_value) {

				for ($i = $new_value; $i < $old_value; $i++) {

					foreach ($sub_fields as $sub_field) {

						acf_delete_value($post_id, $field['name'] . '_' . $i . '_' . $sub_field['name']);
					}
				}
			}


			// save false for empty value
			if (empty($new_value)) {

				$new_value = 0;
			}


			// return
			return $new_value;
		}


		/*
	*  validate_value
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate at all times.
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/

		function validate_value($valid, $value, $field, $input)
		{

			// vars
			$count = 0;


			// check if any value exists
			if (!empty($value)) {

				// remove clone row
				unset($value['acfcloneindex']);

				$count = count($value);
			}


			// validate required
			if ($field['required'] && !$count) {

				$valid = false;
			}


			// validate min
			$min = (int) $field['min'];
			if ($min && $count < $min) {

				// create error
				$error = __('Minimum rows reached ({$count} / {$min})', 'acf');
				$error = str_replace('{$count}', $count, $error);
				$error = str_replace('{$min}', $min, $error);


				// return
				return $error;
			}


			// find most rows in a sub field
			$value = acf_get_request_var($input);


			// validate children
			if ($count > 0) {

				// loop rows
				foreach ($value as $i => $row) {

					// loop sub fields
					foreach ($field['sub_fields'] as $sub_field) {

						// get sub field key
						$sub_field_key = $sub_field['key'];


						// bail ealry if not set
						if (!isset($row[$sub_field_key])) {

							continue;
						}


						// get sub field value
						$sub_value = $row[$sub_field_key];


						// validate
						acf_validate_value($sub_value, $sub_field, "{$input}[{$i}][{$sub_field_key}]");
					}
				}
			}


			// return
			return $valid;
		}


		/*
	*  validate_any_field
	*
	*  This function will add compatibility for the 'column_width' setting
	*
	*  @type	function
	*  @date	30/1/17
	*  @since	5.5.6
	*
	*  @param	$field (array)
	*  @return	$field
	*/

		function validate_any_field($field)
		{

			// bail ealry if not a sub field
			if (empty($field['parent'])) {

				return $field;
			}

			// normalize parent to array without triggering validation
			$parent = $field['parent'];
			if (!is_array($parent)) {
				if (function_exists('acf_is_local_field') && acf_is_local_field($parent)) {
					$parent = acf_get_local_field($parent);
				} else {
					$parent = acf_get_raw_field($parent);
				}
			}
			if (empty($parent) || !is_array($parent)) {

				return $field;
			}

			// bail ealry if not hi-hat repeater
			if (empty($parent['type']) || $parent['type'] !== $this->name) {

				return $field;
			}


			// defaults
			$field['wrapper'] = wp_parse_args($field['wrapper'], array(
				'width'	=> '',
			));


			// convert column_width to width
			if (isset($field['column_width'])) {

				$field['wrapper']['width'] = $field['column_width'];
				unset($field['column_width']);
			}


			// return
			return $field;
		}
	}


	// initialize
	new acf_field_hi_hat_repeater_group();


// class_exists check
endif;

?>