<?php
global $in_mbuilder;
if(
    (
        ! defined( 'DOING_AJAX' )
        && ! $in_mbuilder
        && strpos( $_SERVER[ 'REQUEST_URI' ], 'post.php' ) === false
        && strpos( $_SERVER[ 'REQUEST_URI' ], 'post_new.php') === false
    )
    || isset($_POST['action']) && $_POST['action'] == 'mBuilder_saveContent'
){
    return '';
}

$filedClass = 'vc_col-sm-12 vc_column ';
$separatorCounter = 0;

/* custom icon picker field */
function pixflow_vc_iconpicker_field($settings, $value)
{

    return '<button value="' . $value . '" input-class="wpb_vc_param_value wpb-textinput px-input-vc-icon'
    . $settings['param_name'] . ' ' . $settings['type'] . '_field" name="' . $settings['param_name'] . '" class="iconpicker" data-original-title="" title="">'
    . '</button>';
}

/* custom date picker field */
function pixflow_vc_datepicker_field($settings, $value)
{
    return '<input type="text" value="' . $value . '" name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-textinput ' . $settings['type'] . '_field md_vc_datepicker" data-timepicker="true" data-language="en"  data-time-format="hh:ii"/>';
}

/* custom base64 text field  */
function pixflow_vc_base64_text_field($settings, $value){

    if( preg_match('/pixflow_base64/' , $value)){
        $value = str_replace('pixflow_base64' , '' , $value);
        $value = base64_decode($value);
    }
    return '<input type="text" name="'.$settings['param_name'].'_text" class="to-base64 mbuilder-skip">
            <input type="hidden" name="'.$settings['param_name'].'" value="pixflow_base64'.base64_encode($value).'" class="wpb_vc_param_value wpb-textinput ">';
}
/* custom base64 text field  */
function pixflow_vc_base64_textarea_field($settings, $value){

    if( preg_match('/pixflow_base64/' , $value)){
        $value = str_replace('pixflow_base64' , '' , $value);
        $value = base64_decode($value);
    }
    return '<textarea name="'.$settings['param_name'].'_text" class="to-base64 mbuilder-skip">'.$value.'</textarea>
            <textarea name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-textinput mBuilder-hidden hidden">pixflow_base64'.base64_encode($value).'</textarea>';
}
/* custom color picker field */
function pixflow_vc_colorpicker_field($settings, $value)
{

    $opacity = (isset($settings['opacity']) && $settings['opacity'] === true) ? 'true' : 'false';
    $defaultColor = (isset($settings['defaultColor']) && $settings['defaultColor'] != '') ? $settings['defaultColor'] : '#000';
    $value = ($value != '') ? $value : $defaultColor;
    $id = uniqid();
    return '<input id="' . $id . '" opacity="' . $opacity . '" type="text" value="' . $value . '" name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-textinput ' . $settings['type'] . '_field md_vc_colorpicker" />';
}

/* custom gradient color picker field */
function pixflow_vc_gradientcolorpicker_field($settings, $value)
{
    if( preg_match('/pixflow_base64/' , $value)){
        $value = str_replace('pixflow_base64' , '' , $value);
        $value = base64_decode($value);
    }
    $value = str_replace('``', '"', $value);
    $value = str_replace('\'', '"', $value);
    $output = '';
    $defaults = (object)array('color1' => '#fff', 'color2' => '#000', 'color1Pos' => '0', 'color2Pos' => '100', 'angle' => '0');
    $defaultColor = (isset($settings['defaultColor']) && $settings['defaultColor'] != '') ? $settings['defaultColor'] : $defaults;
//    $value = (!isset($value)) ? $defaultColor : $value;

    $value = ($value != '' && isset($value)) ? json_decode($value) : $defaultColor;
    $value = ($value == null)?$defaultColor:$value;
    $id = uniqid();
    $output .= '<input id="input-' . $id . '" type="text" value="' . json_encode($value) . '" name="' . $settings['param_name'] . '" class="md-hidden wpb_vc_param_value wpb-textinput ' . $settings['type'] . '_field md_vc_gradientcolorpicker md-base64" />';
    $output .= '<div id="' . $id . '" pos1="' . $value->{"color1Pos"} . '" pos2="' . $value->{"color2Pos"} . '" col1="' . $value->{"color1"} . '" col2="' . $value->{"color2"} . '" class="gradient_color_picker"></div>';
    $output .= '<br/><br/>';
    $output .= '<div angle="' . $value->{"angle"} . '" gID="' . $id . '" id="angle-' . $id . '" class="gradient_color_picker_angle"></div><input type="text" id="angleValue-' . $id . '" class="gradient-angle" value="' . $value->{"angle"} . '" />';
    return $output;
}

/* custom range slider controller */
function pixflow_vc_slider_field($settings, $value)
{
// Note : You can define these parameters to your range slider --> min, max, prefix, step.
    $output = '';
    $defaults = array('min' => '0', 'max' => '100', 'prefix' => '%', 'step' => '1', 'decimal' => '0');
    $defaultSetting = (isset($settings['defaultSetting']) && $settings['defaultSetting'] != '') ? $settings['defaultSetting'] : $defaults;
    $defaultSetting['decimal'] = (isset($defaultSetting['decimal'])) ? $defaultSetting['decimal'] : 0;
    if ((int)$defaultSetting['step'] < 1) {
        $value = ((float)$value === '') ? $defaultSetting['min'] : (float)$value;
        $value = number_format($value, 1);
    } else {
        $value = ((int)$value === '') ? $defaultSetting['min'] : (int)$value;
    }
    $id = uniqid();
    $defaultSetting['prefix'] = isset($defaultSetting['prefix']) ? $defaultSetting['prefix'] : '';
    $output .= '<input id="input-' . $id . '" type="text" value="' . $value . '" name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-textinput ' . $settings['type'] . '_field md_vc_slider" />';
    $output .= '<div value="' . $value . '" id="' . $id . '" class="vc_slider" min="' . $defaultSetting['min'] . '" max="' . $defaultSetting['max'] . '" prefix="' . $defaultSetting['prefix'] . '" step="' . $defaultSetting['step'] . '" decimal="' . $defaultSetting['decimal'] . '" ></div>';
    $output .= '<div id="' . $id . '" class="vc_slider_value" value="' . $value . '">' . $value . $defaultSetting['prefix'] . '</div>';
    return $output;
}

/* Spacing field */
function pixflow_vc_spacing_field($settings,$value){
    $value = str_replace('``', '"', $value);
    $value = str_replace("'", '"', $value);

    $value = ($value != '' && isset($value)) ? json_decode($value,true) : $settings['defaultSetting'];
    $value = ($value == null)?$settings['defaultSetting']:$value;

    $output = '';
    $id = uniqid();

    $output = '<div class="spacing-field">';
    $output .= '<input id="' . $id . '" type="text" value=\'' . json_encode($value) . '\' name="' . $settings['param_name'] . '" class="md-hidden wpb_vc_param_value wpb-textinput ' . $settings['type'] . '_field md_vc_spacing" />';
    $output .= '<input class="val margin-left mbuilder-skip" name="'.$settings['param_name'].'_marginLeft" value="'.$value['marginLeft'].'"><input class="val margin-top mbuilder-skip" name="'.$settings['param_name'].'_marginTop" value="'.$value['marginTop'].'"><input class="val margin-right mbuilder-skip" name="'.$settings['param_name'].'_marginRight" value="'.$value['marginRight'].'"><input class="val margin-bottom mbuilder-skip" name="'.$settings['param_name'].'_marginBottom" value="'.$value['marginBottom'].'">';
    $output .= '<div class="border"><input class="val padding-left mbuilder-skip" name="'.$settings['param_name'].'_paddingLeft" value="'.$value['paddingLeft'].'"><input class="val padding-top mbuilder-skip" name="'.$settings['param_name'].'_paddingTop" value="'.$value['paddingTop'].'"><input class="val padding-right mbuilder-skip" name="'.$settings['param_name'].'_paddingRight" value="'.$value['paddingRight'].'" ><input class="val padding-bottom mbuilder-skip" name = "'.$settings['param_name'].'_paddingBottom" value="'.$value['paddingBottom'].'"></div>';
    $output .= '</div>';
    return $output;

}

