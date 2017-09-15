<?php

function pixflow_print_terms($terms, $separatorString){
    $termIndex = 1;
    if ($terms)
        foreach ($terms as $term) {
            echo esc_attr($term->name);

            if (count($terms) > $termIndex)
                echo esc_attr($separatorString);

            $termIndex++;
        }
}

function pixflow_get_post_terms_names($taxonomy)
{
    $terms = get_the_terms(get_the_ID(), $taxonomy);

    if (!is_array($terms))
        return $terms;

    $termNames = array();

    foreach ($terms as $term)
        $termNames[] = $term->name;

    return $termNames;
}

/*
 * Concatenate post category names
 */
function pixflow_implode_post_terms($taxonomy, $separator = ', ')
{
    $terms = pixflow_get_post_terms_names($taxonomy);

    if (!is_array($terms))
        return null;

    return implode($separator, $terms);
}

//Thanks to:
//http://bavotasan.com/tutorials/limiting-the-number-of-words-in-your-excerpt-or-content-in-wordpress/
function pixflow_excerpt($limit)
{
    $excerpt = explode(' ', get_the_excerpt(), $limit);
    if (count($excerpt) >= $limit) {
        array_pop($excerpt);
        $excerpt = implode(" ", $excerpt) . '...';
    } else {
        $excerpt = implode(" ", $excerpt);
    }
    $excerpt = preg_replace('`\[[^\]]*\]`', '', $excerpt);
    return $excerpt;
}

add_action('wp_ajax_pixflow_generateThumbs', 'pixflow_generateThumbs');
add_action('wp_ajax_nopriv_pixflow-generateThumbs', 'pixflow_generateThumbs');
function pixflow_generateThumbs()
{
    set_time_limit(0);
    if (!isset($_SESSION['pixflow_media']) && !is_array($_SESSION['pixflow_media'])) {
        die('err');
    }
    foreach ($_SESSION['pixflow_media'] as $post_id => $item) {
        wp_update_attachment_metadata($post_id, wp_generate_attachment_metadata($post_id, $item));
    }
    die('done!');
}

// Ensure cart contents update when products are added to the cart via AJAX
add_filter('woocommerce_add_to_cart_fragments', 'pixflow_woocommerce_header_add_to_cart_fragment');
function pixflow_woocommerce_header_add_to_cart_fragment($fragments)
{
    ob_start();
    global $woocommerce, $md_allowed_HTML_tags;

    do_action('woocommerce_before_mini_cart');
    ?>
    <ul class="cart_list product_list_widget ">

        <?php if (!$woocommerce->cart->is_empty()) : ?>

            <?php
            foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {

                    $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
                    $product_price = apply_filters('woocommerce_cart_item_price', $woocommerce->cart->get_product_price($_product), $cart_item, $cart_item_key);
                    $url = wp_get_attachment_image_src(get_post_thumbnail_id($_product->id), 'woocommerce_cart_item_thumbnail');
                    $url = (false == $url) ? PIXFLOW_PLACEHOLDER_BLANK : $url['0'];
                    if ($url != '')
                        $thumbnail = '<div class="cart-img" style="background-image: url(' . $url . ')"></div>';

                    ?>
                    <li class="<?php echo esc_attr(apply_filters('woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key)); ?>">
                        <?php
                        echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
                            '<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                            esc_url($woocommerce->cart->get_remove_url($cart_item_key)),
                            esc_attr__('Remove this item', 'massive-dynamic'),
                            esc_attr($product_id),
                            esc_attr($_product->get_sku())
                        ), $cart_item_key);
                        ?>
                        <?php if (!$_product->is_visible()) : ?>
                            <?php echo str_replace(array('http:', 'https:'), '', $thumbnail) . $product_name . '&nbsp;'; ?>
                        <?php else : ?>
                            <a href="<?php echo esc_url($_product->get_permalink($cart_item)); ?>">
                                <?php echo str_replace(array('http:', 'https:'), '', $thumbnail) . $product_name . '&nbsp;'; ?>
                            </a>
                        <?php endif; ?>
                        <?php echo wp_kses($woocommerce->cart->get_item_data($cart_item), $md_allowed_HTML_tags); ?>

                        <?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $cart_item['quantity'], $product_price) . '</span>', $cart_item, $cart_item_key); ?>
                    </li>
                    <?php
                }
            }
            ?>

        <?php else : ?>

            <li class="empty"><?php esc_attr_e('No products in the cart.', 'massive-dynamic'); ?></li>

        <?php endif; ?>

    </ul><!-- end product list -->

    <?php if (!WC()->cart->is_empty()) : ?>

    <p class="total"><strong><?php esc_attr_e('Subtotal', 'massive-dynamic'); ?>
            :</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></p>

    <?php do_action('woocommerce_widget_shopping_cart_before_buttons'); ?>

    <p class="buttons">
        <a href="<?php echo WC()->cart->get_cart_url(); ?>"
           class="button wc-forward"><?php esc_attr_e('View Cart', 'massive-dynamic'); ?></a>
        <a href="<?php echo WC()->cart->get_checkout_url(); ?>"
           class="button checkout wc-forward"><?php esc_attr_e('Checkout', 'massive-dynamic'); ?></a>
    </p>

<?php endif; ?>

    <?php do_action('woocommerce_after_mini_cart'); ?>
    <script type="text/javascript">pixflow_addToCart();</script>
    <?php
    $fragments['ul.cart_list'] = ob_get_clean();

    return $fragments;
}

//Get Metabox value from vafpress function
function pixflow_metabox($key, $default = null)
{
    $value = vp_metabox($key, $default);
    $value = (null == $value) ? $default : $value;
    return $value;
}

function pixflow_drfw_postID_by_url($url)
{
    global $wpdb;
    $id = url_to_postid($url);
    if ($id == 0) {
        $fileupload_url = get_option('fileupload_url', null) . '/';
        if (strpos($url, $fileupload_url) !== false) {
            $url = str_replace($fileupload_url, '', $url);
            $id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '$url' AND wposts.post_type = 'attachment'"));
        }
    }
    return $id;
}

// remove Open Sans font
add_action('wp_enqueue_scripts', 'pixflow_deregister_styles', 100);

function pixflow_deregister_styles()
{
    wp_deregister_style('open-sans');
    wp_register_style('open-sans', false);
    wp_enqueue_style('open-sans');
}

/*********************************************************************/
/* Add featured post checkbox
/********************************************************************/

add_action('add_meta_boxes', 'pixflow_showPageTitleMetaBox');
function pixflow_showPageTitleMetaBox()
{
    add_meta_box('show_page_title_id', esc_attr__('Page Options', 'massive-dynamic'), 'pixflow_pageMetaBox_callback', 'page', 'normal', 'high');
}

function pixflow_pageMetaBox_callback($post)
{
    global $post;
    $onePageScroll = get_post_meta($post->ID, 'one_page_scroll', true);
    $showPageTitle = get_post_meta($post->ID, 'show_page_title', true);
    //$showPageTitle = ($showPageTitle === '')?'yes':$showPageTitle;
    ?>

    <br/>

    <input type="checkbox" name="one_page_scroll"
           value="yes" <?php echo esc_attr(($onePageScroll == 'yes') ? 'checked="checked"' : ''); ?>/> Section Scroll

    <br/>
    <br/>

    <input type="checkbox" name="show_page_title"
           value="yes" <?php echo esc_attr(($showPageTitle == 'yes') ? 'checked="checked"' : ''); ?>/> Display Page Title

    <br/>
    <br/>

    <?php
}

