<?php
ob_start();
if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
	echo "madeline.php copied";
}

$settings = 
	[
		'logger' => [
			'logger' => 0
		],
		'app_info' => [
				'device_model' => 'Madeline',
				'system_version' => ''.rand(1,10),
				'app_version' => '1',
				'lang_code' => 'en',
				'api_id' => 397808,
				'api_hash' => '93f00a035db6b5f7483442f506393b48'
		],


	];
require_once 'madeline.php';
function checkfarsi($string){
    if(preg_match("/[0-9a-zA-Z]+/i", $string)){
        return "en";
    }else{
        return "fa";
    }
}
function we($typew){
	if($typew == "Clear"){
		return "آفتابی☀";
	}
	elseif($typew == "Clouds"){
		return "ابری ☁☁";
	}
	elseif($typew == "Rain"){
		 return "بارانی ☔";
	}
	elseif($typew == "Thunderstorm"){
		return "طوفانی ☔☔☔☔";
	}
	elseif($typew == "Mist"){
		return "مه 💨";
	}
}
//ایدی عددی ادمین
$admin = 518492828;
//Loging in
$MadelineProto = new \danog\MadelineProto\API('session.madeline', $settings);
$MadelineProto->start();
while (true) {
	try{
		$updates = $MadelineProto->get_updates(['offset' => -1, 'limit' => 50, 'timeout' => 0]); // Just like in the bot API, you can specify an offset, a limit and a timeout
		\danog\MadelineProto\Logger::log($updates);
		foreach ($updates as $update) {
			$data = json_decode(file_get_contents("data.json"), true);
			$step = $data['adminStep'];
			$offset = $update['update_id'] + 1; // Just like in the bot API, the offset must be set to the last update_id
			switch ($update['update']['_']) {
				case 'updateNewMessage':
				case 'updateNewChannelMessage':
				case 'updateEditChannelMessage':
				case 'updateEditMessage':
					if(isset($update['update'])){
						$up = $update['update'];
						if(isset($update['update']['message'])){
							$message = $up['message'];
						}
						if(isset($update['update']['message']['message'])){
							$text = strtolower($up['message']['message']);
						}
						if(isset($update['update']['message']['from_id'])){
							$from_id = $up['message']['from_id'];
							$peer = $from_id;
						}
						if(isset($update['update']['message']['to_id']['channel_id'])){
							$channel_id = $up['message']['to_id']['channel_id'];
							$peer = "-100".$channel_id;
						}
					}
					if($from_id == $admin){
						if(preg_match("/^[\/\#\!]?(bot) (on|off)$/i", $text)){
							preg_match("/^[\/\#\!]?(bot) (on|off)$/i", $text, $m);
							$data['power'] = $m[2];
							file_put_contents("data.json", json_encode($data));
							$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Bot Now Is $m[2]", ]);
						}
						if($data['power'] == "on"){
							if(preg_match("/^[\/\#\!]?(send2all)$/i", $text)){
								$data['adminStep'] = "send2all";
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Send Me The Message", ]);
							}
							else if($step == "send2all"){
								$dialogs = $MadelineProto->get_dialogs();
								foreach ($dialogs as $peeer) {
									$peer_info = $MadelineProto->get_info($peeer);
									$peer_type = $peer_info['type'];
									if($peer_info == "supergroup" ||$peer_info == "user"||$peer_info == "chat"){
										$MadelineProto->messages->sendMessage(['peer' => $peeer, 'message' => $text]);
									}
								}
								$data['adminStep'] = "none";
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "sent"]);
							}
							
							else if(preg_match("/^[\/\#\!]?(forward2all)$/i", $text)){
								$data['adminStep'] = "forward2all";
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => 'Forward Me The Message']);
							}
							else if($step == "forward2all"){
								$dialogs = $MadelineProto->get_dialogs();
								foreach ($dialogs as $peeer) {
									$peer_info = $MadelineProto->get_info($peeer);
									$peer_type = $peer_info['type'];
									if($peer_info == "supergroup" ||$peer_info == "user"||$peer_info == "chat"){
										$MadelineProto->messages->forwardMessages(['from_peer' => $peer, 'to_peer' => $peeer, 'id' => [$message['id']], ]);
									}
								}
								$data['adminStep'] = "none";
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "forwarded"]);
							}
							else if(preg_match("/^[\/\#\!]?(save)$/i", $text) && isset($message['reply_to_msg_id'])){
								$me = $MadelineProto->get_self();
								$me_id = $me['id'];
								$MadelineProto->messages->forwardMessages(['from_peer' => $peer, 'to_peer' => $me_id, 'id' => [$message['reply_to_msg_id']], ]);
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "> Saved :D"]);
							}
							else if(preg_match("/^[\/\#\!]?(typing) (on|off)$/i", $text)){
								preg_match("/^[\/\#\!]?(typing) (on|off)$/i", $text, $m);
								$data['typing'] = $m[2];
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Typing Now Is $m[2]", ]);
							}
							else if(preg_match("/^[\/\#\!]?(poker) (on|off)$/i", $text)){
								preg_match("/^[\/\#\!]?(poker) (on|off)$/i", $text, $m);
								$data['poker'] = $m[2];
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Poker Now Is $m[2]", ]);
							}
							else if(preg_match("/^[\/\#\!]?(echo) (on|off)$/i", $text)){
								preg_match("/^[\/\#\!]?(echo) (on|off)$/i", $text, $m);
								$data['echo'] = $m[2];
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Echo Now Is $m[2]", ]);
							}
							else if(preg_match("/^[\/\#\!]?(markread) (on|off)$/i", $text)){
								preg_match("/^[\/\#\!]?(markread) (on|off)$/i", $text, $m);
								$data['markread'] = $m[2];
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Markread Now Is $m[2]", ]);
							}
							else if(preg_match("/^[\/\#\!]?(me)$/i", $text)){
								$me = $MadelineProto->get_self();
								$me_id = $me['id'];
								$me_name = $me['first_name'];
								$me_uname = $me['username'];
								$mes = "ID: $me_id \nName: $me_name \nUsername: @$me_uname";
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $mes]);
							}
							else if(preg_match("/^[\/\#\!]?(info) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(info) (.*)$/i", $text, $m);
								$mee = $MadelineProto->get_full_info($m[2]);
								$me = $mee['User'];
								$me_id = $me['id'];
								$me_status = $me['status']['_'];
								$me_bio = $mee['full']['about'];
								$me_common = $mee['full']['common_chats_count'];
								$me_name = $me['first_name'];
								$me_uname = $me['username']; 
								$mes = "ID: $me_id \nName: $me_name \nUsername: @$me_uname \nStatus: $me_status \nBio: $me_bio \nCommon Groups Count: $me_common";
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $mes]);
							}
							else if(preg_match("/^[\/\#\!]?(block) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(block) (.*)$/i", $text, $m);
								$MadelineProto->contacts->block(['id' => $m[2], ]);
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Blocked!"]);
							}
							else if(preg_match("/^[\/\#\!]?(unblock) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(unblock) (.*)$/i", $text, $m);
								$MadelineProto->contacts->unblock(['id' => $m[2], ]);
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "UnBlocked!"]);
							}
							else if(preg_match("/^[\/\#\!]?(checkusername) (@.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(checkusername) (@.*)$/i", $text, $m);
								$check = $MadelineProto->account->checkUsername(['username' => str_replace("@", "", $m[2]), ]);
								if($check == false){
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Exists!"]);
								} else{
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Free!"]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(setfirstname) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(setfirstname) (.*)$/i", $text, $m);
								$MadelineProto->account->updateProfile(['first_name' => $m[2]]);
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Done!"]);
							}
							else if(preg_match("/^[\/\#\!]?(setlastname) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(setlastname) (.*)$/i", $text, $m);
								$MadelineProto->account->updateProfile(['last_name' => $m[2]]);
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Done!"]);
							}
							else if(preg_match("/^[\/\#\!]?(setbio) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(setbio) (.*)$/i", $text, $m);
								$MadelineProto->account->updateProfile(['about' => $m[2]]);
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Done!"]);
							}
							else if(preg_match("/^[\/\#\!]?(setusername) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(setusername) (.*)$/i", $text, $m);
								$MadelineProto->account->updateUsername(['username' => $m[2]]);
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Done!"]);
							}
							else if(preg_match("/^[\/\#\!]?(join) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(join) (.*)$/i", $text, $m);
								$MadelineProto->channels->joinChannel(['channel' => $m[2], ]);
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Joined!"]);
							}
							else if(preg_match("/^[\/\#\!]?(chats)$/i", $text)){
								$dialogs = json_encode($MadelineProto->get_dialogs());
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => ''.$dialogs]);
							}
							else if(preg_match("/^[\/\#\!]?(add2all) (@.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(add2all) (@.*)$/i", $text, $m);
								$dialogs = $MadelineProto->get_dialogs();
								foreach ($dialogs as $peeer) {
									$peer_info = $MadelineProto->get_info($peeer);
									$peer_type = $peer_info['type'];
									if($peer_type == "supergroup"){
										$MadelineProto->channels->inviteToChannel(['channel' => $peeer, 'users' => [$m[2]], ]);
									}
								}
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Added To All SuperGroups"]);
							}
							else if(preg_match("/^[\/\#\!]?(newanswer) (.*) \|\|\| (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(newanswer) (.*) \|\|\| (.*)$/i", $text, $m);
								$txxt = $m[2];
								$answeer = $m[3];
								if(!isset($data['answering'][$txxt])){
									$data['answering'][$txxt] = $answeer;
									file_put_contents("data.json", json_encode($data));
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "New Word Added To AnswerList"]);
								} else{
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "This Word Was In AnswerList"]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(delanswer) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(delanswer) (.*)$/i", $text, $m);
								$txxt = $m[2];
								if(isset($data['answering'][$txxt])){
									unset($data['answering'][$txxt]);
									file_put_contents("data.json", json_encode($data));
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Word Deleted From AnswerList"]);
								} else{
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "This Word Wasn't In AnswerList"]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(clean answers)$/i", $text)){
								$data['answering'] = [];
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "AnswerList Is Now Empty!"]);
							}
							else if(preg_match("/^[\/\#\!]?(answerlist)$/i", $text)){
								if(count($data['answering']) > 0){
									$txxxt = "AnswerList: 
									";
									$counter = 1;
									foreach($data['answering'] as $k => $ans){
										$txxxt .= "$counter: $k => $ans \n";
										$counter++;
									}
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $txxxt]);
								} else{
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "No Answer!"]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(setenemy) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(setenemy) (.*)$/i", $text, $m);
								$mee = $MadelineProto->get_full_info($m[2]);
								$me = $mee['User'];
								$me_id = $me['id'];
								$me_name = $me['first_name'];
								if(!in_array($me_id, $data['enemies'])){
									$data['enemies'][] = $me_id;
									file_put_contents("data.json", json_encode($data));
									$MadelineProto->contacts->block(['id' => $m[2], ]);
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "$me_name is now in enemy list"]);
								} else{
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "This User Was In EnemyList"]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(delenemy) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(delenemy) (.*)$/i", $text, $m);
								$mee = $MadelineProto->get_full_info($m[2]);
								$me = $mee['User'];
								$me_id = $me['id'];
								$me_name = $me['first_name'];
								if(in_array($me_id, $data['enemies'])){
									$k = array_search($me_id, $data['enemies']);
									unset($data['enemies'][$k]);
									file_put_contents("data.json", json_encode($data));
									$MadelineProto->contacts->unblock(['id' => $m[2], ]);
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "$me_name deleted from enemy list"]);
								} else{
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "This User Wasn't In EnemyList"]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(clean enemylist)$/i", $text)){
								$data['enemies'] = [];
								file_put_contents("data.json", json_encode($data));
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "EnemyList Is Now Empty!"]);
							}
							else if(preg_match("/^[\/\#\!]?(enemylist)$/i", $text)){
								if(count($data['enemies']) > 0){
									$txxxt = "EnemyList: 
									";
									$counter = 1;
									foreach($data['enemies'] as $ene){
										$mee = $MadelineProto->get_full_info($ene);
										$me = $mee['User'];
										$me_name = $me['first_name'];
										$txxxt .= "$counter: $me_name \n";
										$counter++;
									}
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $txxxt]);
								} else{
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "No Enemy!"]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(inv) (@.*)$/i", $text) && $update['update']['_'] == "updateNewChannelMessage"){
								preg_match("/^[\/\#\!]?(inv) (@.*)$/i", $text, $m);
								$peer_info = $MadelineProto->get_info($message['to_id']);
								$peer_type = $peer_info['type'];
								if($peer_type == "supergroup"){
									$MadelineProto->channels->inviteToChannel(['channel' => $message['to_id'], 'users' => [$m[2]], ]);
								} else{
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Just SuperGroups"]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(leave)$/i", $text)){
								$MadelineProto->channels->leaveChannel(['channel' => $message['to_id'], ]);
							}
							else if(preg_match("/^[\/\#\!]?(flood) ([0-9]+) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(flood) ([0-9]+) (.*)$/i", $text, $m);
								$count = $m[2];
								$txt = $m[3];
								$spm = "";
								for($i=1; $i <= $count; $i++){
									$spm .= "$txt \n";
								}
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $spm]);
							}
							else if(preg_match("/^[\/\#\!]?(flood2) ([0-9]+) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(flood2) ([0-9]+) (.*)$/i", $text, $m);
								$count = $m[2];
								$txt = $m[3];
								for($i=1; $i <= $count; $i++){
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $txt]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(music) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(music) (.*)$/i", $text, $m);
								$mu = $m[2];
								$messages_BotResults = $MadelineProto->messages->getInlineBotResults(['bot' => "@melobot", 'peer' => $peer, 'query' => $mu, 'offset' => '0', ]);
								$query_id = $messages_BotResults['query_id'];
								$query_res_id = $messages_BotResults['results'][rand(0, count($messages_BotResults['results']))]['id'];
								$MadelineProto->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'reply_to_msg_id' => $message['id'], 'query_id' => $query_id, 'id' => "$query_res_id", ]);
							}
							else if(preg_match("/^[\/\#\!]?(wiki) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(wiki) (.*)$/i", $text, $m);
								$mu = $m[2];
								$messages_BotResults = $MadelineProto->messages->getInlineBotResults(['bot' => "@wiki", 'peer' => $peer, 'query' => $mu, 'offset' => '0', ]);
								$query_id = $messages_BotResults['query_id'];
								$query_res_id = $messages_BotResults['results'][rand(0, count($messages_BotResults['results']))]['id'];
								$MadelineProto->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'reply_to_msg_id' => $message['id'], 'query_id' => $query_id, 'id' => "$query_res_id", ]);
							}
							else if(preg_match("/^[\/\#\!]?(youtube) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(youtube) (.*)$/i", $text, $m);
								$mu = $m[2];
								$messages_BotResults = $MadelineProto->messages->getInlineBotResults(['bot' => "@uVidBot", 'peer' => $peer, 'query' => $mu, 'offset' => '0', ]);
								$query_id = $messages_BotResults['query_id'];
								$query_res_id = $messages_BotResults['results'][rand(0, count($messages_BotResults['results']))]['id'];
								$MadelineProto->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'reply_to_msg_id' => $message['id'], 'query_id' => $query_id, 'id' => "$query_res_id", ]);
							}
							else if(preg_match("/^[\/\#\!]?(pic) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(pic) (.*)$/i", $text, $m);
								$mu = $m[2];
								$messages_BotResults = $MadelineProto->messages->getInlineBotResults(['bot' => "@pic", 'peer' => $peer, 'query' => $mu, 'offset' => '0', ]);
								$query_id = $messages_BotResults['query_id'];
								$query_res_id = $messages_BotResults['results'][rand(0, count($messages_BotResults['results']))]['id'];
								$MadelineProto->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'reply_to_msg_id' => $message['id'], 'query_id' => $query_id, 'id' => "$query_res_id", ]);
							}
							else if(preg_match("/^[\/\#\!]?(gif) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(gif) (.*)$/i", $text, $m);
								$mu = $m[2];
								$messages_BotResults = $MadelineProto->messages->getInlineBotResults(['bot' => "@gif", 'peer' => $peer, 'query' => $mu, 'offset' => '0', ]);
								$query_id = $messages_BotResults['query_id'];
								$query_res_id = $messages_BotResults['results'][rand(0, count($messages_BotResults['results']))]['id'];
								$MadelineProto->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'reply_to_msg_id' => $message['id'], 'query_id' => $query_id, 'id' => "$query_res_id", ]);
							}
							else if(preg_match("/^[\/\#\!]?(google) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(google) (.*)$/i", $text, $m);
								$mu = $m[2];
								$messages_BotResults = $MadelineProto->messages->getInlineBotResults(['bot' => "@GoogleDEBot", 'peer' => $peer, 'query' => $mu, 'offset' => '0', ]);
								$query_id = $messages_BotResults['query_id'];
								$query_res_id = $messages_BotResults['results'][rand(0, count($messages_BotResults['results']))]['id'];
								$MadelineProto->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'reply_to_msg_id' => $message['id'], 'query_id' => $query_id, 'id' => "$query_res_id", ]);
							}
							else if(preg_match("/^[\/\#\!]?(joke)$/i", $text)){
								preg_match("/^[\/\#\!]?(joke)$/i", $text, $m);
								$messages_BotResults = $MadelineProto->messages->getInlineBotResults(['bot' => "@function_robot", 'peer' => $peer, 'query' => '', 'offset' => '0', ]);
								$query_id = $messages_BotResults['query_id'];
								$query_res_id = $messages_BotResults['results'][0]['id'];
								$MadelineProto->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'reply_to_msg_id' => $message['id'], 'query_id' => $query_id, 'id' => "$query_res_id", ]);
							}
							else if(preg_match("/^[\/\#\!]?(aasab)$/i", $text)){
								preg_match("/^[\/\#\!]?(aasab)$/i", $text, $m);
								$messages_BotResults = $MadelineProto->messages->getInlineBotResults(['bot' => "@function_robot", 'peer' => $peer, 'query' => '', 'offset' => '0', ]);
								$query_id = $messages_BotResults['query_id'];
								$query_res_id = $messages_BotResults['results'][1]['id'];
								$MadelineProto->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'reply_to_msg_id' => $message['id'], 'query_id' => $query_id, 'id' => "$query_res_id", ]);
							}
							else if(preg_match("/^[\/\#\!]?(like) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(like) (.*)$/i", $text, $m);
								$mu = $m[2];
								$messages_BotResults = $MadelineProto->messages->getInlineBotResults(['bot' => "@like", 'peer' => $peer, 'query' => $mu, 'offset' => '0', ]);
								$query_id = $messages_BotResults['query_id'];
								$query_res_id = $messages_BotResults['results'][0]['id'];
								$MadelineProto->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'reply_to_msg_id' => $message['id'], 'query_id' => $query_id, 'id' => "$query_res_id", ]);
							}
							else if(preg_match("/^[\/\#\!]?(search) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(search) (.*)$/i", $text, $m);
								$q = $m[2];
								$res_search = $MadelineProto->messages->search(['peer' => $peer, 'q' => $q, 'filter' => ['_' => 'inputMessagesFilterEmpty'], 'min_date' => 0, 'max_date' => time(), 'offset_id' => 0, 'add_offset' => 0, 'limit' => 50, 'max_id' => $message['id'], 'min_id' => 1, ]);
								$msgs_count = count($res_search['messages']);
								$users_count = count($res_search['users']);
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "Msgs Found: $msgs_count \nFrom Users Count: $users_count"]);
								foreach($res_search['messages'] as $msg){
									$msgid = $msg['id'];
									$MadelineProto->messages->forwardMessages(['from_peer' => $msg, 'to_peer' => $peer, 'id' => [$msgid], ]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(weather) (.*)$/i", $text)){
								preg_match("/^[\/\#\!]?(weather) (.*)$/i", $text, $m);
								$query = $m[2];
								$url = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=".$query."&appid=eedbc05ba060c787ab0614cad1f2e12b&units=metric"), true);
								$city = $url["name"];
								$deg = $url["main"]["temp"];
								$type1 = $url["weather"][0]["main"];
								$type = we($type1);
								if($city != ""){
									$txt = "دمای شهر $city هم اکنون $deg درجه سانتی گراد می باشد

شرایط فعلی آب و هوا: $type";
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $txt]);
								}else{
									$txt = "⚠️شهر مورد نظر شما يافت نشد";
									$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $txt]);
								}
							}
							else if(preg_match("/^[\/\#\!]?(sessions)$/i", $text)){
								$authorizations = $MadelineProto->account->getAuthorizations();
								$txxt="";
								foreach($authorizations['authorizations'] as $authorization){
									$txxt .="
hash: ".$authorization['hash']."
device_model: ".$authorization['device_model']."
platform: ".$authorization['platform']."
system_version: ".$authorization['system_version']."
api_id: ".$authorization['api_id']."
app_name: ".$authorization['app_name']."
app_version: ".$authorization['app_version']."
date_created: ".date("Y-m-d H:i:s",$authorization['date_active'])."
date_active: ".date("Y-m-d H:i:s",$authorization['date_active'])."
ip: ".$authorization['ip']."
country: ".$authorization['country']."
region: ".$authorization['region']."
======================
									";
								}
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $txxt]);
							}
							else if(preg_match("/^[\/\#\!]?(gpinfo)$/i", $text)){
								$peer_inf = $MadelineProto->get_full_info($message['to_id']);
								$peer_info = $peer_inf['Chat'];
								$peer_id = $peer_info['id'];
								$peer_title = $peer_info['title'];
								$peer_type = $peer_inf['type'];
								$peer_count = $peer_inf['full']['participants_count'];
								$des = $peer_inf['full']['about'];
								$mes = "ID: $peer_id \nTitle: $peer_title \nType: $peer_type \nMembers Count: $peer_count \nBio: $des";
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $mes]);
							}
						}
					}
					if($data['power'] == "on"){
						if($message && $data['typing'] == "on" && $update['update']['_'] == "updateNewChannelMessage"){
								$sendMessageTypingAction = ['_' => 'sendMessageTypingAction'];
								$MadelineProto->messages->setTyping(['peer' => $peer, 'action' => $sendMessageTypingAction, ]);
							}
							if($message && $data['echo'] == "on"){
								$MadelineProto->messages->forwardMessages(['from_peer' => $peer, 'to_peer' => $peer, 'id' => [$message['id']], ]);
							}
							if($message && $data['markread'] == "on"){
								if(intval($peer) < 0){
									$MadelineProto->channels->readHistory(['channel' => $peer, 'max_id' => $message['id'], ]);
									$MadelineProto->channels->readMessageContents(['channel' => $peer, 'id' => [$message['id']] ]);
								} else{
									$MadelineProto->messages->readHistory(['peer' => $peer, 'max_id' => $message['id'], ]);
								}
							}
							if(strpos($text, "😐") !== false && $data['poker'] == "on"){
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "😐", 'reply_to_msg_id' => $message['id']]);
							}
							$fohsh = [
"گص کش",
"کس ننه",
"کص ننت",
"کس خواهر",
"کس خوار",
"کس خارت",
"کس ابجیت",
"کص لیس",
"ساک بزن",
"ساک مجلسی",
"ننه الکسیس",
"نن الکسیس",
"ناموستو گاییدم",
"ننه زنا",
"کس خل",
"کس مخ",
"کس مغز",
"کس مغذ",
"خوارکس",
"خوار کس",
"خواهرکس",
"خواهر کس",
"حروم زاده",
"حرومزاده",
"خار کس",
"تخم سگ",
"پدر سگ",
"پدرسگ",
"پدر صگ",
"پدرصگ",
"ننه سگ",
"نن سگ",
"نن صگ",
"ننه صگ",
"ننه خراب",
"تخخخخخخخخخ",
"نن خراب",
"مادر سگ",
"مادر خراب",
"مادرتو گاییدم",
"تخم جن",
"تخم سگ",
"مادرتو گاییدم",
"ننه حمومی",
"نن حمومی",
"نن گشاد",
"ننه گشاد",
"نن خایه خور",
"تخخخخخخخخخ",
"نن ممه",
"کس عمت",
"کس کش",
"کس بیبیت",
"کص عمت",
"کص خالت",
"کس بابا",
"کس خر",
"کس کون",
"کس مامیت",
"کس مادرن",
"مادر کسده",
"خوار کسده",
"تخخخخخخخخخ",
"ننه کس",
"بیناموس",
"بی ناموس",
"شل ناموس",
"سگ ناموس",
"ننه جندتو گاییدم باو ",
"چچچچ نگاییدم سیک کن پلیز D:",
"ننه حمومی",
"چچچچچچچ",
"لز ننع",
"ننه الکسیس",
"کص ننت",
"بالا باش",
"ننت رو میگام",
"کیرم از پهنا تو کص ننت",
"مادر کیر دزد",
"ننع حرومی",
"تونل تو کص ننت",
"کیر تک تک بکس تلع گلد تو کص ننت",
"کص خوار بدخواه",
"خوار کصده",
"ننع باطل",
"حروم لقمع",
"ننه سگ ناموس",
"منو ننت شما همه چچچچ",
"ننه کیر قاپ زن",
"ننع اوبی",
"ننه کیر دزد",
"ننه کیونی",
"ننه کصپاره",
"زنا زادع",
"کیر سگ تو کص نتت پخخخ",
"ولد زنا",
"ننه خیابونی",
"هیس بع کس حساسیت دارم",
"کص نگو ننه سگ که میکنمتتاااا",
"کص نن جندت",
"ننه سگ",
"ننه کونی",
"ننه زیرابی",
"بکن ننتم",
"ننع فاسد",
"ننه ساکر",
"کس ننع بدخواه",
"نگاییدم",
"مادر سگ",
"ننع شرطی",
"گی ننع",
"بابات شاشیدتت چچچچچچ",
"ننه ماهر",
"حرومزاده",
"ننه کص",
"کص ننت باو",
"پدر سگ",
"سیک کن کص ننت نبینمت",
"کونده",
"ننه ولو",
"ننه سگ",
"مادر جنده",
"کص کپک زدع",
"ننع لنگی",
"ننه خیراتی",
"سجده کن سگ ننع",
"ننه خیابونی",
"ننه کارتونی",
"تکرار میکنم کص ننت",
"تلگرام تو کس ننت",
"کص خوارت",
"خوار کیونی",
"پا بزن چچچچچ",
"مادرتو گاییدم",
"گوز ننع",
"کیرم تو دهن ننت",
"ننع همگانی",
"کیرم تو کص زیدت",
"کیر تو ممهای ابجیت",
"ابجی سگ",
"کس دست ریدی با تایپ کردنت چچچ",
"ابجی جنده",
"ننع سگ سیبیل",
"بده بکنیم چچچچ",
"کص ناموس",
"شل ناموس",
"ریدم پس کلت چچچچچ",
"ننه شل",
"ننع قسطی",
"ننه ول",
"دست و پا نزن کس ننع",
"ننه ولو",
"خوارتو گاییدم",
"محوی!؟",
"ننت خوبع!؟",
"کس زنت",
"شاش ننع",
"ننه حیاطی /:",
"نن غسلی",
"کیرم تو کس ننت بگو مرسی چچچچ",
"ابم تو کص ننت :/",
"فاک یور مادر خوار سگ پخخخ",
"کیر سگ تو کص ننت",
"کص زن",
"ننه فراری",
"بکن ننتم من باو جمع کن ننه جنده /:::",
"ننه جنده بیا واسم ساک بزن",
"حرف نزن که نکنمت هااا :|",
"کیر تو کص ننت😐",
"کص کص کص ننت😂",
"کصصصص ننت جووون",
"سگ ننع",
"کص خوارت",
"کیری فیس",
"کلع کیری",
"تیز باش سیک کن نبینمت",
"فلج تیز باش چچچ",
"بیا ننتو ببر",
"بکن ننتم باو ",
"کیرم تو بدخواه",
"چچچچچچچ",
"ننه جنده",
"ننه کص طلا",
"ننه کون طلا",
"کس ننت بزارم بخندیم!؟",
"کیرم دهنت",
"مادر خراب",
"ننه کونی",
"هر چی گفتی تو کص ننت خخخخخخخ",
"کص ناموست بای",
"کص ننت بای ://",
"کص ناموست باعی تخخخخخ",
"کون گلابی!",
"ریدی آب قطع",
"کص کن ننتم کع",
"نن کونی",
"نن خوشمزه",
"ننه لوس",
" نن یه چشم ",
"ننه چاقال",
"ننه جینده",
"ننه حرصی ",
"نن لشی",
"ننه ساکر",
"نن تخمی",
"ننه بی هویت",
"نن کس",
"نن سکسی",
"نن فراری",
"لش ننه",
"سگ ننه",
"شل ننه",
"ننه تخمی",
"ننه تونلی",
"ننه کوون",
"نن خشگل",
"نن جنده",
"نن ول ",
"نن سکسی",
"نن لش",
"کس نن ",
"نن کون",
"نن رایگان",
"نن خاردار",
"ننه کیر سوار",
"نن پفیوز",
"نن محوی",
"ننه بگایی",
"ننه بمبی",
"ننه الکسیس",
"نن خیابونی",
"نن عنی",
"نن ساپورتی",
"نن لاشخور",
"ننه طلا",
"ننه عمومی",
"ننه هر جایی",
"نن دیوث",
"تخخخخخخخخخ",
"نن ریدنی",
"نن بی وجود",
"ننه سیکی",
"ننه کییر",
"نن گشاد",
"نن پولی",
"نن ول",
"نن هرزه",
"نن دهاتی",
"ننه ویندوزی",
"نن تایپی",
"نن برقی",
"نن شاشی",
"ننه درازی",
"شل ننع",
"یکن ننتم که",
"کس خوار بدخواه",
"آب چاقال",
"ننه جریده",
"ننه سگ سفید",
"آب کون",
"ننه 85",
"ننه سوپری",
"بخورش",
"کس ن",
"خوارتو گاییدم",
"خارکسده",
"گی پدر",
"آب چاقال",
"زنا زاده",
"زن جنده",
"سگ پدر",
"مادر جنده",
"ننع کیر خور",
"چچچچچ",
"تیز بالا",
"ننه سگو با کسشر در میره",
"کیر سگ تو کص ننت",
"kos kesh",
"kir",
"kiri",
"nane lashi",
"kos",
"kharet",
"blis kirmo",
"دهاتی",
"کیرم لا کص خارت",
"کیری",
"ننه لاشی",
"ممه",
"کص",
"کیر",
"بی خایه",
"ننه لش",
"بی پدرمادر",
"خارکصده",
"مادر جنده",
"کصکش"
];
							if($message && in_array($from_id, $data['enemies'])){
								$f = $fohsh[rand(0, count($fohsh)-1)];
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $f, 'reply_to_msg_id' => $message['id']]);
							}
							if(isset($data['answering'][$text])){
								$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $data['answering'][$text] , 'reply_to_msg_id' => $message['id']]);
							}
					}
					break;
			}
			//file_put_contents("data.json", json_encode($data));
		}
	} catch(Exception $e){
		$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => "❌ ERROR: \n$e"]);
	} 
	catch(\danog\MadelineProto\RPCErrorException $e){
		$estring = (string) $e;
		$MadelineProto->messages->sendMessage(['peer' => $up, 'message' => "❌ ERROR: \n$e"]);
	}
	catch(\danog\MadelineProto\Exception $e){
		$estring = (string) $e;
		$MadelineProto->messages->sendMessage(['peer' => $up, 'message' => "❌ ERROR: \n$e"]);
	}
	catch(\danog\MadelineProto\TL\Exception $e){
		$estring = (string) $e;
		$MadelineProto->messages->sendMessage(['peer' => $up, 'message' => "❌ ERROR: \n$e"]);
	}
	catch(\danog\MadelineProto\NothingInTheSocketException $e){
		$estring = (string) $e;
		$MadelineProto->messages->sendMessage(['peer' => $up, 'message' => "❌ ERROR: \n$e"]);
	}
	catch(\danog\MadelineProto\PTSException $e){
		$estring = (string) $e;
		$MadelineProto->messages->sendMessage(['peer' => $up, 'message' => "❌ ERROR: \n$e"]);
	}
	catch(\danog\MadelineProto\SecurityException $e){
		$estring = (string) $e;
		$MadelineProto->messages->sendMessage(['peer' => $up, 'message' => "❌ ERROR: \n$e"]);
	}
	catch(\danog\MadelineProto\TL\Conversion\Exception $e){
		$estring = (string) $e;
		$MadelineProto->messages->sendMessage(['peer' => $up, 'message' => "❌ ERROR: \n$e"]);
	}
}

?>
