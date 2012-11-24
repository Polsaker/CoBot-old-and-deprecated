<?php
/**
 * xml2array() will convert the given XML text to an array in the XML structure.
 * Link: http://www.bin-co.com/php/scripts/xml2array/
 */
function xml2array($contents, $get_attributes=1) {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        return array();
    }
    $parser = xml_parser_create();
    xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
    xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
    xml_parse_into_struct( $parser, $contents, $xml_values );
    xml_parser_free( $parser );

    if(!$xml_values) return;
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();
    $current = &$xml_array;

    foreach($xml_values as $data) {
        unset($attributes,$value);
        extract($data);
        $result = '';
        if($get_attributes) {
            $result = array();
            if(isset($value)) $result['value'] = $value;

            if(isset($attributes)) {
                foreach($attributes as $attr => $val) {
                    if($get_attributes == 1) $result['attr'][$attr] = $val; 
                }
            }
        } elseif(isset($value)) {
            $result = $value;
        }
        if($type == "open") {
            $parent[$level-1] = &$current;
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) {
                $current[$tag] = $result;
                $current = &$current[$tag];

            } else { 
                if(isset($current[$tag][0])) {
                    array_push($current[$tag], $result);
                } else {
                    $current[$tag] = array($current[$tag],$result);
                }
                $last = count($current[$tag]) - 1;
                $current = &$current[$tag][$last];
            }
        } elseif($type == "complete") { 
            if(!isset($current[$tag])) { 
                $current[$tag] = $result;
            } else { 
                if((is_array($current[$tag]) and $get_attributes == 0)
                        or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
                    array_push($current[$tag],$result); 
                } else { 
                    $current[$tag] = array($current[$tag],$result);
                }
            }
        } elseif($type == 'close') {
            $current = &$parent[$level-1];
        }
    }
    return($xml_array);
} 
?>