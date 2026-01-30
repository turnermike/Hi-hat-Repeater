<?php
/**
 * Class Hi_Hat_Repeater_Field_Group.
 */
class Hi_Hat_Repeater_Field_Group extends acf_field {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = 'hi_hat_repeater_group';
		$this->label    = __( 'Hi-hat Repeater - Group', 'hi-hat-repeater' );
		$this->category = 'layout';
		$this->defaults = array(
			'sub_fields' => array(),
		);

        parent::__construct();
	}

    /**
     * Renders the field settings.
     *
     * @param array $field The field settings.
     */
    function render_field_settings( $field ) {

        //'sub_fields' setting
        acf_render_field_setting( $field, array(
            'label'         => __('Sub Fields','acf'),
            'instructions'  => '',
            'type'          => 'repeater',
            'name'          => 'sub_fields',
            'layout'        => 'table',
            'button_label'  => __("Add Field",'acf'),
            'sub_fields'    =>  acf_get_field_type('group')->get_meta('sub_fields')
        ));

    }
    public function load_field( $field ) {
        $field['sub_fields'] = acf_get_fields($field);
        return $field;
    }



	/**
	 * Render the field.
	 *
	 * @param array $field The field settings.
	 */
	public function render_field( $field ) {
        $field['value'] = is_array($field['value']) ? $field['value'] : array();

        ?>
        <div class="acf-repeater -table">
            <div class="acf-input">
                <div class="acf-repeater-wrapper">
                    <table class="acf-table">
                        <thead>
                            <tr>
                                <th class="acf-row-handle"></th>
                                <?php foreach ( $field['sub_fields'] as $sub_field ) : ?>
                                    <th class="acf-th-<?php echo esc_attr($sub_field['name']); ?>">
                                        <?php echo esc_html($sub_field['label']); ?>
                                        <?php if ( !empty($sub_field['instructions']) ) : ?>
                                            <p class="description"><?php echo esc_html($sub_field['instructions']); ?></p>
                                        <?php endif; ?>
                                    </th>
                                <?php endforeach; ?>
                                <th class="acf-row-handle"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ( !empty($field['value']) ) : ?>
                                <?php foreach ( $field['value'] as $i => $row ) : ?>
                                    <tr class="acf-row">
                                        <td class="acf-row-handle order" title="<?php _e('Drag to reorder', 'acf'); ?>">
                                            <span><?php echo $i + 1; ?></span>
                                        </td>
                                        <?php foreach ( $field['sub_fields'] as $sub_field ) : ?>
                                            <?php
                                            $sub_field['name'] = $field['name'] . '[' . $i . '][' . $sub_field['name'] . ']';
                                            if ( isset($row[ $sub_field['key'] ]) ) {
                                                $sub_field['value'] = $row[ $sub_field['key'] ];
                                            }
                                            ?>
                                            <td class="acf-field" data-name="<?php echo esc_attr($sub_field['name']); ?>" data-type="<?php echo esc_attr($sub_field['type']); ?>">
                                                <?php acf_render_field( $sub_field ); ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="acf-row-handle remove">
                                            <a href="#" class="acf-button-delete" data-event="remove-row" title="<?php _e('Remove row', 'acf'); ?>"></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr class="acf-clone">
                                <td class="acf-row-handle order"></td>
                                <?php foreach ( $field['sub_fields'] as $sub_field ) : ?>
                                    <?php
                                    $sub_field['name'] = $field['name'] . '[acfcloneindex][' . $sub_field['name'] . ']';
                                    $sub_field['value'] = null;
                                    ?>
                                    <td class="acf-field" data-name="<?php echo esc_attr($sub_field['name']); ?>" data-type="<?php echo esc_attr($sub_field['type']); ?>">
                                        <?php acf_render_field( $sub_field ); ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="acf-row-handle remove"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="acf-actions">
                    <a class="acf-button button button-primary" href="#" data-event="add-row"><?php _e('Add Row', 'acf'); ?></a>
                </div>
            </div>
        </div>
        <?php
	}

    public function load_value( $value, $post_id, $field ) {
        if( empty( $value ) ) {
            return false;
        }

        // load sub fields
        foreach( $field['sub_fields'] as $sub_field ) {
            // add value
            if( isset( $value[ $sub_field['key'] ] ) ) {
                // load value
                $sub_field['value'] = acf_load_value( $value[ $sub_field['key'] ], $post_id, $sub_field );
            }
        }

        return $value;
    }

    public function format_value( $value, $post_id, $field ) {
        if( empty( $value ) ) {
            return false;
        }

        // loop over rows
        foreach( $value as $i => $row ) {
            // loop over sub fields
            foreach( $field['sub_fields'] as $sub_field ) {
                // get sub field key
                $sub_field_key = $sub_field['key'];

                // get sub field value
                $sub_value = isset( $row[ $sub_field_key ] ) ? $row[ $sub_field_key ] : null;

                // format value
                $sub_value = acf_format_value( $sub_value, $post_id, $sub_field );

                // update value
                $value[ $i ][ $sub_field['name'] ] = $sub_value;
            }
        }

        return $value;
    }

	/**
	 * This filter is applied to the $value before it is saved in the db.
	 *
	 * @param mixed $value The value from the form.
	 * @param mixed $post_id The post_id being saved.
	 * @param array $field The field array.
	 * @return mixed
	 */
	public function update_value( $value, $post_id, $field ) {
        if( empty( $value ) ) {
            return $value;
        }

        if( is_array($value) ) {
            // remove dummy field
            unset($value['row-0']);

            // loop through rows
            foreach( $value as $i => $row ) {
                // loop through sub fields
                foreach( $field['sub_fields'] as $sub_field ) {
                    // get sub field key
                    $sub_field_key = $sub_field['key'];

                    // bail early if value doesnt exist
                    if( !isset( $row[ $sub_field_key ] ) ) {
                        continue;
                    }

                    // get sub field value
                    $sub_value = $row[ $sub_field_key ];

                    // update sub field value
                    $sub_value = acf_update_value( $sub_value, $post_id, $sub_field );

                    // update row value
                    $value[ $i ][ $sub_field_key ] = $sub_value;
                }
            }
        }

		return $value;
	}
}
