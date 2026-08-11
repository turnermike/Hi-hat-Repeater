<?php

/**
 * Internal bootstrap file for Hi-Hat Repeater.
 *
 * Intentionally not a WordPress plugin header to avoid duplicate plugin
 * entries when this file exists alongside `hi-hat-repeater.php`.
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
  exit;
}

// Check if ACF is active.
if (! class_exists('acf')) {
  return;
}


// Define constants.
if (!defined('HI_HAT_REPEATER_URL')) {
  define('HI_HAT_REPEATER_URL', plugin_dir_url(__FILE__));
}
if (!defined('HI_HAT_REPEATER_PATH')) {
  define('HI_HAT_REPEATER_PATH', plugin_dir_path(__FILE__));
}
