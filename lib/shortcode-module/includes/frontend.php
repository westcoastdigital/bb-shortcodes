<?php 

$template = $settings->template;

if ( ! empty( $template ) ) {
    echo do_shortcode('[fl_builder_insert_layout slug="'. $template .'" type="fl-builder-template"]'); 
}


?>