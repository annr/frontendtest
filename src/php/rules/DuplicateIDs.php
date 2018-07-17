<?php
function DuplicateIDs() {
    $sug = (object) [
        'title' => 'Duplicate ID attribute(s) found',
        'description' => 'IDs must be unique in an HTML document. You should correct the following duplicate ID(s): ',
        'weight' => 30,
        'category' => ['content'],
    ];

    global $poorly_designed_catchall;	   
    global $poorly_designed_catchall_element_array;
    $code[0] = [];
    $code[1] = 0;
    $code[2] = '';
    $poorly_designed_catchall = array();		
    $poorly_designed_catchall_element_array = array();	
        
    global $_ft_dom_;		
    $elements = $_ft_dom_->getElementsByTagName('html');
    
    foreach($elements as $element) {
        recursivelyGetDuplicateAttributeValue($element,'id');
    }
            
    if(!empty($poorly_designed_catchall_element_array)) { 
        foreach($poorly_designed_catchall_element_array as $element) {
            $code[0][] = $element;
            $code[1]++;
        }
        if(count($poorly_designed_catchall_element_array) > 1) { $code[2] = 's'; } else { $code[1] = ''; }
    }
    
    if (count($code[0]) > 0) {
        // custom string generation for this rule: 
        //   Put duplicate IDs in a comma-delineated string
        $ids = '';
        foreach($code[0] as $id) {
            $ids .= $id . ', ';
        }
        // trim the last comma
        $ids = trim($ids, ', ');
        $sug->description .= $ids;
        return $sug;
    }
    return false;
}



?>