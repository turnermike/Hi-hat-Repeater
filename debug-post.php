<?php
// Add action to capture POST data
add_action('acf/save_post', function ($post_id) {
  if (isset($_POST['acf'])) {
    $debug_file = WP_CONTENT_DIR . '/hi-hat-post-debug.log';
    file_put_contents($debug_file, date('Y-m-d H:i:s') . " - POST data for post $post_id:\n", FILE_APPEND);
    file_put_contents($debug_file, print_r($_POST['acf'], true) . "\n\n", FILE_APPEND);
  }
}, 1);