/* custom multiselect field */
function pixflow_vc_multiselect_field($settings, $value)
{
    $output = '';
    $items = (isset($settings['items']) && is_array($settings['items'])) ? $settings['items'] : array();
    $defaults = (isset($settings['defaults']) && $settings['defaults'] == 'all') ? $items : array();
    $value = ($value != '') ? $value : implode(',', $defaults);
    $values = explode(',', $value);
    $id = uniqid();
    $output .= '<input id="input-' . $id . '" type="text" value="' . $value . '" name="' . $settings['param_name'] . '" class="md-hidden wpb_vc_param_value wpb-textinput ' . $settings['type'] . '_field md_vc_muliselect" />';
    ob_start();
    ?>
    <dl class="dropdown" xmlns="http://www.w3.org/1999/html">
        <dt>
            <a href="#">
                <span class="hida"><?php esc_attr_e('Select Items', 'massive-dynamic') ?></span>
                <span data-id="<?php echo esc_attr($id); ?>" class="multiSel"></span>
            </a>
        </dt>
        <dd>
            <div class="mutliSelect">
                <ul>
                    <?php if (count($items) < 1) { ?>
                        <li><?php esc_attr_e('No items to select!', 'massive-dynamic') ?></li>
                    <?php } else { ?>
                        <?php foreach ($items as $item) { ?>
                            <li>
                                <input
                                    data-id="<?php echo esc_attr($id); ?>" <?php echo (in_array($item, $values)) ? 'checked="checked"' : ''; ?>
                                    type="checkbox"
                                    value="<?php echo esc_attr($item); ?>"/><?php echo esc_attr($item); ?>
                            </li>
                        <?php }
                    } ?>
                </ul>
            </div>
        </dd>
    </dl>
    <?php
    $output .= ob_get_clean();
    return $output;
}

function pixflow_vc_checkbox_field($settings, $value)
{
    $output = '';
    $id = uniqid();
    if (is_array($value)) {
        foreach ($value as $val) {
            $value = $val;
            break;
        }
    }
    $checked = checked($value, 'yes', false);
    $output .= '<input ' . $checked . ' data-name=' . $settings['param_name'] . '  el-id="' . $id . '" value="' . $value . '" class="wpb_vc_param_value ' . $settings['param_name'] . ' ' . $settings['type'] . '" type="checkbox" > ';
    $output .= '<input id="' . $settings['param_name'] . '-" el-id="' . $id . '" type="hidden" value="' . $value . '" name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-textinput ' . $settings['type'] . '_field ' . $settings['param_name'] . ' md_vc_checkbox" />';

    return $output;
}

if (!function_exists('js_composer_bridge_admin')) {

    function pixflow_js_composer_scripts_admin()
    {

        // Find Where we are
        $bodyClasess = get_body_class();

        // "no-customize-support" show we are in dashboard

        if (!in_array('no-customize-support', $bodyClasess)) {
            {
                //Register RTL inner Style
                wp_enqueue_style('rtl-inner-customizer', PIXFLOW_THEME_LIB_URI . '/assets/css/rtl-inner-customizer.min.css');
            }
        }

        // For dashboard Env.

        if (is_rtl()) {
            //Register RTL inner Style
            wp_enqueue_style('rtl-dashboard', PIXFLOW_THEME_LIB_URI . '/assets/css/rtl-dashboard.min.css');
            wp_enqueue_style('rtl-dashboard', PIXFLOW_THEME_LIB_URI . '/assets/css/rtl-dashboard.min.css');
        }

        // run out of admin pannel

        if (is_customize_preview() && is_rtl()) {
            //Register RTL Style
            wp_enqueue_style('rtl-style', pixflow_path_combine(PIXFLOW_THEME_URI, 'rtl-customizer.min.css'), array(), PIXFLOW_THEME_VERSION);
        }


        // presscore stuff
        wp_enqueue_style('', PIXFLOW_THEME_LIB_URI . '/assets/css/vc-extend.min.css');

        wp_enqueue_style('controller-rgba', pixflow_path_combine(PIXFLOW_THEME_CUSTOMIZER_URI, 'assets/css/spectrum.min.css'), array(), PIXFLOW_THEME_VERSION);
        wp_enqueue_script('controller-rgba', pixflow_path_combine(PIXFLOW_THEME_CUSTOMIZER_URI, 'assets/js/spectrum.min.js'), array(), PIXFLOW_THEME_VERSION, true);

        wp_enqueue_style('nouislider-style', pixflow_path_combine(PIXFLOW_THEME_CUSTOMIZER_URI, 'assets/css/jquery.nouislider.min.css'), array(), PIXFLOW_THEME_VERSION);
        wp_enqueue_script('nouislider-script', pixflow_path_combine(PIXFLOW_THEME_CUSTOMIZER_URI, 'assets/js/jquery.nouislider.min.js'), array(), PIXFLOW_THEME_VERSION, true);
    }

}

function pixflow_vc_separator_field($settings)
{
    return '<hr/>' . '<input class="wpb_vc_param_value wpb-textinput" type="hidden" name="' . $settings['param_name'] . '">';
}

function pixflow_vc_url_field($settings, $value)
{
    $output = '';
    $id = esc_attr(uniqid());
    ob_start();
    ?>
    <div class="md_vc_url_control">
        <input id="<?php echo esc_attr($id) ?>" type="text" value="<?php echo esc_attr($value); ?>"
               name="<?php echo esc_attr($settings['param_name']); ?>"
               class="wpb_vc_param_value wpb-textinput <?php echo esc_attr($settings['type']) . '_field'; ?> md_vc_url"/>
        <textarea onclick="this.focus();this.select()" readonly id="<?php echo esc_attr('url_' . $id) ?>" class="add"
                  rows="4" cols="50"><?php esc_attr_e('Type section name and copy URL', 'massive-dynamic') ?></textarea>
    </div>
    <?php
    $output .= ob_get_clean();
    return $output;
}

function pixflow_vc_description_field($settings)
{
    return "<div class='content'>" . $settings['value'] . "</div>" . '<input class="wpb_vc_param_value wpb-textinput" type="hidden" name="' . $settings['param_name'] . '">';
}



//hooks
add_action('admin_enqueue_scripts', 'pixflow_js_composer_scripts_admin', 15);

// Removing shortcodes

pixflow_remove_element("vc_wp_meta");
pixflow_remove_element("vc_wp_recentcomments");
pixflow_remove_element("vc_wp_pages");
pixflow_remove_element("vc_wp_custommenu");
//pixflow_remove_element("vc_wp_text");
pixflow_remove_element("vc_wp_posts");
pixflow_remove_element("vc_wp_links");
pixflow_remove_element("vc_wp_categories");
pixflow_remove_element("vc_wp_archives");
pixflow_remove_element("vc_wp_rss");
//pixflow_remove_element("vc_teaser_grid");
pixflow_remove_element("vc_button");
pixflow_remove_element("vc_button2");
pixflow_remove_element("vc_cta_button");
pixflow_remove_element("vc_cta_button2");
pixflow_remove_element("vc_message");
pixflow_remove_element("vc_progress_bar");
pixflow_remove_element("vc_pie");
pixflow_remove_element("vc_posts_slider");
//pixflow_remove_element("vc_posts_grid");
pixflow_remove_element("vc_carousel");
pixflow_remove_element("vc_images_carousel");
//pixflow_remove_element("vc_column_text");
pixflow_remove_element("vc_separator");
pixflow_remove_element("vc_text_separator");
pixflow_remove_element("vc_toggle");
pixflow_remove_element("vc_single_image");
pixflow_remove_element("vc_gallery");
pixflow_remove_element("vc_tabs");
pixflow_remove_element("vc_tour");
pixflow_remove_element("vc_accordion");
pixflow_remove_element("vc_video");
//pixflow_remove_element("vc_raw_html");
//pixflow_remove_element("vc_raw_js");
pixflow_remove_element("vc_flickr");
pixflow_remove_element("vc_custom_heading");
//pixflow_remove_element("vc_basic_grid");
//pixflow_remove_element("vc_media_grid");
//pixflow_remove_element("vc_masonry_grid");
//pixflow_remove_element("vc_masonry_media_grid");
pixflow_remove_element("vc_icon");
//pixflow_remove_element("vc_btn");
pixflow_remove_element("vc_cta");
pixflow_remove_element("vc_wp_search");
pixflow_remove_element("vc_wp_calendar");
pixflow_remove_element("vc_wp_calendar");
pixflow_remove_element("vc_wp_tagcloud");
pixflow_remove_element("vc_tta_tabs");
pixflow_remove_element("vc_tta_tour");
pixflow_remove_element("vc_tta_accordion");
pixflow_remove_element("vc_tta_section");
pixflow_remove_element("vc_tta_pageable");
pixflow_remove_element("vc_widget_sidebar");

