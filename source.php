<?php
$curl = curl_init();
$api_key = getenv("API_KEY");
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
if (isset($_POST['searchquery'])) {
    $url = "https://newsapi.org/v2/everything?q={$_POST['searchquery']}&from={$date}&sortBy=publishedAt&apiKey={$api_key}&pageSize=100&page=1";
} else if (isset($_POST['category'])) {
    $url = "https://newsapi.org/v2/top-headlines?language=en&country=in&category={$_POST['category']}&from={$date}&sortBy=publishedAt&apiKey={$api_key}&pageSize=100&page=1";
} else if (isset($_POST['allnews'])) {
    $url = "https://newsapi.org/v2/top-headlines?language=en&from={$date}&sortBy=publishedAt&apiKey={$api_key}&pageSize=100&page=1";
}

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
));
$response = curl_exec($curl);
curl_close($curl);

echo $response;