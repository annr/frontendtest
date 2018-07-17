<?php
function TitleMIssingOrNotWithinHead() {
    // GLOBALS
    global $_ft_dom_;
    global $_ft_dom_element_head_;

    $sug = (object) [
        'title' => 'Title element is missing or not within &#x3C;head&#x3E;',
        'description' => 'Fun fact: The &#x3C;title&#x3E; tag is the only element of and HTML document that is always required. &#x3C;title&#x3E; is included within &#x3C;head&#x3E;.',
        'weight' => 80,
        'references' => ['https://developer.mozilla.org/en-US/docs/Web/HTML/Element/title'],
        'category' => ['content'],
    ];

    // RULE CODE 
    
    // Search WHOLE DOM for title tag
    $title_elements = $_ft_dom_->getElementsByTagName('title');

    // Search HEAD for title tag
    $title_elements_in_head = $_ft_dom_element_head_->getElementsByTagName('title');

    if($title_elements->length == 0 || $title_elements_in_head->length == 0) { 
        return $sug;
    }

    return false;

}
?>