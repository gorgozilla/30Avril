<?php
/**
 * mBuilder AJAX functions
 *
 * @author PixFlow
 */
if (defined('DOING_AJAX') && DOING_AJAX) {

    /**
     * Setting Panel Generator
     *
     * @since 1.0.0
     */
    function mBuilder_settingPanel(){
        global $mBuilderShortcodes;
        do_action('mBuilder_shortcodes_init');
        $type = $_POST['type'];
        $content = $_POST['content'];
        $attrs = MBuilder::parseAttributes($_POST['attr']);
        if($content == '' && trim($_POST['attr']) == ''){
            $content = $mBuilderShortcodes[$type]['default_content'];
        }
        // Check Text shortcode and add multi title attr to it
        if('md_text' == $type && (isset($attrs['md_text_number']) && $attrs['md_text_number']>1)){
            $attrs[] = 'md_text_use_title_slider="yes';
            $attrs['md_text_use_title_slider'] = 'yes';
        }

        echo '<form id="mBuilder-form" onsubmit="return false;">';
        MBuilder::buildForm($mBuilderShortcodes[$type]['params'],$attrs,$content);
        echo '</form>';
        wp_die();

    }
    add_action( 'wp_ajax_mBuilder_settingPanel', 'mBuilder_settingPanel');
    add_action( 'wp_ajax_nopriv_mBuilder_settingPanel', 'mBuilder_settingPanel');

    /**
     * execute shortcode and build it then send it to the builder
     *
     * @since 1.0.0
     */
    function mBuilder_buildShortcode(){
        global $shortcode_tags,$mBuilderShortcodes,$mBuilderInCustomizer;
        do_action('mBuilder_shortcodes_init');
        $mBuilderInCustomizer = true;
        $action = $_POST['act'];
        $shortcode = $_POST['shortcode'];
        $attrs = MBuilder::parseAttributes($_POST['attrs']);
//        $attrs = array_map(function($value){
//            return str_replace('"',"``",$value);
//        }, $attrs);

        if($action == 'build'){
            $mBuilderShortcodes['vc_row']['default_content'] = '[vc_column][/vc_column]';
            $mBuilderShortcodes['vc_row_inner']['default_content'] = '[vc_column_inner][/vc_column_inner]';
            if($_POST['shortcode'] != 'vc_row') {
                echo "<div class='mBuilder-element mBuilder-" . $shortcode . " vc_".$shortcode."' data-mBuilder-el='" . $shortcode . "'>";
            }

            // Get enqueued scripts and styles before run shortcode
            $beforeScripts = $beforeStyles = $afterScripts = $afterStyles = array();
            global $wp_scripts, $wp_styles;
            $scripts = $wp_scripts->queue;
            $styles = $wp_styles->queue;
            foreach($scripts as $handle){
                $beforeScripts[]=$wp_scripts->registered[$handle]->src;
            }
            foreach($styles as $handle){
                $beforeStyles[]=$wp_styles->registered[$handle]->src;
            }
            ob_start();
            echo call_user_func_array($shortcode_tags[ $_POST['shortcode'] ],array($attrs,$mBuilderShortcodes[$_POST['shortcode']]['default_content']));
            $output = ob_get_clean();
            // Get enqueued scripts and styles in shortcode and print them
            global $wp_scripts, $wp_styles;
            $scripts = $wp_scripts->queue;
            $styles = $wp_styles->queue;
            foreach($scripts as $handle){
                $afterScripts[]=$wp_scripts->registered[$handle]->src;
            }
            $shortcodeScripts = array_diff($afterScripts, $beforeScripts);
            foreach($shortcodeScripts as $script){
                $script = (substr($script,0,'4')=='/wp-')?get_site_url().$script:$script;
                echo '<script src="'.$script.'"></script>';
            }
            foreach($styles as $handle){
                $afterStyles[]=$wp_styles->registered[$handle]->src;
            }
            $shortcodeStyles = array_diff($afterStyles, $beforeStyles);
            foreach($shortcodeStyles as $style){
                $style = (substr($style,0,'4')=='/wp-')?get_site_url().$style:$style;
                echo "<link rel='stylesheet' href='".$style."' type='text/css' media='all'/>";
            }
            echo $output;
            if($_POST['shortcode'] != 'vc_row') {
                echo "</div>";
            }
        }elseif($action == 'rebuild'){
            $id = $_POST['id'];
            $content = stripslashes($_POST['content']);
            // Get enqueued scripts and styles before run shortcode
            $beforeScripts = $beforeStyles = $afterScripts = $afterStyles = array();
            global $wp_scripts, $wp_styles;
            $scripts = $wp_scripts->queue;
            $styles = $wp_styles->queue;
            foreach($scripts as $handle){
                $beforeScripts[]=$wp_scripts->registered[$handle]->src;
            }
            foreach($styles as $handle){
                $beforeStyles[]=$wp_styles->registered[$handle]->src;
            }
            if($_POST['shortcode'] != 'vc_row') {
                echo "<div class='mBuilder-element mBuilder-".$shortcode." vc_".$shortcode."' data-mBuilder-el='".$shortcode."' data-mbuilder-id='".$id."'>";
            }

            ob_start();
            $shortcode = call_user_func_array($shortcode_tags[ $shortcode ],array($attrs,$content));
            echo $shortcode;
            $output = ob_get_clean();
            // Get enqueued scripts and styles in shortcode and print them
            global $wp_scripts, $wp_styles;
            $scripts = $wp_scripts->queue;
            $styles = $wp_styles->queue;
            foreach($scripts as $handle){
                $afterScripts[]=$wp_scripts->registered[$handle]->src;
            }
            $shortcodeScripts = array_diff($afterScripts, $beforeScripts);
            foreach($shortcodeScripts as $script){
                $script = (substr($script,0,'4')=='/wp-')?get_site_url().$script:$script;
                echo '<script src="'.$script.'"></script>';
            }
            foreach($styles as $handle){
                $afterStyles[]=$wp_styles->registered[$handle]->src;
            }
            $shortcodeStyles = array_diff($afterStyles, $beforeStyles);
            foreach($shortcodeStyles as $style){
                $style = (substr($style,0,'4')=='/wp-')?get_site_url().$style:$style;
                echo "<link rel='stylesheet' href='".$style."' type='text/css' media='all'/>";
            }
            echo $output;
            if($_POST['shortcode'] != 'vc_row') {
                echo "</div>";
            }
        }
        wp_die();
    }
    add_action( 'wp_ajax_mBuilder_buildShortcode', 'mBuilder_buildShortcode');
    add_action( 'wp_ajax_nopriv_mBuilder_buildShortcode', 'mBuilder_buildShortcode');

    /**
     * do shortcode and build it then send it to the builder
     *
     * @since 1.0.0
     */
    function mBuilder_doShortcode(){
        global $shortcode_tags,$mBuilderShortcodes,$mBuilderInCustomizer;
        do_action('mBuilder_shortcodes_init');
        $mBuilderInCustomizer = true;
        $shortcode = stripslashes($_POST['shortcode']);

        echo pixflow_js_remove_wpautop($shortcode);

        wp_die();
    }
    add_action( 'wp_ajax_mBuilder_doShortcode', 'mBuilder_doShortcode');
    add_action( 'wp_ajax_nopriv_mBuilder_doShortcode', 'mBuilder_doShortcode');

    /**
     * Get content from builder and return generated wordPress shortcodes
     *
     * @return void
     * @since 1.0.0
     */
    function mBuilder_getContent(){
        $models = $_POST['models'];
        $then = $_POST['then'];
        $builder = MBuilder::getInstance();
        $content = $builder->getContent($models);

        if($then == 'doSaveTemp'){
            $_POST['content'] = $content;
            pixflow_save_temp_vc_content();
        }
        echo $content;
        wp_die();
    }
    add_action( 'wp_ajax_mBuilder_getContent', 'mBuilder_getContent');
    add_action( 'wp_ajax_nopriv_mBuilder_getContent', 'mBuilder_getContent');

    /**
     * Get content from builder and generate wordpress shortcodes then save it to the database
     *
     * @return void
     * @since 1.0.0
     */
    function mBuilder_saveContent(){
        $models = $_POST['models'];
        $id = $_POST['id'];
        $builder = MBuilder::getInstance();
        $builder->getContent($models);
        $builder->saveContent($id);
        wp_die();
    }
    add_action( 'wp_ajax_mBuilder_saveContent', 'mBuilder_saveContent');
    add_action( 'wp_ajax_nopriv_mBuilder_saveContent', 'mBuilder_saveContent');

    /**
     * Google Font Styles DropDown Loader
     *
     * @param $key
     * @param $value
     *
     * @return void|string - options of select input
     * @since 1.0.0
     */
    function pixflow_loadFontStyles($key=0,$value=''){
        global $fonts;
        $fontKey = (isset($_POST['fontKey']))?$_POST['fontKey']:$key;
        $value = (isset($_POST['value']))?$_POST['value']:$value;
        $fontStyles = $fonts[$fontKey];
        $fontStyles = explode(',',$fontStyles->font_types);
        $options = '';
        foreach ( $fontStyles as $style ){
            $selected = (strtolower( $value ) == strtolower( $style )) ? 'selected="selected"' : '';
            $options .= '<option value="'.$style.'" '.$selected.'>';
            $title = explode(':',$style);
            $options .= $title[0].'</option>';
        }
        if(isset($_POST['fontKey'])){
            echo $options;
            wp_die();
        }else{
            return $options;
        }
    }
    add_action( 'wp_ajax_pixflow_loadFontStyles', 'pixflow_loadFontStyles');
    add_action( 'wp_ajax_nopriv_pixflow_loadFontStyles', 'pixflow_loadFontStyles');
}