add_action('save_post', 'pixflow_savePageMetaBox');
function pixflow_savePageMetaBox()
{
    global $post;
    if (null != $post && !isset($_POST['show_page_title']) && !isset($_POST['one_page_scroll'])) {
        delete_post_meta($post->ID, 'one_page_scroll');
        delete_post_meta($post->ID, 'show_page_title');
    } else {
        if (isset($_POST['one_page_scroll'])) {
            update_post_meta($post->ID, 'one_page_scroll', $_POST['one_page_scroll']);
        }
        if (isset($_POST['show_page_title'])) {
            update_post_meta($post->ID, 'show_page_title', $_POST['show_page_title']);
        }
    }
}

// Embed pixflow metabox to theme, so we deactivate pixflow metabox anymore
function pixflow_deactivatePixflowMetabox($plugin, $network_activation)
{
    if (defined('PX_Metabox_VER')) {
        deactivate_plugins(WP_PLUGIN_DIR . '/pixflow-metabox/pixflow-metabox.php');
    }
}

add_action('activated_plugin', 'pixflow_deactivatePixflowMetabox', 10, 2);

/*Set FrontPage post meta*/
function pixflow_setFrontPgaePostMeta($oldValue, $newValue)
{
    update_post_meta($oldValue, 'pixflow_front_page', 'false');
    update_post_meta($newValue, 'pixflow_front_page', 'true');
}
add_action('update_option_page_on_front', 'pixflow_setFrontPgaePostMeta', 10, 2);

/**
 * @summary Create pixflow sample page
 *
 *  @return int id of pixflow sample page and if created before return id of home page
 * @since 1.0.0
 */

