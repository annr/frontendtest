<?php

function LikelyStaleCopyright()
{
    global $_ft_data_;

    // Copyright © 2005-2012 

    // an improved version would be to allow space around the hyphen.
    // also, could be an em dash or n dash.
    // $pattern = '/Copyright\s*(&copy;|©)\s*([1-2][0-9][0-9][0-9])\s?[^-]/';	
    $pattern = '/Copyright\s*(&copy;|©)\s*([1-2][0-9][0-9][0-9])[^-]/';		
    preg_match($pattern, $_ft_data_, $match);
    
    // if(isset($match[2]) && intval($match[2]) < date('Y')) {
    //     $code[0] = '`'.$match[0].'`';										
    //     return $code;
    // }			
    return false;
}

?>