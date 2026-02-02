<?php

/**
 * Plugin Name: Hi-Hat Repeater
 * Plugin URI:  https://github.com/yourusername/hi-hat-repeater
 * Description: An Advanced Custom Fields add-on for a repeater-like field with multiple text areas. Includes GraphQL support.
 * Version:     1.2.1
 * Author:      Your Name
 * Author URI:  https://github.com/yourusername
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hi-hat-repeater
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * ACF: 5.0.0
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
  exit;
}

// Check if ACF is active.
if (! class_exists('acf')) {
  return;
}

// Debug POST data
include_once __DIR__ . '/debug-post.php';


// Define constants.
if (!defined('HI_HAT_REPEATER_URL')) {
  define('HI_HAT_REPEATER_URL', plugin_dir_url(__FILE__));
}
if (!defined('HI_HAT_REPEATER_PATH')) {
  define('HI_HAT_REPEATER_PATH', plugin_dir_path(__FILE__));
}
