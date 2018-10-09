<?php
include 'helpers/common.php';

// First pass includes these rules:
include 'rules/AddSSLCertificate.php';
include 'rules/AvoidRedirects.php';

$response = makeCurlRequestAndSetDataFromCurl();

// this tests for valid request:
if ($response && isset($response['url'])) {

    // unset response. it was set to the request for the error case
    $response = [];
    // - AddSSLCertificate
    $sug = AddSSLCertificate();
    if (!empty($sug)) {
      $response[] = $sug;
    }
    // - AvoidRedirects
    $sug = AvoidRedirects();
    if (!empty($sug)) {
      $response[] = $sug;
    }
}
// this could enclude suggestions or curl error details
header('Content-Type: application/json');
echo json_encode($response);
?>