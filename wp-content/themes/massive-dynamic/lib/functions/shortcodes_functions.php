<?php

function unset_filters_for($hook = '')
{
    global $wp_filter;
    if (empty($hook) || !isset($wp_filter[$hook]))
        return;

    unset($wp_filter[$hook]);
}

unset_filters_for('vc_shortcode_output');

/**
 * Detect manual insert new line
 * @param string value
 * @return string with new br tags
 */
function pixflow_detect_new_lines($value)
{
	if( get_post_type( get_the_ID() ) == 'post'){
		$newLineArray = array("\r\n","\n\r","\n","\r");
		$new_string = str_replace($newLineArray,"<br />", $value);
	}else{
		$new_string = array();
		$new_string_with_br = explode('<br />', nl2br($value));
		for ($i = 0; $i < count($new_string_with_br); $i++) {
			if (strlen($new_string_with_br[$i]) !== 1) {
				$new_string[] = trim($new_string_with_br[$i]);
			}
		}
		$new_string = implode('<br />', $new_string);
	}

	return $new_string;
}

/**
 * Check the content contains p tag or not
 * @param string value
 * @return string with p tags
 */
function pixflow_detectPTags($value)
{
    if (strpos($value, '</p>') == false) :
        $NewString = '<p>' . $value . '</p>';
        return $NewString;
    else:
        return $value;
    endif;
}

/**
 * Check the content of text shortcode Which is not something else.
 * @param string content of text shortcode
 * @return string that removes extra stuff.
 */
function pixflow_detectBasetext($content)
{
    if (preg_match("/<div[^>]*?class=\"md-text-content\">(.*?)<\\/div>/si", $content, $match)) {
        $content = $match[1];
        return trim($content);
    } else {
        return $content;
    }
}

function pixflow_output_validation($content)
{
    if (strpos($content, '</style>') || strpos($content, '</script>')) {
        $content = preg_replace('/(<script[^>]*>.+?<\/script>)/s', '', $content);
//        $content = trim(preg_replace(array('/\r/', '/\n/' , '/<!--(.*)-->/Uis'), '',$content));
        return $content;
    } else {
        return $content;
    }
}
// use wp_filesystem for import
function pixflow_import_media($content)
{
    unset($_SESSION['pixflow_' . get_site_url() .'inlinejs']);
    $_SESSION['pixflow_' . get_site_url() .'inlinejs'] = $content;
}

// pixflow_get_inline_scripts
function pixflow_get_inline_scripts($data)
{
    global $jsString;
    if (preg_match_all('#<\s*?script\b[^>]*>(.*?)</script\b[^>]*>#s', $data, $match)) {
        foreach ($match[1] as $jsdata) {
            $jsString .= $jsdata;
        }
        return trim($jsString);
    } else {
        return "";
    }
}

/**
 * load dependency file (shortcode or widget) and return array of dependent js,css and shortcodes
 * @param string $name shortcode name or widget name
 * @param string $type can be shortcode or widget
 * @return array
 */
function pixflow_load_dependecy_file($name, $type = 'shortcode'){
    $return = array('js'=>'','css'=>'');

    if('shortcode'==$type){
        $path = PIXFLOW_THEME_SHORTCODES;
    }elseif('widget'==$type){
        $path = PIXFLOW_THEME_WIDGETS;
    }
    $dependency_list =  $path. '/' . $name . '/dependency.json';

    if(!file_exists($dependency_list)) {
        return $return;
    }
    $require_plugin = json_decode(@file_get_contents($dependency_list), true);
    if($require_plugin){
        $require_plugin['shortcode'] = (isset($require_plugin['shortcode']))?$require_plugin['shortcode']:array();
        return $require_plugin;
    }else{
        return $return;
    }
}

/**
 * load dependet scripts of shortcodes and widgets
 * @param array list of depentens plugins
 * @return string as dependent scripts
 */
function pixflow_load_dependent_scripts($require_plugins){
    global $pixflow_loaded_plugins;
    $pixflow_loaded_dependency = array();
    $scripts = '';
    // Load dependent plugin scripts
    if (count($require_plugins['js']) != 0 &&
        ( is_array( $require_plugins['js'] ) || is_object( $require_plugins['js']  ) ) ) {
        foreach ($require_plugins['js'] as $js_path) {
            if(file_exists(PIXFLOW_THEME_DIR . '/'. $js_path) &&
                array_search( $js_path , $pixflow_loaded_dependency , true ) == false ) {
                if(in_array($js_path,$pixflow_loaded_plugins)){
                    continue;
                }
                $scripts .= @file_get_contents(PIXFLOW_THEME_DIR . '/'. $js_path);
                $pixflow_loaded_dependency[] = $js_path ;
                $pixflow_loaded_plugins[] = $js_path ;
            }
        }
    }
    return $scripts;
}