$sociallink = array(
    'Facebook' => 'facebook3',
    'Twitter' => 'twitter2',
    'Vimeo' => 'vimeo',
    'YouTube' => 'youtube',
    'Google+' => 'googleplus2',
    'Dribbble' => 'dribbble2',
    'Tumblr' => 'tumblr2',
    'linkedin' => 'linkedin2',
    'Flickr' => 'flickr2',
    'forrst' => 'forrst',
    'github' => 'github',
    'lastfm' => 'lastfm',
    'paypal' => 'paypal',
    'RSS' => 'feed2',
    'skype' => 'skype',
    'wordpress' => 'wordpress',
    'yahoo' => 'yahoo',
    'steam' => 'steam',
    'reddit' => 'reddit',
    'stumbleupon' => 'stumbleupon',
    'pinterest' => 'pinterest',
    'deviantart' => 'deviantart2',
    'xing' => 'xing',
    'blogger' => 'blogger',
    'soundcloud' => 'soundcloud',
    'delicious' => 'delicious',
    'foursquare' => 'foursquare',
    'instagram' => 'instagram'
);

/******************* shortcode row ***************************/
// remove css animation and disable
pixflow_remove_param('vc_row', 'css_animation');
pixflow_remove_param('vc_column', 'css_animation');
pixflow_remove_param('vc_row', 'disable_element');
// remove bg image
pixflow_remove_param('vc_row', 'bg_image');
pixflow_remove_param('vc_row', 'full_height');
pixflow_remove_param('vc_row', 'content_placement');
pixflow_remove_param('vc_row', 'video_bg');
pixflow_remove_param('vc_row', 'video_bg_url');
pixflow_remove_param('vc_row', 'video_bg_parallax');

// remove get class
pixflow_remove_param('vc_row', 'el_class');

// remove get id
pixflow_remove_param('vc_row', 'el_id');

// remove default parallax
pixflow_remove_param('vc_row', 'parallax');

// remove stretch
pixflow_remove_param('vc_row', 'full_width');

// remove default parallax
pixflow_remove_param('vc_row', 'parallax_image');

// remove default css editor
pixflow_remove_param('vc_row', 'css');

// remove columns gap
pixflow_remove_param('vc_row', 'gap');

// remove columns position
pixflow_remove_param('vc_row', 'columns_placement');

// remove equal height
pixflow_remove_param('vc_row', 'equal_height');

// remove Parallax speed
pixflow_remove_param('vc_row', 'parallax_speed_video');
pixflow_remove_param('vc_row', 'parallax_speed_bg');

$row_setting = array(
    "name" => "Row",
    'show_settings_on_create' => false,
    "category" => esc_attr__("Structure", 'massive-dynamic'),
);

pixflow_map_update('vc_row', $row_setting);

$separator_setting = array(
    "'show_settings_on_create" => true,
    "controls" => '',
);

// row spacing - Padding all directions
pixflow_add_param('vc_row', array(
    'type' => 'md_vc_url',
    "weight" => "2",
    "heading" => esc_attr__("URL", 'massive-dynamic'),
    "param_name" => "row_section_id",
    "value" => "",
    'group' => esc_attr__("+URL", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue first last",
));

pixflow_add_param("vc_row", array(
    "type" => "md_vc_description",
    "param_name" => "row_section_id_description",
    "admin_label" => false,
    "group" => esc_attr__("+URL", 'massive-dynamic'),
    "value" => wp_kses(__("<strong>How to add this row in menu:</strong>
                    <ul>
                        <li>First enter a unique ID in URL field, this ID should not be used for any other row in this page</li>
                        <li>Click on generated URL and copy it, then press save changes button</li>
                        <li>In builder's sidebar, click on Menus, then click on your current menu or add a new one</li>
                        <li>Next, click on Add Items and choose Custom Links</li>
                        <li>Paste the generated URL in URL field and give it a name in Link Text field</li>
                        <li>Click on Add To Menu button and refresh your page</li>
                    </ul>", "massive-dynamic"), array('strong' => array(), 'ul' => array(), 'li' => array()))
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_slider',
    "weight" => "2",
    "heading" => esc_attr__("Padding Top", 'massive-dynamic'),
    "param_name" => "row_padding_top",
    "description" => esc_attr__("insert top padding for current row . example : 200 ", 'massive-dynamic'),
    "value" => "45",
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue first",
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "px",
        "step" => '5',
    )
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Spacing", 'massive-dynamic'),
    "param_name" => "row_padding_tab_separator" . ++$separatorCounter,
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_slider',
    "heading" => esc_attr__("Padding Bottom", 'massive-dynamic'),
    "param_name" => "row_padding_bottom",
    "description" => esc_attr__("insert bottom padding for current row . example : 200", 'massive-dynamic'),
    "value" => "45",
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue",
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "px",
        "step" => '5',
    )
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Spacing", 'massive-dynamic'),
    "param_name" => "row_padding_tab_separator" . ++$separatorCounter,
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_slider',
    "heading" => esc_attr__("Padding Right", 'massive-dynamic'),
    "param_name" => "row_padding_right",
    "description" => esc_attr__("insert Right padding for current row . example : 200", 'massive-dynamic'),
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue",
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "px",
        "step" => '5',
    )
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Spacing", 'massive-dynamic'),
    "param_name" => "row_padding_tab_separator" . ++$separatorCounter,
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_slider',
    "heading" => esc_attr__("Padding Left", 'massive-dynamic'),
    "param_name" => "row_padding_left",
    "description" => esc_attr__("insert left padding for current row . example : 200", 'massive-dynamic'),
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue last",
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "px",
        "step" => '5',
    )
));

// row spacing Margin only top and bottom

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_slider',
    "edit_field_class" => $filedClass . "glue first",
    "heading" => esc_attr__("Margin Top", 'massive-dynamic'),
    "param_name" => "row_margin_top",
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "px",
        "step" => '5',
    )
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Spacing", 'massive-dynamic'),
    "param_name" => "row_padding_tab_separator" . ++$separatorCounter,
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_slider',
    "edit_field_class" => $filedClass . "glue last",
    "heading" => esc_attr__("Margin Bottom", 'massive-dynamic'),
    "param_name" => "row_margin_bottom",
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "px",
        "step" => '5',
    )
));

// Background color overlay for default state
pixflow_add_param("vc_row", array(
    "type" => "md_vc_colorpicker",
    "edit_field_class" => $filedClass . "glue first last",
    "heading" => esc_attr__("Color", 'massive-dynamic'),
    "param_name" => "background_color",
    "group" => esc_attr__("BG [color]", 'massive-dynamic'),
    "weight" => "3",
    "opacity" => true,
    "admin_label" => false,
    "description" => esc_attr__("Choose a color to be used as this section's background. Please noticed that background color, has higher priority than background image.", 'massive-dynamic'),
    "value" => "rgba(255,255,255,1)",
    'dependency' => array(
        'element' => "row_type",
        'value' => array('none'),
    )
));

// Background Video for Video state
pixflow_add_param("vc_row", array(
    "type" => "textfield",
    "edit_field_class" => $filedClass . "glue first",
    "heading" => esc_attr__("Webm file URL", 'massive-dynamic'),
    "param_name" => "row_webm_url",
    "group" => esc_attr__("BG [video]", 'massive-dynamic'),
    "weight" => "3",
    "admin_label" => false,
    'dependency' => array(
        'element' => "row_type",
        'value' => array('video'),
    )
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("BG [video]", 'massive-dynamic'),
    "weight" => "3",
    "param_name" => "row_webm_url_separator" . ++$separatorCounter,
    "dependency" => array(
        'element' => "row_type",
        'value' => array('video')
    )
));

pixflow_add_param("vc_row", array(
    "type" => "textfield",
    "edit_field_class" => $filedClass . "glue",
    "heading" => esc_attr__("MP4 file URL", 'massive-dynamic'),
    "param_name" => "row_mp4_url",
    "group" => esc_attr__("BG [video]", 'massive-dynamic'),
    "weight" => "3",
    "admin_label" => false,
    'dependency' => array(
        'element' => "row_type",
        'value' => array('video'),
    )
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("BG [video]", 'massive-dynamic'),
    "weight" => "3",
    "param_name" => "row_poster_url_separator" . ++$separatorCounter,
    "dependency" => array(
        'element' => "row_type",
        'value' => array('video')
    )
));

