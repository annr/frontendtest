<?php
include 'helpers/common.php';
$response = makeCurlRequestAndSetDataFromCurl();

header('Content-Type: application/json');
echo json_encode($response);
?>