<?php
/**
 * Class that manage post admin columns.
 */

/**
 * Post admin columns class.
 */
class MB_Admin_Columns_Post extends MB_Admin_Columns_Base {
	/**
	 * Initialization.
	 */
	protected function init() {
		// Actions to show post columns can be executed via normal page request or via Ajax when quick edit
		// Priority 20 allows us to overwrite WooCommerce settings
		$priority = 20;
		add_filter( "manage_{$this->object_type}_posts_columns", array( $this, 'columns' ), $priority );
		add_action( "manage_{$this->object_type}_posts_custom_column", array( $this, 'show' ), $priority, 2 );
		add_filter( "manage_edit-{$this->object_type}_sortable_columns", array(
			$this,
			'sortable_columns'
		), $priority );

		// Other actions need to run only in Management page
		add_action( 'load-edit.php', array( $this, 'execute' ) );
	}

	/**
	 * Actions need to run only in Management page.
	 */
	public function execute() {
		if ( ! $this->is_screen() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'pre_get_posts', array( $this, 'filter' ) );
	}

	/**
	 * Show column content.
	 *
	 * @param string $column Column ID
	 * @param int $post_id Post ID
	 */
	public function show( $column, $post_id ) {
		if ( false === ( $field = $this->find_field( $column ) ) ) {
			return;
		}

		$config = array(
			'before' => '',
			'after'  => '',
		);
		if ( is_array( $field['admin_columns'] ) ) {
			$config = wp_parse_args( $field['admin_columns'], $config );
		}
		printf(
			'<div class="mb-admin-columns mb-admin-columns-%s" id="mb-admin-columns-%s">%s%s%s</div>',
			$field['type'],
			$field['id'],
			$config['before'],
			rwmb_the_value( $field['id'], '', $post_id, false ),
			$config['after']
		);
	}

	/**
	 * Sort by meta value
	 *
	 * @param WP_Query $query
	 */
	public function filter( $query ) {
		if ( ! isset( $_GET['orderby'] ) || false === ( $field = $this->find_field( $_GET['orderby'] ) ) ) {
			return;
		}
		$query->set( 'orderby', in_array( $field['type'], array(
			'number',
			'slider',
			'range'
		) ) ? 'meta_value_num' : 'meta_value' );
		$query->set( 'meta_key', $_GET['orderby'] );
	}

	/**
	 * Check if we in right page in admin area.
	 * @return bool
	 */
	private function is_screen() {
		return $this->object_type == get_current_screen()->post_type;
	}
}
