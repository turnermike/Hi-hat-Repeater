<?php

if (! defined('ABSPATH')) {
    exit;
}

if (!class_exists('Hi_Hat_Repeater_Field_Group_Table')) :

    class Hi_Hat_Repeater_Field_Group_Table extends acf_field
    {
        function __construct()
        {
            $this->name = 'hi_hat_repeater_group';
            $this->label = __('Hi-hat Repeater - Group', 'hi-hat-repeater');
            $this->category = 'layout';
            $this->defaults = array(
                'sub_fields' => array(),
                'min' => 0,
                'max' => 0,
                'layout' => 'table',
                'button_label' => __('Add Row', 'hi-hat-repeater'),
                'collapsed' => ''
            );

            parent::__construct();
        }

        function load_field($field)
        {
            $field['min'] = (int) $field['min'];
            $field['max'] = (int) $field['max'];
            $field['sub_fields'] = acf_get_fields($field);

            return $field;
        }

        function render_field($field)
        {
            // Prevent duplicate rendering - check if this field has already been output
            static $rendered_fields = array();
            $field_key = $field['key'] . '_' . get_the_ID();

            if (isset($rendered_fields[$field_key])) {
                return;
            }
            $rendered_fields[$field_key] = true;

            $sub_fields = $field['sub_fields'];
            $show_order = true;
            $show_add = true;
            $show_remove = true;

            if (empty($sub_fields)) {
                return;
            }

            $value = is_array($field['value']) ? $field['value'] : array();

            $div = array(
                'class' => 'acf-repeater hi-hat-repeater-group',
                'data-min' => $field['min'],
                'data-max' => $field['max']
            );

            if (empty($value)) {
                $div['class'] .= ' -empty';
            }

            // If there are less values than min, populate empty rows
            $min = (int) $field['min'];

            // Only add empty rows if min > 0
            if ($min > 0 && count($value) < $min) {
                for ($i = count($value); $i < $min; $i++) {
                    $value[] = array();
                }
            }

            // If no values and min is 0, don't add any rows
            // JavaScript will handle adding the first row when user clicks "Add Row"

            // Setup values
            acf_setup_meta($value, 'hi_hat_repeater_group', true);
?>
            <div <?php acf_esc_attr_e($div); ?>>
                <table class="acf-table">
                    <thead>
                        <tr>
                            <?php if ($show_order): ?>
                                <th class="acf-row-handle"></th>
                            <?php endif; ?>

                            <?php foreach ($sub_fields as $sub_field):
                                $sub_field = acf_prepare_field($sub_field);
                                if (!$sub_field) continue;

                                $atts = array();
                                $atts['class'] = 'acf-th';
                                $atts['data-key'] = $sub_field['key'];
                                $atts['data-type'] = $sub_field['type'];
                                $atts['data-name'] = $sub_field['_name'];

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
                        <a class="acf-button button button-primary" href="#" data-event="add-row"><?php echo esc_html($field['button_label']); ?></a>
                    </div>
                <?php endif; ?>

                <script type="text-html" class="acf-clone">
                    <?php $this->render_row($field, array(), 'acfcloneindex'); ?>
                </script>
            </div>
        <?php
            acf_reset_meta('hi_hat_repeater_group');
        }

        function render_row($field, $row, $i)
        {
            $sub_fields = $field['sub_fields'];
            $show_order = true;
            $show_remove = true;
        ?>
            <tr class="acf-row<?php if ($i === 'acfcloneindex') echo ' acf-clone'; ?>" data-id="<?php echo esc_attr($i); ?>">
                <?php if ($show_order): ?>
                    <td class="acf-row-handle order" title="<?php esc_attr_e('Drag to reorder', 'hi-hat-repeater'); ?>">
                        <?php if ($i !== 'acfcloneindex'): ?><?php echo esc_html((int) $i + 1); ?><?php endif; ?>
                    </td>
                <?php endif; ?>

                <?php foreach ($sub_fields as $sub_field): ?>
                    <?php
                    $sub_field_key = $sub_field['key'];
                    $sub_field['value'] = isset($row[$sub_field_key]) ? $row[$sub_field_key] : '';
                    $sub_field['prefix'] = $field['name'] . '[' . $i . ']';
                    ?>
                    <td class="acf-field" data-name="<?php echo esc_attr($sub_field['_name']); ?>" data-type="<?php echo esc_attr($sub_field['type']); ?>" data-key="<?php echo esc_attr($sub_field['key']); ?>">
                        <?php acf_render_field($sub_field); ?>
                    </td>
                <?php endforeach; ?>

                <?php if ($show_remove): ?>
                    <td class="acf-row-handle remove">
                        <a class="acf-icon -minus small" href="#" data-event="remove-row"></a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php
        }

        function render_field_settings($field)
        {
            // Render the Sub Fields section
            $args = array(
                'fields' => isset($field['sub_fields']) ? $field['sub_fields'] : array(),
                'parent' => $field['ID'],
            );
        ?>
            <div class="acf-field acf-field-setting-sub_fields" data-setting="sub_fields" data-name="sub_fields">
                <div class="acf-label">
                    <label><?php esc_html_e('Sub Fields', 'hi-hat-repeater'); ?></label>
                </div>
                <div class="acf-input acf-input-sub">
                    <?php acf_get_view('acf-field-group/fields', $args); ?>
                </div>
            </div>
<?php

            acf_render_field_setting($field, array(
                'label' => __('Minimum Rows', 'hi-hat-repeater'),
                'instructions' => '',
                'type' => 'number',
                'name' => 'min',
                'placeholder' => '0',
            ));

            acf_render_field_setting($field, array(
                'label' => __('Maximum Rows', 'hi-hat-repeater'),
                'instructions' => '',
                'type' => 'number',
                'name' => 'max',
                'placeholder' => '0',
            ));

            acf_render_field_setting($field, array(
                'label' => __('Layout', 'hi-hat-repeater'),
                'instructions' => '',
                'type' => 'radio',
                'name' => 'layout',
                'layout' => 'horizontal',
                'choices' => array(
                    'table' => __('Table', 'hi-hat-repeater'),
                    'block' => __('Block', 'hi-hat-repeater'),
                    'row' => __('Row', 'hi-hat-repeater')
                )
            ));

            acf_render_field_setting($field, array(
                'label' => __('Button Label', 'hi-hat-repeater'),
                'instructions' => '',
                'type' => 'text',
                'name' => 'button_label',
                'placeholder' => __('Add Row', 'hi-hat-repeater')
            ));
        }

        function load_value($value, $post_id, $field)
        {
            // If value is already an array (already loaded), return it as-is
            if (is_array($value)) {
                return $value;
            }

            if (empty($value) || !is_numeric($value)) {
                return array();
            }

            $value = intval($value);
            $rows = array();

            for ($i = 0; $i < $value; $i++) {
                $row = array();

                if (!empty($field['sub_fields'])) {
                    foreach ($field['sub_fields'] as $sub_field) {
                        $sub_field['name'] = $field['name'] . '_' . $i . '_' . $sub_field['_name'];
                        $row[$sub_field['key']] = acf_get_value($post_id, $sub_field);
                    }
                }

                $rows[] = $row;
            }
            return $rows;
        }

        function update_value($value, $post_id, $field)
        {
            // If value is numeric (row count from previous save), return it as-is
            if (is_numeric($value) && !is_array($value)) {
                return $value;
            }

            // If value is null or empty (ACF re-calling after null return), get stored row count
            if ($value === null || $value === '') {
                $stored_count = get_post_meta($post_id, $field['name'], true);
                return $stored_count ? $stored_count : 0;
            }

            if (!is_array($value) || empty($field['sub_fields'])) {
                delete_post_meta($post_id, $field['name']);
                return 0;
            }

            // Remove clone row
            unset($value['acfcloneindex']);

            // Handle empty array (all rows deleted)
            if (empty($value)) {
                $old_value = get_post_meta($post_id, $field['name'], true);
                $old_value = is_numeric($old_value) ? intval($old_value) : 0;

                // Delete all old rows
                for ($i = 0; $i < $old_value; $i++) {
                    foreach ($field['sub_fields'] as $sub_field) {
                        $sub_field['name'] = $field['name'] . '_' . $i . '_' . $sub_field['_name'];
                        acf_delete_value($post_id, $sub_field);
                    }
                }

                // Delete the count meta too
                delete_post_meta($post_id, $field['name']);
                return 0;
            }

            // Get old value
            $old_value = get_post_meta($post_id, $field['name'], true);
            $old_value = is_numeric($old_value) ? intval($old_value) : 0;

            // Save each row - ensure sequential indices
            $new_value = 0;
            $value = array_values($value); // Re-index to ensure 0, 1, 2, 3...

            foreach ($value as $i => $row) {
                if (!is_array($row)) {
                    continue;
                }

                $i = intval($i);

                foreach ($field['sub_fields'] as $sub_field) {
                    $sub_field_key = $sub_field['key'];
                    $sub_value = isset($row[$sub_field_key]) ? $row[$sub_field_key] : '';

                    $sub_field['name'] = $field['name'] . '_' . $i . '_' . $sub_field['_name'];
                    acf_update_value($sub_value, $post_id, $sub_field);
                }

                $new_value = $i + 1;
            }

            // Delete old rows
            if ($old_value > $new_value) {
                for ($i = $new_value; $i < $old_value; $i++) {
                    foreach ($field['sub_fields'] as $sub_field) {
                        $sub_field['name'] = $field['name'] . '_' . $i . '_' . $sub_field['_name'];
                        acf_delete_value($post_id, $sub_field);
                    }
                }
            }

            // Return row count - ACF will store this in the main field meta
            return $new_value;
        }

        function validate_value($valid, $value, $field, $input)
        {
            $count = 0;

            if (!empty($value)) {
                unset($value['acfcloneindex']);
                $count = count($value);
            }

            if ($field['required'] && !$count) {
                return false;
            }

            $min = (int) $field['min'];
            if ($min && $count < $min) {
                /* translators: 1: current number of rows, 2: minimum number of rows. */
                return sprintf(__('Minimum rows not reached (%1$d / %2$d)', 'hi-hat-repeater'), $count, $min);
            }

            $max = (int) $field['max'];
            if ($max && $count > $max) {
                /* translators: 1: current number of rows, 2: maximum number of rows. */
                return sprintf(__('Maximum rows exceeded (%1$d / %2$d)', 'hi-hat-repeater'), $count, $max);
            }

            return $valid;
        }

        function validate_any_field($field)
        {
            if (empty($field['parent'])) {
                return $field;
            }

            $parent = acf_get_field($field['parent']);
            if (empty($parent) || $parent['type'] !== $this->name) {
                return $field;
            }

            $field['wrapper'] = wp_parse_args($field['wrapper'], array('width' => ''));

            if (isset($field['column_width'])) {
                $field['wrapper']['width'] = $field['column_width'];
                unset($field['column_width']);
            }

            return $field;
        }
    }

endif;
