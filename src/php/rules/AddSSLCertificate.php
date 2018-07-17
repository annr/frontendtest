<?php
function AddSSLCertificate()
{
    global $_ft_request_headers_;

    $sug = (object) [
        'title' => 'Install an SSL certificate to have your site served with HTTPS',
        'description' => 'It\'s pretty much required these days, and Google will penalize you in search results and make your site look less secure in Chrome if you don\'t have an SSL certificate. <br><br>But no worries! Getting an SSL cert is free and easy with <a href="https://letsencrypt.org/" target="_blank">Let\'s Encrypt</a>.',            'weight' => 10,
        'category' => ['server'],
        'weight' => 30,
    ];

    if (stripos($_ft_request_headers_['url'], 'https://') !== 0) {
      return $sug;
    }
    return false;
}
?>