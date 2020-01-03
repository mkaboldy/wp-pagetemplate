<?php
/**
Plugin Name: WP-PageTemplate
Plugin URI: https://github.com/mkaboldy/wp-pagetemplate
Description: Adds a template name column to page admin
Version: 1.1
Author: Miklos Kaboldy
Author URI: https://github.com/mkaboldy

 * @package WordPress
 */

namespace WP_PageTemplate;

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

if ( is_admin() && ! class_exists( 'WP_PageTemplate' ) ) {
	/**
	 * Main class to implement template columns in admin.
	 * Do not instantiate, static only.
	 */
	class WP_PageTemplate {
		/**
		 * Available templates of the current theme
		 *
		 * @var array
		 */
		private static $templates = array();
		/**
		 * Column identifier in admin view
		 *
		 * @var string
		 */
		private static $column_id = 'template';

		/**
		 * Initialize static variables and configure hooks.
		 */
		public static function init() {
			$current_theme = wp_get_theme();
			foreach ( $GLOBALS['_wp_post_type_features'] as $post_type => $features ) {

				if ( isset( $features['page-attributes'] ) ) {
					add_filter( "manage_{$post_type}_posts_columns", array( __NAMESPACE__ . '\WP_PageTemplate', 'manage_posts_columns' ) );
					add_action( "manage_{$post_type}_posts_custom_column", array( __NAMESPACE__ . '\WP_PageTemplate', 'manage_posts_custom_column' ), 10, 2 );
					$available_templates           = $current_theme->get_page_templates( null, $post_type );
					$available_templates           = ! empty( $available_templates ) ? array_merge(
						array(
							'' => apply_filters( 'default_page_template_title', __( 'Default template' ), 'rest-api' ),
						),
						$available_templates
					) : $available_templates;
					self::$templates[ $post_type ] = $available_templates;
				}
			}
		}
        public static function manage_posts_columns( $columns ) {
            $columns[ self::$column_id ] = __('Template');
            return $columns;
        }
        public static function manage_posts_custom_column( $column, $post_id ) {
            global $post;
            if ( self::$column_id === $column ) {
                $template_file = get_post_meta($post->ID, '_wp_page_template', true);
                $post_type = get_post_type($post_id);
                $template_name = self::$templates[ $post_type ][ $template_file ];
                echo "$template_name ($template_file)";
            }
        }
    }
    WP_PageTemplate::init();
}
