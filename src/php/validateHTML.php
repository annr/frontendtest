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
    // unset response to be safe
    $response = [];
    $api_response = json_decode($api_response);
    $count_warnings = 0;
    foreach ($api_response->messages as &$message) {
        $location = '';
        $extract ='';
        $description = null;
        $title = $message->message;
        if ($message->type === 'error') {
            if (!empty($message->lastLine) && !empty($message->firstColumn) && !empty($message->lastColumn)) {
                $location = 'From line ' .$message->lastLine .', column ' . $message->firstColumn . '; to line ' . $message->lastLine . ', column ' . $message->lastColumn . ':<br>';
            }
            if (!empty($message->extract)) {
                $extract = '<code>' . htmlentities($message->extract) . '</code>';
            }
            if (!empty($location) && !empty($extract)) {
                $description = $location . $extract;
            }
            // overrides
            // these tests are very brittle -- they require the language returning just so.
            if (strpos($title, 'The character encoding was not declared.') !== false) {
                $title = 'Include <meta charset="utf-8"/> in <head>';
                $description = 'From <a href="https://www.w3.org/International/questions/qa-html-encoding-declarations" target="_blank" rel="noopener">Declaring character encodings in HTML</a>';
                $description .= ' ...always specify the encoding used for an HTML or XML page. If you don\'t, you risk that characters in your content are incorrectly interpreted.';
            }
            //start tag to declare the language of this document.
            $sug = (object) [
                'title' => $title,
                'description' => $description,
                'weight' => 70,
                'category' => ['w3c-validation'],
            ];
            $response[] = $sug;
        }

        if ($message->type === 'info') { 
            $count_warnings++;
            if (strpos($title, 'start tag to declare the language of this document') !== false) {
                $title = 'Consider adding a lang attribute to the html start tag to declare the language of this document.';
                $description = '<a href="https://www.w3.org/International/getting-started/language" target="_blank" rel="noopener">Language on the Web</a>';
                $sug = (object) [
                    'title' => $title,
                    'description' => $description,
                    'weight' => 30,
                    'category' => ['w3c-validation'],
                ];
                $response[] = $sug;
            }
        }
    }
    if ($count_warnings >= $WARNINGS_THRESHOLD) {
        $sug = (object) [
            'title' => 'W3C Validator found ' . $count_warnings . ' warnings.',
            'description' => 'Visit <a href="https://validator.w3.org/">https://validator.w3.org/</a> to see all warnings.',
            'weight' => 30,
            'category' => ['w3c-validation'],
        ];
        $response[] = $sug;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
?>