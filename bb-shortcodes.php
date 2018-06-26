<?php
/*
Plugin Name: Beaver Builder Shortcodes
Plugin URI:  https://beaverlodgehq.com/downloads/beaver-builder-shortcodes
Description: Displays template shortcode in the template dashboard.
Version:     1.1.0
Author:      West Coast Digital
Author URI:  https://westcoastdigital.com.au
Text Domain: bb-shortcode
Domain Path: /languages
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

function wcd_edit_template_columns() {
	require_once 'lib/columns.php';
	new WCD_BB_Template_Admin_Columns( 'fl-builder-template', array() );
}
add_action( 'admin_init', 'wcd_edit_template_columns', 20 );

if ( ! class_exists( 'RW_Meta_Box' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'assets/meta-box/meta-box.php' );
}

if ( ! class_exists( 'MB_Admin_Columns' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'assets/mb-admin-columns/mb-admin-columns.php' );
}

require_once 'lib/tinymce.php';

function wcd_template_admin_scripts( $hook ) {
    wp_enqueue_script( 'ZeroClipboard', plugins_url('assets/copy-to-clipboard/jquery.copy-to-clipboard.js', __FILE__) );
    wp_enqueue_script( 'shortcode-copy', plugins_url('lib/bb-shortcodes.js', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'wcd_template_admin_scripts' );

define( 'BB_SHORTCODE_MODULE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BB_SHORTCODE_MODULE_URL', plugins_url( '/', __FILE__ ) );

function wcd_template_module() {
    if ( class_exists( 'FLBuilder' ) ) {
        require_once( plugin_dir_path( __FILE__ ) . 'lib/shortcode-module/shortcode-module.php' );
    }
}
add_action( 'init', 'wcd_template_module' );