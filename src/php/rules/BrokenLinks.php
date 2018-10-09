<?php
//======================================================================
// Find broken links in entire document
//
// It checks all the broken links and adds them to a single result.
// It could be more efficient.
//======================================================================

function BrokenLinks() {
    $sug = (object) [
        'title' => 'broken link(s) found', // dynamically set below
        'description' => '', // dynamically set below
        'weight' => 0, // dynamically set below
        'category' => ['content'],
    ];

    $code = array('');
    $code[0] = [];
    $code[1] = 0;
    $code[2] = '';
    $code[3] = '';

    // GLOBALS
    global $_ft_dom_;

    $element = $_ft_dom_;
    $url_array = array();

    // max number of link we check
    // it takes SOOOOO long to test links. Until we can optimze this and set it to be faster, let's make it very low.
    $max_resource_tests = 20;

    // max number of broken links we display
    // therefore, there might be $max_resource_tests broken links but we only show $max_disp_threshold
    $max_disp_threshold = 5;

    $links = $element->getElementsByTagName('a');

    // TODO: rewrite the following three loops to reuse code.

    foreach ($links as $link) {
        if($link->hasAttribute('href')) {
            if(strpos($link->getAttribute('href'),'javascript:') !== false || strpos($link->getAttribute('href'),'mailto:') !== false){
                // error_log('returning from mailto or js:');
                continue;
            }
            if(!in_array($link->getAttribute('href'),$url_array))
            {
                $url = getAbsoluteResourceLink($link->getAttribute('href'));
                if(httpBadStatusCode($url)) { 
                    $code[1]++;
                    if($code[1] <= $max_disp_threshold) { 
                        $code[0][] = printCodeWithLineNumber($link); 
                    }
                }
                $url_array[] = $link->getAttribute('href');
            } 
        }
        //don't try to do too many.
        if(count($url_array) > $max_resource_tests) {
            continue;
        }
    }

    $links = $element->getElementsByTagName('link');

    foreach ($links as $link) {
        if($link->hasAttribute('href')) {
            if(!in_array($link->getAttribute('href'),$url_array))
            {
                $url = getAbsoluteResourceLink($link->getAttribute('href'));
                if(httpBadStatusCode($url)) { 
                    $code[1]++;
                    if($code[1] <= $max_disp_threshold) { 
                        $code[0][] = printCodeWithLineNumber($link); 
                    }
                }
                $url_array[] = $link->getAttribute('href');
            } 
        }
        //don't try to do too many.
        if(count($url_array) > $max_resource_tests) {
            continue;
        }
    }

   $imgs = $element->getElementsByTagName('img');

    foreach ($imgs as $img) {
        if($img->hasAttribute('src')) {
            if(!in_array($img->getAttribute('src'),$url_array))
            {
                $url = getAbsoluteResourceLink($img->getAttribute('src'));
                if(httpBadStatusCode($url)) { 
                    $code[1]++;
                    if($code[1] <= $max_disp_threshold) { 
                        $code[0][] = printCodeWithLineNumber($img); 
                    }
                }
                $url_array[] = $img->getAttribute('src');
            } 
        }
        //don't try to do too many.
        if(count($url_array) > $max_resource_tests) {
            continue;
        }
    }

    
    $scripts = $element->getElementsByTagName('script');

    foreach ($scripts as $script) {
        if($script->hasAttribute('src')) {
            if(!in_array($script->getAttribute('src'),$url_array))
            {
                $url = getAbsoluteResourceLink($script->getAttribute('src'));
                if(httpBadStatusCode($url)) {
                    $code[1]++;
                    if($code[1] <= $max_disp_threshold) {
                        $code[0][] = printCodeWithLineNumber($script);
                        
                    }
                } 
                $url_array[] = $script->getAttribute('src');
            } 
        }
        //don't try to do too many.
        if(count($url_array) > $max_resource_tests) {
            continue;
        }
    }

    if(count($url_array) > $max_resource_tests) {
        $code[3] = "<br><br>NOTE: By default, FrontendTest checks a maximum of $max_resource_tests links, which it reached. Either fix the broken links shown and run FrontendTest again, or use a more thorough broken link checking tool.";
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