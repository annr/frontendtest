<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function makeCurlRequestAndSetDataFromCurl() {
    global $_ft_url_;
    global $_ft_data_;
    global $_ft_request_headers_;
    $_ft_url_ = $_GET['url'];

    $ch = curl_init();	
    curl_setopt($ch, CURLOPT_URL,$_ft_url_);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10); //follow up to 10 redirections - avoids loops
    $_ft_data_ = curl_exec($ch);

    if (curl_errno($ch)) {
    // If this happens this is all we do;
    // everything else is in the else statement.
    $response = [
        'status' => 'error',
        'curl_error_number' => curl_errno($ch),
        'curl_error' => curl_error($ch)
    ];
    } else {
        $_ft_request_headers_ = curl_getinfo($ch);
        $response = $_ft_request_headers_;
    }
    
    curl_close($ch);

    // This is our local response. It will either contain an error or http_headers
    return $response;
}

function setDomDocument() {
    global $_ft_data_;
    global $_ft_dom_;
    $_ft_dom_ = new \DomDocument();
    $_ft_dom_->preserveWhiteSpace = true;
    @$_ft_dom_->loadHTML($_ft_data_);
    // to see general contents of the dom, you can print_r($_ft_dom_)
}

function setDomDocumentHeadElement() {
    global $_ft_dom_;
    global $_ft_dom_element_head_;
    $_ft_dom_element_head_ = $_ft_dom_->getElementsByTagName('head')->item(0);
}

function setDomDocumentBodyElement() {
    global $_ft_dom_;
    global $_ft_dom_element_body_;
    $_ft_dom_element_body_ = $_ft_dom_->getElementsByTagName('body')->item(0);
}

function printDomElementValue($element) {
    if (isset($element->nodeValue)) {
        echo 'Node value: ' . $element->nodeValue;
    } else {
        echo 'Element node not set.';
    }
}

function printElem($element) {   
    printDomElementValue($element);
}

function removeCommentsFromString($code_str)
{	
    //what is a comment? it's <!-- then anything including -- then -->
    //also it's <!> but let's ignore that for now.
    //importantly a comment is <!-- until it gets to a close comment. this is one comment <!--<!--<!--<!-->
    
    //this seems to work
    return preg_replace('/<!--.*-->/','',$code_str);
}

/* Not sure if I will keep the following functions... */

	
function recursivelyGetDuplicateAttributeValue( $node, $attribute_name ) {
    global $poorly_designed_catchall;
    global $poorly_designed_catchall_element_array;
    //global $poorly_designed_catchall_instances;
             
    if ($node->hasAttribute($attribute_name) !== false) { 
         //if it's in the collected attribute values AND not already in the catchall element array
         if(in_array($node->getAttribute($attribute_name),$poorly_designed_catchall)) {
             if(!in_array($node->getAttribute($attribute_name),$poorly_designed_catchall_element_array)) {
                 $poorly_designed_catchall_element_array[] = $node->getAttribute($attribute_name);					
             } //else {
                 //$poorly_designed_catchall_instances++;
             //}
         } else {
             $poorly_designed_catchall[] = $node->getAttribute($attribute_name);				
         }
    }
    if ( $node->hasChildNodes() ) {
      $children = $node->childNodes;
      foreach( $children as $kid ) {
        if ( $kid->nodeType == XML_ELEMENT_NODE ) {
          recursivelyGetDuplicateAttributeValue( $kid,$attribute_name );
        }
      }
    }
 }	

