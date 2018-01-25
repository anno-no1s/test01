<?php

/**************************************************

	2018年1月テスト 問1プログラム

**************************************************/

$requestUrl = 'https://premier.no1s.biz/';
$cookieFile = './file.cookie';
touch($cookieFile);

$getData = requestData($requestUrl, 'GET', $cookieFile);
$getData = changeHtmlToXml($getData);
$csrfToken = (string) $getData->body->div[1]->div->div->form->div[0]->input[1]->attributes()->value;
$requestOption = [
    'email'     => 'micky.mouse@no1s.biz',
    'password'  => 'micky',
    '_csrfToken' => $csrfToken,
];

$page1 = getOutputData($requestUrl, $cookieFile, $requestOption);
$page2 = getOutputData($requestUrl.'admin?page=2', $cookieFile, $requestOption);
$page3 = getOutputData($requestUrl.'admin?page=3', $cookieFile, $requestOption);
$outputData = array_merge($page1, $page2, $page3);

if($f = fopen('output.csv', 'w')){
    foreach($outputData as $line){
        fwrite($f, implode($line, ',') . ",\n");
    }
}
fclose($f);

function requestData($url, $method, $cookieFile, $option = null) {
    $curl = curl_init() ;
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieFile);

    if ($option) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($option));
    }

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

function changeHtmlToXml($html) {
    $domDocument = new DOMDocument();
    @$domDocument->loadHTML($html);
    $xmlString = $domDocument->saveXML();

    return simplexml_load_string($xmlString);
}

function getOutputData($url, $cookieFile, $requestOption) {
    $postData = requestData($url, 'POST', $cookieFile, $requestOption);
    $postData = changeHtmlToXml($postData);
    $outputData = [];
    foreach($postData->body->div[1]->div->div->table->tr as $tr){
        if (isset($tr->td)) {
            $outputData[] = array_map(function($td) {
                return '"' . $td . '"';
            }, (array) $tr->td);
        }
    }
    return $outputData;
}
