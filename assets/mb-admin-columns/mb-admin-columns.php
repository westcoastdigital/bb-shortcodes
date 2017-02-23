<?php
/**
 * Plugin Name: MB Admin Columns
 * Plugin URI: https://metabox.io/plugins/mb-admin-columns/
 * Description: Show custom fields in the post list table.
 * Version: 1.0.2
 * Author: Rilwis
 * Author URI: http://www.deluxeblogtips.com
 * License: GPL2+
 * Text Domain: mb-admin-columns
 * Domain Path: /lang/
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'MB_Admin_Columns' ) ) {
	/**
	 * Plugin main class.
	 */
	class MB_Admin_Columns {
		/**
		 * Constructor.
		 * Add hooks.
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'init' ) );
		}

		/**
		 * Initialization.
		 * Load plugin files and bootstrap for posts and taxonomies.
		 */
		public function init() {
			if ( ! defined( 'RWMB_VER' ) || class_exists( 'MB_Admin_Columns_Post' ) ) {
				return;
			}

			require_once dirname( __FILE__ ) . '/inc/base.php';
			require_once dirname( __FILE__ ) . '/inc/post.php';
//			require dirname( __FILE__ ) . '/inc/taxonomy.php';

			$this->posts();
//			$this->taxonomies();
		}

		/**
		 * Add admin columns for posts.
		 */
		protected function posts() {
			$meta_boxes = RWMB_Core::get_meta_boxes();
			$meta_boxes = array_map( array( 'RW_Meta_Box', 'normalize' ), $meta_boxes );

			foreach ( $meta_boxes as $meta_box ) {
				$fields = $this->get_fields( $meta_box );
				if ( empty( $fields ) ) {
					continue;
				}

				foreach ( $meta_box['post_types'] as $post_type ) {
					new MB_Admin_Columns_Post( $post_type, $fields );
				}
			}
		}

		/**
		 * Add admin columns for terms.
		 */
		protected function taxonomies() {
			foreach ( MB_Term_Meta_Loader::$meta_boxes as $meta_box ) {
				$fields = $this->get_fields( $meta_box );
				if ( empty( $fields ) ) {
					continue;
				}

				$taxonomies = (array) $meta_box['taxonomies'];
				foreach ( $taxonomies as $taxonomy ) {
					new MB_Admin_Columns_Taxonomy( $taxonomy, $fields );
				}
			}
		}

		/**
		 * Get meta box fields.
		 *
		 * @param $meta_box
		 *
		 * @return array
		 */
		protected function get_fields( $meta_box ) {
			$fields = array();
			foreach ( $meta_box['fields'] as $field ) {
				if ( isset( $field['admin_columns'] ) ) {
					$fields[] = $field;
				}
			}

			return $fields;
		}
	}

	new MB_Admin_Columns;
}