/**
 * load dependet styles of shortcodes and widgets
 * @param array list of depentens plugins and shortcodes
 * @return string as dependent styles
*/
function pixflow_load_dependent_styles($require_plugins){
    global $pixflow_loaded_plugins;
    $styles = '';
    $pixflow_loaded_dependency = array();
    // Load dependent plugin styles
    if (count($require_plugins['css']) != 0 &&
        ( is_array( $require_plugins['css'] ) || is_object( $require_plugins['css'] ) ) ) {
        foreach ($require_plugins['css'] as $css_path) {
            if(file_exists(PIXFLOW_THEME_DIR . '/'. $css_path) &&
                array_search( $css_path , $pixflow_loaded_dependency , true ) == false ) {
                if(in_array($css_path,$pixflow_loaded_plugins)){
                    continue;
                }
                $styles .= @file_get_contents(PIXFLOW_THEME_DIR . '/'. $css_path);
                $pixflow_loaded_dependency[] = $css_path;
                $pixflow_loaded_plugins[] = $css_path;
            }
        }
    }
    return $styles;
}

function pixflow_get_style_script($atts, $content = null, $shortcodename = '')
{
    global $cssString;
    $shortCode_deny = array(
        'master_slider' => 'pixflow_sc_masterslider' ,
        'row' => 'mBuilder_vcrow' ,
        'col' => 'mBuilder_vccolumn'
    );
    if (preg_match('/vc_/', $shortcodename)) {
        if ($shortcodename == 'vc_column_inner') {
            $funcName = 'mBuilder_vccolumn';
        } else {
            $funcName = str_replace('vc_', 'mBuilder_vc', $shortcodename);
        }
    } else {
        $funcName = str_replace('md', 'pixflow_sc', $shortcodename);
    }
    if(function_exists($funcName)){
        $output = call_user_func_array($funcName, array($atts, $content));
        // Output shortcode attributes if row dropped as section
        if ( isset( $_POST['attrs'] ) && strpos( $_POST['attrs'], 'section_id' ) ) {
            $attributes = '';
            foreach( $atts as $k => $v ) {
                $attributes .= "$k=\"$v\" ";
            }
            $output .= '<span class="section-shortcode-attrs">'.$attributes.'</span>';
        }
        // Minify Scripts and Styles
        $output = pixflow_minify_shortcodes_scripts($output);

        if (is_customize_preview() == false && (!defined('DOING_AJAX') || !DOING_AJAX)) {
            if(array_search($funcName , $shortCode_deny) == FALSE ){
                /*
                pixflow_import_media(pixflow_get_inline_scripts($output));
                return pixflow_output_validation($output);
                */
                return $output;
            }else{
                return $output;
            }
        } else {
            return $output;
        }
    }else{
        return ;
    }
}

// Load Require Plugin
function pixflow_load_dependency($name,$type = 'shortcode'){
    global $pixflow_loaded_shortcodes;
    global $pixflow_loaded_plugins;

    // Load dependency array
    $require_plugins = pixflow_load_dependecy_file($name,$type);
    $return = array(
        'js' => '' ,
        'css' => ''
    );

    // Load dependent Shortcodes
    if(isset($require_plugins['shortcode'])){
        foreach($require_plugins['shortcode'] as $dependent_shortcodes){
            if(in_array($dependent_shortcodes,$pixflow_loaded_shortcodes)){
                continue;
            }
            $shortcodes_files = pixflow_load_dependency($dependent_shortcodes,'shortcode');
            $return['js'] .= $shortcodes_files['js'];
            $return['css'] .= $shortcodes_files['css'];
            $return['js'] .= @file_get_contents(PIXFLOW_THEME_SHORTCODES . '/' . $dependent_shortcodes . '/script.min.js');
            $return['css'] .= @file_get_contents(PIXFLOW_THEME_SHORTCODES. '/' . $dependent_shortcodes . '/style.min.css');
            $shortcode_index_file = PIXFLOW_THEME_SHORTCODES . '/'. $dependent_shortcodes . '/index.php';
            if(file_exists($shortcode_index_file)) {
                require_once $shortcode_index_file;
            }
            $pixflow_loaded_shortcodes[] = $dependent_shortcodes;
        }
    }

    // Load dependent scripts
    $return['js'] .= pixflow_load_dependent_scripts($require_plugins);

    // Load dependent styles
    $return['css'] .= pixflow_load_dependent_styles($require_plugins);

    return $return;
}

/*
 * load required shortcodes that used do_shortcode
 * @param array list of dependents shortcodes
*/
function pixflow_load_do_shortcodes(){
    $do_shortcodes = array();
    // load video shortcode for loop-blog-video
    if ( (is_front_page() && is_home()) || (!is_front_page() && is_home()) || is_archive() ) {
        $do_shortcodes[] = 'md_video';
    }
    // load subscribe shortcode on single blog and sbscribe widget
    if (is_singular('post') || is_active_widget( '', '', 'md_subscribe_widgett')) {
        $do_shortcodes[] = 'md_subscribe';
    }
    return $do_shortcodes;
}

function pixflow_rename_shortcode($value){
    return trim(str_replace('/index' , '' , $value));
}