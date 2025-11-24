<?php

header('Content-Type: application/json');


// fetch_weather.php
// ... (header dan kode lainnya)

// GANTI DENGAN KUNCI API ANDA
$apiKey = "40a921a6559b4b87a5e5c907378db396"; // Coba 32 karakter dulu
$locationKey = "305739";

$apiUrl = "https://dataservice.accuweather.com/currentconditions/v1/" . $locationKey . "?apikey=" . $apiKey . "&details=true&language=id-id";

// ... (sisa kode cURL) ...


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


http_response_code($httpcode);
echo $response;

?>