<?php
/**
 * Plugin Name: WP Disclaimer Popup
 * Plugin URI: https://github.com/artarmstrong/wp-disclaimer-popup
 * Description: Creates a disclaimer popup on the page where the short code is used.
 * Version: 1.0
 * Author: arthurjarmstrong
 * Author URI: http://artarmstrong.com
 * License: GPL2
 */
 
/*  Copyright 2014  Art Amstrong  (email : me@artarmstrong.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Error reporting
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

// Includes
include( plugin_dir_path( __FILE__ ) . '_inc/meta-box.php');
include( plugin_dir_path( __FILE__ ) . '_inc/shortcodes.php');

// Plugin installer!
register_activation_hook(__FILE__, 'wpd_install');
function wpd_install () {

	// Initial Settings
	$settings = serialize( array(
		'hold' => 1,
		'accept' => 'Accept',
		'decline' => 'Decline',
		'redirect' => get_site_url()
	));
	update_option('wpd_settings', $settings);

}

// Plugin uninstall!
register_uninstall_hook(__FILE__, 'wpd_uninstall');
function wpd_uninstall(){
	delete_option('wpd_settings');
}

// Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'wpd_enqueue_scripts_styles' );
function wpd_enqueue_scripts_styles() {
	wp_enqueue_style( 'wpd-css', plugins_url('_css/wp-disclaimer-popup.css', __FILE__) );
	wp_enqueue_style( 'fancybox-css', plugins_url('_js/fancyBox/jquery.fancybox.css', __FILE__) );
	wp_enqueue_script( 'wpd-js', plugins_url('_js/wp-disclaimer-popup.js', __FILE__), array('jquery'), '1.0.0', true );
	wp_enqueue_script( 'cookie-js', plugins_url('_js/jquery-cookie/jquery.cookie.js', __FILE__), array('jquery'), false, true );
	wp_enqueue_script( 'fancybox-js', plugins_url('_js/fancyBox/jquery.fancybox.js', __FILE__), array('jquery'), false, true );
}

// Create custom post type
add_action( 'init', 'wpd_create_post_type' );
function wpd_create_post_type() {
	register_post_type( 'disclaimers',
		array(
			'labels' => array(
				'name' => __( 'Disclaimers' ),
				'singular_name' => __( 'Disclaimer' )
			),
		'public' => true,
		'has_archive' => true,
		)
	);
}

// Add title header filters
add_filter('manage_disclaimers_posts_columns', 'wpd_posts_columns');
function wpd_posts_columns( $defaults ) {
    return array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title'),
        'shortcode' => __('Shortcode'),
        'date' => __('Date')
    );
}

// Customize the shortcode header
add_action( 'manage_posts_custom_column' , 'wpd_posts_custom_columns', 10, 2 );
function wpd_posts_custom_columns( $column, $post_id ) {
  switch ( $column ) {
	  case 'shortcode' :
  	  echo "[wp-disclaimer id=\"$post_id\"]";
  		break;
  }
}