pixflow_add_param("vc_row", array(
    "type" => "attach_image",
    "edit_field_class" => $filedClass . "glue last",
    "heading" => esc_attr__("Video Preview Image", 'massive-dynamic'),
    "param_name" => "row_poster_url",
    "group" => esc_attr__("BG [video]", 'massive-dynamic'),
    "weight" => "3",
    "admin_label" => false,
    'dependency' => array(
        'element' => "row_type",
        'value' => array('video'),
    )
));

pixflow_add_param("vc_row", array(
    "type" => "md_vc_description",
    "param_name" => "row_video_description",
    "admin_label" => false,
    "group" => esc_attr__("BG [video]", 'massive-dynamic'),
    'dependency' => array(
        'element' => "row_type",
        'value' => array('video'),
    ),
    "value" => "You should add a URL to videos in related fields. These URLs should either end with .mp4 or .webm . Video preview image will be shown when the video is not loaded yet.",
));

// Background color overlay for image state
pixflow_add_param("vc_row", array(
    "type" => "md_vc_colorpicker",
    "edit_field_class" => $filedClass . "glue first last",
    "heading" => esc_attr__("Color", 'massive-dynamic'),
    "param_name" => "background_color_image",
    "group" => esc_attr__("BG [Image]", 'massive-dynamic'),
    "weight" => "3",
    "opacity" => true,
    "admin_label" => false,
    "value" => "rgba(0,0,0,0.2)",
    'dependency' => array(
        'element' => "row_type",
        'value' => array('image'),
    )
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "edit_field_class" => $filedClass . "stick-to-top",
    "group" => esc_attr__("BG [Image]", 'massive-dynamic'),
    "weight" => "3",
    "param_name" => "row_bg_tab_separator" . ++$separatorCounter,
    "dependency" => array(
        'element' => "row_type",
        'value' => array('image')
    )
));

// Color Transition

pixflow_add_param("vc_row", array(
    "type" => "md_vc_colorpicker",
    "edit_field_class" => $filedClass . "glue first",
    "heading" => esc_attr__("Starting Color", 'massive-dynamic'),
    "param_name" => "first_color",
    "group" => esc_attr__("BG [Transition]", 'massive-dynamic'),
    "weight" => "3",
    "opacity" => false,
    "admin_label" => false,
    "description" => esc_attr__("Choose a second color as destination color for row background..", 'massive-dynamic'),
    'dependency' => array(
        'element' => "row_type",
        'value' => array('transition'),
    )
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("BG [Transition]", 'massive-dynamic'),
    "weight" => "3",
    "param_name" => "row_bg_tab_separator" . ++$separatorCounter,
    "dependency" => array(
        'element' => "row_type",
        'value' => array('transition')
    )
));

pixflow_add_param("vc_row", array(
    "type" => "md_vc_colorpicker",
    "edit_field_class" => $filedClass . "glue last",

    "heading" => esc_attr__("Destination Color", 'massive-dynamic'),
    "param_name" => "second_color",
    "group" => esc_attr__("BG [Transition]", 'massive-dynamic'),
    "weight" => "3",
    "opacity" => false,
    "admin_label" => false,
    "description" => esc_attr__("Choose a second color as destination color for row background..", 'massive-dynamic'),
    'dependency' => array(
        'element' => "row_type",
        'value' => array('transition'),
    )
));

pixflow_add_param("vc_row", array(
    "type" => "md_vc_description",
    "param_name" => "row_transition_description",
    "admin_label" => false,
    "group" => esc_attr__("BG [Transition]", 'massive-dynamic'),
    'dependency' => array(
        'element' => "row_type",
        'value' => array('transition'),
    ),
    "value" => esc_attr__("To see color transition correctly, it's better to have a row with great height. Try adding several elements inside same row.", 'massive-dynamic'),
));

// Gradient

pixflow_add_param("vc_row", array(
    "type" => "md_vc_gradientcolorpicker",
    "edit_field_class" => $filedClass . "glue first",
    "heading" => esc_attr__("Gradient", 'massive-dynamic'),
    "param_name" => "row_gradient_color",
    "group" => esc_attr__("BG [Gradient]", 'massive-dynamic'),
    "weight" => "3",
    "description" => esc_attr__("Choose a color to be used as this section's background. Please notice that background color, has higher priority than background image.", 'massive-dynamic'),
    'dependency' => array(
        'element' => "row_type",
        'value' => array('gradient')
    ),
    'defaultColor' => (object)array(
        'color1' => '#fff',
        'color2' => 'rgba(255,255,255,0)',
        'color1Pos' => '0',
        'color2Pos' => '100',
        'angle' => '0'),
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("BG [Gradient]", 'massive-dynamic'),
    "param_name" => "row_bg_tab_separator" . ++$separatorCounter,
    "weight" => "3",
    "admin_label" => false,
    "dependency" => array(
        'element' => "row_type",
        'value' => array('gradient')
    )
));

// Select image

pixflow_add_param("vc_row", array(
    'type' => 'attach_image',
    "edit_field_class" => $filedClass . "glue",
    'heading' => esc_attr__('Choose Image', 'massive-dynamic'),
    'param_name' => 'row_image',
    'description' => esc_attr__('choose image from media library.', 'massive-dynamic'),
    "value" => "",
    "group" => esc_attr__("BG [Image]", 'massive-dynamic'),
    "weight" => "3",
    'dependency' => array(
        'element' => "row_type",
        'value' => array('image')
    ),
));

pixflow_add_param("vc_row", array(
    'type' => 'attach_image',
    "edit_field_class" => $filedClass . "glue",
    'heading' => esc_attr__('Choose Image', 'massive-dynamic'),
    'param_name' => 'row_image_gradient',
    'description' => esc_attr__('choose image from media library.', 'massive-dynamic'),
    "value" => "",
    "group" => esc_attr__("BG [Gradient]", 'massive-dynamic'),
    "weight" => "3",
    'dependency' => array(
        'element' => "row_type",
        'value' => array('gradient')
    ),
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("BG [Image]", 'massive-dynamic'),
    "param_name" => "row_image_separator" . ++$separatorCounter,
    "weight" => "3",
    'dependency' => array(
        'element' => "row_type",
        'value' => array('image')
    ),
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("BG [Gradient]", 'massive-dynamic'),
    "param_name" => "row_image_separator" . ++$separatorCounter,
    "weight" => "3",
    'dependency' => array(
        'element' => "row_type",
        'value' => array('gradient')
    ),
));

pixflow_add_param("vc_row", array(
    "type" => "dropdown",
    "heading" => esc_attr__("Image Position", 'massive-dynamic'),
    "param_name" => "row_image_position",
    "group" => esc_attr__("BG [Image]", 'massive-dynamic'),
    "weight" => "3",
    "edit_field_class" => $filedClass . "glue last",
    "value" => array(
        esc_attr__("Fit to row", 'massive-dynamic') => "default",
        esc_attr__("Top", 'massive-dynamic') => "top",
        esc_attr__("Bottom", 'massive-dynamic') => "bottom",
    ),
    'dependency' => array(
        'element' => "row_type",
        'value' => array('image')
    ),
));

