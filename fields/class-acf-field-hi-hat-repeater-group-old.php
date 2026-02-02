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
            $debug_file = WP_CONTENT_DIR . '/hi-hat-debug.log';
            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Group field __construct called\n", FILE_APPEND);

            // vars
            $this->name = 'hi_hat_repeater_group';
            $this->label = __('Hi-hat Repeater - Group', 'acf');
            $this->category = 'layout';
            $this->defaults = array(
                'sub_fields'    => array(),
                'min'            => 0,
                'max'            => 0,
                'layout'         => 'table',
                'button_label'    => __('Add Row', 'acf'),
                'collapsed'        => ''
            );


            // actions
            add_action('wp_ajax_acf/fields/repeater/add_row',            array($this, 'ajax_add_row'));
            add_action('wp_ajax_nopriv_acf/fields/repeater/add_row',    array($this, 'ajax_add_row'));


            // filters
            add_filter('acf/validate_field',                        array($this, 'validate_any_field'));


            // do not delete!
            parent::__construct();

            // Add filter to intercept value updates
            add_filter("acf/update_value/type={$this->name}", array($this, 'update_value'), 10, 3);
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
            $debug_file = WP_CONTENT_DIR . '/hi-hat-debug.log';
            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - render_field called for: " . $field['name'] . "\n", FILE_APPEND);
            file_put_contents($debug_file, "Field key: " . $field['key'] . "\n", FILE_APPEND);
            file_put_contents($debug_file, "Field value: " . print_r($field['value'], true) . "\n", FILE_APPEND);

            // ensure uploader assets are available for sub fields (image, file, etc.)
            if (function_exists('acf_enqueue_uploader')) {
                acf_enqueue_uploader();
            }
            if (function_exists('wp_enqueue_media')) {
                wp_enqueue_media();
            }

            // bail early if no sub fields
            if (empty($field['sub_fields'])) {
                return;
            }

            // load values into sub fields
            foreach ($field['sub_fields'] as &$sub_field) {

                if (isset($field['value'][$sub_field['key']])) {
                    $sub_field['value'] = $field['value'][$sub_field['key']];
                } elseif (isset($sub_field['default_value'])) {
                    $sub_field['value'] = $sub_field['default_value'];
                }

                // update prefix to allow for nested values
                $sub_field['prefix'] = $field['name'];

                // restore required
                if (!empty($field['required'])) {
                    $sub_field['required'] = 0;
                }
            }

            // render
            if ($field['layout'] == 'table') {
                $this->render_field_table($field);
            } else {
                $this->render_field_block($field);
            }
        }


        function render_field_block($field)
        {
            $label_placement = ($field['layout'] == 'block') ? 'top' : 'left';

            echo '<div class="acf-fields -' . esc_attr($label_placement) . ' -border">';

            foreach ($field['sub_fields'] as $sub_field) {
                acf_render_field_wrap($sub_field);
            }

            echo '</div>';
        }


        function render_field_table($field)
        {
?>
            <table class="acf-table">
                <thead>
                    <tr>
                        <?php foreach ($field['sub_fields'] as $sub_field) :

                            // prepare field (allow sub fields to be removed)
                            $sub_field = acf_prepare_field($sub_field);

                            // bail early if no field
                            if (!$sub_field) {
                                continue;
                            }

                            $atts = array();
                            $atts['class'] = 'acf-th';
                            $atts['data-name'] = isset($sub_field['_name']) ? $sub_field['_name'] : $sub_field['name'];
                            $atts['data-type'] = $sub_field['type'];
                            $atts['data-key'] = $sub_field['key'];

                            if ($sub_field['wrapper']['width']) {
                                $atts['data-width'] = $sub_field['wrapper']['width'];
                                $atts['style'] = 'width: ' . $sub_field['wrapper']['width'] . '%;';
                            }

                        ?>
                            <th <?php echo acf_esc_attrs($atts); ?>>
                                <?php acf_render_field_label($sub_field); ?>
                                <?php acf_render_field_instructions($sub_field); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr class="acf-row">
                        <?php foreach ($field['sub_fields'] as $sub_field) {
                            acf_render_field_wrap($sub_field, 'td');
                        } ?>
                    </tr>
                </tbody>
            </table>
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

        ?>
            <tr class="acf-row<?php if ($i === 'acfcloneindex') {
                                    echo ' acf-clone';
                                } ?>">

                <?php if ($show_order): ?>
                    <td class="acf-row-handle order" title="<?php _e('Drag to reorder', 'acf'); ?>">
                        <span><?php echo $i === 'acfcloneindex' ? '' : ($i + 1); ?></span>
                    </td>
                <?php endif; ?>

                <?php foreach ($sub_fields as $sub_field): ?>
                    <?php
                    $sub_field_key = $sub_field['key'];
                    // Set prefix BEFORE calling acf_prepare_field
                    $sub_field['prefix'] = $field['name'] . '[' . $i . ']';
                    $sub_field['value'] = isset($row[$sub_field_key]) ? $row[$sub_field_key] : null;
                    // Now prepare the field with the correct prefix set
                    $sub_field = acf_prepare_field($sub_field);
                    acf_render_field_wrap($sub_field, 'td');
                    ?>
                <?php endforeach; ?>

                <?php if ($show_remove): ?>
                    <td class="acf-row-handle remove">
                        <a href="#" class="acf-icon -minus" data-event="remove-row"></a>
                    </td>
                <?php endif; ?>

            </tr>
        <?php
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
                'fields'     => $field['sub_fields'],
                'parent'     => $field['ID'],
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
                'label'            => __('Minimum Rows', 'acf'),
                'instructions'    => '',
                'type'            => 'number',
                'name'            => 'min',
                'placeholder'    => '0',
            ));


            // max
            acf_render_field_setting($field, array(
                'label'            => __('Maximum Rows', 'acf'),
                'instructions'    => '',
                'type'            => 'number',
                'name'            => 'max',
                'placeholder'    => '0',
            ));


            // layout
            acf_render_field_setting($field, array(
                'label'            => __('Layout', 'acf'),
                'instructions'    => '',
                'type'            => 'radio',
                'name'            => 'layout',
                'layout'        => 'horizontal',
                'choices'        => array(
                    'table'            => __('Table', 'acf'),
                    'block'            => __('Block', 'acf'),
                    'row'            => __('Row', 'acf')
                )
            ));


            // button_label
            acf_render_field_setting($field, array(
                'label'            => __('Button Label', 'acf'),
                'instructions'    => '',
                'type'            => 'text',
                'name'            => 'button_label',
                'placeholder'    => __('Add Row', 'acf')
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
            // bail early if no sub fields
            if (empty($field['sub_fields'])) {
                return $value;
            }

            // modify names
            $field = $this->prepare_field_for_db($field);

            // load sub fields
            $value = array();

            foreach ($field['sub_fields'] as $sub_field) {
                $value[$sub_field['key']] = acf_get_value($post_id, $sub_field);
            }

            return $value;
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

            // modify names
            $field = $this->prepare_field_for_db($field);

            foreach ($field['sub_fields'] as $sub_field) {
                $sub_value = acf_extract_var($value, $sub_field['key']);
                $sub_value = acf_format_value($sub_value, $post_id, $sub_field);

                $sub_field_name = isset($sub_field['_name']) ? $sub_field['_name'] : $sub_field['name'];
                $value[$sub_field_name] = $sub_value;
            }

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
            $debug_file = WP_CONTENT_DIR . '/hi-hat-debug.log';
            file_put_contents($debug_file, "\n=== HI-HAT GROUP UPDATE_VALUE CALLED ===" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            file_put_contents($debug_file, 'Field name: ' . $field['name'] . "\n", FILE_APPEND);
            file_put_contents($debug_file, 'Post ID: ' . $post_id . "\n", FILE_APPEND);
            file_put_contents($debug_file, 'Value: ' . print_r($value, true) . "\n", FILE_APPEND);
            // bail early if no value
            if (!acf_is_array($value)) {
                return null;
            }

            // bail early if no sub fields
            if (empty($field['sub_fields'])) {
                return null;
            }

            // modify names
            $field = $this->prepare_field_for_db($field);

            foreach ($field['sub_fields'] as $sub_field) {
                $v = false;

                if (isset($value[$sub_field['key']])) {
                    $v = $value[$sub_field['key']];
                } elseif (isset($value[$sub_field['_name']])) {
                    $v = $value[$sub_field['_name']];
                } else {
                    continue;
                }

                acf_update_value($v, $post_id, $sub_field);
            }

            file_put_contents($debug_file, "=== END HI-HAT GROUP UPDATE_VALUE ===\n\n", FILE_APPEND);

            return '';
        }


        function prepare_field_for_db($field)
        {
            if (empty($field['sub_fields'])) {
                return $field;
            }

            foreach ($field['sub_fields'] as &$sub_field) {
                $sub_field_name = isset($sub_field['_name']) ? $sub_field['_name'] : $sub_field['name'];
                $sub_field['name'] = $field['name'] . '_' . $sub_field_name;
            }

            return $field;
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
            if (empty($value)) {
                return $valid;
            }

            if (empty($field['sub_fields'])) {
                return $valid;
            }

            foreach ($field['sub_fields'] as $sub_field) {
                $k = $sub_field['key'];

                if (!isset($value[$k])) {
                    continue;
                }

                if (!empty($field['required'])) {
                    $sub_field['required'] = 1;
                }

                acf_validate_value($value[$k], $sub_field, "{$input}[{$k}]");
            }

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
                'width'    => '',
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