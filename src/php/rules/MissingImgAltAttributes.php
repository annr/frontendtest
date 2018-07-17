<?php
function MissingImgAltAttributes()
{
    $sug = (object) [
        'title' => 'Missing img alt attribute(s)',
        'description' => 'The img alt attribute (Ex. <code>&lt;img alt="Picture of my baby"&gt;</code>) is important for Accessibility and required. Add alt text to the following image(s): <br><br>',
        'weight' => 30,
        'category' => ['content', 'accessibility'],
    ];

    global $_ft_dom_element_body_;
    $max_disp_threshold = 4;

    $code = array('');
    $code[0] = [];
    $code[1] = 0; 
    $code[2] = '';
    $elements = $_ft_dom_element_body_->getElementsByTagName('img');

    foreach ($elements as $element) { 	
        if (!$element->hasAttribute('alt')) {				
            $code[1]++;				
            if($code[1] <= $max_disp_threshold) { 
                $code[0][] = printCodeWithLineNumber($element); 
            }
        }		
    }
    if(count($code[0]) > 0) {
        if($code[1] > 1) { $code[2] = 's'; }
        if($code[1] > $max_disp_threshold) { $code[0] .= '...'; }
    }

    // TO-DO: each rule has similar code and it should be reused
    if (count($code[0]) > 0) {
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
