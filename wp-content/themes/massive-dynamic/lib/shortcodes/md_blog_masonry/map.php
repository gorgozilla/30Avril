<?php
/**
 * Pixflow
 */


/*-----------------------------------------------------------------------------------*/
/*  Blog Masonry
/*-----------------------------------------------------------------------------------*/
$posts_cats = array();
$terms = get_terms('category', 'orderby=count&hide_empty=0');
if (!empty($terms) && !is_wp_error($terms)) {
    foreach ($terms as $term) {
        $posts_cats[] = $term->name;
    }
}
pixflow_map(
    array(
        'base' => 'md_blog_masonry',
        'name' => esc_attr__('Blog Masonry', 'massive-dynamic'),
        "show_settings_on_create" => false,
        "category" => esc_attr__('Media','massive-dynamic'),
        'params' => array(
            array(
                "type" => "md_group_title",
                "heading" => esc_attr__("Categories", 'massive-dynamic'),
                "param_name" => "category_group",
                'group' => esc_attr__('General', 'massive-dynamic'),
                "edit_field_class" => $filedClass . "glue first ",
            ),
            array(
                'type' => 'md_vc_multiselect',
                "edit_field_class" => $filedClass . "first glue",
                'heading' => esc_attr__('Category', 'massive-dynamic'),
                'param_name' => 'blog_category',
                'items' => $posts_cats,
                'defaults' => 'all',
                'group' => esc_attr__('General', 'massive-dynamic')
            ),
            array(
                "type" => 'md_vc_separator',
                "param_name" => "blog_category_separator" . ++$separatorCounter,
                "admin_label" => false,
                'group' => esc_attr__('General', 'massive-dynamic')
            ),
            array(
                "type" => 'md_vc_slider',
                "heading" => esc_attr__("Post Number", 'massive-dynamic'),
                "param_name" => "blog_post_number",
                "value" => "5",
                "edit_field_class" => $filedClass . "glue last",
                'defaultSetting' => array(
                    "min" => "0",
                    "max" => "30",
                    "prefix" => "",
                    "step" => '1',
                ),
                'group' => esc_attr__('General', 'massive-dynamic')

            ),
            array(
                "type" => "md_group_title",
                "heading" => esc_attr__("appearance", 'massive-dynamic'),
                'group' => esc_attr__('General', 'massive-dynamic'),
                "param_name" => "app_group",
                "edit_field_class" => $filedClass . "glue first ",
            ),
            array(
                "type" => "dropdown",
                "edit_field_class" => $filedClass . "glue first ",
                "heading" => esc_attr__("Column Number", 'massive-dynamic'),
                "param_name" => "blog_column",
                "group" => esc_attr__("General", 'massive-dynamic'),
                "value" => array(
                    esc_attr__("Three", 'massive-dynamic') => "three",
                    esc_attr__("Four", 'massive-dynamic') => "four",
                ),
            ),
            array(
                "type" => "md_vc_colorpicker",

                "edit_field_class" => $filedClass . "glue",
                "heading" => esc_attr__("Background Color", 'massive-dynamic'),
                "param_name" => "blog_background_color",
                'group' => esc_attr__('General', 'massive-dynamic'),
                "value" => 'rgb(87,63,203)',
                "admin_label" => false,
                "opacity" => false,
            ),
            array(
                "type" => 'md_vc_separator',
                "param_name" => "blog_background_color_separator" . ++$separatorCounter,
                'group' => esc_attr__('General', 'massive-dynamic'),
                "admin_label" => false,
            ),
            array(
                "type" => "md_vc_colorpicker",

                "edit_field_class" => $filedClass . "glue",
                "heading" => esc_attr__("Text Color", 'massive-dynamic'),
                "param_name" => "blog_foreground_color",
                'group' => esc_attr__('General', 'massive-dynamic'),
                "value" => 'rgb(255,255,255)',
                "admin_label" => false,
                "opacity" => false,
            ),
            array(
                "type" => 'md_vc_separator',
                "param_name" => "blog_foreground_color_separator" . ++$separatorCounter,
                'group' => esc_attr__('General', 'massive-dynamic'),
                "admin_label" => false,
            ),
            array(
                "type" => "md_vc_colorpicker",

                "edit_field_class" => $filedClass . "glue ",
                "heading" => esc_attr__("Accent Color", 'massive-dynamic'),
                "param_name" => "blog_accent_color",
                'group' => esc_attr__('General', 'massive-dynamic'),
                "value" => 'rgb(220,38,139)',
                "admin_label" => false,
                "opacity" => false,
            ), array(
                "type" => 'md_vc_separator',
                "param_name" => "blog_accent_color_separator" . ++$separatorCounter,
                'group' => esc_attr__('General', 'massive-dynamic'),
                "admin_label" => false,
            ),
            array(
                "type" => "md_vc_colorpicker",
                'group' => esc_attr__('General', 'massive-dynamic'),
                "edit_field_class" => $filedClass . "glue",
                "heading" => esc_attr__("Text Accent Color", 'massive-dynamic'),
                "param_name" => "blog_text_accent_color",
                "value" => 'rgb(0,0,0)',
                "admin_label" => false,
                "opacity" => false,
            ),
            array(
                "type" => 'md_vc_separator',
                "param_name" => "blog_accent_color_separator" . ++$separatorCounter,
                'group' => esc_attr__('General', 'massive-dynamic'),
                "admin_label" => false,
            ), array(
                "type" => "md_vc_colorpicker",
                "edit_field_class" => $filedClass . "glue last",
                "heading" => esc_attr__("Shadow Color", 'massive-dynamic'),
                'group' => esc_attr__('General', 'massive-dynamic'),
                "param_name" => "blog_post_shadow",
                "value" => 'rgba(0,0,0,.12)',
                "admin_label" => false,
                "opacity" => true,
            ),
        )
    )
);

pixflow_add_params('md_blog_masonry', pixflow_addAnimationTab('md_blog_masonry'));
