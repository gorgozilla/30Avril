<?php
/**
 * Live Text Editor
 *
 * @author Pixflow
 */

add_shortcode('md_live_text', 'pixflow_get_style_script'); // pixflow_sc_live_text

function pixflow_sc_live_text( $atts, $content = null ){
    ob_start();
    extract( shortcode_atts( array(
        'meditor_letter_spacing'       => '0' ,
        'meditor_line_height'          => '1.2',
        'align'     => 'center',
    ), $atts ) );

    $animation = array();
    $animation = pixflow_shortcodeAnimation('md_live_text',$atts);
    $id = pixflow_sc_id('livetext');
    $content = base64_decode($content);
    $content = pixflow_convert_font_to_span($content);
    $font_load = '' ;
    $font_list_result = pixflow_extract_font_families( $content ) ;

    if ( $font_list_result !== false ) {
        foreach ($font_list_result as $font){
            if( strpos( $font_load , $font) === false){
                $font_load .= $font. '|' ;
            }
        }
        pixflow_merge_fonts( $font_load);
    }
    $align = trim($align);
    ?>
    <div class="md-live-text gizmo-container small-gizmo <?php echo esc_attr($id.' '.$animation['has-animation'].' md-align-'.$align) ?>" <?php echo esc_attr($animation['animation-attrs']); ?>>
        <div class="live-text-container" >
            <div class="meditor inline-md-editor" style="<?php echo 'line-height:' . $meditor_line_height . 'em;letter-spacing:'
                . $meditor_letter_spacing . 'px'; ?>" data-letterspace="<?php echo $meditor_letter_spacing; ?>" data-lineheight="<?php echo $meditor_line_height; ?>">
                <div>
                   <?php echo ($content != '' ) ? pixflow_js_remove_wpautop($content) : '<p>click here to edit</p>';?>
                </div>
            </div>
        </div>
    </div>
<?php
    pixflow_callAnimation(true,$animation['animation-type'],'.'.$id);
    return ob_get_clean();
}
