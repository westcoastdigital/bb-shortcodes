<?php

class BBShortcodeClass extends FLBuilderModule {

    public function __construct()
    {
        parent::__construct(array(
            'name'              => __('Template', 'bb-shortcode'),
            'description'       => __('Add a template to your site.', 'bb-shortcode'),
            'category'		    => __('Basic Modules', 'bb-shortcode'),
            'dir'               => BB_SHORTCODE_MODULE_DIR . '/lib/shortcode-module/',
            'url'               => BB_SHORTCODE_MODULE_URL . '/lib/shortcode-module/',
            'partial_refresh'   => true,
        ));
    }
}

FLBuilder::register_module('BBShortcodeClass', array(
    'content'       => array(
        'title'         => __('Template Selection', 'bb-shortcode'),
        'sections'      => array(
            'template-section'          => array(
                'title'         => __('Template Selection', 'bb-shortcode'),
                'fields'        => array(
                    'template' => array(
                        'type'          => 'select',
                        'label'         => __( 'Template', 'bb-shortcode' ),
                        'options'       => wcd_template_options(),
                    ), // end template field
                ) // end fields
            ) // end template
        ) // end sections
    ) // end content tab
));

function wcd_template_options ( $posts = array( '' => 'Choose a template') ) { 
    
    $args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post_type'        => 'fl-builder-template',
        'post_status'      => 'publish',
        'suppress_filters' => true 
    );
    $posts_array = get_posts( $args );

    if ( ! empty( $posts_array ) ) {

        foreach ( $posts_array as $post ) {
            $title = $post->post_title;
            $slug = $post->post_name;

            $posts += array(
                $slug => $title
            );
        }

    }   

    return apply_filters( 'wcd_template_options', $posts );
}