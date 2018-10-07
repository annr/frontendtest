<?php
include 'helpers/common.php';

/* This is the third step and request.
 * 
 * We run the following tests in this script:
 */
include 'rules/DuplicateIDs.php';
include 'rules/MissingImgAltAttributes.php';
include 'rules/ClassOrIDSameAsAvailableTag.php';

$response = makeCurlRequestAndSetDataFromCurl();

// this tests for valid request:
if ($response && isset($response['url'])) {
  
  // unset response. it was set to the request for the error case
  $response = [];

  // sets $_ft_dom_ global
  setDomDocument();

  // set the $_ft_dom_element_body_ global
  setDomDocumentBodyElement();

  // set $_ft_web_root_, $_ft_host_... globals
  setMiscFtGlobals();

  // there must be a more efficient way of
  // adding suggestions to response, but for 
  // now I'm trying to work as fast as possible

  // - HasDuplicateIDs
  $sug = DuplicateIDs();
  if (!empty($sug)) {
    $response[] = $sug;
  }

  // - MissingImgAltAttributes
  $sug = MissingImgAltAttributes();
  if (!empty($sug)) {
    $response[] = $sug;
  }

  // - ClassOrIDSameAsAvailableTag
  $sug = ClassOrIDSameAsAvailableTag();
  if (!empty($sug)) {
    $response[] = $sug;
  }
}

// this could enclude suggestions or curl error details
header('Content-Type: application/json');
echo json_encode($response);
?>


