<?php
//======================================================================
// RULE DEPRECATED. We check this using https://validator.w3.org/ now.
//======================================================================

function DoctypeNotFirstElement() 
{
    global $_ft_data_;
    global $_ft_dom_;

    $sug = (object) [
        'title' => 'A DOCTYPE declaration should be the very first element in the HTML document',
        'description' => 'We recommend using the HTML5 doctype: <code>&lt;!DOCTYPE html&gt;</code>',
        'weight' => 80,
        'category' => ['content'], // maybe there's a better cat for this
    ];

    //echo substr(trim(strtolower($_ft_data_)),0,300);

    // feel like comments may not be legitimately allowed above the doctype.
 
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
