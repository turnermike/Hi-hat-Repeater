<?php
/**
 * Test Hi-hat Repeater field classes.
 */

use PHPUnit\Framework\TestCase;

class HiHatRepeaterFieldTest extends TestCase {

    /**
     * Test WYSIWYG field name.
     */
    public function test_wysiwyg_field_name() {
        $field = new Hi_Hat_Repeater_Field_Wysiwyg();
        $this->assertEquals('hi_hat_repeater_wysiwyg', $field->name);
    }

    /**
     * Test WYSIWYG field label.
     */
    public function test_wysiwyg_field_label() {
        $field = new Hi_Hat_Repeater_Field_Wysiwyg();
        $this->assertEquals(__('Hi-hat Repeater - WYSIWYG', 'hi-hat-repeater'), $field->label);
    }

    /**
     * Test Textarea field name.
     */
    public function test_textarea_field_name() {
        $field = new Hi_Hat_Repeater_Field_Textarea();
        $this->assertEquals('hi_hat_repeater_textarea', $field->name);
    }

    /**
     * Test Textarea field label.
     */
    public function test_textarea_field_label() {
        $field = new Hi_Hat_Repeater_Field_Textarea();
        $this->assertEquals(__('Hi-hat Repeater - Textarea', 'hi-hat-repeater'), $field->label);
    }

    /**
     * Test Image field name.
     */
    public function test_image_field_name() {
        $field = new Hi_Hat_Repeater_Field_Image();
        $this->assertEquals('hi_hat_repeater_image', $field->name);
    }

    /**
     * Test Image field label.
     */
    public function test_image_field_label() {
        $field = new Hi_Hat_Repeater_Field_Image();
        $this->assertEquals(__('Hi-hat Repeater - Image', 'hi-hat-repeater'), $field->label);
    }

    /**
     * Test field category.
     */
    public function test_field_category() {
        $wysiwyg_field = new Hi_Hat_Repeater_Field_Wysiwyg();
        $textarea_field = new Hi_Hat_Repeater_Field_Textarea();
        $image_field = new Hi_Hat_Repeater_Field_Image();
        $this->assertEquals('content', $wysiwyg_field->category);
        $this->assertEquals('content', $textarea_field->category);
        $this->assertEquals('content', $image_field->category);
    }

    /**
     * Test update_value method with array input (WYSIWYG).
     */
    public function test_wysiwyg_update_value_with_array() {
        $field = new Hi_Hat_Repeater_Field_Wysiwyg();
        $value = ['Item 1', 'Item 2', '', 'Item 3', ''];
        $result = $field->update_value($value, 1, []);

        $this->assertIsArray($result);
        $this->assertEquals(['Item 1', 'Item 2', 'Item 3'], $result);
    }

    /**
     * Test update_value method with array input (Textarea).
     */
    public function test_textarea_update_value_with_array() {
        $field = new Hi_Hat_Repeater_Field_Textarea();
        $value = ['Item 1', 'Item 2', '', 'Item 3', ''];
        $result = $field->update_value($value, 1, []);

        $this->assertIsArray($result);
        $this->assertEquals(['Item 1', 'Item 2', 'Item 3'], $result);
    }

    /**
     * Test update_value method with non-array input.
     */
    public function test_update_value_with_non_array() {
        $wysiwyg_field = new Hi_Hat_Repeater_Field_Wysiwyg();
        $textarea_field = new Hi_Hat_Repeater_Field_Textarea();
        $value = 'not an array';
        
        $this->assertEquals('not an array', $wysiwyg_field->update_value($value, 1, []));
        $this->assertEquals('not an array', $textarea_field->update_value($value, 1, []));
    }

    /**
     * Test update_value method filters empty strings.
     */
    public function test_update_value_filters_empty_strings() {
        $wysiwyg_field = new Hi_Hat_Repeater_Field_Wysiwyg();
        $textarea_field = new Hi_Hat_Repeater_Field_Textarea();
        $value = ['Item 1', '', '   ', 'Item 2'];
        
        $wysiwyg_result = $wysiwyg_field->update_value($value, 1, []);
        $textarea_result = $textarea_field->update_value($value, 1, []);

        $this->assertIsArray($wysiwyg_result);
        $this->assertEquals(['Item 1', 'Item 2'], $wysiwyg_result);
        $this->assertIsArray($textarea_result);
        $this->assertEquals(['Item 1', 'Item 2'], $textarea_result);
    }

    /**
     * Test update_value method with non-array input (Image).
     */
    public function test_image_update_value_with_non_array() {
        $image_field = new Hi_Hat_Repeater_Field_Image();
        $value = 'not an array';
        
        $this->assertEquals('not an array', $image_field->update_value($value, 1, []));
    }

    /**
     * Test update_value method filters invalid attachment IDs (Image).
     */
    public function test_image_update_value_filters_invalid_ids() {
        $image_field = new Hi_Hat_Repeater_Field_Image();
        $value = ['', '0', 'invalid', '123'];
        
        $result = $image_field->update_value($value, 1, []);

        $this->assertIsArray($result);
        // Should filter out empty, zero, and invalid IDs
        // Note: This test assumes no attachment with ID 123 exists in test environment
        // In a real test environment with WordPress test setup, you'd create test attachments
        $this->assertEmpty($result);
    }
}