<?php
/**
 * mBuilder provides some functionality for editing shortcodes in customizer.
 *
 * mBuilder is a visual editor for shortcodes and makes working with shortcodes more easier and fun.
 * It is added as a part of Massive Dynamic since V3.0.0 and designed to work with customizer. Enjoy Editing ;)
 *
 * @author  PixFlow
 *
 */

$fonts_list = '[{"font_family":"Roboto","font_styles":"regular,100,100italic,300,300italic,italic,500,500italic,700,700italic,900,900italic","font_types":"400 regular:400:normal,100 light regular:100:normal,100 light italic:100:italic,300 light regular:300:normal,300 light italic:300:italic,400 italic:400:italic,500 bold regular:500:normal,500 bold italic:500:italic,700 bold regular:700:normal,700 bold italic:700:italic,900 bold regular:900:normal,900 bold italic:900:italic"},{"font_family":"Open Sans",	"font_styles":"300,300italic,regular,italic,600,600italic,700,700italic,800,800italic",	"font_types":"300 light regular:300:normal,300 light italic:300:italic,400 regular:400:normal,400 italic:400:italic,600 bold regular:600:normal,600 bold italic:600:italic,700 bold regular:700:normal,700 bold italic:700:italic,800 bold regular:800:normal,800 bold italic:800:italic"}	,{"font_family":"Open Sans Condensed","font_styles":"300,300italic,700","font_types":"300 light regular:300:normal,300 light italic:300:italic,700 bold regular:700:normal"},{"font_family":"Orbitron","font_styles":"regular,500,700,900","font_types":"400 regular:400:normal,500 bold regular:500:normal,700 bold regular:700:normal,900 bold regular:900:normal"},{"font_family":"Oswald","font_styles":"300,regular,700","font_types":"300 light regular:300:normal,400 regular:400:normal,700 bold regular:700:normal"},{"font_family":"Oxygen","font_styles":"300,regular,700","font_types":"300 light regular:300:normal,400 regular:400:normal,700 bold regular:700:normal"},{"font_family":"PT Sans","font_styles":"regular,italic,700,700italic","font_types":"400 regular:400:normal,400 italic:400:italic,700 bold regular:700:normal,700 bold italic:700:italic"},{"font_family":"PT Serif","font_styles":"regular,italic,700,700italic","font_types":"400 regular:400:normal,400 italic:400:italic,700 bold regular:700:normal,700 bold italic:700:italic"},{"font_family":"Pacifico","font_styles":"regular","font_types":"400 regular:400:normal"},{"font_family":"Permanent Marker","font_styles":"regular","font_types":"400 regular:400:normal"},{"font_family":"Philosopher","font_styles":"regular,italic,700,700italic","font_types":"400 regular:400:normal,400 italic:400:italic,700 bold regular:700:normal,700 bold italic:700:italic"},{"font_family":"Playfair Display","font_styles":"regular,italic,700,700italic,900,900italic","font_types":"400 regular:400:normal,400 italic:400:italic,700 bold regular:700:normal,700 bold italic:700:italic,900 bold regular:900:normal,900 bold italic:900:italic"},{"font_family":"Poppins","font_styles":"300,regular,500,600,700","font_types":"300 light regular:300:normal,400 regular:400:normal,500 bold regular:500:normal,600 bold regular:600:normal,700 bold regular:700:normal"},{"font_family":"Radley","font_styles":"regular,italic","font_types":"400 regular:400:normal,400 italic:400:italic"},{"font_family":"Raleway","font_styles":"100,200,300,regular,500,600,700,800,900","font_types":"100 light regular:100:normal,200 light regular:200:normal,300 light regular:300:normal,400 regular:400:normal,500 bold regular:500:normal,600 bold regular:600:normal,700 bold regular:700:normal,800 bold regular:800:normal,900 bold regular:900:normal"},{"font_family":"Roboto Condensed","font_styles":"300,300italic,regular,italic,700,700italic","font_types":"300 light regular:300:normal,300 light italic:300:italic,400 regular:400:normal,400 italic:400:italic,700 bold regular:700:normal,700 bold italic:700:italic"},{"font_family":"Roboto Slab","font_styles":"100,300,regular,700","font_types":"100 light regular:100:normal,300 light regular:300:normal,400 regular:400:normal,700 bold regular:700:normal"},{"font_family":"Satisfy","font_styles":"regular","font_types":"400 regular:400:normal"},{"font_family":"Signika","font_styles":"300,regular,600,700","font_types":"300 light regular:300:normal,400 regular:400:normal,600 bold regular:600:normal,700 bold regular:700:normal"},{"font_family":"Source Code Pro","font_styles":"200,300,regular,500,600,700,900","font_types":"200 light regular:200:normal,300 light regular:300:normal,400 regular:400:normal,500 bold regular:500:normal,600 bold regular:600:normal,700 bold regular:700:normal,900 bold regular:900:normal"},{"font_family":"Ubuntu","font_styles":"300,300italic,regular,italic,500,500italic,700,700italic","font_types":"300 light regular:300:normal,300 light italic:300:italic,400 regular:400:normal,400 italic:400:italic,500 bold regular:500:normal,500 bold italic:500:italic,700 bold regular:700:normal,700 bold italic:700:italic"},{"font_family":"Ubuntu Mono","font_styles":"regular,italic,700,700italic","font_types":"400 regular:400:normal,400 italic:400:italic,700 bold regular:700:normal,700 bold italic:700:italic"},{"font_family":"Vollkorn","font_styles":"regular,italic,700,700italic","font_types":"400 regular:400:normal,400 italic:400:italic,700 bold regular:700:normal,700 bold italic:700:italic"},{"font_family":"Montserrat","font_styles":"regular,700","font_types":"400 regular:400:normal,700 bold regular:700:normal"},{"font_family":"Ubuntu",	"font_styles":"300,300italic,regular,italic,500,500italic,700,700italic", "font_types":"300 light regular:300:normal,300 light italic:300:italic,400 regular:400:normal,400 italic:400:italic,500 bold regular:500:normal,500 bold italic:500:italic,700 bold regular:700:normal,700 bold italic:700:italic"}]';
$fonts = json_decode( $fonts_list );

