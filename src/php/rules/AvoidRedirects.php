<?php
function AvoidRedirects()
{
    global $_ft_url_;
    global $_ft_request_headers_;

    $sug = (object) [
        'title' => 'Avoid Redirects',
        'description' => 'From your input URL, ' .
          $_ft_url_ . 
          ', to the ultimately served URL, ' .
          $_ft_request_headers_['url'] . 
          ' you have ' . $_ft_request_headers_['redirect_count'] .
          ' redirects. Your site will be faster if you reduce them.',
        'category' => ['server'],
        'weight' => 40,
    ];

    if($_ft_request_headers_['redirect_count'] > 1) { // you might want to make this > 0 eventually. 
      return $sug;
    }
    return false;
}
?>
