<?php
include 'helpers/common.php';
$response = makeCurlRequestAndSetDataFromCurl();

echo json_encode($response);
?>