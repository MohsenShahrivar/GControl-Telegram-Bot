<?php
require_once('core.php');
require_once('db.php');


$content = file_get_contents("php://input");
$update = json_decode($content, true);
$user_id = $update["message"]['from']['id']; 
$chat_id = $update["message"]['chat']['id']; 
$text = $update["message"]['text'];
$chat_id_group = $update["message"]['chat']['id'];
MessageRequestJson("sendMessage", array('chat_id' =>-1001415994812,'text'=>"The robot was successfully updated✅"));