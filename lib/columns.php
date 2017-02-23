<?php

class WCD_BB_Template_Admin_Columns extends MB_Admin_Columns_Post {
	public function columns( $columns ) {
		$columns = parent::columns( $columns );

		$position = '';
		$target   = '';
		$this->add( $columns, 'shortcode', 'Shortcode', $position, $target );

		return $columns;
	}
	public function show( $column, $post_id ) {
        $slug = basename( get_permalink($post_id) );
        $blog_id = get_current_blog_id();
		switch ( $column ) {
			case 'shortcode':
				$shortcode .= '<p id="shortcode-'.$post_id.'">';
                $shortcode .= '[fl_builder_insert_layout slug="'.$slug.'" type="fl-builder-template" site="'.$blog_id.'"]';
                $shortcode .= '</p>';
                $shortcode .= '<button class="button button-primary shortcode-btn" data-clipboard-target="#shortcode-'.$post_id.'">Copy Shortcode</button>';
                echo $shortcode;
				break;
		}
	}
}