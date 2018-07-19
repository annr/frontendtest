<?php

// REFERENCES
//   https://developer.mozilla.org/en-US/docs/Mozilla/Mobile/Viewport_meta_tag
function SetViewport()
{
    // GLOBALS
    global $_ft_data_;
    global $_ft_dom_element_head_;

    $sug = (object) [
        'title' => 'Set viewport for mobile devices',
        'description' => 'Without the META viewport tag set, pages are rendered too zoomed out and the content on narrow screen becomes too small to be legible. There is a nice and easy fix though. Add this tag within &lt;head&gt; tags: <br><br><code>&lt;meta name="viewport" content="width=device-width, initial-scale=1"&gt;</code>',
        'weight' => 40,
        'category' => ['mobile'],
    ];

    // RULE CONFIG

    // RULE CODE    
    $test = true;
    $elements = $_ft_dom_element_head_->getElementsByTagName('meta');
        
    foreach ($elements as $element) { 
        if ($element->hasAttribute('name') && $element->getAttribute('name') == 'viewport') {					
            $test = false;
        }
    }

    if($test === true) { 
        return $sug;
    }

    return false;
}
?>
