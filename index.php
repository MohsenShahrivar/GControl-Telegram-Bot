<?php
require_once('core.php');
require_once('db.php');

/// UNCOMPRESSED VERSIO


$content = file_get_contents("php://input");
$update = json_decode($content, true);
$user_id = $update["message"]['from']['id'];
$user_first_name = $update["message"]['from']['first_name'];
$user_last_name = $update["message"]['from']['last_name'];
$chat_id = $update["message"]['chat']['id'];
$text = $update["message"]['text'];
$message_id = $update["message"]['message_id'];
$new_chat_member = $update["message"]['new_chat_members'];
$new_chat_member_length = count($new_chat_member);
$left_chat_member = $update["message"]['left_chat_member']['id'];
$reserved_user_1 = 849953698;
$reserved_user_2 = 1032655086;
$reserved_user_3 = 7154536230;
$reserved_user_4 = 10659668824;;
$message__type = $update["message"]['entities'][0]['type'];
$invite_num = 20;
// Define Time const. ///////////////////////////////////////////////////////////////////////////////////////////
/*$itime = 0;
while ( $itime < 60 ) {
	$itime = $itime + 1;
	$wait_for[$itime] = $itime; // int
}

*/
MessageRequestJson("sendMessage", array('chat_id' =>$reserved_user_1,'parse_mode'=>'Markdown','text'=>$content));

// Delete Left Message ////////////////////////////////////////////////////////////////////////////////////////

if ( $left_chat_member != NULL) {
	MessageRequestJson("deleteMessage", array('chat_id' =>$chat_id,'message_id'=>$message_id));
}

/// Seccess Section  ////





// Welcome Message///////////////////////////////////////////////////////////////////////////////////////////////

$delmessage_i = 1;
if ($new_chat_member_length >= $delmessage_i && $new_chat_member_length != NULL) {
  MessageRequestJson("deleteMessage", array('chat_id' =>$chat_id,'message_id'=>$message_id));
}
/*
foreach($new_chat_member as $key => $value) {
  $added_user_id = $value['id'];
  $added_user_fname = $value['first_name'];

  MessageRequestJson("sendMessage", array('chat_id' =>$chat_id,'parse_mode'=>'Markdown','text'=>"کاربر  [$added_user_fname](tg://user?id=$added_user_id) عزیز"." \n "."به گروه گالری شهروز خوش آمدید!!😄
در گالری شهروز ، تنوع و قیمت مناسب را تجربه کنید !
نشانی ما : نیشابور - خیابان امام خمینی 34 - رو به روی شیرینی پزی خواهانی

🔥 پیشنهاد ویژه 🔥
😍هم اکنون با افزودن 20 عضو جدید به گروه از ما یک تکه لباس دریافت کنید!😍
راستی اسکرین شات یادت نره !! 🖇

تماس با ما :
📞 0915 551 5649"));
}

/// Delete WELCOME MESSAGES AFTER 15 SECOND. ( MORE THAN 1)
if ( $new_chat_member_length > 1 ) {
	// WAIT 15 SECONDS
	sleep(15);

	// DEFINE AGAIN $delmessage_i
	$delmessage_i = 1;

	while( $new_chat_member_length >= $delmessage_i ){

		MessageRequestJson("deleteMessage", array('chat_id' =>$chat_id,'message_id'=> $delmessage_i + $message_id ));

		$delmessage_i++;
	}
}

/// Delete WELCOME MESSAGES AFTER 5 SECOND. ( JUST ONE MESSAGE )
if ( $new_chat_member_length == 1 ) {

	// WAIT 15 SECONDS
	sleep(5);

	$after_message_id = 1 + $message_id;

	MessageRequestJson("deleteMessage", array('chat_id' =>$chat_id,'message_id'=>$after_message_id));
}

 */


/// Seccess Section  ////









/// Get Member Section  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

$new_chat_member = $update["message"]['new_chat_members'];
$new_chat_member_length = count($new_chat_member);

// Get Information from Data Base

$db = Db::getInstance();
$get_information = $db->query("SELECT * FROM addmember WHERE user_id=:user_id", array(
    'user_id' => $user_id,
	));


// get information for Registered user
// Check That User ID Exist


if( count ( $get_information[0][user_id] ) != Null ) {

 	// Get Status for this user id
    $status = $get_information[0][status];

	if ( $status == 2 ) {
		MessageRequestJson("restrictChatMember", array('chat_id' =>$chat_id,'user_id'=>$user_id,'permissions'=>array('can_send_messages' =>True, 'can_invite_users' => True)));
	}
    // Get the number of people he has already invited by this user id
    $add_num_member = $get_information[0][add_num_member];
    /*
     MessageRequestJson("sendMessage", array('chat_id' =>$chat_id,'parse_mode'=>'Markdown','text'=>"get info for old user"));
     */
}


// Set info for NEW USER
// User ID does'nt exist

elseif( count ( $get_information[0][user_id] ) == Null ) {
	// Set user id in Data Base
	$db = Db::getInstance();
        $db->insert("INSERT INTO addmember (user_id) VALUES (:user_id)", array(
        'user_id' => $user_id
         ));
    // Set add_num_member in Data Base
	$db = Db::getInstance();
        $db->modify("UPDATE addmember SET add_num_member=:add_num_member WHERE user_id=:user_id", array(
        'add_num_member' => 0,
        'user_id' => $user_id,
        ));
    // Set status in Data Base
	$db = Db::getInstance();
        $db->modify("UPDATE addmember SET status=:status WHERE user_id=:user_id", array(
        'status' => 0,
        'user_id' => $user_id,
        ));   /*
 MessageRequestJson("sendMessage", array('chat_id' =>$chat_id,'parse_mode'=>'Markdown','text'=>"set info for new user"));
 */
}



