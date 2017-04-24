<?php
if(session_id() == '' && !headers_sent()) {
    session_start();
}
unset($_SESSION['temp_status']);
unset($_SESSION['vc_temp_content']);
require_once(PIXFLOW_THEME_LIB . '/constants.php');
//Return theme option
function pixflow_opt($option){
    $opt = get_option(PIXFLOW_OPTIONS_KEY);
    return stripslashes($opt[$option]);
}
function pixflow_get_custom_sidebars()
{
    $sidebarStr = pixflow_opt('custom_sidebars');

    if(strlen($sidebarStr) < 1)
        return array();

    $arr      = explode(',', $sidebarStr);
    $sidebars = array();

    foreach($arr as $item)
    {
        $sidebars["custom-" . hash("crc32b", $item)] = str_replace('%666', ',', $item);
    }

    return $sidebars;
}
function pixflow_get_theme_mod($name, $default = null , $post_id = false){

    if($post_id != false){
        $post_id = $post_id;
    }elseif(isset($_SESSION['pixflow_post_id']) && $_SESSION['pixflow_post_id']!=null){
        $post_id = $_SESSION['pixflow_post_id'];
    }else{
        if(is_home()|| is_404()|| is_search()){
            $post_id = get_option( 'page_for_posts' );
        }elseif(function_exists('is_shop') && (is_shop() || is_product_category()) && !is_product()) {
            $post_id = get_option( 'woocommerce_shop_page_id' );
        }else{
            $post_id = get_the_ID();
        }
    }
    $post_type = get_post_type($post_id);
    if((isset($_SESSION['temp_status'])) && $_SESSION['temp_status']['id'] == $post_id){
        $setting_status = $_SESSION['temp_status']['status'];
    }elseif(get_option( 'page_for_posts' ) != $post_id && ($post_type == 'post' || $post_type == 'portfolio' || $post_type == 'product')){
        if(isset($_SESSION[$post_type . '_status'])){
            $setting_status = $_SESSION[$post_type . '_status'];
        }else{
            $setting_status = get_option( $post_type.'_setting_status' );
        }
    }else{
        $setting_status = get_post_meta( $post_id,'setting_status',true ) ;
    }

    $setting_status = ($setting_status == 'unique')?'unique':'general';

    $customizedValues = (isset($_SESSION[$setting_status.'_customized']))?$_SESSION[$setting_status.'_customized']:array();
    if(isset($_POST['customized'])){
        $customizedValues = json_decode( wp_unslash( $_POST['customized'] ), true );
    }

    if(count($customizedValues) && array_key_exists($name,$customizedValues)){
        $value = $customizedValues[$name];

    }else{
        global $md_uniqueSettings;
        $settings = $md_uniqueSettings;

        if($setting_status == 'unique' && in_array($name, $settings)){

            if($post_type == 'post' || $post_type == 'portfolio' || $post_type == 'product' ){
                $value = get_option( $post_type.'_'.$name );
                $value = ($value === false)?get_theme_mod($name,$default):$value;
            }else{
                $value = get_post_meta( $post_id,$name,true );
                $value = ($value === 'false')?false:$value;
            }

            if($value === ''){
                $value = get_theme_mod($name,$default);
                $value = ($value === '')?$default:$value;
            }
        }else{
            $value = get_theme_mod($name,$default);
        }
    }
    $value = ($value === 'false')?false:$value;
    return $value;
}
function pixflow_path_combine($path1, $path2)
{
    $dirSep = '/';//It should be DIRECTORY_SEPARATOR constant but doesn't work with URIs in WordPress
    $e1   = $path1{strlen($path1) - 1};
    $b2   = $path2{0};

    //Convert
    if($e1 === '\\')
        $e1 = $dirSep;

    if($b2 === '\\')
        $b2 = $dirSep;


    //Both paths has no separator chars
    if($e1 !== $dirSep && $b2 !== $dirSep)
    {
        $value = $path1 . $dirSep . $path2;
    }
    //One path has directory separator and the other doesn't
    elseif(($e1 === $dirSep && $b2 !== $dirSep) ||
        ($e1 !== $dirSep && $b2 === $dirSep)
    )
    {
        $value = $path1 . $path2;
    }
    //Else both path has directory separator
    else
    {
        $value = $path1 . mb_substr($path2, 1);
    }

    $args  = func_get_args();

    if(count($args) < 3)
        return $value;

    $newArgs = array_merge(array($value), array_slice($args, 2));

    return call_user_func_array('pixflow_path_combine', $newArgs);
}
function custom_remove_themes_section() {
    global $wp_customize;
    $wp_customize->remove_section( 'themes' );
    $wp_customize->remove_control( 'active_theme' );
}
add_action( 'customize_register', 'custom_remove_themes_section' );
require_once(PIXFLOW_THEME_LIB . '/customizer/customizer.php');
require_once(PIXFLOW_THEME_LIB . '/menus.php');
require_once(PIXFLOW_THEME_LIB . '/sidebars.php');