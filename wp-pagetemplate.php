<?php
/*
Plugin Name: WP-PageTemplate
Plugin URI: https://github.com/mkaboldy/wp-pagetemplate
Description: Adds a template name column to page admin
Version: 1.0
Author: Miklos Kaboldy
Author URI: https://github.com/mkaboldy
*/

// Prevent direct access to this file.
if (! defined('ABSPATH')) {
    header('HTTP/1.0 403 Forbidden');
    echo 'This file should not be accessed directly!';
    exit; // Exit if accessed directly.
}

add_filter('manage_page_posts_columns', function ($columns) {
    $columns['template'] = 'Template';
    return $columns;
});

add_action('manage_page_posts_custom_column', function ($column, $post_id) {
    global $post;
    if ('template' === $column) {
        echo get_post_meta($post->ID, '_wp_page_template', true);
    }
}, 10, 2);
