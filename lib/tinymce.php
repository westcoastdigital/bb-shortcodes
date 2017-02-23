<?php

// Hooks your functions into the correct filters
function wcd_mce_button() {
	// check user permissions
	if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
		return;
	}
	// check if WYSIWYG is enabled
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'wcd_add_mce_plugin' );
		add_filter( 'mce_buttons', 'wcd_register_mce_button' );
	}
}
add_action( 'admin_head', 'wcd_mce_button' );

// Script for our mce button
function wcd_add_mce_plugin( $plugin_array ) {
	$plugin_array['wcd_mce_button'] = plugin_dir_url( __FILE__ ) . 'tinymce.js';
	return $plugin_array;
}

// Register our button in the editor
function wcd_register_mce_button( $buttons ) {
	array_push( $buttons, 'wcd_mce_button' );
	return $buttons;
}

// Function to fetch cpt posts list
function wcd_posts( $post_type ) {

	global $wpdb;
   	$cpt_type = 'fl-builder-template';
	$cpt_post_status = 'publish';
        $cpt = $wpdb->get_results( $wpdb->prepare(
        "SELECT ID, post_title
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_type = %s
            AND $wpdb->posts.post_status = %s
            ORDER BY ID DESC",
        $cpt_type,
        $cpt_post_status
    ) );

    $list = array();

    foreach ( $cpt as $post ) {
		$selected = '';
		$post_id = $post->ID;
		$post_name = $post->post_title;
		$list[] = array(
			'text' =>	$post_name,
			'value'	=>	$post_id
		);
	}

	wp_send_json( $list );
}

// Function to fetch buttons
function wcd_list_ajax() {
	// check for nonce
	check_ajax_referer( 'wcd-nonce', 'security' );
	$posts = wcd_posts( 'post' );
	return $posts;
}
add_action( 'wp_ajax_wcd_cpt_list', 'wcd_list_ajax' );

// Function to output button list ajax script
function wcd_cpt_list() {
	// create nonce
	global $pagenow;
	if( $pagenow != 'admin.php' ){
		$nonce = wp_create_nonce( 'wcd-nonce' );
		?><script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				var data = {
					'action'	: 'wcd_cpt_list', // wp ajax action
					'security'	: '<?php echo $nonce; ?>' // nonce value created earlier
				};
				// fire ajax
			  	jQuery.post( ajaxurl, data, function( response ) {
			  		// if nonce fails then not authorized else settings saved
			  		if( response === '-1' ){
				  		// do nothing
				  		console.log('error');
			  		} else {
			  			if (typeof(tinyMCE) != 'undefined') {
			  				if (tinyMCE.activeEditor != null) {
								tinyMCE.activeEditor.settings.cptPostsList = response;
							}
						}
			  		}
			  	});
			});
		</script>
<?php 
	}
}
add_action( 'admin_footer', 'wcd_cpt_list' );