function pixflow_create_sample_page(){
    if( get_option('pixflow_sample_page') == true ){
        $home_page_id = (int) get_option( 'page_on_front' );
        return $home_page_id ;
    }
    $contentMassivePage = "
[vc_row row_type='image' type_width='full_size' box_size_states='content_box_size' el_class='' row_fit_to_height='no' row_vertical_align='yes' row_equal_column_heigh='no' row_content_vertical_align='0' row_padding_top='185' row_padding_bottom='195' row_padding_right='0' row_padding_left='0' row_margin_top='0' row_margin_bottom='0' background_color='rgba(255,255,255,1)' row_webm_url='' row_mp4_url='' background_color_image='rgba(255, 255, 255, 0.84)' row_image='http://demo.massivedynamic.co/general/wp-content/uploads/2016/11/business-wom-an.jpg' row_image_position='top' row_bg_image_size_tab_image='cover' row_bg_repeat_image_gp='no' first_color='#000' second_color='#000' row_gradient_color='pixflow_base64eyJjb2xvcjEiOiIjZmZmIiwiY29sb3IyIjoicmdiYSgyNTUsMjU1LDI1NSwwKSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=' row_image_position_gradient='fit' row_bg_image_size_tab_gradient='cover' row_bg_repeat_gradient_gp='no' row_inner_shadow='no' row_sloped_edge='no' row_slope_edge_position='top' row_sloped_edge_color='#000' row_sloped_edge_angle='-3' parallax_status='yes' parallax_speed='4' align='yes'][vc_column][md_text md_text_alignment=\"center\" md_text_title_line_height=\"66\" md_text_desc_line_height=\"27\" md_text_title_bottom_space=\"23\" md_text_separator_bottom_space=\"10\" md_text_description_bottom_space=\"25\" md_text_title_separator=\"no\" md_text_separator_width=\"110\" md_text_separator_height=\"5\" md_text_separator_color=\"rgb(0, 255, 153)\" md_text_use_desc_custom_font=\"yes\" md_text_desc_google_fonts=\"font_family:Poppins%3A300%2Cregular%2C500%2C600%2C700|font_style:400%20regular%3A400%3Anormal\" md_text_style=\"solid\" md_text_solid_color=\"rgb(58, 58, 58)\" md_text_gradient_color=\"pixflow_base64eyJjb2xvcjEiOiIjODcwMmZmIiwiY29sb3IyIjoiIzA2ZmY2ZSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=\" md_text_title_size=\"40\" md_text_letter_space=\"-1\" md_text_hover_letter_space=\"-1\" md_text_easing=\"cubic-bezier(0.215, 0.61, 0.355, 1)\" md_text_use_title_custom_font=\"yes\" md_text_title_google_fonts=\"font_family:Poppins%3A300%2Cregular%2C500%2C600%2C700|font_style:600%20bold%20regular%3A600%3Anormal\" md_text_number=\"1\" md_text_title1_text=\"<div><span style='color: rgb(255, 255, 255); font-size: 56px; font-family: Poppins;' data-mce-style='color: #ffffff; font-size: 56px; font-family: Poppins;'>Your First Step In </span></div><div><span style='color: rgb(255, 255, 255); font-size: 56px; font-family: Poppins;' data-mce-style='color: #ffffff; font-size: 56px; font-family: Poppins;'>Massive </span><span style='color: rgb(255, 255, 255); font-size: 56px; font-family: Poppins;' data-mce-style='color: #ffffff; font-size: 56px; font-family: Poppins;'>Live Customizer</span></div>\" md_text_title1=\"pixflow_base64PGRpdj48c3BhbiBzdHlsZT0iZm9udC1zaXplOiA1NnB4OyBmb250LWZhbWlseTogUG9wcGluczsgY29sb3I6IHJnYigwLCAwLCAwKTsiIGRhdGEtbWNlLXN0eWxlPSJmb250LXNpemU6IDU2cHg7IGZvbnQtZmFtaWx5OiBQb3BwaW5zOyBjb2xvcjogIzAwMDAwMDsiPllvdXIgRmlyc3QgU3RlcCBJbiA8L3NwYW4+PC9kaXY+PGRpdj48c3BhbiBzdHlsZT0iY29sb3I6IHJnYigwLCAwLCAwKTsiIGRhdGEtbWNlLXN0eWxlPSJjb2xvcjogIzAwMDAwMDsiPjxzcGFuIHN0eWxlPSJmb250LXNpemU6IDU2cHg7IGZvbnQtZmFtaWx5OiBQb3BwaW5zOyIgZGF0YS1tY2Utc3R5bGU9ImZvbnQtc2l6ZTogNTZweDsgZm9udC1mYW1pbHk6IFBvcHBpbnM7Ij5NYXNzaXZlIDwvc3Bhbj48c3BhbiBzdHlsZT0iZm9udC1zaXplOiA1NnB4OyBmb250LWZhbWlseTogUG9wcGluczsiIGRhdGEtbWNlLXN0eWxlPSJmb250LXNpemU6IDU2cHg7IGZvbnQtZmFtaWx5OiBQb3BwaW5zOyI+TGl2ZSBDdXN0b21pemVyPC9zcGFuPjwvc3Bhbj48L2Rpdj4=\"          md_text_title2=\"Typography Shortcode\" md_text_title3=\"Typography Shortcode\" md_text_title4=\"Typography Shortcode\" md_text_title5=\"Typography Shortcode\" md_text_content_size=\"16\" md_text_content_color=\"rgb(82, 82, 82)\" md_text_use_button=\"no\" md_text_button_style=\"fade-oval\" md_text_button_text=\"READ MORE\" md_text_button_icon_class=\"icon-angle-right\" md_text_button_color=\"rgba(0,0,0,1)\" md_text_button_text_color=\"rgba(255,255,255,1)\" md_text_button_bg_hover_color=\"rgb(0,0,0)\" md_text_button_hover_color=\"rgb(255,255,255)\" md_text_button_size=\"standard\" left_right_padding=\"0\" md_text_button_url=\"#\" md_text_button_target=\"_self\" md_text_animation=\"no\" md_text_animation_speed=\"600\" md_text_animation_delay=\"0.3\" md_text_animation_position=\"bottom\" md_text_animation_show=\"scroll\" md_text_animation_easing=\"Power4.easeOut\" align=\"center\"  md_text_fonts=\"\"  md_text_use_title_slider=\"yes\"][/md_text][md_button button_style=\"fill-rectangle\" button_text=\"BEGIN HERE\" button_icon_class=\"icon-empty\" button_color=\"rgb(12, 119, 230)\" button_text_color=\"#fff\" button_bg_hover_color=\"#9b9b9b\" button_hover_color=\"#FFF\" button_size=\"standard\" left_right_padding=\"21\" button_align=\"center\" button_url=\"#\" button_target=\"_self\" md_button_animation_speed=\"400\" md_button_animation_delay=\"0.0\" md_button_animation_position=\"center\" md_button_animation_show=\"once\" align=\"center\"][/md_button][/vc_column][/vc_row][vc_row row_type='none' type_width='full_size' box_size_states='content_box_size' el_class='' row_fit_to_height='no' row_vertical_align='no' row_equal_column_heigh='no' row_content_vertical_align='0' row_padding_top='100' row_padding_bottom='100' row_padding_right='0' row_padding_left='0' row_margin_top='0' row_margin_bottom='0' background_color='rgba(255,255,255,1)' row_webm_url='' row_mp4_url='' background_color_image='rgba(0,0,0,0.2)' row_image_position='default' row_bg_image_size_tab_image='cover' row_bg_repeat_image_gp='no' first_color='#000' second_color='#000' row_gradient_color='pixflow_base64eyJjb2xvcjEiOiIjZmZmIiwiY29sb3IyIjoicmdiYSgyNTUsMjU1LDI1NSwwKSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=' row_image_position_gradient='fit' row_bg_image_size_tab_gradient='cover' row_bg_repeat_gradient_gp='no' row_inner_shadow='no' row_sloped_edge='no' row_slope_edge_position='top' row_sloped_edge_color='#000' row_sloped_edge_angle='-3' parallax_status='no' parallax_speed='1'][vc_column width=\"2/12\" css=\".vc_custom_1457598198860{padding-right: 50px !important;}\"][/vc_column][vc_column el_class=\"\" width=\"8/12\" margin_top=\"0\" margin_right=\"0\" margin_bottom=\"0\" margin_left=\"0\" padding_top=\"0\" padding_right=\"100\" padding_bottom=\"0\" padding_left=\"100\" border_color=\"rgba(0,0,0,1)\" border_style=\"solid\" border_top_width=\"0\" border_right_width=\"0\" border_bottom_width=\"0\" border_left_width=\"0\" background_color=\"rgba(0,0,0,0)\" background_image=\"undefined\" css=\"{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;padding-top:0px;padding-right:100px;padding-bottom:0px;padding-left:100px;border-color:rgba(0,0,0,1);border-top-width:0px;border-right-width:0px;border-bottom-width:0px;border-left-width:0px;background-color:rgba(0,0,0,0);background-image:undefined;border-style:solid;background-size:;}\" md_laptop_visibility=\"yes\" md_tablet_portrait_visibility=\"yes\" md_tablet_landscape_visibility=\"yes\" md_mobile_portrait_visibility=\"yes\" md_mobile_landscape_visibility=\"yes\" md_tablet_portrait_width=\"0\"][md_text md_text_alignment=\"center\" md_text_title_line_height=\"12\" md_text_desc_line_height=\"12\" md_text_title_bottom_space=\"13\" md_text_separator_bottom_space=\"10\" md_text_description_bottom_space=\"0\" md_text_title_separator=\"no\" md_text_separator_width=\"110\" md_text_separator_height=\"5\" md_text_separator_color=\"rgb(0, 255, 153)\" md_text_use_desc_custom_font=\"yes\" md_text_desc_google_fonts=\"font_family:Roboto%3Aregular%2C100%2C100italic%2C300%2C300italic%2Citalic%2C500%2C500italic%2C700%2C700italic%2C900%2C900italic|font_style:400%20regular%3A400%3Anormal\" md_text_style=\"solid\" md_text_solid_color=\"rgba(20,20,20,1)\" md_text_gradient_color=\"pixflow_base64eyJjb2xvcjEiOiIjODcwMmZmIiwiY29sb3IyIjoiIzA2ZmY2ZSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=\" md_text_title_size=\"32\" md_text_letter_space=\"2\" md_text_hover_letter_space=\"2\" md_text_easing=\"cubic-bezier(0.215, 0.61, 0.355, 1)\" md_text_use_title_custom_font=\"no\" md_text_title_google_fonts=\"font_family:Roboto%3Aregular%2C100%2C100italic%2C300%2C300italic%2Citalic%2C500%2C500italic%2C700%2C700italic%2C900%2C900italic|font_style:400%20regular%3A400%3Anormal\" md_text_number=\"1\" md_text_title1_text=\"<div><span style='color: rgb(77, 77, 77); font-weight: 500; font-family: Poppins; font-size: 14px;' data-mce-style='color: #4d4d4d; font-weight: 500; font-family: Poppins; font-size: 14px;'>See it yourself</span></div>\" md_text_title1=\"pixflow_base64PGRpdj48c3BhbiBzdHlsZT0iY29sb3I6IHJnYigxMiwgMTE5LCAyMzApOyBmb250LXdlaWdodDogNTAwOyBmb250LWZhbWlseTogUG9wcGluczsgZm9udC1zaXplOiAxNXB4OyIgZGF0YS1tY2Utc3R5bGU9ImNvbG9yOiAjMGM3N2U2OyBmb250LXdlaWdodDogNTAwOyBmb250LWZhbWlseTogUG9wcGluczsgZm9udC1zaXplOiAxNXB4OyI+U2VlIGl0IHlvdXJzZWxmPC9zcGFuPjwvZGl2Pg==\"       md_text_title2=\"Typography Shortcode\" md_text_title3=\"Typography Shortcode\" md_text_title4=\"Typography Shortcode\" md_text_title5=\"Typography Shortcode\" md_text_content_size=\"14\" md_text_content_color=\"rgba(20,20,20,1)\" md_text_use_button=\"no\" md_text_button_style=\"fade-oval\" md_text_button_text=\"READ MORE\" md_text_button_icon_class=\"icon-angle-right\" md_text_button_color=\"rgba(0,0,0,1)\" md_text_button_text_color=\"rgba(255,255,255,1)\" md_text_button_bg_hover_color=\"rgb(0,0,0)\" md_text_button_hover_color=\"rgb(255,255,255)\" md_text_button_size=\"standard\" left_right_padding=\"0\" md_text_button_url=\"#\" md_text_button_target=\"_self\" md_text_animation_speed=\"400\" md_text_animation_delay=\"0.0\" md_text_animation_position=\"center\" md_text_animation_show=\"once\"  md_text_fonts=\"\"  md_text_use_title_slider=\"yes\"][/md_text][md_text md_text_alignment=\"center\" md_text_title_line_height=\"40\" md_text_desc_line_height=\"25\" md_text_title_bottom_space=\"25\" md_text_separator_bottom_space=\"26\" md_text_description_bottom_space=\"0\" md_text_title_separator=\"yes\" md_text_separator_width=\"80\" md_text_separator_height=\"5\" md_text_separator_color=\"rgb(38, 38, 38)\" md_text_use_desc_custom_font=\"yes\" md_text_desc_google_fonts=\"font_family:Roboto%3Aregular%2C100%2C100italic%2C300%2C300italic%2Citalic%2C500%2C500italic%2C700%2C700italic%2C900%2C900italic|font_style:400%20regular%3A400%3Anormal\" md_text_style=\"solid\" md_text_solid_color=\"rgba(20,20,20,1)\" md_text_gradient_color=\"pixflow_base64eyJjb2xvcjEiOiIjODcwMmZmIiwiY29sb3IyIjoiIzA2ZmY2ZSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=\" md_text_title_size=\"32\" md_text_letter_space=\"-1\" md_text_hover_letter_space=\"-1\" md_text_easing=\"cubic-bezier(0.215, 0.61, 0.355, 1)\" md_text_use_title_custom_font=\"no\" md_text_title_google_fonts=\"font_family:Roboto%3Aregular%2C100%2C100italic%2C300%2C300italic%2Citalic%2C500%2C500italic%2C700%2C700italic%2C900%2C900italic|font_style:400%20regular%3A400%3Anormal\" md_text_use_title_slider=\"yes\"  md_text_number=\"1\" md_text_title1_text=\"<div><span style='position: relative; font-weight: 600; font-family: Poppins;' data-mce-style='position: relative; font-weight: 600; font-family: Poppins;'>Experience the first live </span><span style='position: relative; font-weight: 600; font-family: Poppins;' data-mce-style='position: relative; font-weight: 600; font-family: Poppins;'>text </span></div><div><span style='position: relative; font-weight: 600; font-family: Poppins;' data-mce-style='position: relative; font-weight: 600; font-family: Poppins;'>editor </span><span style='position: relative; font-weight: 600; font-family: Poppins;' data-mce-style='position: relative; font-weight: 600; font-family: Poppins;'>in the market</span></div>\" md_text_title1=\"pixflow_base64PGRpdj48c3BhbiBzdHlsZT0icG9zaXRpb246IHJlbGF0aXZlOyBmb250LXdlaWdodDogNjAwOyBmb250LWZhbWlseTogUG9wcGluczsiIGRhdGEtbWNlLXN0eWxlPSJwb3NpdGlvbjogcmVsYXRpdmU7IGZvbnQtd2VpZ2h0OiA2MDA7IGZvbnQtZmFtaWx5OiBQb3BwaW5zOyI+RXhwZXJpZW5jZSB0aGUgZmlyc3QgbGl2ZSA8L3NwYW4+PHNwYW4gc3R5bGU9InBvc2l0aW9uOiByZWxhdGl2ZTsgZm9udC13ZWlnaHQ6IDYwMDsgZm9udC1mYW1pbHk6IFBvcHBpbnM7IiBkYXRhLW1jZS1zdHlsZT0icG9zaXRpb246IHJlbGF0aXZlOyBmb250LXdlaWdodDogNjAwOyBmb250LWZhbWlseTogUG9wcGluczsiPnRleHQgPC9zcGFuPjwvZGl2PjxkaXY+PHNwYW4gc3R5bGU9InBvc2l0aW9uOiByZWxhdGl2ZTsgZm9udC13ZWlnaHQ6IDYwMDsgZm9udC1mYW1pbHk6IFBvcHBpbnM7IiBkYXRhLW1jZS1zdHlsZT0icG9zaXRpb246IHJlbGF0aXZlOyBmb250LXdlaWdodDogNjAwOyBmb250LWZhbWlseTogUG9wcGluczsiPmVkaXRvciA8L3NwYW4+PHNwYW4gc3R5bGU9InBvc2l0aW9uOiByZWxhdGl2ZTsgZm9udC13ZWlnaHQ6IDYwMDsgZm9udC1mYW1pbHk6IFBvcHBpbnM7IiBkYXRhLW1jZS1zdHlsZT0icG9zaXRpb246IHJlbGF0aXZlOyBmb250LXdlaWdodDogNjAwOyBmb250LWZhbWlseTogUG9wcGluczsiPmluIHRoZSBtYXJrZXQ8L3NwYW4+PC9kaXY+\"  md_text_title2=\"Typography Shortcode\" md_text_title3=\"Typography Shortcode\" md_text_title4=\"Typography Shortcode\" md_text_title5=\"Typography Shortcode\" md_text_content_size=\"14\" md_text_content_color=\"rgba(20,20,20,1)\" md_text_use_button=\"no\" md_text_button_style=\"fade-oval\" md_text_button_text=\"READ MORE\" md_text_button_icon_class=\"icon-angle-right\" md_text_button_color=\"rgba(0,0,0,1)\" md_text_button_text_color=\"rgba(255,255,255,1)\" md_text_button_bg_hover_color=\"rgb(0,0,0)\" md_text_button_hover_color=\"rgb(255,255,255)\" md_text_button_size=\"standard\" left_right_padding=\"0\" md_text_button_url=\"#\" md_text_button_target=\"_self\" md_text_animation_speed=\"400\" md_text_animation_delay=\"0.0\" md_text_animation_position=\"center\" md_text_animation_show=\"once\" align=\"center\"  md_text_fonts=\"\" md_text_fonts=\"\"]<p><span style=\"color: #666666; font-size: 16px; font-weight: 300; font-family: Poppins;\" data-mce-style=\"color: #666666; font-size: 16px; font-weight: 300; font-family: Poppins;\">Now You Know Us We have never been so happy before. Massive Dynamic has over 10 years of experience in Design, Technology and Marketing. We take pride in delivering Intelligent Designs and Engaging. Now You Know Us We have never been so happy before.&nbsp;</span></p>[/md_text][/vc_column][vc_column width=\"2/12\" el_id='5822e016098b7'][/vc_column][/vc_row][vc_row row_type='image' type_width='full_size' box_size_states='content_box_size' el_class='' row_fit_to_height='no' row_vertical_align='no' row_equal_column_heigh='no' row_content_vertical_align='0' row_padding_top='165' row_padding_bottom='140' row_padding_right='0' row_padding_left='0' row_margin_top='0' row_margin_bottom='0' background_color='rgba(255,255,255,1)' row_webm_url='' row_mp4_url='' background_color_image='rgba(12, 119, 230, 0.75)' row_image='http://demo.massivedynamic.co/general/wp-content/uploads/2016/11/fgh.jpg' row_image_position='bottom' row_bg_image_size_tab_image='cover' row_bg_repeat_image_gp='no' first_color='#000' second_color='#000' row_gradient_color='pixflow_base64eyJjb2xvcjEiOiIjZmZmIiwiY29sb3IyIjoicmdiYSgyNTUsMjU1LDI1NSwwKSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=' row_image_position_gradient='fit' row_bg_image_size_tab_gradient='cover' row_bg_repeat_gradient_gp='no' row_inner_shadow='no' row_sloped_edge='no' row_slope_edge_position='top' row_sloped_edge_color='#000' row_sloped_edge_angle='-3' parallax_status='yes' parallax_speed='4' align='no'][vc_column el_class=\"\" width=\"4/12\" margin_top=\"0\" margin_right=\"0\" margin_bottom=\"0\" margin_left=\"0\" padding_top=\"0\" padding_right=\"50\" padding_bottom=\"0\" padding_left=\"0\" border_color=\"rgba(0,0,0,1)\" border_style=\"solid\" border_top_width=\"0\" border_right_width=\"0\" border_bottom_width=\"0\" border_left_width=\"0\" background_color=\"rgba(0,0,0,0)\" background_image=\"undefined\" css=\"{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;padding-top:0px;padding-right:50px;padding-bottom:0px;padding-left:0px;border-color:rgba(0,0,0,1);border-top-width:0px;border-right-width:0px;border-bottom-width:0px;border-left-width:0px;background-color:rgba(0,0,0,0);background-image:undefined;border-style:solid;background-size:;}\" md_laptop_visibility=\"yes\" md_tablet_portrait_visibility=\"yes\" md_tablet_landscape_visibility=\"yes\" md_mobile_portrait_visibility=\"yes\" md_mobile_landscape_visibility=\"yes\" md_tablet_portrait_width=\"0\"][md_iconbox_side2 iconbox_side2_title=\"\" iconbox_side2_title_big=\"Unique Elements\" iconbox_side2_title_big_heading=\"h5\" iconbox_side2_description=\"Massive Builder provides a platform to simply drag & drop elements, choose styles and see the result instantly… You can literally create a whole website in minutes! \" iconbox_side2_type=\"icon\" iconbox_side2_icon=\"icon-android2\" iconbox_side2_alignment=\"left\" iconbox_side2_small_title_color=\"#12be83\" iconbox_side2_general_color=\"#ffffff\" iconbox_side2_icon_color=\"rgb(255, 255, 255)\" iconbox_side2_button=\"no\" iconbox_side2_button_style=\"fade-square\" iconbox_side2_button_text=\"Read more\" iconbox_side2_class=\"icon-empty\" iconbox_side2_button_color=\"#5e5e5e\" iconbox_side2_button_text_color=\"#fff\" iconbox_side2_button_bg_hover_color=\"#9b9b9b\" iconbox_side2_button_hover_color=\"#FFF\" iconbox_side2_button_size=\"standard\" iconbox_side2_left_right_padding=\"0\" iconbox_side2_button_url=\"#\" iconbox_side2_button_target=\"_self\" md_iconbox_side2_animation_speed=\"400\" md_iconbox_side2_animation_delay=\"0.0\" md_iconbox_side2_animation_position=\"center\" md_iconbox_side2_animation_show=\"once\"][/md_iconbox_side2][/vc_column][vc_column el_class=\"\" width=\"4/12\" margin_top=\"0\" margin_right=\"0\" margin_bottom=\"0\" margin_left=\"0\" padding_top=\"0\" padding_right=\"20\" padding_bottom=\"0\" padding_left=\"30\" border_color=\"rgba(0,0,0,1)\" border_style=\"solid\" border_top_width=\"0\" border_right_width=\"0\" border_bottom_width=\"0\" border_left_width=\"0\" background_color=\"rgba(0,0,0,0)\" background_image=\"undefined\" css=\"{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;padding-top:0px;padding-right:20px;padding-bottom:0px;padding-left:30px;border-color:rgba(0,0,0,1);border-top-width:0px;border-right-width:0px;border-bottom-width:0px;border-left-width:0px;background-color:rgba(0,0,0,0);background-image:undefined;border-style:solid;background-size:;}\" md_laptop_visibility=\"yes\" md_tablet_portrait_visibility=\"yes\" md_tablet_landscape_visibility=\"yes\" md_mobile_portrait_visibility=\"yes\" md_mobile_landscape_visibility=\"yes\" md_tablet_portrait_width=\"0\"][md_iconbox_side2 iconbox_side2_title=\"\" iconbox_side2_title_big=\"Drag & Drop\" iconbox_side2_title_big_heading=\"h5\" iconbox_side2_description=\"Massive Dynamic comes with most advanced live website builder on WordPress. Featuring latest web technologies, enjoyable UX and the most beautiful design trends. \" iconbox_side2_type=\"icon\" iconbox_side2_icon=\"icon-vector-square-1\" iconbox_side2_alignment=\"left\" iconbox_side2_small_title_color=\"#12be83\" iconbox_side2_general_color=\"#ffffff\" iconbox_side2_icon_color=\"rgb(255, 255, 255)\" iconbox_side2_button=\"no\" iconbox_side2_button_style=\"fade-square\" iconbox_side2_button_text=\"Read more\" iconbox_side2_class=\"icon-empty\" iconbox_side2_button_color=\"#5e5e5e\" iconbox_side2_button_text_color=\"#fff\" iconbox_side2_button_bg_hover_color=\"#9b9b9b\" iconbox_side2_button_hover_color=\"#FFF\" iconbox_side2_button_size=\"standard\" iconbox_side2_left_right_padding=\"0\" iconbox_side2_button_url=\"#\" iconbox_side2_button_target=\"_self\" md_iconbox_side2_animation_speed=\"400\" md_iconbox_side2_animation_delay=\"0.0\" md_iconbox_side2_animation_position=\"center\" md_iconbox_side2_animation_show=\"once\"][/md_iconbox_side2][/vc_column][vc_column el_class=\"\" width=\"4/12\" margin_top=\"0\" margin_right=\"0\" margin_bottom=\"0\" margin_left=\"0\" padding_top=\"0\" padding_right=\"0\" padding_bottom=\"0\" padding_left=\"50\" border_color=\"rgba(0,0,0,1)\" border_style=\"solid\" border_top_width=\"0\" border_right_width=\"0\" border_bottom_width=\"0\" border_left_width=\"0\" background_color=\"rgba(0,0,0,0)\" background_image=\"undefined\" css=\"{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:50px;border-color:rgba(0,0,0,1);border-top-width:0px;border-right-width:0px;border-bottom-width:0px;border-left-width:0px;background-color:rgba(0,0,0,0);background-image:undefined;border-style:solid;background-size:;}\" md_laptop_visibility=\"yes\" md_tablet_portrait_visibility=\"yes\" md_tablet_landscape_visibility=\"yes\" md_mobile_portrait_visibility=\"yes\" md_mobile_landscape_visibility=\"yes\" md_tablet_portrait_width=\"0\"][md_iconbox_side2 iconbox_side2_title=\"\" iconbox_side2_title_big=\"Text Editor\" iconbox_side2_title_big_heading=\"h5\" iconbox_side2_description=\"Massive Builder provides a rich user experience for everyone, whether you are a web ninja or a WordPress beginner, it helps you create any website quickly.\" iconbox_side2_type=\"icon\" iconbox_side2_icon=\"icon-pencil-ruler\" iconbox_side2_alignment=\"left\" iconbox_side2_small_title_color=\"#12be83\" iconbox_side2_general_color=\"#ffffff\" iconbox_side2_icon_color=\"rgb(255, 255, 255)\" iconbox_side2_button=\"no\" iconbox_side2_button_style=\"fade-square\" iconbox_side2_button_text=\"Read more\" iconbox_side2_class=\"icon-empty\" iconbox_side2_button_color=\"#5e5e5e\" iconbox_side2_button_text_color=\"#fff\" iconbox_side2_button_bg_hover_color=\"#9b9b9b\" iconbox_side2_button_hover_color=\"#FFF\" iconbox_side2_button_size=\"standard\" iconbox_side2_left_right_padding=\"0\" iconbox_side2_button_url=\"#\" iconbox_side2_button_target=\"_self\" md_iconbox_side2_animation_speed=\"400\" md_iconbox_side2_animation_delay=\"0.0\" md_iconbox_side2_animation_position=\"center\" md_iconbox_side2_animation_show=\"once\"][/md_iconbox_side2][/vc_column][/vc_row][vc_row row_type='none' type_width='full_size' box_size_states='content_box_size' el_class='' row_fit_to_height='no' row_vertical_align='no' row_equal_column_heigh='no' row_content_vertical_align='0' row_padding_top='100' row_padding_bottom='30' row_padding_right='0' row_padding_left='0' row_margin_top='0' row_margin_bottom='0' background_color='rgba(255,255,255,1)' row_webm_url='' row_mp4_url='' background_color_image='rgba(0,0,0,0.2)' row_image_position='default' row_bg_image_size_tab_image='cover' row_bg_repeat_image_gp='no' first_color='#000' second_color='#000' row_gradient_color='pixflow_base64eyJjb2xvcjEiOiIjZmZmIiwiY29sb3IyIjoicmdiYSgyNTUsMjU1LDI1NSwwKSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=' row_image_position_gradient='fit' row_bg_image_size_tab_gradient='cover' row_bg_repeat_gradient_gp='no' row_inner_shadow='no' row_sloped_edge='no' row_slope_edge_position='top' row_sloped_edge_color='#000' row_sloped_edge_angle='-3' parallax_status='no' parallax_speed='1' align='no'][vc_column width=\"2/12\" el_id='582409850115c'][vc_empty_space height=\"60\" el_id='58240985011f7'][/vc_empty_space][/vc_column][vc_column width=\"8/12\"][md_text md_text_alignment=\"center\" md_text_title_line_height=\"43\" md_text_desc_line_height=\"12\" md_text_title_bottom_space=\"0\" md_text_separator_bottom_space=\"31\" md_text_description_bottom_space=\"0\" md_text_title_separator=\"no\" md_text_separator_width=\"110\" md_text_separator_height=\"5\" md_text_separator_color=\"rgb(50, 50, 50)\" md_text_use_desc_custom_font=\"yes\" md_text_desc_google_fonts=\"font_family:Roboto%3Aregular%2C100%2C100italic%2C300%2C300italic%2Citalic%2C500%2C500italic%2C700%2C700italic%2C900%2C900italic|font_style:400%20regular%3A400%3Anormal\" md_text_style=\"solid\" md_text_solid_color=\"rgba(20,20,20,1)\" md_text_gradient_color=\"pixflow_base64eyJjb2xvcjEiOiIjODcwMmZmIiwiY29sb3IyIjoiIzA2ZmY2ZSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=\" md_text_title_size=\"32\" md_text_letter_space=\"-1\" md_text_hover_letter_space=\"-1\" md_text_easing=\"cubic-bezier(0.215, 0.61, 0.355, 1)\" md_text_use_title_custom_font=\"no\" md_text_title_google_fonts=\"font_family:Roboto%3Aregular%2C100%2C100italic%2C300%2C300italic%2Citalic%2C500%2C500italic%2C700%2C700italic%2C900%2C900italic|font_style:400%20regular%3A400%3Anormal\" md_text_number=\"1\" md_text_title1_text=\"<div style='font-weight: 500; font-family: Poppins;font-weight: 500;font-family: Poppinsfont-weight: 600;font-family: Poppins' data-mce-style='font-weight: 500; font-family: Poppins;'><span data-mce-style='color: #2e2e2e; font-size: 32px; font-weight: 600;' style='color: rgb(46, 46, 46); font-size: 32px; font-weight: 600;'><span style='position: relative; font-weight: 300; font-family: Poppins;' data-mce-style='position: relative; font-weight: 300; font-family: Poppins;'>Take Control Of</span></span></div><div style='font-weight: 500; font-family: Poppins;font-weight: 500;font-family: Poppinsfont-weight: 600;font-family: Poppins' data-mce-style='font-weight: 500; font-family: Poppins;'><span style='font-size: 32px;' data-mce-style='font-size: 32px;'><span data-mce-style='color: #2e2e2e; font-weight: 600;' style='color: rgb(46, 46, 46); font-weight: 600;'>your </span><span data-mce-style='color: #2e2e2e; font-weight: 600;' style='color: rgb(46, 46, 46); font-weight: 600;'>text </span><span data-mce-style='color: #2e2e2e; font-weight: 600;' style='color: rgb(46, 46, 46); font-weight: 600;'>with single click</span></span><br></div>\" md_text_title1=\"pixflow_base64PGRpdiBzdHlsZT0iZm9udC13ZWlnaHQ6IDUwMDsgZm9udC1mYW1pbHk6IFBvcHBpbnM7Zm9udC13ZWlnaHQ6IDUwMDtmb250LWZhbWlseTogUG9wcGluc2ZvbnQtd2VpZ2h0OiA2MDA7Zm9udC1mYW1pbHk6IFBvcHBpbnMiIGRhdGEtbWNlLXN0eWxlPSJmb250LXdlaWdodDogNTAwOyBmb250LWZhbWlseTogUG9wcGluczsiPjxzcGFuIGRhdGEtbWNlLXN0eWxlPSJjb2xvcjogIzJlMmUyZTsgZm9udC1zaXplOiAzMnB4OyBmb250LXdlaWdodDogNjAwOyIgc3R5bGU9ImNvbG9yOiByZ2IoNDYsIDQ2LCA0Nik7IGZvbnQtc2l6ZTogMzJweDsgZm9udC13ZWlnaHQ6IDYwMDsiPjxzcGFuIHN0eWxlPSJwb3NpdGlvbjogcmVsYXRpdmU7IGZvbnQtd2VpZ2h0OiAzMDA7IGZvbnQtZmFtaWx5OiBQb3BwaW5zOyIgZGF0YS1tY2Utc3R5bGU9InBvc2l0aW9uOiByZWxhdGl2ZTsgZm9udC13ZWlnaHQ6IDMwMDsgZm9udC1mYW1pbHk6IFBvcHBpbnM7Ij5UYWtlIGNvbnRyb2wgb2Y8L3NwYW4+PC9zcGFuPjwvZGl2PjxkaXYgc3R5bGU9ImZvbnQtd2VpZ2h0OiA1MDA7IGZvbnQtZmFtaWx5OiBQb3BwaW5zO2ZvbnQtd2VpZ2h0OiA1MDA7Zm9udC1mYW1pbHk6IFBvcHBpbnNmb250LXdlaWdodDogNjAwO2ZvbnQtZmFtaWx5OiBQb3BwaW5zIiBkYXRhLW1jZS1zdHlsZT0iZm9udC13ZWlnaHQ6IDUwMDsgZm9udC1mYW1pbHk6IFBvcHBpbnM7Ij48c3BhbiBzdHlsZT0iZm9udC1zaXplOiAzMnB4OyIgZGF0YS1tY2Utc3R5bGU9ImZvbnQtc2l6ZTogMzJweDsiPjxzcGFuIGRhdGEtbWNlLXN0eWxlPSJjb2xvcjogIzJlMmUyZTsgZm9udC13ZWlnaHQ6IDYwMDsiIHN0eWxlPSJjb2xvcjogcmdiKDQ2LCA0NiwgNDYpOyBmb250LXdlaWdodDogNjAwOyI+eW91ciA8L3NwYW4+PHNwYW4gZGF0YS1tY2Utc3R5bGU9ImNvbG9yOiAjMmUyZTJlOyBmb250LXdlaWdodDogNjAwOyIgc3R5bGU9ImNvbG9yOiByZ2IoNDYsIDQ2LCA0Nik7IGZvbnQtd2VpZ2h0OiA2MDA7Ij50ZXh0IDwvc3Bhbj48c3BhbiBkYXRhLW1jZS1zdHlsZT0iY29sb3I6ICMyZTJlMmU7IGZvbnQtd2VpZ2h0OiA2MDA7IiBzdHlsZT0iY29sb3I6IHJnYig0NiwgNDYsIDQ2KTsgZm9udC13ZWlnaHQ6IDYwMDsiPndpdGggc2luZ2xlIGNsaWNrPC9zcGFuPjwvc3Bhbj48L2Rpdj4=\"    md_text_title2=\"Typography Shortcode\" md_text_title3=\"Typography Shortcode\" md_text_title4=\"Typography Shortcode\" md_text_title5=\"Typography Shortcode\" md_text_content_size=\"14\" md_text_content_color=\"rgba(20,20,20,1)\" md_text_use_button=\"no\" md_text_button_style=\"fade-oval\" md_text_button_text=\"READ MORE\" md_text_button_icon_class=\"icon-angle-right\" md_text_button_color=\"rgba(0,0,0,1)\" md_text_button_text_color=\"rgba(255,255,255,1)\" md_text_button_bg_hover_color=\"rgb(0,0,0)\" md_text_button_hover_color=\"rgb(255,255,255)\" md_text_button_size=\"standard\" left_right_padding=\"0\" md_text_button_url=\"#\" md_text_button_target=\"_self\" md_text_animation_speed=\"400\" md_text_animation_delay=\"0.0\" md_text_animation_position=\"center\" md_text_animation_show=\"once\" align=\"center\"  md_text_fonts=\"\"  md_text_use_title_slider=\"yes\"]<div class=\"disable-edit\" style=\"z-index: 100;\" data-mce-style=\"z-index: 100;\"> <br></div><p> <br></p>[/md_text][/vc_column][vc_column width=\"2/12\" el_id='58240985011af'][vc_empty_space height=\"60\" el_id='5824098501240'][/vc_empty_space][/vc_column][/vc_row][vc_row row_type='none' type_width='full_size' box_size_states='content_box_size' el_class='' row_fit_to_height='no' row_vertical_align='no' row_equal_column_heigh='no' row_content_vertical_align='0' row_padding_top='25' row_padding_bottom='0' row_padding_right='0' row_padding_left='0' row_margin_top='0' row_margin_bottom='0' background_color='rgba(255,255,255,1)' row_webm_url='' row_mp4_url='' background_color_image='rgba(0,0,0,0.2)' row_image_position='default' row_bg_image_size_tab_image='cover' row_bg_repeat_image_gp='no' first_color='#000' second_color='#000' row_gradient_color='pixflow_base64eyJjb2xvcjEiOiIjZmZmIiwiY29sb3IyIjoicmdiYSgyNTUsMjU1LDI1NSwwKSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=' row_image_position_gradient='fit' row_bg_image_size_tab_gradient='cover' row_bg_repeat_gradient_gp='no' row_inner_shadow='no' row_sloped_edge='no' row_slope_edge_position='top' row_sloped_edge_color='#000' row_sloped_edge_angle='-3' parallax_status='no' parallax_speed='1'][vc_column el_class=\"\" width=\"6/12\" margin_top=\"0\" margin_right=\"0\" margin_bottom=\"0\" margin_left=\"0\" padding_top=\"0\" padding_right=\"10\" padding_bottom=\"10\" padding_left=\"10\" border_color=\"rgba(0,0,0,1)\" border_style=\"solid\" border_top_width=\"0\" border_right_width=\"0\" border_bottom_width=\"0\" border_left_width=\"0\" background_color=\"rgba(0,0,0,0)\" background_image=\"undefined\" css=\"{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;padding-top:0px;padding-right:10px;padding-bottom:10px;padding-left:10px;border-color:rgba(0,0,0,1);border-top-width:0px;border-right-width:0px;border-bottom-width:0px;border-left-width:0px;background-color:rgba(0,0,0,0);background-image:undefined;border-style:solid;background-size:;}\" md_laptop_visibility=\"yes\" md_tablet_portrait_visibility=\"yes\" md_tablet_landscape_visibility=\"yes\" md_mobile_portrait_visibility=\"yes\" md_mobile_landscape_visibility=\"yes\" md_tablet_portrait_width=\"0\"][md_image_box_slider image_box_slider_image=\"http://demo.massivedynamic.co/general/wp-content/uploads/2016/11/photo-1477973370894-00f376675344.jpg\" image_box_slider_height=\"400\" image_box_slider_size=\"cover\" image_box_slider_hover=\"no\" image_box_slider_hover_link=\"\" image_box_slider_effect_slider=\"fade\" image_box_slider_speed=\"3000\" image_box_slider_hover_effect=\"text\" image_box_slider_hover_text_effect=\"light\" image_box_slider_hover_text=\"Text Hover\" md_image_box_slider_animation_speed=\"400\" md_image_box_slider_animation_delay=\"0.0\" md_image_box_slider_animation_position=\"center\" md_image_box_slider_animation_show=\"once\"][/md_image_box_slider][/vc_column][vc_column el_class=\"\" width=\"6/12\" margin_top=\"0\" margin_right=\"0\" margin_bottom=\"0\" margin_left=\"0\" padding_top=\"0\" padding_right=\"10\" padding_bottom=\"10\" padding_left=\"10\" border_color=\"rgba(0,0,0,1)\" border_style=\"solid\" border_top_width=\"0\" border_right_width=\"0\" border_bottom_width=\"0\" border_left_width=\"0\" background_color=\"rgba(0,0,0,0)\" css=\"{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;padding-top:0px;padding-right:10px;padding-bottom:10px;padding-left:10px;border-color:rgba(0,0,0,1);border-top-width:0px;border-right-width:0px;border-bottom-width:0px;border-left-width:0px;background-color:rgba(0,0,0,0);background-image:undefined;border-style:solid;background-size:;}\" md_laptop_visibility=\"yes\" md_tablet_portrait_visibility=\"yes\" md_tablet_landscape_visibility=\"yes\" md_mobile_portrait_visibility=\"yes\" md_mobile_landscape_visibility=\"yes\" md_tablet_portrait_width=\"0\"][md_image_box_slider image_box_slider_image=\"http://demo.massivedynamic.co/general/wp-content/uploads/2016/11/Death_to_stock_photography_Wake_Up_1-870x587.jpg\" image_box_slider_height=\"400\" image_box_slider_size=\"cover\" image_box_slider_hover=\"no\" image_box_slider_hover_link=\"\" image_box_slider_effect_slider=\"fade\" image_box_slider_speed=\"3000\" image_box_slider_hover_effect=\"text\" image_box_slider_hover_text_effect=\"light\" image_box_slider_hover_text=\"Text Hover\" md_image_box_slider_animation_speed=\"400\" md_image_box_slider_animation_delay=\"0.0\" md_image_box_slider_animation_position=\"center\" md_image_box_slider_animation_show=\"once\"][/md_image_box_slider][/vc_column][/vc_row][vc_row row_type='none' type_width='full_size' box_size_states='content_box_size' el_class='' row_fit_to_height='no' row_vertical_align='no' row_equal_column_heigh='no' row_content_vertical_align='0' row_padding_top='0' row_padding_bottom='100' row_padding_right='0' row_padding_left='0' row_margin_top='0' row_margin_bottom='0' background_color='rgba(255,255,255,1)' row_webm_url='' row_mp4_url='' background_color_image='rgba(0,0,0,0.2)' row_image_position='default' row_bg_image_size_tab_image='cover' row_bg_repeat_image_gp='no' first_color='#000' second_color='#000' row_gradient_color='pixflow_base64eyJjb2xvcjEiOiIjZmZmIiwiY29sb3IyIjoicmdiYSgyNTUsMjU1LDI1NSwwKSIsImNvbG9yMVBvcyI6IjAuMDAiLCJjb2xvcjJQb3MiOiIxMDAuMDAiLCJhbmdsZSI6MH0=' row_image_position_gradient='fit' row_bg_image_size_tab_gradient='cover' row_bg_repeat_gradient_gp='no' row_inner_shadow='no' row_sloped_edge='no' row_slope_edge_position='top' row_sloped_edge_color='#000' row_sloped_edge_angle='-3' parallax_status='no' parallax_speed='1' align='no'][vc_column el_class=\"\" width=\"4/12\" margin_top=\"0\" margin_right=\"0\" margin_bottom=\"0\" margin_left=\"0\" padding_top=\"10\" padding_right=\"10\" padding_bottom=\"0\" padding_left=\"10\" border_color=\"rgba(0,0,0,1)\" border_style=\"solid\" border_top_width=\"0\" border_right_width=\"0\" border_bottom_width=\"0\" border_left_width=\"0\" background_color=\"rgba(0,0,0,0)\" background_image=\"undefined\" css=\"{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;padding-top:10px;padding-right:10px;padding-bottom:0px;padding-left:10px;border-color:rgba(0,0,0,1);border-top-width:0px;border-right-width:0px;border-bottom-width:0px;border-left-width:0px;background-color:rgba(0,0,0,0);background-image:undefined;border-style:solid;background-size:;}\" md_laptop_visibility=\"yes\" md_tablet_portrait_visibility=\"yes\" md_tablet_landscape_visibility=\"yes\" md_mobile_portrait_visibility=\"yes\" md_mobile_landscape_visibility=\"yes\" md_tablet_portrait_width=\"0\" el_id='5823172781703'][md_image_box_slider image_box_slider_image=\"http://demo.massivedynamic.co/general/wp-content/uploads/2016/11/photo-1471171768346-d08fb2813c45.jpg\" image_box_slider_height=\"400\" image_box_slider_size=\"cover\" image_box_slider_hover=\"no\" image_box_slider_hover_link=\"\" image_box_slider_effect_slider=\"fade\" image_box_slider_speed=\"3000\" image_box_slider_hover_effect=\"text\" image_box_slider_hover_text_effect=\"light\" image_box_slider_hover_text=\"Text Hover\" md_image_box_slider_animation_speed=\"400\" md_image_box_slider_animation_delay=\"0.0\" md_image_box_slider_animation_position=\"center\" md_image_box_slider_animation_show=\"once\"][/md_image_box_slider][/vc_column][vc_column el_class=\"\" width=\"4/12\" margin_top=\"0\" margin_right=\"0\" margin_bottom=\"0\" margin_left=\"0\" padding_top=\"10\" padding_right=\"10\" padding_bottom=\"0\" padding_left=\"10\" border_color=\"rgba(0,0,0,1)\" border_style=\"solid\" border_top_width=\"0\" border_right_width=\"0\" border_bottom_width=\"0\" border_left_width=\"0\" background_color=\"rgba(0,0,0,0)\" background_image=\"undefined\" css=\"{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;padding-top:10px;padding-right:10px;padding-bottom:0px;padding-left:10px;border-color:rgba(0,0,0,1);border-top-width:0px;border-right-width:0px;border-bottom-width:0px;border-left-width:0px;background-color:rgba(0,0,0,0);background-image:undefined;border-style:solid;background-size:;}\" md_laptop_visibility=\"yes\" md_tablet_portrait_visibility=\"yes\" md_tablet_landscape_visibility=\"yes\" md_mobile_portrait_visibility=\"yes\" md_mobile_landscape_visibility=\"yes\" md_tablet_portrait_width=\"0\" el_id='5823172781791'][md_image_box_slider image_box_slider_image=\"http://demo.massivedynamic.co/general/wp-content/uploads/2016/11/photo-1478207820126-2b0217b53ed1.jpg\" image_box_slider_height=\"400\" image_box_slider_size=\"cover\" image_box_slider_hover=\"no\" image_box_slider_hover_link=\"\" image_box_slider_effect_slider=\"fade\" image_box_slider_speed=\"3000\" image_box_slider_hover_effect=\"text\" image_box_slider_hover_text_effect=\"light\" image_box_slider_hover_text=\"Text Hover\" md_image_box_slider_animation_speed=\"400\" md_image_box_slider_animation_delay=\"0.0\" md_image_box_slider_animation_position=\"center\" md_image_box_slider_animation_show=\"once\"][/md_image_box_slider][/vc_column][vc_column el_class=\"\" width=\"4/12\" margin_top=\"0\" margin_right=\"0\" margin_bottom=\"0\" margin_left=\"0\" padding_top=\"10\" padding_right=\"10\" padding_bottom=\"0\" padding_left=\"10\" border_color=\"rgba(0,0,0,1)\" border_style=\"solid\" border_top_width=\"0\" border_right_width=\"0\" border_bottom_width=\"0\" border_left_width=\"0\" background_color=\"rgba(0,0,0,0)\" css=\"{margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;padding-top:10px;padding-right:10px;padding-bottom:0px;padding-left:10px;border-color:rgba(0,0,0,1);border-top-width:0px;border-right-width:0px;border-bottom-width:0px;border-left-width:0px;background-color:rgba(0,0,0,0);background-image:undefined;border-style:solid;background-size:;}\" md_laptop_visibility=\"yes\" md_tablet_portrait_visibility=\"yes\" md_tablet_landscape_visibility=\"yes\" md_mobile_portrait_visibility=\"yes\" md_mobile_landscape_visibility=\"yes\" md_tablet_portrait_width=\"0\"][md_image_box_slider image_box_slider_image=\"http://demo.massivedynamic.co/general/wp-content/uploads/2016/11/sas.jpg\" image_box_slider_height=\"400\" image_box_slider_size=\"cover\" image_box_slider_hover=\"no\" image_box_slider_hover_link=\"\" image_box_slider_effect_slider=\"fade\" image_box_slider_speed=\"3000\" image_box_slider_hover_effect=\"text\" image_box_slider_hover_text_effect=\"light\" image_box_slider_hover_text=\"Text Hover\" md_image_box_slider_animation_speed=\"400\" md_image_box_slider_animation_delay=\"0.0\" md_image_box_slider_animation_position=\"center\" md_image_box_slider_animation_show=\"once\"][/md_image_box_slider][/vc_column][/vc_row]
            ";

    global $user_ID;
    $page = array();
    $page['post_type'] = 'page';
    $page['post_content'] = $contentMassivePage;
    $page['post_parent'] = 0;
    $page['post_author'] = $user_ID;
    $page['post_status'] = 'publish';
    $page['post_title'] = esc_attr__('Test Page', 'massive-dynamic');
    $page_id = wp_insert_post($page);
    update_post_meta($page_id, 'pixflow_sample_page', 'true');
    update_option( 'pixflow_sample_page' , true );
    return $page_id;
}

add_filter( 'tiny_mce_before_init', 'pixflow_unsetAutoresizeOn' );
function pixflow_unsetAutoresizeOn( $init ) {
    unset( $init['wp_autoresize_on'] );
    return $init;
}

// Add massive link edit button to each page
function pixflow_render_edit_button($actions, $page_object){
    $page_object = (array) $page_object ;
    if( pixflow_is_builder_editable( $page_object["ID"] ) == true ){
        $actions['massive-dynamic-link']  = '<a href="'. get_site_url() . '/?page_id=' . $page_object["ID"] .'&mbuilder=true'
            .'" class="md-link">' . __('Edit with Massive Builder','massive-dynamic') . '</a>';
    }
    return $actions;
}
add_filter( 'page_row_actions', 'pixflow_render_edit_button' , 10 , 2 );
