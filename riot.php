<?php

$secret = 'oF8Hz9pNp9fDQoaYarAe';

// get the value of the variable 'endpoint' from the URL
$endpoint = $_GET['endpoint'];
$theirSecret = $_GET['secret'];

if ($theirSecret != $secret) {
    echo 'Invalid secret';
    exit;
}
$API_KEY = 'RGAPI-e0175c0f-e515-42f9-88a6-5af4b38b10c2';


// make request to the Riot API using the endpoint and API KEY 
$response = file_get_contents("https://europe.api.riotgames.com$endpoint?api_key=$API_KEY");

// output the response JSON
header('Content-Type: application/json');
echo $response;