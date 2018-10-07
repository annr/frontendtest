<?php
include 'helpers/common.php';

/* This is the second step and request. FET already grabbed the headers once.
 * 
 * We run the following tests in this script:
 */
include 'rules/DoctypeNotFirstElement.php';
include 'rules/NotHTML5Doctype.php';
include 'rules/TitleMIssingOrNotWithinHead.php';
include 'rules/SetViewport.php';
include 'rules/IncludeFavicon.php';

$response = makeCurlRequestAndSetDataFromCurl();

if ($response && isset($response['url'])) {
  
  // unset response. it was set to http headers
  $response = [];

  // sets $_ft_dom_ global
  setDomDocument();

  // set the $_ft_dom_element_head_ global
  setDomDocumentHeadElement();

  // set $_ft_web_root_, $_ft_host_... globals
  setMiscFtGlobals();

  // - DoctypeNotFirstElement
  $sug = DoctypeNotFirstElement();
  if (!empty($sug)) {
    $response[] = $sug;
  }

  //  - NotHTML5Doctype
  // 
  //   SPECIAL CONDITIONAL RULE: Only test for HTML5 DOCTYPE if DOCTYPE is 
  //   first element. First things first. Although I think these rules could 
  //   be combined or re-thought

  if (false === DoctypeNotFirstElement()) {
    $sug = NotHTML5Doctype();
    if (!empty($sug)) {
      $response[] = $sug;
    }
  }

  // - TitleMIssingOrNotWithinHead
  $sug = TitleMIssingOrNotWithinHead();
  if (!empty($sug)) {
    $response[] = $sug;
  }

  // - SetViewport
  $sug = SetViewport();
  if (!empty($sug)) {
    $response[] = $sug;
  }

  // - IncludeFavicon
  $sug = IncludeFavicon();
  if (!empty($sug)) {
    $response[] = $sug;
  }

}

// this could enclude suggestions or curl error details
header('Content-Type: application/json');
echo json_encode($response);
?>