// We have already obtained the necessary information from the database or registered it in the database

if ($new_chat_member_length != 0){

	$db = Db::getInstance();
            $db->modify("UPDATE addmember SET status=:status WHERE user_id=:user_id", array(
            'status' => 1,
            'user_id' => $user_id,
            ));
    $status = 1; // This user id at least added one user so status == 1

	// Aggregate the number of people who have already been added and the number of people who have just been added
	// Add up all the numbers this user id has added
    $add_num_member = $add_num_member + $new_chat_member_length;


    // Set This add_num_member in Data Base
    $db = Db::getInstance();
            $db->modify("UPDATE addmember SET add_num_member=:add_num_member WHERE user_id=:user_id", array(
            'add_num_member' => $add_num_member,
            'user_id' => $user_id,
            ));

    // Check the number of people added to decrypt
	if ( $add_num_member > $invite_num ) {
	$db = Db::getInstance();
            $db->modify("UPDATE addmember SET status=:status WHERE user_id=:user_id", array(
            'status' => 2,
            'user_id' => $user_id,
            ));
    $status = 2;
	MessageRequestJson("restrictChatMember", array('chat_id' =>$chat_id,'user_id'=>$user_id,'permissions'=>array('can_send_messages' =>True, 'can_invite_users' => True)));
	if ( fmod( $add_num_member , 10 ) == 0 ) {
        MessageRequestJson("sendMessage", array('chat_id' =>$chat_id,'parse_mode'=>'Markdown','text'=>"کاربر  [$user_first_name $user_last_name](tg://user?id=$user_id) عزیز"." \n "."شما تا کنون ".$add_num_member." افزوده اید"." \n "."از شما متشکریم!"));
        sleep(4);
        MessageRequestJson("deleteMessage", array('chat_id' =>$chat_id,'message_id'=>$message_id + 1));
	}
	MessageRequestJson("sendMessage", array('chat_id' =>$reserved_user_1,'parse_mode'=>'Markdown','text'=>"کاربر  [$user_first_name $user_last_name](tg://user?id=$user_id) "." \n "."تعداد نفراتی که افزوده است : ".$add_num_member));
	MessageRequestJson("sendMessage", array('chat_id' =>$reserved_user_2,'parse_mode'=>'Markdown','text'=>"کاربر  [$user_first_name $user_last_name](tg://user?id=$user_id) "." \n "."تعداد نفراتی که افزوده است : ".$add_num_member));
	MessageRequestJson("sendMessage", array('chat_id' =>$reserved_user_3,'parse_mode'=>'Markdown','text'=>"کاربر  [$user_first_name $user_last_name](tg://user?id=$user_id) "." \n "."تعداد نفراتی که افزوده است : ".$add_num_member));

}
}

if ( $user_id != $reserved_user_1 &&  $user_id != $reserved_user_2 &&  $user_id != $reserved_user_3 && $user_id != $reserved_user_4 ) {
	// Delete messages from people who do not meet the requirements
	if ( $new_chat_member_length == 0 &&  $left_chat_member == NULL && $status != 2 ){
		MessageRequestJson("deleteMessage", array('chat_id' =>$chat_id,'message_id'=>$message_id));
		$two_min_wait = time() + (2* 60);
		MessageRequestJson("restrictChatMember", array('chat_id' =>$chat_id,'user_id'=>$user_id,'permissions'=>array('can_send_messages' =>False, 'can_invite_users' => True),'until_date'=>$two_min_wait));

		// Deleting notice after $sleep_time_10 second
	    	$after_message_id = $message_id + 1;
		MessageRequestJson("sendMessage", array('chat_id' =>$chat_id,'text'=>"تنها بعد از افزودن 20 عضو می توانید پیام دهید! 🟡"));
		sleep(3);
		MessageRequestJson("deleteMessage", array('chat_id' =>$chat_id,'message_id'=>$after_message_id));
		exit();

	}

	// Delete links on Time
	if ( $new_chat_member_length == 0 &&  $left_chat_member == NULL && $user_id != $reserved_user_1 ){

		if ( $message__type == "hashtag" || $message__type == "url" || $message__type == "mention" ) {

			MessageRequestJson("deleteMessage", array('chat_id' =>$chat_id,'message_id'=>$message_id));

			MessageRequestJson("sendMessage", array('chat_id' =>$chat_id,'parse_mode'=>'Markdown','text'=>"کاربر ⚠️ "."[$user_first_name $user_last_name](tg://user?id=$user_id)"." ⚠️"."
			از ارسال لینک ، آیدی یا هشتک های خاص به شدت خودداری نمایید!⚠️
			در صورت مشاهده دوباره دسترسی شما محدود خواهد شد.❌
			"));

			// deleting notice 
			$after_message_id_1 = $message_id + 1;
			$two_min_wait = time() + 24 * 60 * 60;
			sleep(5);
			MessageRequestJson("restrictChatMember", array('chat_id' =>$chat_id,'user_id'=>$user_id,'permissions'=>array('can_send_messages' =>False, 'can_invite_users' => True),'until_date'=>$two_min_wait));
			MessageRequestJson("deleteMessage", array('chat_id' =>$chat_id,'message_id'=>$after_message_id_1));
		}
	}
}


