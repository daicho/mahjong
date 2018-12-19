<?php
define("ACCESS_TOKEN", "p92VGjm5eJxJdzbz/cwIu3ErYj0pTf50tFV/ESKg2mLCHi0fHPvuPSaQ0pKXHspeB9tO+CK/7VPcjJpbPLZ61tZzAe6uq4HLBDmHY4+3YVAKI/BQcsuRbt5OISbA3AzUZV+gUZ7uOpADST8FR2L9HwdB04t89/1O/w1cDnyilFU=");
define("USER_ID", "U370e9c2081a4b305e756946b5f6313a5");

// APIから送信されてきたJSONを取得
$line_json = file_get_contents("php://input");
$line_obj = json_decode($line_json);
$event = $line_obj->events[0];
$event_type = $event->type;
$source_id = $event->source->userId;
$replytoken = $event->replyToken;

$post_data = [
	"to" => USER_ID,
	"messages" => [
		[
			"type" => "text",
		    "text" => $line_json
		]
	]
];

// curlを使用してメッセージを返信する
$ch = curl_init("https://api.line.me/v2/bot/message/push");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	"Content-Type:application/json",
    "Authorization:Bearer " . ACCESS_TOKEN
));
$result = curl_exec($ch);
curl_close($ch);

if ($event_type == "message") {
    $message_type = $event->message->type;

    if ($message_type == "text") {
    	$message_text = $event->message->text;
    	
    	$csv = file_get_contents(urlencode("https://raw.githubusercontent.com/daicho/mahjong/master/三人麻雀/成績/いっしー.csv"));
    	//$csv = file_get_contents("http://sekiei.jp/mysql/unko.php");
    	
		$post_data = [
			"to" => $source_id,
			"messages" => [
				[
					"type" => "text",
				    "text" => $csv
				]
			]
		];
		
		// curlを使用してメッセージを返信する
		$ch = curl_init("https://api.line.me/v2/bot/message/push");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type:application/json",
		    "Authorization:Bearer " . ACCESS_TOKEN
		));
		$result = curl_exec($ch);
		curl_close($ch);
	}
}