pixflow_add_param("vc_row", array(
    "type" => "dropdown",
    "weight" => "3",
    "heading" => esc_attr__("Image Position", 'massive-dynamic'),
    "param_name" => "row_image_position_gradient",
    "group" => esc_attr__("BG [Gradient]", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue last",
    "value" => array(
        esc_attr__("Fit to row", 'massive-dynamic') => "fit",
        esc_attr__("Top", 'massive-dynamic') => "top",
        esc_attr__("Bottom", 'massive-dynamic') => "bottom",
    ),
    'dependency' => array(
        'element' => "row_type",
        'value' => array('gradient')
    ),
));


// ***** row - general - tab *****

pixflow_add_param("vc_row", array(
    "type" => "dropdown",
    "weight" => "5",
    "heading" => esc_attr__("Row Background", 'massive-dynamic'),
    "param_name" => "row_type",
    "description" => esc_attr__("Choose different type of containers and set the options.", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue first last",
    "value" => array(
        esc_attr__("Solid Color", 'massive-dynamic') => "none",
        esc_attr__("Image", 'massive-dynamic') => "image",
        esc_attr__("Color Transition", 'massive-dynamic') => "transition",
        esc_attr__("Gradient and Image", 'massive-dynamic') => "gradient",
        esc_attr__("Video", 'massive-dynamic') => "video",
    ),
));

// Background Image Size On mage Tab
pixflow_add_param("vc_row", array(
    "type" => "dropdown",
    "edit_field_class" => $filedClass . "glue last first",
    "heading" => esc_attr__("Image Size", 'massive-dynamic'),
    "param_name" => "row_bg_image_size_tab_image",
    "description" => esc_attr__("Enable Image Size", 'massive-dynamic'),
    "group" => esc_attr__("BG [Image]", 'massive-dynamic'),
    "value" => array(
        esc_attr__("Stretch", 'massive-dynamic') => "cover",
        esc_attr__("Real Size", 'massive-dynamic') => "auto",
        esc_attr__("Fit To Height", 'massive-dynamic') => "contain",
    ),
    'dependency' => array(
        'element' => "row_type",
        'value' => array('image')
    ),
));

// Background Image Size On Gradient Tab
pixflow_add_param("vc_row", array(
    "type" => "dropdown",
    "edit_field_class" => $filedClass . "glue last first",
    "heading" => esc_attr__("Image Size", 'massive-dynamic'),
    "param_name" => "row_bg_image_size_tab_gradient",
    "description" => esc_attr__("Enable Image Size", 'massive-dynamic'),
    "group" => esc_attr__("BG [Gradient]", 'massive-dynamic'),
    "value" => array(
        esc_attr__("Stretch", 'massive-dynamic') => "cover",
        esc_attr__("Real Size", 'massive-dynamic') => "auto",
        esc_attr__("Fit To Height", 'massive-dynamic') => "contain",
    ),
    'dependency' => array(
        'element' => "row_type",
        'value' => array('gradient')
    ),
));

// Background width

pixflow_add_param("vc_row", array(
    "type" => "dropdown",
    "weight" => "4",
    "edit_field_class" => $filedClass . "first glue",

    "heading" => esc_attr__("Background Width", 'massive-dynamic'),
    "param_name" => "type_width",
    "description" => esc_attr__("Full width will use all of your screen width, while Boxed will created an invisible box in middle of your screen.", 'massive-dynamic'),
    "value" => array(
        esc_attr__("Full Screen", 'massive-dynamic') => "full_size",
        esc_attr__("Container", 'massive-dynamic') => "box_size",
    )
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "weight" => "4",
    "param_name" => "row_bg_tab_separator" . ++$separatorCounter,
));

// Content width

pixflow_add_param("vc_row", array(
    "type" => "dropdown",
    "weight" => "4",
    "edit_field_class" => $filedClass . "glue last",
    "heading" => esc_attr__("Content Width", 'massive-dynamic'),
    "param_name" => "box_size_states",
    "description" => esc_attr__("Full width will use all of your screen width, while Boxed will created an invisible box in middle of your screen.", 'massive-dynamic'),
    "value" => array(
        esc_attr__("Container", 'massive-dynamic') => "content_box_size",
        esc_attr__("Full Screen", 'massive-dynamic') => "content_full_size",
    )
));


// Inner shadow

pixflow_add_param("vc_row", array(
    "type" => "md_vc_checkbox",
    "weight" => "3",
    "edit_field_class" => $filedClass . "glue first last",
    "param_name" => "row_inner_shadow",
    "heading" => esc_attr__('Inner shadow', 'massive-dynamic'),
    'checked' => false,
    'value' => array(esc_attr__('No', 'massive-dynamic') => 'no'),
    "group" => esc_attr__("Design", 'massive-dynamic'),
));

// Sloped Row Edges

pixflow_add_param("vc_row", array(
    "type" => "md_vc_checkbox",
    "edit_field_class" => $filedClass . "glue first last",
    "param_name" => "row_sloped_edge",
    "heading" => esc_attr__('Sloped Edge', 'massive-dynamic'),
    'value' => array(esc_attr__('No', 'massive-dynamic') => 'no'),
    'checked' => false,
    "group" => esc_attr__("Design", 'massive-dynamic'),
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "edit_field_class" => $filedClass . "stick-to-top",
    "param_name" => "row_sloped_edge" . ++$separatorCounter,
    'dependency' => array(
        'element' => "row_sloped_edge",
        'value' => array('yes')
    ),
    "group" => esc_attr__("Design", 'massive-dynamic'),
));

pixflow_add_param("vc_row", array(
    "type" => "dropdown",
    "edit_field_class" => $filedClass . "glue last",
    "heading" => esc_attr__("Slope Position", 'massive-dynamic'),
    "param_name" => "row_slope_edge_position",
    "description" => esc_attr__("Choose to have sloped edge on top, down or both position of your row.", 'massive-dynamic'),
    "value" => array(
        esc_attr__("Top of Row", 'massive-dynamic') => "top",
        esc_attr__("Bottom of Row", 'massive-dynamic') => "bottom",
        esc_attr__("Top and Bottom", 'massive-dynamic') => "both",
    ),
    'dependency' => array(
        'element' => "row_sloped_edge",
        'value' => array('yes')
    ),
    "group" => esc_attr__("Design", 'massive-dynamic'),
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "edit_field_class" => $filedClass . "stick-to-top",
    "param_name" => "row_sloped_edge" . ++$separatorCounter,
    'dependency' => array(
        'element' => "row_sloped_edge",
        'value' => array('yes')
    ),
    "group" => esc_attr__("Design", 'massive-dynamic'),
));

pixflow_add_param("vc_row", array(
    "type" => "md_vc_colorpicker",
    "edit_field_class" => $filedClass . "glue last",
    "heading" => esc_attr__("Sloped Edge Color", 'massive-dynamic'),
    "param_name" => "row_sloped_edge_color",
    "admin_label" => false,
    "opacity" => false,
    "description" => esc_attr__("Enter sloped edge color.", 'massive-dynamic'),
    'dependency' => array(
        'element' => "row_sloped_edge",
        'value' => array('yes')
    ),
    "group" => esc_attr__("Design", 'massive-dynamic'),
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "edit_field_class" => $filedClass . "stick-to-top",
    "param_name" => "row_sloped_edge" . ++$separatorCounter,
    'dependency' => array(
        'element' => "row_sloped_edge",
        'value' => array('yes')
    ),
    "group" => esc_attr__("Design", 'massive-dynamic'),
));

pixflow_add_param("vc_row", array(
    'type' => 'dropdown',
    "edit_field_class" => $filedClass . "glue last",
    'heading' => esc_attr__('Slope Angle', 'massive-dynamic'),
    'param_name' => 'row_sloped_edge_angle',
    'description' => esc_attr__('Set the slope angle', 'massive-dynamic'),
    'dependency' => array(
        'element' => "row_sloped_edge",
        'value' => array('yes')
    ),
    "value" => array(
        "-3 " . esc_attr__('degree', 'massive-dynamic') => "-3",
        "-2 " . esc_attr__('degree', 'massive-dynamic') => "-2",
        "-1 " . esc_attr__('degree', 'massive-dynamic') => "-1",
        "0 " . esc_attr__('degree', 'massive-dynamic') => "0",
        "1 " . esc_attr__('degree', 'massive-dynamic') => "1",
        "2 " . esc_attr__('degree', 'massive-dynamic') => "2",
        "3 " . esc_attr__('degree', 'massive-dynamic') => "3",
    ),
    "group" => esc_attr__("Design", 'massive-dynamic'),
));

// Row parallax

pixflow_add_param("vc_row", array(
    "type" => "md_vc_checkbox",
    "edit_field_class" => $filedClass . "last glue first",
    "heading" => esc_attr__("Set Parallax", 'massive-dynamic'),
    "param_name" => "parallax_status",
    "description" => esc_attr__("Parallax enable or disable.", 'massive-dynamic'),
    'value' => array(esc_attr__('Enable', 'massive-dynamic') => 'no'),
    "group" => esc_attr__("Design", 'massive-dynamic'),
    'checked' => true,
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "edit_field_class" => $filedClass . "stick-to-top",
    "param_name" => "row_bg_tab_separator" . ++$separatorCounter,
    "group" => esc_attr__("Design", 'massive-dynamic'),
    "dependency" => array(
        'element' => "parallax_status",
        'value' => array('yes')
    )
));

pixflow_add_param("vc_row", array(
    'type' => 'md_vc_slider',
    "edit_field_class" => $filedClass . "glue last",
    'heading' => esc_attr__('Parallax Speed', 'massive-dynamic'),
    'param_name' => 'parallax_speed',
    'description' => esc_attr__('Set controllers for image parallax', 'massive-dynamic'),
    "group" => esc_attr__("Design", 'massive-dynamic'),
    'dependency' => array(
        'element' => "parallax_status",
        'value' => array('yes')
    ),
    'defaultSetting' => array(
        "min" => "1",
        "max" => "10",
        "prefix" => " / 10",
        "step" => '1',
    )
));

pixflow_add_param("vc_row", array(
    "type" => "textfield",
    "edit_field_class" => $filedClass . "glue first last",
    "heading" => esc_attr__("Extra Class Name", 'massive-dynamic'),
    "param_name" => "el_class",
    "description" => esc_attr__("Enable fit to height feature", 'massive-dynamic')
));


// Fit to screen
pixflow_add_param("vc_row", array(
    "type" => "md_vc_checkbox",
    "edit_field_class" => $filedClass . "glue first last",
    "heading" => esc_attr__("Fit To Screen", 'massive-dynamic'),
    "param_name" => "row_fit_to_height",
    "description" => esc_attr__("Enable fit to height feature", 'massive-dynamic'),
    'value' => array(esc_attr__('Enable', 'massive-dynamic') => 'no'),
    'checked' => false,
));

pixflow_add_param("vc_row", array(
    "type" => 'md_vc_separator',
    "edit_field_class" => $filedClass . "stick-to-top",
    "param_name" => "row_vertical_align_separator" . ++$separatorCounter,
    'dependency' => array(
        'element' => "row_fit_to_height",
        'value' => array('yes')
    ),
));

pixflow_add_param("vc_row", array(
    "type" => "md_vc_checkbox",
    "edit_field_class" => $filedClass . "last glue",
    "heading" => esc_attr__("Centered Content", 'massive-dynamic'),
    "param_name" => "row_vertical_align",
    "description" => esc_attr__("Enable vertical align feature", 'massive-dynamic'),
    'value' => array(esc_attr__('Enable', 'massive-dynamic') => 'no'),
    'checked' => false,
    'dependency' => array(
        'element' => "row_fit_to_height",
        'value' => array('yes')
    ),
));

// Equal Column Heigh
pixflow_add_param("vc_row", array(
    "type" => "md_vc_checkbox",
    "edit_field_class" => $filedClass . "glue first last",
    "heading" => esc_attr__("Equalize Column's Height", 'massive-dynamic'),
    "param_name" => "row_equal_column_heigh",
    'value' => array(esc_attr__('Enable', 'massive-dynamic') => 'no'),
    'checked' => false,
));

// Content Vertical Align
pixflow_add_param("vc_row", array(
    "type" => "dropdown",
    "edit_field_class" => $filedClass . "glue first last",
    "heading" => esc_attr__("Element's Vertical Position", 'massive-dynamic'),
    "param_name" => "row_content_vertical_align",
    "value" => array(
        esc_attr__('None', 'massive-dynamic') => "0",
        esc_attr__('Top', 'massive-dynamic') => "top",
        esc_attr__('Middle', 'massive-dynamic') => "middle",
        esc_attr__('Bottom', 'massive-dynamic') => "bottom",
    )
));

// Background Repeat for Image
pixflow_add_param("vc_row", array(
    "type" => "md_vc_checkbox",
    "edit_field_class" => $filedClass . "glue last first",
    "heading" => esc_attr__("Repeat Image", 'massive-dynamic'),
    "param_name" => "row_bg_repeat_image_gp",
    "description" => esc_attr__("Enable repeat background", 'massive-dynamic'),
    "group" => esc_attr__("BG [Image]", 'massive-dynamic'),
    'value' => array(esc_attr__('No', 'massive-dynamic') => 'no'),
    'checked' => false,
    'dependency' => array(
        'element' => "row_type",
        'value' => array('image')
    ),
));

// Background Repeat for gradient
pixflow_add_param("vc_row", array(
    "type" => "md_vc_checkbox",
    "edit_field_class" => $filedClass . "glue last first",
    "heading" => esc_attr__("Repeat Image", 'massive-dynamic'),
    "param_name" => "row_bg_repeat_gradient_gp",
    "description" => esc_attr__("Enable repeat background", 'massive-dynamic'),
    "group" => esc_attr__("BG [Gradient]", 'massive-dynamic'),
    'value' => array(esc_attr__('No', 'massive-dynamic') => 'no'),
    'checked' => false,
    'dependency' => array(
        'element' => "row_type",
        'value' => array('gradient')
    ),
));



// Row description

pixflow_add_param("vc_row", array(
    "type" => "md_vc_description",
    "param_name" => "row_parallax_description",
    "admin_label" => false,
    "group" => esc_attr__("Design", 'massive-dynamic'),
    'dependency' => array(
        'element' => "parallax_status",
        'value' => array('yes')
    ),
    "value" => esc_attr__("Speed 1 is the slowest and 10 is fastest. For faster parallax speed, you need a taller image, otherwise the background image will keep repeating.", 'massive-dynamic')
));

pixflow_add_param("vc_row", array(
    "type" => "md_vc_description",
    "param_name" => "row_sloped_edge_description",
    "admin_label" => false,
    "group" => esc_attr__("Design", 'massive-dynamic'),
    'dependency' => array(
        'element' => "row_sloped_edge",
        'value' => array('yes')
    ),
    "value" => esc_attr__("Please note that sloped edge does not work with color transition and video background.", 'massive-dynamic')
));

// Row description

pixflow_add_param("vc_row", array(
    "type" => "md_vc_description",
    "param_name" => "row_type_width_description",
    "admin_label" => false,
    "value" => wp_kses(__("<ul>
                        <li>When you change Row Background, you can choose the related options in BG tab.</li>
                        <li>Container size can be set from Site Content > Main Layout > Container Width</li>
                        <li>Full Screen size will ignore the container width and get the same width as user's screen</li>
                        <li>Fit To Screen option increases the row height to same height of user's screen, it's a great choice for first row.</li>
                        <li>Centered Content will only appear if you choose Fit To Screen, it will move(vertically) all columns of current row to center of the row. Also it will ignore top padding and bottom padding.</li>
                        <li>Element's Vertical Position gives you the option to arrange elements based on the highest column in this row.</li>
                    </ul>", 'massive-dynamic'), array('ul' => array(), 'li' => array()))
));

// VC shortcodes update

pixflow_map_update('vc_facebook', array(
    "weight" => '-1',
    "category" => esc_attr__('Business', 'massive-dynamic'),
));

pixflow_map_update('vc_tweetmeme', array(
    "weight" => '-2',
    "category" => esc_attr__('Business', 'massive-dynamic'),
));

pixflow_map_update('vc_googleplus', array(
    "weight" => '-3',
    "category" => esc_attr__('Business', 'massive-dynamic'),
));

pixflow_map_update('vc_pinterest', array(
    "weight" => '-4',
    "category" => esc_attr__('Business', 'massive-dynamic'),
));

pixflow_map_update('vc_gmaps', array(
    "weight" => '-6',
    "category" => esc_attr__('Business', 'massive-dynamic'),
));

pixflow_map_update('vc_round_chart', array(
    "weight" => '-7',
    "category" => esc_attr__('Business', 'massive-dynamic'),
));

pixflow_map_update('vc_line_chart', array(
    "weight" => '-7',
    "category" => esc_attr__('Business', 'massive-dynamic'),
));

require_once pixflow_path_dir('SHORTCODES_DIR', 'vc-column.php');
if (!class_exists('WPBakeryShortCode_VC_Column')) {
    class WPBakeryShortCode_VC_Column{}
}

if (!class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode{}
}








/*-----------------------------------------------------------------------------------*/
/*  Inner Row
/*-----------------------------------------------------------------------------------*/

// remove css animation and disable
pixflow_remove_param('vc_row_inner', 'css_animation');
pixflow_remove_param('vc_row_inner', 'disable_element');
// remove bg image
pixflow_remove_param('vc_row_inner', 'bg_image');

// remove get class
//pixflow_remove_param('vc_row_inner', 'el_class');

// remove get id
pixflow_remove_param('vc_row_inner', 'el_id');

// remove default parallax
pixflow_remove_param('vc_row_inner', 'parallax');

// remove stretch
pixflow_remove_param('vc_row_inner', 'full_width');

// remove default parallax
pixflow_remove_param('vc_row_inner', 'parallax_image');

// remove default css editor
pixflow_remove_param('vc_row_inner', 'css');

//remove spacing attributes
pixflow_remove_param('vc_row_inner', 'row_inner_padding_top');
pixflow_remove_param('vc_row_inner', 'row_inner_padding_bottom');
pixflow_remove_param('vc_row_inner', 'row_inner_padding_left');
pixflow_remove_param('vc_row_inner', 'row_inner_padding_right');

pixflow_remove_param('vc_row_inner', 'row_inner_margin_top');
pixflow_remove_param('vc_row_inner', 'row_inner_margin_bottom');

pixflow_remove_param('vc_row_inner', 'row_inner_type');

$row_setting = array(
    "name" => "Inner Row",
    'show_settings_on_create' => false,
    "category" => esc_attr__('Container','massive-dynamic'),
);



pixflow_map_update('vc_row_inner', $row_setting);

$separator_setting = array(
    "'show_settings_on_create" => true,
    "controls" => '',
);

// row spacing - Padding all directions

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_slider',
    "weight" => "2",
    "heading" => esc_attr__("Padding Top", 'massive-dynamic'),
    "param_name" => "inner_row_padding_top",
    "description" => esc_attr__("insert top padding for current row . example : 200 ", 'massive-dynamic'),
    "value" => "45",
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue first",
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "",
        "step" => '5',
    )
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Spacing", 'massive-dynamic'),
    "param_name" => "row_padding_tab_separator" . ++$separatorCounter,
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_slider',
    "heading" => esc_attr__("Padding Bottom", 'massive-dynamic'),
    "param_name" => "inner_row_padding_bottom",
    "description" => esc_attr__("insert bottom padding for current row . example : 200", 'massive-dynamic'),
    "value" => "47",
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue",
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "",
        "step" => '5',
    )
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Spacing", 'massive-dynamic'),
    "param_name" => "row_inner_padding_tab_separator" . ++$separatorCounter,
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_slider',
    "heading" => esc_attr__("Padding Right", 'massive-dynamic'),
    "param_name" => "inner_row_padding_right",
    "description" => esc_attr__("insert Right padding for current row . example : 200", 'massive-dynamic'),
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue",
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "",
        "step" => '5',
    )
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Spacing", 'massive-dynamic'),
    "param_name" => "row_inner_padding_tab_separator" . ++$separatorCounter,
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_slider',
    "heading" => esc_attr__("Padding Left", 'massive-dynamic'),
    "param_name" => "inner_row_padding_left",
    "description" => esc_attr__("insert left padding for current row . example : 200", 'massive-dynamic'),
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue last",
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "",
        "step" => '5',
    )
));

