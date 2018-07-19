<?php
include 'helpers/common.php';

/* This is the last step where we put any slow tests
 * 
 * We run the following test(s) in this script:
 */
include 'rules/BrokenLinks.php';

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

  // Start Rules:

  // - BrokenLinks
  $sug = BrokenLinks();
  if (!empty($sug)) {
    $response[] = $sug;
  }

}
// this could enclude suggestions or curl error details
echo json_encode($response);
?>
