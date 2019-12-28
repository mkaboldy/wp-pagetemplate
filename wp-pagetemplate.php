<?php
/*
Plugin Name: WP-PageTemplate
Plugin URI: https://github.com/mkaboldy/wp-pagetemplate
Description: Adds a template name column to page admin
Version: 1.1
Author: Miklos Kaboldy
Author URI: https://github.com/mkaboldy
*/

namespace WP_PageTemplate;

// Prevent direct access to this file.
if (! defined('ABSPATH')) {
    header('HTTP/1.0 403 Forbidden');
    echo 'This file should not be accessed directly!';
    exit; // Exit if accessed directly.
}

if (! class_exists('WP_PageTemplate')) {
    class WP_PageTemplate {
        private static $templates = [];
        private static $column_id = 'template';
    
        public static function init() {
            self::$templates[] = 'Temp';
            add_filter('manage_page_posts_columns', [__NAMESPACE__ . '\WP_PageTemplate','manage_page_posts_columns']);
            add_action('manage_page_posts_custom_column', [__NAMESPACE__ . '\WP_PageTemplate','manage_page_posts_custom_column'], 10, 2);
        }
        public static function manage_page_posts_columns($columns) {
            $columns[self::$column_id] = __('Template');
            return $columns;
        }
        public static function manage_page_posts_custom_column($column, $post_id) {
            global $post;
            if (self::$column_id === $column) {
                $template_file = get_post_meta($post->ID, '_wp_page_template', true);
                $template_name = self::$templates[0];
                echo "$template_name ($template_file)";
            }
        }
    }
    WP_PageTemplate::init();
}
