<?php

/* 
 * An example of an XML-style doctype is: 
 * <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
 */

function NotHTML5Doctype()
{
    global $_ft_request_headers_;
    global $_ft_dom_;

    $sug = (object) [
        'title' => 'Site should be updated to use the HTML5 Doctype',
        'description' => 'Add or replace the doctype at the top of ' .
        $_ft_request_headers_['url'] . ' with <code>&lt;!DOCTYPE html&gt;</code>.',
        'weight' => 80
    ];

    // I dislike this code and the way PHP works. We might as well parse the string -- that would be better.

    // $_ft_dom_->doctype is never null. When there is no doctype provided, PHP adds a
    // doctype with this publicId: -//W3C//DTD HTML 4.0 Transitional//EN

    // so the following test will determine if the doctype doesn't exist or it's old.
    $test = $_ft_dom_->doctype != null && $_ft_dom_->doctype->publicId != '';

    if($test) {
        return $sug;
    }

    return false;
}
?>
