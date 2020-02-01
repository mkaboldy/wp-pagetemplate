<?php
/**
 * WP-PageTemplate plugin class.
 *
 * @package WordPress
 */

namespace WP_PageTemplate;

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_PageTemplate' ) ) {
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
		 *
		 * @return void
		 */
		public static function init() {

			$current_theme = wp_get_theme();

			foreach ( $GLOBALS['_wp_post_type_features'] as $post_type => $features ) {

				if ( isset( $features['page-attributes'] ) ) {

					add_filter( "manage_{$post_type}_posts_columns", array( __NAMESPACE__ . '\WP_PageTemplate', 'manage_posts_columns' ) );
					add_action( "manage_{$post_type}_posts_custom_column", array( __NAMESPACE__ . '\WP_PageTemplate', 'manage_posts_custom_column' ), 10, 2 );
					add_filter( "manage_edit-{$post_type}_sortable_columns", array( __NAMESPACE__ . '\WP_PageTemplate', 'manage_sortable_columns' ) );
					add_action( 'pre_get_posts', array( __NAMESPACE__ . '\WP_PageTemplate', 'pre_get_posts' ) );

					$available_templates = $current_theme->get_page_templates( null, $post_type );
					$available_templates = ! empty( $available_templates ) ? array_merge(
						array(
							''        => apply_filters( 'default_page_template_title', __( 'Default template' ), 'rest-api' ),
							'default' => apply_filters( 'default_page_template_title', __( 'Default template' ), 'rest-api' ),
						),
						$available_templates
					) : $available_templates;

					self::$templates[ $post_type ] = $available_templates;
				}
			}
		}

		/**
		 * Adds template column header, callback to hook 'manage_{$post_type}_posts_columns'
		 *
		 * @param array $columns existing admin columns.
		 * @return array the updated columns.
		 */
		public static function manage_posts_columns( $columns ) {
			$columns[ self::$column_id ] = __( 'Template' );
			return $columns;
		}

		/**
		 * Adds content to template column, callback of hook "manage_{$post_type}_posts_custom_column"
		 *
		 * @param string $column column id.
		 * @param int    $post_id the id of the current post.
		 */
		public static function manage_posts_custom_column( $column, $post_id ) {
			global $post;
			if ( self::$column_id === $column ) {
				$display_name  = '';
				$display_class = '';
				$display_title = '';
				$display_file  = '';

				$template_file = esc_html( get_post_meta( $post->ID, '_wp_page_template', true ) );

				if ( $template_file ) {
					$display_file = '(' . $template_file . ')';
				}

				$post_type = $post->post_type;

				if ( isset( self::$templates[ $post_type ][ $template_file ] ) ) {
					$display_name = self::$templates[ $post_type ][ $template_file ];
				} else {
					$display_title = __( 'Template file doesn\'t exist' );
					$display_class = 'notice notice-error';
				}

				printf(
					'%1$s <span class="%2$s" title="%3$s"> %4$s </span>',
					esc_html( $display_name ),
					esc_html( $display_class ),
					esc_html( $display_title ),
					esc_html( $display_file )
				);
			}
		}

		/**
		 * Adds Template column to sortable columns , callback to hook 'manage_{$post_type}_sortable_columns'
		 *
		 * @param array $columns existing admin columns.
		 * @return array the updated columns.
		 */
		public static function manage_sortable_columns( $columns ) {
			$columns[ self::$column_id ] = self::$column_id;
			return $columns;
		}

		/**
		 * Adds Template column to sortable columns , callback to hook 'pre_get_posts'
		 *
		 * @param \WP_Query $query the WP_query object.
		 * @return void
		 */
		public static function pre_get_posts( $query ) {

			$orderby = $query->get( 'orderby' );

			if ( self::$column_id === $orderby ) {
				$query->set( 'meta_key', '_wp_page_template' );
				$query->set( 'orderby', 'meta_value' );
			}
		}
	}
	WP_PageTemplate::init();
}
