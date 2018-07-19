<?php

function IncludeFavicon()
{
    // GLOBALS
    global $_ft_web_root_;
    global $_ft_dom_element_head_;

    $sug = (object) [
        'title' => 'Include Favicon',
        'description' => 'Favicons are small images that appear in browsers next to location bars, bookmarks and tabs. They improve the user experience and they are easy to make. Either create your favicon with an <a href="http://www.favicon.cc">online tool</a> or search for a Photoshop plugin. Name the file favicon.ico, put it to your root web directory, and add <code>&lt;link rel="icon" type="image/x-icon" href="/favicon.ico" /&gt;</code> within <code>&lt;head&gt;</code>.',
        'weight' => 30,
        'category' => ['content'],
    ];

    // RULE CONFIG

    // RULE CODE    
    $test = true;
    // <link rel="icon" href="/favicon.ico" sizes="32x32" />
    $elements = $_ft_dom_element_head_->getElementsByTagName('link');
        
    foreach ($elements as $element) { 
        if ($element->hasAttribute('rel') && strpos($element->getAttribute('rel'), 'icon') !== false) {					
            $test = false;
        }
    }

    // also the server can be configured to include whatever favicon relative to root
    // it is usually this: $ft_web_root . 'favicon.ico'

    if(http200Test($_ft_web_root_ . 'favicon.ico')) { 
        $test = false;
    }

    if($test === true) { 
        return $sug;
    }

    return false;
}
?>
