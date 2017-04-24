<?php

require_once(PIXFLOW_THEME_LIB .'/post-types/post-type.php');

class PixflowPortfolio extends PixflowPostType
{

    function __construct()
    {
        parent::__construct('portfolio');
    }

    function Pixflow_CreatePostType()
    {
        
        $labels = array(
            'name' => __( 'Portfolio', 'px-portfolio'),
            'singular_name' => __( 'Portfolio', 'px-portfolio' ),
            'add_new' => __('Add New', 'px-portfolio'),
            'add_new_item' => __('Add New Portfolio', 'px-portfolio'),
            'edit_item' => __('Edit Portfolio', 'px-portfolio'),
            'new_item' => __('New Portfolio', 'px-portfolio'),
            'view_item' => __('View Portfolio', 'px-portfolio'),
            'search_items' => __('Search Portfolio', 'px-portfolio'),
            'not_found' =>  __('No portfolios found', 'px-portfolio'),
            'not_found_in_trash' => __('No portfolios found in Trash', 'px-portfolio'),
            'parent_item_colon' => ''
        );
        $args = array(
            'labels' =>  $labels,
            'public' => true,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_icon' => PIXFLOW_THEME_LIB_URI . '/assets/img/portfolio-icon.png',
            'rewrite' => array('slug' => __( 'portfolios', 'px-portfolio' ), 'with_front' => true),
            'supports' => array('title',
                'editor',
                'thumbnail',
                'post-formats'
            ),
        );
        register_post_type( 'portfolio', $args );

        /* Register the corresponding taxonomy */
        register_taxonomy('skills', $this->postType,
            array("hierarchical" => true,
                "label" => __( "Skills", 'massive-dynamic' ),
                "singular_label" => __( "Skill",  'massive-dynamic' ),
                "rewrite" => false,
            ));

        // Add meta box goes into our admin_init function

    }

    function Pixflow_RegisterScripts()
    {
        wp_register_script('portfolio', PIXFLOW_THEME_LIB_URI . '/post-types/js/portfolio.js', array('jquery'), PIXFLOW_THEME_VERSION);

        parent::Pixflow_RegisterScripts();
    }

    function Pixflow_EnqueueScripts()
    {
        if (! wp_script_is( 'niceScroll', 'enqueued' )) {
            wp_enqueue_script( 'niceScroll',pixflow_path_combine(PIXFLOW_THEME_LIB_URI, 'assets/script/jquery.nicescroll.min.js'),false,PIXFLOW_THEME_VERSION,true);
        }
        wp_enqueue_script('portfolio');
    }
}
function pixflow_portfolio(){
    new PixflowPortfolio();
}
add_action('after_setup_theme', 'pixflow_portfolio');



