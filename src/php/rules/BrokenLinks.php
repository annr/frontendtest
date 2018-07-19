<?php
function BrokenLinks() {

    // GLOBALS
    global $_ft_dom_;

    $sug = (object) [
        'title' => 'broken link(s) found',  // dynamically set below
        'description' => '', // dynamically set below
        'weight' => 0, // dynamically set below
        'category' => ['content'],
    ];

    $code = array('');
    $code[0] = [];
    $code[1] = 0;
    $code[2] = '';
    $code[3] = '';

    $passed_elem_array = array();
    $url_array = array();

    // max number of link we check
    $max_resource_tests = 20;

    // max number of broken links we display
    // therefore, there might be $max_resource_tests broken links but we only show $max_disp_threshold
    $max_disp_threshold = 5;

    $elements = $_ft_dom_->getElementsByTagName('a');
    //only make this test if there is a reasonable number of links.
    foreach ($elements as $element) { 	
        if($element->hasAttribute('href')) { 
            if(strpos($element->getAttribute('href'),'javascript:') !== false || strpos($element->getAttribute('href'),'mailto:') !== false){
                //error_log('returning from mailto or js:');
                continue;
            }
            if(!in_array($element->getAttribute('href'),$url_array))
            { 				
                $url = getAbsoluteResourceLink($element->getAttribute('href'));	
                if(httpBadStatusCode($url)) { 
                    $code[1]++;
                    if($code[1] <= $max_disp_threshold) { 
                        $code[0][] = printCodeWithLineNumber($element); 
                        
                    }
                } 
                $url_array[] = $element->getAttribute('href');						
            } 
        }
        //don't try to do too many.
        if(count($url_array) > $max_resource_tests) {
            $code[3] = "<br><br>NOTE: By default, FrontendTest checks a maximum of $max_resource_tests links, which it reached. Either fix the broken links shown and run FrontendTest again, or use a more thorough broken link checking tool.";
            continue;
        }
    }

    // broken links we give a weight of 30 and then multiply all the instances by 3 and add to that.
    $sug->weight = 60 + count($code[0])*5;
    if ($sug->weight > 100) {
        $sug->weight = 100;
    }
    $sug->title = '' . count($code[0]) . ' ' . $sug->title;

    if (count($code[0])) {
      $instances = '';
      foreach($code[0] as $instance) {
          $instances .= '<code>' . $instance . '</code><br>';
      }
      $sug->description .= $instances;
      if($code[1] > 1) { $code[2] = 's'; }
      if($code[1] > $max_disp_threshold) { $sug->description .= '...'; }
      $sug->description .= $code[3];
      return $sug;
    }
    
    return false;
}
?>