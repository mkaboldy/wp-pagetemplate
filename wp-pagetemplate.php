<?php
/**
Plugin Name: WP-PageTemplate
Plugin URI: https://github.com/mkaboldy/wp-pagetemplate
Description: Adds a template name column to page admin
Version: 1.2
Author: Miklos Kaboldy
Author URI: https://github.com/mkaboldy

 * @package WordPress
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

if ( ! is_admin() ) {
	return;
}

require 'classes/class-wp-pagetemplate.php';
