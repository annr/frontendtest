<?php
function AddSSLCertificate()
{
    global $_ft_request_headers_;

    $sug = (object) [
        'title' => 'Install SSL certificate to have site served with https://',
        'description' => 'Google Chrome will make your site appear insecure and you may penalized in search results if you don\'t include an SSL certificate. On the bright side, getting an SSL cert is simple and free with <a href="https://letsencrypt.org/" target="_blank">Let\'s Encrypt</a>.',            'weight' => 10,
        'category' => ['server'],
        'weight' => 30,
    ];

    if (stripos($_ft_request_headers_['url'], 'https://') !== 0) {
      return $sug;
    }
    return false;
}
?>