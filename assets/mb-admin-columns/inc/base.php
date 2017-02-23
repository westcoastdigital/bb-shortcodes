<?php
/**
 * Base class that manage admin columns.
 */

/**
 * Base class.
 */
abstract class MB_Admin_Columns_Base {
	/**
	 * Object type, should be post type for posts or taxonomy for terms.
	 * @var string
	 */
	protected $object_type;

	/**
	 * List of fields for the object type.
	 * @var array
	 */
	protected $fields;

	/**
	 * Constructor.
	 *
	 * @param string $object_type Post type
	 * @param array $fields List of fields
	 */
	public function __construct( $object_type, $fields ) {
		$this->object_type = $object_type;
		$this->fields      = $fields;

		$this->init();
	}

	/**
	 * Initialization, must be defined in subclasses.
	 */
	abstract protected function init();

	/**
	 * Enqueue admin styles.
	 */
	public function enqueue() {
		list( , $url ) = RWMB_Loader::get_path( dirname( dirname( __FILE__ ) ) );
		wp_enqueue_style( 'mb-admin-columns', $url . 'css/admin-columns.css' );
	}

	/**
	 * Get list of columns.
	 *
	 * @param array $columns Default WordPress columns
	 *
	 * @return array
	 */
	public function columns( $columns ) {
		foreach ( $this->fields as $field ) {
			$config = $field['admin_columns'];

			// Just show this column
			if ( true === $config ) {
				$this->add( $columns, $field['id'], $field['name'] );
				continue;
			}

			// If position is specified
			if ( is_string( $config ) ) {
				$config = strtolower( $config );
				list( $position, $target ) = array_map( 'trim', explode( ' ', $config . ' ' ) );
				$this->add( $columns, $field['id'], $field['name'], $position, $target );
			}

			// If an array of configuration is specified
			if ( is_array( $config ) ) {
				$config = wp_parse_args( $config, array(
					'position' => '',
					'title'    => $field['name'],
				) );
				list( $position, $target ) = array_map( 'trim', explode( ' ', $config['position'] . ' ' ) );
				$this->add( $columns, $field['id'], $config['title'], $position, $target );
			}
		}

		return $columns;
	}

	/**
	 * Make columns sortable
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		foreach ( $this->fields as $field ) {
			if ( is_array( $field['admin_columns'] ) && ! empty( $field['admin_columns']['sort'] ) ) {
				$columns[ $field['id'] ] = $field['id'];
			}
		}

		return $columns;
	}

	/**
	 * Add a new column
	 *
	 * @param array $columns Array of columns
	 * @param string $id New column ID
	 * @param string $title New column title
	 * @param string $position New column position. Empty to not specify the position. Could be 'before', 'after' or 'replace'
	 * @param string $target The target column. Used with combination with $position
	 */
	protected function add( &$columns, $id, $title, $position = '', $target = '' ) {
		// Just add new column
		if ( ! $position ) {
			$columns[ $id ] = $title;

			return;
		}

		// Add new column in a specific position
		$new = array();
		switch ( $position ) {
			// Replace
			case 'replace':
				foreach ( $columns as $key => $value ) {
					if ( $key == $target ) {
						$new[ $id ] = $title;
					} else {
						$new[ $key ] = $value;
					}
				}
				break;
			case 'before':
				foreach ( $columns as $key => $value ) {
					if ( $key == $target ) {
						$new[ $id ] = $title;
					}
					$new[ $key ] = $value;
				}
				break;
			case 'after':
				foreach ( $columns as $key => $value ) {
					$new[ $key ] = $value;
					if ( $key == $target ) {
						$new[ $id ] = $title;
					}
				}
				break;
			default:
				return;
		}
		$columns = $new;
	}

	/**
	 * Find field by ID
	 *
	 * @param string $field_id
	 *
	 * @return array|bool False if not found. Array of field parameters if found.
	 */
	protected function find_field( $field_id ) {
		$fields = wp_list_filter( $this->fields, array( 'id' => $field_id ) );

		return empty( $fields ) ? false : reset( $fields );
	}
}