// row spacing Margin only top and bottom

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_slider',
    "edit_field_class" => $filedClass . "glue first",
    "heading" => esc_attr__("Margin Top", 'massive-dynamic'),
    "param_name" => "inner_row_margin_top",
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "",
        "step" => '5',
    )
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Spacing", 'massive-dynamic'),
    "param_name" => "row_inner_padding_tab_separator" . ++$separatorCounter,
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_slider',
    "edit_field_class" => $filedClass . "glue last",
    "heading" => esc_attr__("Margin Bottom", 'massive-dynamic'),
    "param_name" => "inner_row_margin_bottom",
    'group' => esc_attr__("Spacing", 'massive-dynamic'),
    'defaultSetting' => array(
        "min" => "0",
        "max" => "800",
        "prefix" => "",
        "step" => '5',
    )
));

// ***** row - background - tab *****


pixflow_add_param("vc_row_inner", array(
    "type" => "dropdown",
    "weight" => "3",
    "class" => "",
    "heading" => esc_attr__("Background Type", 'massive-dynamic'),
    "param_name" => "inner_row_type",
    "group" => esc_attr__("Background", 'massive-dynamic'),
    "description" => esc_attr__("Choose different type of containers and set the options.", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue first",
    "value" => array(
        esc_attr__("Solid Color", 'massive-dynamic') => "none",
        esc_attr__("Gradient and Image", 'massive-dynamic') => "gradient",
    ),
));



pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Background", 'massive-dynamic'),
    "param_name" => "row_inner_bg_tab_separator" . ++$separatorCounter,
));


