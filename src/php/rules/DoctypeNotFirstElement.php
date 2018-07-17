<?php

function DoctypeNotFirstElement() 
{
    $sug = (object) [
        'title' => 'A DOCTYPE declaration should be the very first element in the HTML document',
        'description' => 'We recommend using the HTML5 doctype: <code>&lt;!DOCTYPE html&gt;</code>',
        'weight' => 80,
        'category' => ['content'], // maybe there's a better cat for this
    ];

    global $_ft_data_;
    //echo substr(trim(strtolower($_ft_data_)),0,300);

    $data_without_comments = trim(removeCommentsFromString($_ft_data_));

    // this is an old comment I no longer understand: 

    //remove the BOM:
    //$data_without_comments = preg_replace('/\x{EF}\x{BB}\x{BF}/','',$data_without_comments);

    if(stripos($data_without_comments, '<!doctype ') !== 0)
    {	
        return $sug;
    }
    return false;
}

?>
