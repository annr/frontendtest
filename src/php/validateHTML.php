<?php
$WARNINGS_THRESHOLD = 10;

$_ft_url = $_GET['url'];
$api_url = 'https://validator.w3.org/nu/?out=json&parser=html5&doc=';
$request_url = $api_url . $_ft_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $request_url);

    $headers = ['Content-Type: application/json; charset=utf-8',
                'User-Agent: Validator.nu/LV http://validator.w3.org/services'];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10); //follow up to 10 redirections - avoids loops

    if (curl_errno($ch)) {
        // If this happens this is all we do;
        // everything else is in the else statement.
        $api_response = [
            'status' => 'error',
            'curl_error_number' => curl_errno($ch),
            'curl_error' => curl_error($ch)
        ];
    } else {
        $api_response = curl_exec($ch);
    }

    curl_close($ch);
    $api_response = json_decode($api_response);
    $count_warnings = 0;

    foreach ($api_response->messages as &$message) {
        if ($message->type === 'error') {
            $location = 'From line ' .$message->lastLine .', column ' . $message->firstColumn . '; to line ' . $message->lastLine . ', column ' . $message->lastColumn . ':<br>';
            $extract = '<code>' . $message->extract . '</code>';
            $sug = (object) [
                'title' => $message->message,
                'description' => $location . $extract,
                'weight' => 70,
                'category' => ['w3c-validation'],
            ];
            $response[] = $sug;
        }

        if ($message->type === 'info') { 
            $count_warnings++;
        }
    }
    if ($count_warnings > $WARNINGS_THRESHOLD) {
        $sug = (object) [
            'title' => 'W3C Validator found ' . $count_warnings . ' warnings.',
            'description' => 'Visit <a href="https://validator.w3.org/">https://validator.w3.org/</a> to see all warnings.',
            'weight' => 30,
            'category' => ['w3c-validation'],
        ];
        $response[] = $sug;
    }

    //header('Content-Type: application/json');
    echo json_encode($response);
?>