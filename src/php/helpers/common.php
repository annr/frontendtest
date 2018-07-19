<?php
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

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

function elementValueAsString($element) {
    if (isset($element->nodeValue)) {
        return $element->nodeValue;
    }
    return '';
}

function elementAsString($element) {
    return $element->ownerDocument->saveXML($element);
}

function elementParentAsString($element) {
    $parent = $element->parentNode;
    return $parent->ownerDocument->saveXML($parent);
}

function removeCommentsFromString($code_str)
{
    // what is a comment? it's <!-- then anything including -- then -->
    // also it's <!> but let's ignore that for now. (??)
    // importantly a comment is <!-- until it gets to a close comment. this is one comment <!--<!--<!--<!-->
    
    // this seems to work
    return preg_replace('/<!--.*-->/','',$code_str);
}

/* Not sure if I will keep the following functions... */
function recursivelyGetDuplicateAttributeValue( $node, $attribute_name ) {
    global $poorly_designed_catchall;
    global $poorly_designed_catchall_element_array;
    // global $poorly_designed_catchall_instances;

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
    // TL;DR: this isn't going to work well with HTML elements that span lines.
    // For this reason we don't use it for `ClassOrIDSameAsAvailableTag`

    // there is an issue with this code. because an element may have had linebreaks removed,
    // it might not be matched. Therefore we must try to match the substring before any line breaks.
    // This means that an element may not be accurately matched to it's line. fixed it as well as we
    // can for now, and I will not show line number if it can't be matched (and output to error log)

    global $_ft_data_;

    $doc=new \DOMDocument();
    $doc->preserveWhiteSpace = true; 
    $doc->formatOutput = false; 
    $doc->appendChild($doc->importNode($element,true));
    $code_str = trim($doc->saveHTML());

    //echo "\n\ncode: \n".$code_str;
    // if we can find the code in the string, great we're in business.
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
            //echo "FT ERROR. HELLUVA TIME MATCHING DOM ELEMENT WITH RAW SOURCE \n" . $code_str;
            //there is a line break or some other char in the middle of a tag!
            //remove all whitespace from both strings and see if you can find it.

            // instead we'll just get the element as a string value.
            // It's converted to XML, and it might not match their document exactly.

            // TO-DO make this better.
            return htmlentities(elementAsString($element));
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

function http200Test($url)
{
    $headers = @get_headers($url, 1);
    if(!$headers) { return false;}
    $header_str = explode(' ',$headers[0]);
    if($header_str[1] == '200') {
        return true;
    }
    return false;
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
    global $_ft_dom_;
        
    $code = array('');

    $elements = $_ft_dom_->getElementsByTagName($element_str);

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

function setMiscFtGlobals()
{
    global $_ft_request_headers_;
    global $_ft_host_;
    global $_ft_get_;
    global $_ft_url_root_;
    global $_ft_web_root_;

    $http_request_split = explode("\n", $_ft_request_headers_['request_header']);
    $get_split = explode(" ", $http_request_split[0]);
    $host_split = explode(" ", $http_request_split[1]);
    
    $_ft_host_ = trim($host_split[1]);
    $_ft_get_ = $get_split[1];
    $protocol = explode('/',$get_split[2]);
    $_ft_url_root_ = strtolower($protocol[0]) . '://' . $_ft_host_ . substr($_ft_get_, 0, (strrpos($_ft_get_, '/') + 1));

    $_ft_web_root_ = strtolower($protocol[0]) . '://' . $_ft_host_ . '/';
}

function getAbsoluteResourceLink($page_link)
{
    global $_ft_url_root_;
    global $_ft_web_root_;

    // replace spaces with %20.
    // what other ways do we need to encode the URL so that we can test the link?
    $link = str_replace(' ','%20',$page_link);

    if(substr($link, 0, 2) === '//') {
        //add the http protocol. this might not be perfect.
        $link = 'http:'.$link;
    } elseif (substr($link, 0, 2) === '//' || substr($link, 0, 7) === 'http://' || substr($link, 0, 8) === 'https://') {
        //link is fine, do not modify
    } elseif (substr($link, 0, 1) === '/') {
        //if forward slash, add url minus forward slash.
        $link = $_ft_web_root_.substr($link, 1);
    } elseif (substr($link, 0, 2) === './') {
        //if dot forward slash, add url minus dot forward slash.
        $link = $_ft_web_root_.substr($link, 2);
    } elseif (substr($link, 0, 3) === '../') {
        $new_root = $_ft_url_root_;
        for($i = 0; $i < substr_count($link,'../'); $i++) {
            ///use root.
            //trim url from last or second to last forward slash to end.
            //remove folders from url root, and shift relative
            $new_root = substr($new_root, 0, strrpos($new_root,'/',-2) + 1);
            //trim first:
            $link = substr($link, 3);
        }
        $link = $new_root.$link;
    } elseif (substr($link, 0, 1) !== '/') {
        //if relative, add url making sure forward slash exists
        $link = $_ft_url_root_.$link;
    }

    return $link;
}

// I'm not sure how much value beyond http200Test httpBadStatusCode gives us
function httpBadStatusCode($url)
{
    $headers = @get_headers($url, 1);
  
    //not being able to get headers is an indication (to me!) that it has a bad status code. 
    //it's the case with http://mdch.at/lG4hzm linked from http://mdchat.org/
    if(!$headers) { return true; }

    $header_str = explode(' ', $headers[0]);
    $bad_codes = array('400','404','408','410');

    if(in_array($header_str[1],$bad_codes)) {
        //var_dump($header_str);
        //echo $url;

        // this extra test is required for certain web sites that do not want their content spidered.
        // it won't be easy to catch all of these kinds of cases, but the specific case I'm fixing:
        // www.mdchat.org with URLs such as
        // http://www.scribd.com/embeds/48466709/content?start_page=1&view_mode=list&access_key=key-1ocsvs7iik05cs4ry0lh
        // is handled in this case
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0");
        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);
        $info = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        //echo "INFO: " . $info;

        curl_close($handle);
        //var_dump($response);
        if(in_array($info, $bad_codes)) {
            return true;
        }
    }
    return false;
}

?>