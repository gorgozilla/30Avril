<?php
function pixflow_forbiddenStyle()
{
    global $wp_styles;
    // loop over all of the registered scripts
    foreach ($wp_styles->registered as $handle => $data) {
        // remove it
        wp_deregister_style($handle);
        wp_dequeue_style($handle);
    }
    wp_enqueue_style("robotoFont", "https://fonts.googleapis.com/css?family=Roboto");
    wp_enqueue_style("forbiddenStyles", PIXFLOW_THEME_CSS_URI . "/forbidden-styles.min.css");
}

/*
 * Compress CSS
 * */
function pixflow_minify_css($buffer) {
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $search = array('    ','   ','  ');
    $buffer = str_replace($search, ' ', $buffer);
    $search = array('    {','   {','  {',' {','{    ','{   ','{  ','{ ');
    $buffer = str_replace($search, '{', $buffer);
    $search = array('    }','   }','  }',' }','}    ','}   ','}  ','} ');
    $buffer = str_replace($search, '}', $buffer);
    $buffer = trim(str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer));
    return $buffer;
}

/*
 * Compress JS
 * */
function pixflow_minify_js($code){
    static $last = '';
    $code += array_fill(1, 5, null); // avoid E_NOTICE
    list(, $context, $regexp, $result, $word, $operator) = $code;
    if ($word != '') {
        $result = ($last == 'word' ? "\n" : ($last == 'return' ? " " : "")) . $result;
        $last = ($word == 'return' || $word == 'throw' || $word == 'break' ? 'return' : 'word');
    } elseif ($operator) {
        $result = ($last == $operator[0] ? "\n" : "") . $result;
        $last = $operator[0];
    } else {
        if ($regexp) {
            $result = $context . ($context == '/' ? "\n" : "") . $regexp;
        }
        $last = '';
    }

    return $result;
}

/*
 * Callback function for find and replace minify JS scripts
 * */
function pixflow_replace_js_minify($script){
    if(is_array($script) && isset($script[0])){
        if(defined('AUTOPTIMIZE_PLUGIN_DIR')){
            $replace = '<script>var $=jQuery;$(document).ready(function(){'. $script[1].'})</script>';
        }else{
            $replace = $script[0];
        }
        return trim(preg_replace_callback('(
            (?:
                (^|[-+\([{}=,:;!%^&*|?~]|/(?![/*])|return|throw) # context before regexp
                (?:\s|//[^\n]*+\n|/\*(?:[^*]|\*(?!/))*+\*/)* # optional space
                (/(?![/*])(?:
                    \\\\[^\n]
                    |[^[\n/\\\\]++
                    |\[(?:\\\\[^\n]|[^]])++
                )+/) # regexp
                |(^
                    |\'(?:\\\\.|[^\n\'\\\\])*\'
                    |"(?:\\\\.|[^\n"\\\\])*"
                    |([0-9A-Za-z_$]+)
                    |([-+]+)
                    |.
                )
            )(?:\s|//[^\n]*+\n|/\*(?:[^*]|\*(?!/))*+\*/)* # optional space
        )sx', 'pixflow_minify_js',$replace."\n"));
    }
    return '';
}

/*
 * Callback function for find and replace minify CSS styles
 * */
function pixflow_replace_css_minify($style){
    if(is_array($style) && isset($style[0])){
        $minify_style = trim(pixflow_minify_css($style[0]));
        return $minify_style;
    }
    return '';
}

/*
 * Minify all shortcode internal Scripts and Styles
 */
function pixflow_minify_shortcodes_scripts($content){
    $result = preg_replace_callback('#<\s*?script\b[^>]*>(.*?)</script\b[^>]*>#is','pixflow_replace_js_minify',$content);
    $content = (null != $result)?$result:$content;
    $result = preg_replace_callback('#<\s*?style\b[^>]*>(.*?)</style\b[^>]*>#is','pixflow_replace_css_minify',$content);
    $content = (null != $result)?$result:$content;
    return $content;
}