$mBuilderShortcodes = array();
$mBuilderInCustomizer = false;
$mBuilderExternalTypes = array();

/**
 * @version 1.0.0
 */
class MBuilder{

    /**
     * @var MBuilder - The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * @var array - models of each shortcode
     */
    public $models;

    /**
     * @var string - content of shortcodes
     */
    public $content = '';

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return MBuilder - The *Singleton* instance.
     * @since 1.0.0
     */
    public static function getInstance(){
        if (null === MBuilder::$instance) {
            MBuilder::$instance = new MBuilder();
        }

        return MBuilder::$instance;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     * @since 1.0.0
     */
    private function __clone(){}

    /**
     * Private unserialize method to prevent unserializing of the *Singleton* instance.
     *
     * @return void
     * @since 1.0.0
     */
    private function __wakeup(){}

    /**
     * MBuilder constructor.
     */
    protected function __construct(){
        global $mBuilderShortcodes;
        if(is_customize_preview()) {
            do_action('mBuilder_before_init');

            // Enqueue required assets
            wp_enqueue_script('tinyMce', PIXFLOW_THEME_LIB_URI . '/mbuilder/assets/js/tinymce.min.js',array(),PIXFLOW_THEME_VERSION);
            wp_enqueue_script('mBuilder', PIXFLOW_THEME_LIB_URI . '/mbuilder/assets/js/mbuilder.js',array(),PIXFLOW_THEME_VERSION,true);
            wp_enqueue_style('gizmo', pixflow_path_combine(PIXFLOW_THEME_LIB_URI . '/assets/css/', 'vc-gizmo.css'), array(), PIXFLOW_THEME_VERSION);
            wp_enqueue_style('mBuilder-gizmo', pixflow_path_combine(PIXFLOW_THEME_LIB_URI . '/mbuilder/assets/css/', 'mbuilder.css'), array(), PIXFLOW_THEME_VERSION);


            $mBuilderValues = array(
                'ajax_url'    => admin_url('admin-ajax.php'),
                'ajax_nonce'  => wp_create_nonce('ajax-nonce'),
                'deleteText'  => __('Delete','massive-dynamic'),
                'duplicateText'  => __('Duplicate','massive-dynamic'),
                'columnText'  => __('Column Setting','massive-dynamic'),
                'rowText'     => __('Row','massive-dynamic'),
                'layoutText'  => __('Layout','massive-dynamic'),
                'customColText'  => __('Custom Column','massive-dynamic'),
                'deleteDescText' => __('Are you sure ?','massive-dynamic'),
                'settingText'    => __('Setting','massive-dynamic'),
                'leaveMsg' => esc_attr__('You are about to leave this page and you haven\'t saved changes yet, would you like to save changes before leaving?','massive-dynamic'),
                'unsaved' => esc_attr__('Unsaved Changes!','massive-dynamic'),
                'save_leave' => esc_attr__('Save & Leave','massive-dynamic'),
            );
            wp_localize_script('mBuilder', 'mBuilderValues', $mBuilderValues);
            wp_enqueue_style('admin',pixflow_path_combine(PIXFLOW_THEME_LIB_URI,'/assets/css/admin.css'),false,PIXFLOW_THEME_VERSION);





            do_action('mBuilder_shortcodes_init');

            foreach($mBuilderShortcodes as $key => $value){
                unset($value['params']);
                $mBuilderShortcode[$key] = $value;
            }

            wp_localize_script('mBuilder', 'mBuilderShortcodes', $mBuilderShortcode);
        }

    }

    /**
     * Build setting panel form and inputs to edit shortcodes visually
     *
     * @return void
     * @since 1.0.0
     */
    public static function buildForm($params,$atts = array(),$content=null){
        if(isset($atts['md_text_use_title_slider']) || in_array('md_text_use_title_slider',$atts)){
            if((isset($atts['md_text_number']) && $atts['md_text_number']<2) || !isset($atts['md_text_number'])){
                $atts['md_text_use_title_slider'] = '';
            }
        }
        $innerContent = $content;
        global $mBuilderExternalTypes;
        foreach($params as $param){
            if($param['group'] == '') $param['group'] = esc_attr__( "General",  'mBuilder');
            $form[$param['group']][] = $param;
            extract( shortcode_atts( array(
                $param['param_name']  => $param['value']
            ),$atts ));
        }
        if(isset($atts['css']) && $atts['css'] != ''){
            $css = $atts['css'];
            $r = preg_match ('/.*?{(.*?)}.*?/is', $css,$matches);
            if(is_array($matches) && isset($matches[1])){
                $css = $matches[1];
            }else{
                $css = '';
            }
            $css = str_replace('``','\'',$css);
            $pat = '~[!]important~s';
            $css = trim(preg_replace($pat,'', $css));
            $pat = '~px~s';
            $css = trim(preg_replace($pat,'', $css));
            $css = explode(';',$css);
            $final_css = array();
            foreach($css as $prop){
                if($prop == ''){
                    continue;
                }
                $property = explode(':',$prop);
                $final_css[str_replace('-','_',trim($property[0]))]=trim($property[1]);
            }
        }
        if(count($form[esc_attr__( "General",  'mBuilder')])) {
            $generalTab = $form[esc_attr__("General", 'mBuilder')];
            unset($form[esc_attr__("General", 'mBuilder')]);
            $form = array(esc_attr__("General", 'mBuilder') => $generalTab) + $form;
            $content = $innerContent;
        }
        $groupHtml = array();
        echo '<div id="mBuilderTabs">';
        echo "<ul>";
        foreach($form as $key=>$group){
            echo '<li><a href="#mBuilder'.str_replace(' ','',$key).'">'.$key.'</a></li>';
            foreach($group as $k=>$param){
                $dependency = '';
                if(isset($param['dependency'])){
                    $dependency = "data-mBuilder-dependency='".json_encode($param['dependency'])."'";
                }
                if(isset($param['mb_dependency'])){
                    $dependency = "data-mBuilder-dependency='".json_encode($param['mb_dependency'])."'";
                }
                $groupHtml[$key] .='<div class="vc_col-sm-12 vc_column '.$param['type'].' ' . $param['edit_field_class'] . '" '.$dependency.' >';
                if(isset($atts['css']) && array_key_exists($param['param_name'],$final_css) && ${$param['param_name']}==''){
                    ${$param['param_name']} = $final_css[$param['param_name']];
                }
                if($param['param_name'] == 'content'){
                    ${$param['param_name']} = $innerContent;
                }
                if(count($mBuilderExternalTypes[$param['type']])){
                    $groupHtml[$key] .='<div class="mBuilder_element_label">' . $param['heading'] . '</div><div class="edit_form_line">' ;
                    $groupHtml[$key] .= call_user_func_array ($mBuilderExternalTypes[$param['type']]['callback'],array($param,${$param['param_name']}));
                    $groupHtml[$key] .='</div>';
                    $groupJs[] = $mBuilderExternalTypes[$param['type']]['requiredjs'];
                }else {
                    switch ($param['type']) {
                        case 'textfield':
                            $groupHtml[$key] .=
                                '<div class="mBuilder_element_label">' . $param['heading'] . '</div>' .
                                '<div class="edit_form_line"><input type="text" class="simple-textbox wpb_vc_param_value wpb-textinput" value="' . ${$param['param_name']} . '" name="' . $param['param_name'] . '"></div>';
                            break;
                        case 'hidden':
                            $groupHtml[$key] .=
                                '<input type="hidden" class="wpb_vc_param_value wpb-textinput" value="' . ${$param['param_name']} . '" name="' . $param['param_name'] . '">';
                            break;
                        case 'textarea_html':
                            $groupHtml[$key] .=
                                '<div class="edit_form_line"><textarea name="' . $param['param_name'] . '">' . stripslashes($content) . '</textarea></div>';
                            break;
                        case 'textarea':
                            $groupHtml[$key] .=
                                '<div class="mBuilder_element_label">' . $param['heading'] . '</div>' .
                                '<div class="edit_form_line"><textarea name="' . $param['param_name'] . '">' . ${$param['param_name']} . '</textarea></div>';
                            break;
                        case 'separator':
                            $groupHtml[$key] .=
                                '<div class="edit_form_line"><hr></div>';
                            break;
                        case 'dropdown':
                            $options = '';
                            foreach ($param['value'] as $optkey => $optValue) {
                                $select = ($optValue == ${$param['param_name']}) ? 'selected="selected"' : '';
                                $options .= '<option value="' . $optValue . '" ' . $select . '>' . $optkey . '</option>';
                            }
                            $groupHtml[$key] .=
                                '<div class="mBuilder_element_label">' . $param['heading'] . '</div>' .
                                '<div class="edit_form_line"><select name="' . $param['param_name'] . '">' .
                                $options .
                                '</select></div>';
                            break;
                        case 'attach_image':
                            $image_id = (int)${$param['param_name']};
                            $placeholder = '';
                            if($image_id != '' && is_int($image_id) && $image_id != 0){
                                $style = 'background-image: url('.wp_get_attachment_url( $image_id ).')';
                                $placeholder .= '<div data-id="'.$image_id.'" class="mBuilder-upload-img single has-img" style="'.$style.'"><span class="remove-img">X</span>';
                            }else{
                                $placeholder = '<div class="mBuilder-upload-img single"><span class="remove-img mBuilder-hidden">X</span>';
                            }
                            $groupHtml[$key] .=
                                '<div class="mBuilder_element_label">' . $param['heading'] . '</div>' .
                                '<div class="edit_form_line">'.
                                    $placeholder.
                                    '<input type="text" name="'.$param['param_name'].'" value="'.${$param['param_name']}.'"></div>'.
                                '</div>';
                            break;
                        case 'attach_images':
                            $images_id = (${$param['param_name']}!='')?explode(',',${$param['param_name']}):array();
                            $placeholder = '';
                            if(count($images_id)){
                                foreach($images_id as $id){
                                    $style = 'background-image: url('.wp_get_attachment_url( $id ).')';
                                    $placeholder .= '<div data-id="'.$id.'" class="mBuilder-upload-img multi has-img" style="'.$style.'"><span class="remove-img">X</span></div>';
                                }
                                $placeholder .= '<div class="mBuilder-upload-img multi"><span class="remove-img mBuilder-hidden">X</span></div>';
                            }else{
                                $placeholder = '<div class="mBuilder-upload-img multi"><span class="remove-img mBuilder-hidden">X</span></div>';
                            }

                            $groupHtml[$key] .=
                                '<div class="mBuilder_element_label">' . $param['heading'] . '</div>' .
                                '<div class="edit_form_line  mBuilder-upload-imgs">'.
                                $placeholder.
                                '<input type="text" name="'.$param['param_name'].'" value="'.${$param['param_name']}.'" class="mBuilder-hidden">'.
                                '</div>';
                            break;
                        case 'google_fonts':
                            $inputValue = ${$param['param_name']};
                            $value = urldecode(${$param['param_name']});
                            $value = str_replace("font_family:", "", $value);
                            $value = str_replace("font_style:", "", $value);
                            $fontFamily = explode(':',$value);
                            $fontFamily = $fontFamily[0];
                            $fontStyle = explode('|',$value);
                            $fontStyle = $fontStyle[1];
                            $value = array('font_family'=>$fontFamily,'font_style'=>$fontStyle);
                            global $fonts;
                            $fontKey = 0;
                            $options = '';

                            foreach ( $fonts as $fKey=>$font_data ) {
                                $select='';
                                if( strtolower( $value['font_family'] ) == strtolower( $font_data->font_family )){
                                    $fontKey=$fKey;
                                    $select = 'selected="selected"';
                                }
                                $options .=
                                    '<option data-font-id="'.$fKey.'" value="'.$font_data->font_family . ':' . $font_data->font_styles.'" data-font="'.$font_data->font_family.'" '.$select.'>'.$font_data->font_family.'</option>';
                            }
                            $fontStyleOptions = pixflow_loadfontStyles($fontKey,$value['font_style']);
                            $groupHtml[$key] .=
                                '<div class="mBuilder_element_label">Font</div>' .
                                '<div class="edit_form_line  mBuilder-google-font-picker">'.
                                '<select class="google-fonts-families" data-input="'.$param['param_name'].'">'.
                                $options.
                                '</select>'.
                                '<select class="google-fonts-styles" data-input="'.$param['param_name'].'">'.
                                $fontStyleOptions.
                                '</select>'.
                                '<input type="text" name="'.$param['param_name'].'" value="'.$inputValue.'" class="mBuilder-hidden"/>'.
                                '</div>';
                            break;
                        default:
                            $groupHtml[$key] .= '[Unknown controller]';
                            break;
                    }
                }
                $groupHtml[$key] .= '</div>';
            }
        }
        echo "</ul>";
        $groupJs = array_unique($groupJs);
        foreach ($groupHtml as $key=>$html){
            echo '<div id="mBuilder'.str_replace(' ','',$key).'" class="mBuilder-edit-el">'.$html."</div>";
        }
        $spectrum = PIXFLOW_THEME_CUSTOMIZER_URI.'/assets/js/spectrum.js';
        echo '<script src="'.$spectrum.'"></script>';
        $spectrumCSS = PIXFLOW_THEME_CUSTOMIZER_URI.'/assets/css/spectrum.css';
        echo '<link rel="stylesheet" href="'.$spectrumCSS.'">';

        $nouislider = PIXFLOW_THEME_CUSTOMIZER_URI.'/assets/js/jquery.nouislider.js';
        echo '<script src="'.$nouislider.'"></script>';
        $nouisliderCSS = PIXFLOW_THEME_CUSTOMIZER_URI.'/assets/css/jquery.nouislider.css';
        echo '<link rel="stylesheet" href="'.$nouisliderCSS.'">';

        foreach($groupJs as $value){
            echo '<script src="'.$value.'"></script>';
        }
        echo "</div>";

    }

    public static function parseAttributes($attributes){
        $attr = json_decode(stripslashes($attributes),true);

        if(!is_array($attr)){

            if($attr == null){
                $attr = stripslashes($attributes);
            }
            $attributes = array();
            if(preg_match('/^ *\[/s',$attr )) {
                if (!preg_match('/^\[[^\]]*? /s', $attr)) {
                    return $attributes;
                }
                $attr = preg_replace('/^\[[^\]]*? /s','' ,$attr );
            }
            while($attr) {
                $attr = trim($attr);
                if(preg_match('/^\].*/s',$attr )){
                    $attr = null;
                    break;
                }
                preg_match('/(?=[^\'"]*)[\'"]/s', $attr, $separator);

                if(isset($separator[0])){
                    if($separator[0] == '') {
                        echo $attr;
                        break;
                    }
                    $attrs = explode($separator[0], $attr, 2);
                    $key = $attrs[0];
                    if(preg_match('/^'.$separator[0].'/s',$attrs[1])){
                        $value = array();
                        $value[0] = '';
                        $value[1] = '';
                        $value[2] = substr($attrs[1],1);
                    }else{
                        $value = preg_split("/([^\\\])$separator[0]/s", $attrs[1], 2, PREG_SPLIT_DELIM_CAPTURE);
                    }
                    $key = str_replace('=', '', $key);
                    $attr = $value[2];
                    $value = $value[0].$value[1];
                    $value = str_replace('\"','"',$value);
                    $key = trim($key);
                    $attributes[$key] = $value;
                }
            }
            return $attributes;
        }
        return $attr;
    }

    public static function getModelAttribute($attributes,$attr){
        $attrs = MBuilder::parseAttributes($attributes);
        if(isset($attrs[$attr])){
            return $attrs[$attr];
        }else{
            return false;
        }
    }

    /**
     * Prepare content from models
     *
     * @param $models - shortcode models
     *
     * @return string - content of the page by shortcode tags
     * @since 1.0.0
     */
    public function getContent($models){
        $this->content = '';
        $this->models = json_decode(stripslashes($models),true);

        // Find childs
        foreach ($this->models as $id=>$model) {
            $current_id = $id;
            $this->models[$id]['flag'] = false;
            $this->models[$id]['id'] = $id;
            //find childes
            $childes = array();
            foreach ($this->models as $key2=>$model2) {
                $el = $model2;
                if(isset($el['parentId'])){
                    if($el['parentId'] == $current_id){
                        $childes[] = $key2;
                    }
                }
            }
            $orderedChildes = array();
            $o = 1;
            foreach($childes as $child){
                if(array_key_exists('order', $this->models[$child])){
                    if(isset($orderedChildes[$this->models[$child]['order']])){
                        $orderedChildes[++$this->models[$child]['order']] = $child;
                    }else{
                        $orderedChildes[$this->models[$child]['order']] = $child;
                    }
                }else{
                    $orderedChildes[$o++] = $child;
                }
            }
            ksort($orderedChildes);
            $this->models[$id]['childes'] = $orderedChildes;
        }
        $els = $this->models;
        $rows = array();

        foreach($this->models as $key=>$item){
            if($item['type'] == 'vc_row'){
                $rows[$key] = $item['order'];
                unset($this->models[$key]);
            }
        }
        arsort($rows);
        foreach($rows as $key=>$item){
            $this->models = array($key=>$els[$key])+$this->models;
        }
        foreach ($this->models as $id=>$model) {
            if($this->models[$id]['flag']){
                continue;
            }else{
                $this->models[$id]['flag'] = true;
            }
            $this->generateContent($id);
        }
        return $this->content;
    }

    /**
     * Save content of page/post to the database
     *
     * @param $id - post/page ID
     *
     * @return void
     * @since 1.0.0
     */
    public function saveContent($id){
        $current_item = array(
            'ID'           => $id,
            'post_content' => $this->content,
        );
        $post_id = wp_update_post( $current_item, true );
        if (is_wp_error($post_id)) {
            $errors = $post_id->get_error_messages();
            foreach ($errors as $error) {
                echo $error;
            }
        }else{
            echo 'updated';
        }
    }

    /**
     * replace shortcode models with wordpress shortcode pattern
     *
     * @param $id - Shortcode model ID
     *
     * @return void
     * @since 1.0.0
     */
    public function generateContent($id){
        $type = trim($this->models[$id]['type']);
        $attr = trim($this->models[$id]['attr']);


        $pat = '~el_id=".*?"~s';
        $attr = trim(preg_replace($pat,'', $attr));
        $childes = $this->models[$id]['childes'];
        $content = $this->models[$id]['content'];
        $attr = ($attr != '')?' '.$attr:$attr;
        $this->content .= '['.$type.$attr.']';
        if(count($childes)){
            foreach ($childes as $child) {
                if( $this->models[$child]['flag']){
                    continue;
                }else{
                    $this->models[$child]['flag'] = true;
                }
                $this->content .= $this->generateContent($child);
            }
        }
        if($content != ''){
            $this->content .= $content;
        }
        $this->content .='[/'.$type.']';
    }

    /**
     * A filter on the_content if mBuilder is loaded to change normal texts to the Text Shortcode
     *
     * @since 1.0.0
     */
    public function textToShortcode($content){
        $content = shortcode_unautop( trim( $content ) );
        $not_shortcodes = preg_split( '/' . get_shortcode_regex(). '/', $content );
        foreach ( $not_shortcodes as $string ) {
            $temp = str_replace( array('<p>','</p>'), '', $string );
            if ( strlen( trim( $temp ) ) > 0 ) {
                $content = preg_replace( '/(' . preg_quote( $string, '/' ) . '(?!\[\/))/', '[vc_row][vc_column][md_text]$1[/md_text][/vc_column][/vc_row]', $content );
            }
        }

        return $content;
    }

}

/**
 * Add visual composer classes to the editor
 *
 * @param $classes - classes of the body
 *
 * @return string - new classes for the body
 * @since 1.0.0
 */
function addBodyClasses($classes){
    if (is_customize_preview()) {
        $classes[] = 'compose-mode';
        $classes[] = 'vc_editor';
    }
    return $classes;
}
add_filter('body_class', 'addBodyClasses');

/**
 * Massive Dynamic Starts using mBuilder as its default builder
 *
 * @param $content
 * @return string
 */
function pixflow_mBuilder($content){
    $mBuilder = MBuilder::getInstance();

    // Skip load Builder if its not in customizer
    if(!is_customize_preview()) {
        return $content;
    }
    // Skip load Builder if its blog or single portfolio template page
    if ( true == is_home() || (true == is_singular( 'portfolio' ) && 'standard' == pixflow_metabox('portfolio_options.template_type','standard')) ) {
        return $content;
    }
    // Skip load Builder if its shop page
    if(function_exists('is_shop')){
        if(is_product() && !(is_shop() || is_product_category())){
            return $content;
        }
    }


    if(!strpos($content,'[md_blog')){
        $content = $mBuilder->textToShortcode($content);
    }


    do_action('mBuilder_before_render');

    return $content;
}
add_filter('the_content','pixflow_mBuilder');
/**
 * Add visual basic shortcodes to mBuilder
 *
 *
 * @return void
 * @since 1.0.0
 */
function mBuilderPrerequisits(){
    add_shortcode("vc_row",'mBuilder_vcRow');
    add_shortcode("vc_row_inner",'mBuilder_vcInnerRow');
    add_shortcode("vc_column",'mBuilder_vcColumn');
    add_shortcode("vc_column_inner",'mBuilder_vcColumn');
    require_once(PIXFLOW_THEME_LIB.'/mbuilder/includes/visualcomposer-functions.php');
}
require_once(PIXFLOW_THEME_LIB.'/mbuilder/includes/visualcomposer-compatibilities.php');
require_once(PIXFLOW_THEME_LIB.'/mbuilder/includes/ajax-actions.php');

//////////////////////
add_action('init', 'mBuilderPrerequisits', 998);

function pixflow_tinymce_config( $init ) {
    $init['wpautop'] = false;
    $init['cleanup'] = false;
    $init['forced_root_block'] = false;
    $init['force_br_newlines'] = true;
    $init['remove_linebreaks'] = false;
    $init['convert_newlines_to_brs'] = false;
    $init['remove_redundant_brs'] = false;
    return $init;
}
add_filter('tiny_mce_before_init', 'pixflow_tinymce_config');

/**
 * Late load bootstrap styles to override visualcomposer styles.
 *
 * @since 1.0.0
 */
function mbuilderLateLoadStyles(){
    wp_enqueue_style('bootstrap-style',pixflow_path_combine(PIXFLOW_THEME_CSS_URI,'bootstrap.css'),array(),PIXFLOW_THEME_VERSION);
}
add_action('get_footer','mbuilderLateLoadStyles',999);
