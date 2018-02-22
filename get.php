<?php

/**************************************************

	2018年2月テスト 問1プログラム

**************************************************/

$requestUrl = 'https://api.qrserver.com/v1/create-qr-code/';

$requestOption = [
    'data'   => '',
    'size'   => '200x200',
    'format' => 'ping'
];

echo "Start creating QR code.\n";

echo "1.\n";
$requestOption['data'] = 'http://www.giants.jp/top.html';
$getData = requestData($requestUrl, 'GET', $requestOption);
outputImage($getData, 'giants');

echo "2.\n";
$requestOption['data'] = 'https://www.amazon.co.jp/dp/B01BHPEC9G';
$getData = requestData($requestUrl, 'GET', $requestOption);
outputImage($getData, 'amazon');

echo "3.\n";
$requestOption['data'] = 'http://www.cosme.net/product/product_id/10023860/top';
$getData = requestData($requestUrl, 'GET', $requestOption);
outputImage($getData, 'cosme');

echo "End.\n";

function requestData($url, $method, $option = null) {

    if ($option) {
        $url .= '?' . http_build_query($option);
    }

    $curl = curl_init() ;
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);

    $response1 = curl_exec($curl);
    $response2 = curl_getinfo($curl);
    $errno = curl_errno($curl);
    curl_close($curl);

    if (CURLE_OK !== $errno) {
        echo 'Curl error: ' . $errno;
        exit;
    }

    return substr($response1, $response2['header_size']);
}

function outputImage($data, $fileName) {

    $path = './img';
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_buffer($finfo, $data);
    finfo_close($finfo);

    $extensionList = [
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'png' => 'image/png'
    ];

    if(!$extension = array_search($mimeType, $extensionList, true)){
        return;
    }

    if(!file_exists($path)){
        mkdir($path);
    }

    if ($f = fopen($path . '/' . $fileName . '.' . $extension, 'w')) {
        fwrite($f, $data);
    }
    fclose($f);
}

