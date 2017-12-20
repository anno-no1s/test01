<?php

/**************************************************

	2017年12月テスト 問1プログラム

**************************************************/

$apiKey = "UhuBsj54DbRnnHGQYaPywHmAi";
$apiSecret = "e1zSaGoQorVKzyW5W7U8LaAmkUsf3nnjdIHCxh45Y03bnnBj86";
$accessToken = "42566916-5CI2BDtmiUQdWFKtTY6ajgxmLlYjURI4ftW2K88h3";
$accessTokenSecret = "YHfdFB1RJxIcHQ6XOriX0GZhmZmFpNmI5wegaBIRDflIU";
$requestUrl = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$requestMethod = 'GET';

$requestOption = [
    'screen_name' => '@realDonaldTrump',
    'count'       => 10,
    'tweet_mode'  => 'extended',
];

$signatureKey = implode('&', [
    rawurlencode($apiSecret), 
    rawurlencode($accessTokenSecret)
]);

$oauthParams = [
    'oauth_token'            => $accessToken,
    'oauth_consumer_key'     => $apiKey,
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp'        => time(),
    'oauth_nonce'            => microtime(),
    'oauth_version'          => '1.0',
];

$params = array_merge($requestOption, $oauthParams);
ksort($params);
$requestParams = http_build_query($params, '', '&');
$requestParams = str_replace(['+', '%7E'], ['%20', '~'], $requestParams);
$signatureData = implode('&', [
    rawurlencode($requestMethod),
    rawurlencode($requestUrl),
    rawurlencode($requestParams)
]);
$hash = hash_hmac('sha1', $signatureData, $signatureKey, true);
$signature = base64_encode($hash);
$params['oauth_signature'] = $signature;

$request_header = [
    'Authorization: OAuth ' . http_build_query($params, '', ','),
];

if($requestOption) {
    $requestUrl .= '?' . http_build_query($requestOption);
}

$curl = curl_init() ;
curl_setopt($curl, CURLOPT_URL, $requestUrl);
curl_setopt($curl, CURLOPT_HEADER, true);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $requestMethod);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $request_header);
curl_setopt($curl, CURLOPT_TIMEOUT, 5);
$response1 = curl_exec($curl);
$response2 = curl_getinfo($curl);
$errno = curl_errno($curl);
curl_close($curl);

if (CURLE_OK !== $errno) {
    echo 'Curl error: ' . $errno;
    exit;
}

$json = substr($response1, $response2['header_size']);
$array = json_decode($json, true);

$view = array_map(function($ar) {
    return [
        'created_at' => date("Y年m月d日 H時i分s秒", strtotime($ar['created_at'])),
        'text'       => $ar['full_text'],
    ];
}, $array);

// 表示
foreach ($view as $v) {
    echo '<<< ' . $v['created_at'] . ' >>>', PHP_EOL;
    echo $v['text'], PHP_EOL;
    echo '----------------------------------------------------------------------', PHP_EOL;
}

