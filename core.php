<?php
define('BOT_TOKEN','1422956944:AAGtPEZf1tyt-5NhFhA90ek7wEGNAfhrpV4');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
define('ADMIN_ID', 'xxxxxxxxx');


function MessageRequestJson($method, $parameters) {

  if (!$parameters) {
    $parameters = array();
  }
  
  $parameters["method"] = $method;

  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
  $result = curl_exec($handle);
  return $result; 
}


function baseUrl(){
    return 'https://mysmartbot.info/';
}


function randomImage(){
    $images = glob("images/*.{jpg,png}",GLOB_BRACE);
    $randomImage = $images[array_rand($images)];
    return baseUrl()."faranesh/f/".$randomImage;
}


?>