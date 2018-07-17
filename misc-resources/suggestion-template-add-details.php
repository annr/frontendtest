<?php
function RuleName()
{
    // GLOBALS
    global $_ft_dom_element_body_;

    $sug = (object) [
        'title' => 'Title',
        'description' => 'Description',
        'weight' => 10,
        'category' => ['category'],
    ];

    // RULE CONFIG
    $max_disp_threshold = 4;

    // legacy system for templating
    $code = array('');
    $code[0] = [];
    $code[1] = 0; 
    $code[2] = '';

    // RULE CODE
    $elements = $_ft_dom_element_body_->getElementsByTagName('img');

    foreach ($elements as $element) { 	
        if (!$element->hasAttribute('alt')) {				
            $code[1]++;				
            if($code[1] <= $max_disp_threshold) { 
                $code[0][] = printCodeWithLineNumber($element); 
            }
        }		
    }

    // ADD DETAILS TO DESCRIPTION
    if(count($code[0]) > 0) {
        if($code[1] > 1) { $code[2] = 's'; }
        if($code[1] > $max_disp_threshold) { $code[0] .= '...'; }
    }

    if (count($code[0])) {
      $instances = '';
      foreach($code[0] as $instance) {
          $instances .= '<code>' . $instance . '</code><br>';
      }
      $sug->description .= $instances;
      return $sug;
    }

    return false;
}
?>
