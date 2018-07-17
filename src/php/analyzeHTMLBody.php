<?php
include 'helpers/common.php';

/* This is the third step and request.
 * 
 * We run the following tests in this script:
 */
include 'rules/DuplicateIDs.php';
include 'rules/MissingImgAltAttributes.php';

$response = makeCurlRequestAndSetDataFromCurl();

// this tests for valid request:
if ($response && isset($response['url'])) {
  
  // unset response. it was set to the request for the error case
  $response = [];

  // sets $_ft_dom_ global
  setDomDocument();

  // set the $_ft_dom_element_body_ global
  setDomDocumentBodyElement();

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

}

// this could enclude suggestions or curl error details
echo json_encode($response);
?>


