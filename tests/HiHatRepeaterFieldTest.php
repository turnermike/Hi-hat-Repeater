<?php
/**
 * Test Hi_Hat_Repeater_Field class.
 */

use PHPUnit\Framework\TestCase;

class HiHatRepeaterFieldTest extends TestCase {

    /**
     * Test field name.
     */
    public function test_field_name() {
        $field = new Hi_Hat_Repeater_Field();
        $this->assertEquals('hi_hat_repeater', $field->name);
    }

    /**
     * Test field label.
     */
    public function test_field_label() {
        $field = new Hi_Hat_Repeater_Field();
        $this->assertEquals(__('Hi-Hat Repeater', 'hi-hat-repeater'), $field->label);
    }

    /**
     * Test field category.
     */
    public function test_field_category() {
        $field = new Hi_Hat_Repeater_Field();
        $this->assertEquals('basic', $field->category);
    }

    /**
     * Test update_value method with array input.
     */
    public function test_update_value_with_array() {
        $field = new Hi_Hat_Repeater_Field();
        $value = ['Item 1', 'Item 2', '', 'Item 3', ''];
        $result = $field->update_value($value, 1, []);

        $this->assertIsArray($result);
        $this->assertEquals(['Item 1', 'Item 2', 'Item 3'], $result);
    }

    /**
     * Test update_value method with non-array input.
     */
    public function test_update_value_with_non_array() {
        $field = new Hi_Hat_Repeater_Field();
        $value = 'not an array';
        $result = $field->update_value($value, 1, []);

        $this->assertEquals('not an array', $result);
    }

    /**
     * Test update_value method filters empty strings.
     */
    public function test_update_value_filters_empty_strings() {
        $field = new Hi_Hat_Repeater_Field();
        $value = ['Item 1', '', '   ', 'Item 2'];
        $result = $field->update_value($value, 1, []);

        $this->assertIsArray($result);
        $this->assertEquals(['Item 1', 'Item 2'], $result);
    }

    /**
     * Test GraphQL registration functions exist.
     */
    public function test_graphql_functions_exist() {
        $this->assertTrue(function_exists('hi_hat_repeater_register_graphql_support'));
        $this->assertTrue(function_exists('hi_hat_repeater_manual_graphql_registration'));
    }
}