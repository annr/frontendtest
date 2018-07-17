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

    // RULE CODE    
    $test = false;

    if($test === true) { 
        return $sug;
    }

    return false;
}
?>