function printCodeWithLineNumber($element)
{	
    //there is an issue with this code. because an element may have had linebreaks removed, it might not be matched. 
    //therefore we must try to match the substring before any line breaks. this means that an element may not be accurately matched to it's line. 
    //fixed it as well as I can for now, and I will not show line number if it can't be matched (and output to error log)

    global $_ft_data_;

    $doc=new \DOMDocument();
    $doc->preserveWhiteSpace = true; 
    $doc->formatOutput = false; 
    $doc->appendChild($doc->importNode($element,true));
    $code_str = trim($doc->saveHTML());
    
    //$tic = '`';
    //if($add_tics === false) {$tic = '';}

    //echo "\n\ncode: \n".$code_str;
    //if we can find the code in the string, great we're in business.
    // if strtolower() is too slow, we can take it off $code_str 
    $element_pos = stripos(strtolower($_ft_data_), strtolower($code_str));
    if($element_pos !== false) {
        $text = substr($_ft_data_, 0, stripos(strtolower($_ft_data_), strtolower($code_str)));
    } else {
        //the first guess is that the code has XHTML end tags not matching the re-saved HTML element.
        //replace first occurance of '>' in $code_str with ' />'
        $test_code_str = str_replace('>',' />',$code_str);
        //echo "\nnew test:\n".$test_code_str;
        
        //success?
        if(stripos($_ft_data_, $test_code_str) !== false) {
            //replace overridden code str, because that's what's in their code. 
            $code_str = $test_code_str;
            $text = substr($_ft_data_, 0, stripos(strtolower($_ft_data_), $code_str));			
        }			
        //if text still is not set....
        //it might be that there is a linebreak in the orig html doc or some other difference from the original.
        //this is too expensive of a test to do, but the way we might do it is:			
        if(!isset($text))
        {	
            echo "FT ERROR. HELLUVA TIME MATCHING DOM ELEMENT WITH RAW SOURCE \n" . $code_str;	
            //there is a line break or some other char in the middle of a tag!
            //remove all whitespace from both strings and see if you can find it.
        }		
    }
    
    if(!isset($text) && strpos($code_str,"\n") > 0)
    {
        $code_str = substr($code_str, 0, strpos($code_str,"\n"));
        $text = substr($_ft_data_, 0, stripos(strtolower($_ft_data_), $code_str));
    }
    
    //if code_str has no spaces, and is greater than x chars, add a space to break the line every x chars.		
    //determine if the test is necessary, and pass in range. this is a dangerous recursive thing!
    $pattern = '/\s|-/';
    $unbrokencharspans = preg_split($pattern,$code_str);
    foreach($unbrokencharspans as $unbrokencharspan) {
        if(strlen($unbrokencharspan) > 65) { 
            $start = strpos($code_str,$unbrokencharspan); 
            //echo "\nstart: " . $start;
            $end = $start + strlen($unbrokencharspan); 
            //echo "\end: " . $end;
            //echo "\n breaking...".$code_str ."\n\n";
            $code_str = addWhitespaceForReportFormatting($code_str,$start,$end);			
        }
    }

    if(isset($text) && $text != '') {
        $line = 1; //the first line is one.
        $line += substr_count($text, "\n");
        //when the line number is 1, it's not valuable.
        if($line != 1){
            $code = '('. $line . ') '. htmlentities($code_str);				
        } else {
            $code = htmlentities($code_str);					
        }
    } else {
        //line number not found, so don't print it.
        $code = htmlentities($code_str);
        //error_log('FT ERROR with request id ' . $ft_request_id . ': DOM ELEMENT NOT FOUND IN RAW SOURCE '.$code_str);		
    }

    return $code;

}

function addWhitespaceForReportFormatting($str,$index_start=0,$index_end=null) {
    $index = $index_start;
    //maxchars would be changed if the width of the email is.
    $maxchars = 65;
    if(!isset($index_end)) {
        $index_end = strlen($str);
    }

    while($index < $index_end) 
    {
        //echo "\nindex " . $index;
        //echo "\nindex_end " . $index_end;
        $str = Helper::str_insert(' ', $str, $index + $maxchars);
        $index = $index + $maxchars;
    }		
    //echo "\nbroken str: " . $str;
    return $str;
}

function testForElement($element_str)
{	
    global $ft_dom;
        
    $code = array('');

    $elements = $ft_dom->getElementsByTagName($element_str);

    if($elements->length == 0) {
        return false;
    } else {
        foreach ($elements as $element) { 
            $code[0] .=  printCodeWithLineNumber($element);						
        }	
    }
    
    if($code[0] != '') {
        return $code;
    }		
    
    return false;
}
?>