// Background color overlay for default state
pixflow_add_param("vc_row_inner", array(
    "type" => "md_vc_colorpicker",
    "edit_field_class" => $filedClass . "glue last",

    "heading" => esc_attr__("Color", 'massive-dynamic'),
    "param_name" => "row_inner_background_color",
    "group" => esc_attr__("Background", 'massive-dynamic'),
    "opacity" => true,
    "admin_label" => false,
    "description" => esc_attr__("Choose a color to be used as this section's background. Please noticed that background color, has higher priority than background image.", 'massive-dynamic'),
    "value" => "rgba(255,255,255,0)",
    'dependency' => array(
        'element' => "inner_row_type",
        'value' => array('none'),
    )
));


// Gradient

pixflow_add_param("vc_row_inner", array(
    "type" => "md_vc_gradientcolorpicker",
    "edit_field_class" => $filedClass . "glue",

    "heading" => esc_attr__("Gradient", 'massive-dynamic'),
    "param_name" => "row_inner_gradient_color",
    "group" => esc_attr__("Background", 'massive-dynamic'),
    "description" => esc_attr__("Choose a color to be used as this section's background. Please notice that background color, has higher priority than background image.", 'massive-dynamic'),
    'dependency' => array(
        'element' => "inner_row_type",
        'value' => array('gradient')
    ),
    'defaultColor' => (object)array(
        'color1' => '#fff',
        'color2' => 'rgba(255,255,255,0)',
        'color1Pos' => '0',
        'color2Pos' => '100',
        'angle' => '0'),
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Background", 'massive-dynamic'),
    "param_name" => "row_inner_bg_tab_separator" . ++$separatorCounter,
    "admin_label" => false,
    "dependency" => array(
        'element' => "inner_row_type",
        'value' => array('gradient')
    )
));

// Select image

pixflow_add_param("vc_row_inner", array(
    'type' => 'attach_image',
    "edit_field_class" => $filedClass . "glue",
    'heading' => esc_attr__('Choose Image', 'massive-dynamic'),
    'param_name' => 'row_inner_image',
    'description' => esc_attr__('choose image from media library.', 'massive-dynamic'),
    "value" => "",
    "group" => esc_attr__("Background", 'massive-dynamic'),
    'dependency' => array(
        'element' => "inner_row_type",
        'value' => array('gradient')
    ),
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_separator',
    "group" => esc_attr__("Background", 'massive-dynamic'),
    "param_name" => "row_inner_image_separator" . ++$separatorCounter,
    'dependency' => array(
        'element' => "inner_row_type",
        'value' => array('gradient')
    ),
));

pixflow_add_param("vc_row_inner", array(
    "type" => "dropdown",
    "heading" => esc_attr__("Image Position", 'massive-dynamic'),
    "param_name" => "row_inner_image_position",
    "group" => esc_attr__("Background", 'massive-dynamic'),
    "edit_field_class" => $filedClass . "glue last",
    "value" => array(
        esc_attr__("Fit to row", 'massive-dynamic') => "fit",
        esc_attr__("Top", 'massive-dynamic') => "top",
        esc_attr__("Bottom", 'massive-dynamic') => "bottom",
    ),
    'dependency' => array(
        'element' => "inner_row_type",
        'value' => array('gradient')
    ),
));


// ***** row - general - tab *****

// Background width

pixflow_add_param("vc_row_inner", array(
    "type" => "dropdown",
    "weight" => "4",
    "edit_field_class" => $filedClass . "first glue",

    "heading" => esc_attr__("Background Width", 'massive-dynamic'),
    "param_name" => "row_inner_type_width",
    "description" => esc_attr__("Full width will use all of your screen width, while Boxed will created an invisible box in middle of your screen.", 'massive-dynamic'),
    "value" => array(
        esc_attr__("Full Screen", 'massive-dynamic') => "full_size",
        esc_attr__("Container", 'massive-dynamic') => "box_size",
    )
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'md_vc_separator',
    "weight" => "4",
    "param_name" => "row_inner_bg_tab_separator" . ++$separatorCounter,
));


// Content width

pixflow_add_param("vc_row_inner", array(
    "type" => "dropdown",
    "weight" => "4",
    "edit_field_class" => $filedClass . "glue last",

    "heading" => esc_attr__("Content Width", 'massive-dynamic'),
    "param_name" => "row_inner_box_size_states",
    "description" => esc_attr__("Full width will use all of your screen width, while Boxed will created an invisible box in middle of your screen.", 'massive-dynamic'),
    "value" => array(
        esc_attr__("Container", 'massive-dynamic') => "content_box_size",
        esc_attr__("Full Screen", 'massive-dynamic') => "content_full_size",
    )
));

