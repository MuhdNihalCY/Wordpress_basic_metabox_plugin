<?php
/* 
Plugin Name: Metabox basic
Description: A simple input checkbox plugin
Version: 1.0
Author: Nihal
License: GPL2
*/

// styles hooked
function enqueue_metabox_styles() {
    // Get the URL of the plugin directory
    $dir_url = plugins_url('style.css', __FILE__);

    // Enqueue your custom stylesheet
    wp_enqueue_style('style', $dir_url);
}
// Ensure this line is present to call the function
add_action('wp_enqueue_scripts', 'enqueue_metabox_styles');

function metabox_basic_add() {
    add_meta_box(
        'metabox_id',
        'Metabox basic plugin',
        'basic_metabox_render',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'metabox_basic_add');

// metabox render at admin
function basic_metabox_render($post) {
    // get the existing value with proper security
    $input_data = get_post_meta($post->ID, 'input_value', true);
    $checkbox_data = get_post_meta($post->ID, 'checkbox_value', true);

    // Add a nonce field for security
    wp_nonce_field('custom_metabox_nonce', 'custom_metabox_nonce');

    // Display input field
    echo '<label for="input_value">Input Field:</label>';
    echo '<input type="text" id="input_value" name="input_value" value="' . esc_attr($input_data) . '" /><br>';

    // Display checkbox field
    echo '<label for="checkbox_value"><input type="checkbox" id="checkbox_value" name="checkbox_value" value="1" ' . checked($checkbox_data, '1', false) . ' /> Show on Frontend</label>';
}

// save metabox data
function save_metabox_data($post_id) {
    // Security checks
    if (!isset ($_POST['custom_metabox_nonce']) || !wp_verify_nonce($_POST['custom_metabox_nonce'], 'custom_metabox_nonce') || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || !current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save input field data with proper sanitization
    $input_value = isset($_POST['input_value']) ? sanitize_text_field(trim($_POST['input_value'])) : '';

    // Save checkbox field data with proper type casting
    $checkbox_value = isset ($_POST['checkbox_value']) ? true : false;

    // Update post meta with sanitized data
    update_post_meta($post_id, 'input_value', $input_value);
    update_post_meta($post_id, 'checkbox_value', $checkbox_value);
}
add_action('save_post', 'save_metabox_data');

// display metabox
function display_metabox_data($content) {
    global $post;
    if (!isset ($post))
        return;
    $checkbox_data = get_post_meta($post->ID, 'checkbox_value', true);


    if ($checkbox_data) {
        $input_data = get_post_meta($post->ID, 'input_value', true);
        // Display input field data if checkbox is checked
        $content .= '<div class="custom-input-field"><p class="meta-data-value">' . esc_html($input_data) . '</p></div>';
    }

    return $content;
}
add_filter('the_content', 'display_metabox_data');