pixflow_add_param("vc_row_inner", array(
    "type" => 'textfield',
    "edit_field_class" => $filedClass . "glue first last",
    "heading" => esc_attr__("Extra Class Name", 'massive-dynamic'),
    "param_name" => "el_class",
));

// Inner shadow

pixflow_add_param("vc_row_inner", array(
    "type" => "md_vc_checkbox",
    "edit_field_class" => $filedClass . "glue first last",
    "param_name" => "row_inner_inner_shadow",
    "heading" => esc_attr__('Inner shadow', 'massive-dynamic')
));


// Row description

pixflow_add_param("vc_row_inner", array(
    "type" => "md_vc_description",

    "param_name" => "row_inner_type_width_description",
    "admin_label" => false,
    "value" => wp_kses(__("<ul>
                        <li>Container size can be set from Site Content > Main Layout > Container Width</li>
                        <li>Full Screen size will ignore the container width and get the same width as user's screen</li>
                    </ul>", 'massive-dynamic'), array('ul' => array(), 'li' => array()))
));


/*************************************
 * Add Animation tab to shortcodes
 *************************************/

function pixflow_addAnimationTab($shortcode)
{
    if ($shortcode == '') {
        return array();
    }
    $shortcode_deny = array(
        'md_portfolio_multisize',
        'vc_empty_space',
        'vc_row',
        'md_tabs',
        'md_tab',
        'md_separator',
        'md_showcase',
        'md_blog',
        'md_blog_carousel',
        'md_blog_classic',
        'md_blog_masonry',
        'md_instagram',
        'md_music',
        'md_masterslider',
        'md_rev_slider',
        'md_slider',
        'md_slider_carousel',
        'md_team_member_classic',
        'md_client_carousel',
        'md_process_steps',
        'md_tab',
        'md_tabs',
        'md_accordion_tab',
        'md_accordion',
        'md_hor_tab',
        'md_hor_tab2',
        'md_hor_tabs',
        'md_hor_tabs2',
        'md_modernTab',
        'md_modernTabs',
        'md_toggle',
        'md_toggle2',
        'md_toggle-tab',
        'md_toggle-tab2',
        'md_process_panel',
        'md_product_compare',
        'md_skill_style1',
        'md_skill_style2'
    );
    $filedClass = 'vc_col-sm-12 vc_column ';
    $separatorCounter = 0;
    $animationTab = array(
        array(
            'type' => 'md_vc_checkbox',
            "edit_field_class" => $filedClass . "glue first last",
            'heading' => esc_attr__('Use Animation', 'massive-dynamic'),
            'param_name' => $shortcode . '_animation',
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            'checked' => false,
        ),
        array(
            "type" => "dropdown",
            "edit_field_class" => $filedClass . "glue first last",
            "heading" => esc_attr__("Animation Type", 'massive-dynamic'),
            "param_name" => $shortcode . "_animation_type",
            "admin_label" => false,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "value" => array(
                esc_attr__("Fade", 'massive-dynamic') => 'fade',
                esc_attr__("Float", 'massive-dynamic') => 'float'
            ),
            "dependency" => array(
                'element' => $shortcode . "_animation",
                'value' => array('yes'),
            ),
        ),
        array(
            "type" => "dropdown",
            "edit_field_class" => $filedClass . "glue first",
            "heading" => esc_attr__("Speed", 'massive-dynamic'),
            "param_name" => $shortcode . "_animation_speed",
            "admin_label" => false,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "value" => array(
                esc_attr__("Super Fast", 'massive-dynamic') => 200,
                esc_attr__("Fast", 'massive-dynamic') => 400,
                esc_attr__("Medium", 'massive-dynamic') => 600,
                esc_attr__("Slow", 'massive-dynamic') => 800,
            ),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('fade'),
            ),
        ),
        array(
            "type" => 'md_vc_separator',
            "param_name" => $shortcode . "_animation_speed_separator" . ++$separatorCounter,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('fade'),
            ),
        ),
        array(
            'type' => 'md_vc_slider',
            "edit_field_class" => $filedClass . "glue",
            'heading' => esc_attr__('Animation delay', 'massive-dynamic'),
            'param_name' => $shortcode . '_animation_delay',
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            'defaultSetting' => array(
                "min" => "0",
                "max" => "2",
                "prefix" => " s",
                "step" => "0.1",
                "decimal" => "1",
            ),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('fade'),
            ),
        ),
        array(
            "type" => 'md_vc_separator',
            "param_name" => $shortcode . "_animation_delay_separator" . ++$separatorCounter,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('fade'),
            ),
        ),
        array(
            "type" => "dropdown",
            "edit_field_class" => $filedClass . "glue",
            "heading" => esc_attr__("Animate From", 'massive-dynamic'),
            "param_name" => $shortcode . "_animation_position",
            "admin_label" => false,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "value" => array(
                esc_attr__("Center", 'massive-dynamic') => "center",
                esc_attr__("Top", 'massive-dynamic') => "top",
                esc_attr__("Right", 'massive-dynamic') => "right",
                esc_attr__("Bottom", 'massive-dynamic') => "bottom",
                esc_attr__("Left", 'massive-dynamic') => "left"
            ),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('fade'),
            ),
        ),
        array(
            "type" => 'md_vc_separator',
            "param_name" => $shortcode . "_animation_position_separator" . ++$separatorCounter,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('fade'),
            ),
        ),
        array(
            "type" => "dropdown",
            "edit_field_class" => $filedClass . "glue ",
            "heading" => esc_attr__("Animation Type", 'massive-dynamic'),
            "param_name" => $shortcode . "_animation_show",
            "admin_label" => false,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "value" => array(
                esc_attr__("Play Once", 'massive-dynamic') => "once",
                esc_attr__("Play On scroll", 'massive-dynamic') => "scroll"
            ),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('fade'),
            ),
        ),
        array(
            "type" => 'md_vc_separator',
            "param_name" => $shortcode . "_animation_position_separator" . ++$separatorCounter,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('fade'),
            ),
        ),
        array(
            "type" => "dropdown",
            "edit_field_class" => $filedClass . "glue last",
            "heading" => esc_attr__("Animation Easing", 'massive-dynamic'),
            "param_name" => $shortcode . "_animation_easing",
            "admin_label" => false,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "value" => array(
                esc_attr__("Quart", 'massive-dynamic') => "Quart.easeInOut",
                esc_attr__("Quint", 'massive-dynamic') => "Power4.easeOut",
            ),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('fade'),
            ),
        ),


    );
    if( array_search($shortcode , $shortcode_deny) === false ){
        $animationTab[] = array(
            "type" => "dropdown",
            "edit_field_class" => $filedClass . "glue first last",
            "heading" => esc_attr__("Movement Speed", 'massive-dynamic'),
            "param_name" => $shortcode . "_parallax_speed",
            "admin_label" => false,
            "group" => esc_attr__('Animation', 'massive-dynamic'),
            "value" => array(
                esc_attr__("Level 1", 'massive-dynamic') => 1,
                esc_attr__("Level 2", 'massive-dynamic') => 2,
                esc_attr__("Level 3", 'massive-dynamic') => 3,
                esc_attr__("Level 4", 'massive-dynamic') => 4,
                esc_attr__("Level 5", 'massive-dynamic') => 5

            ),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('float'),
            ),
        );
    }
    else {
        $animationTab[] = array(
            "type" => "md_vc_description",
            "param_name" => "float_description",
            "group" => esc_attr__("Animation", 'massive-dynamic'),
            "admin_label" => false,
            "value" => esc_attr__("This Shortcode does not support float animation", 'massive-dynamic'),
            "dependency" => array(
                'element' => $shortcode . "_animation_type",
                'value' => array('float'),
            ),
        );
    }
    return $animationTab;
}

/* **************************************************************************************************** */
if(
    strpos( $_SERVER[ 'REQUEST_URI' ], 'post.php' ) !== false
    || strpos( $_SERVER[ 'REQUEST_URI' ], 'post_new.php') !== false
){
    MBuilder::load_shortcode_maps();
}elseif(defined( 'DOING_AJAX' ) && isset($_POST['action']) && 'vc_edit_form' == $_POST['action']){
    MBuilder::load_shortcode_map($_POST['tag']);
}