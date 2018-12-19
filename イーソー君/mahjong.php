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
    	
    	$fname = "https://raw.githubusercontent.com/daicho/mahjong/master/" . urlencode("三人麻雀") . "/" . urlencode("成績") . "/" . urlencode($message_text) . ".csv";
    	$myfname = "record/" . $message_text . ".csv";
    	
    	file_put_contents($myfname, mb_convert_encoding(file_get_contents($fname), 'UTF-8', 'SJIS'));
    	
		$csv = new SplFileObject($myfname);
		$csv->setFlags(SplFileObject::READ_CSV);
		
		foreach ($csv as $row) {
		    if (!is_null($row))
		    	$data[] = $row;
		}
		
		$send_text  = "【" . $data[1][0]  . "】" . $data[1][1]  . "\n";
		$send_text .= "【" . $data[2][0]  . "】" . $data[2][1]  . "\n";
		$send_text .= "【" . $data[3][0]  . "】" . $data[3][1]  . "\n";
		$send_text .= "【" . $data[4][0]  . "】" . $data[4][1]  . "\n";
		$send_text .= "【" . $data[5][0]  . "】" . $data[5][1]  . "\n";
		$send_text .= "【" . $data[6][0]  . "】" . $data[6][1]  . " / " . $data[6][2]  . "\n";
		$send_text .= "【" . $data[7][0]  . "】" . $data[7][1]  . " / " . $data[7][2]  . "\n";
		$send_text .= "【" . $data[8][0]  . "】" . $data[8][1]  . " / " . $data[8][2]  . "\n";
		$send_text .= "【" . $data[9][0]  . "】" . $data[9][1]  . " / " . $data[9][2]  . "\n";
		$send_text .= "【" . $data[11][0] . "】" . $data[11][2] . "\n";
		$send_text .= "【" . $data[12][0] . "】" . $data[12][2] . "\n";
		$send_text .= "【" . $data[13][0] . "】" . $data[13][2] . "\n";
		$send_text .= "【" . $data[14][0] . "】" . $data[14][2] . "\n";
		$send_text .= "【" . $data[15][0] . "】" . $data[15][1] . "\n";
		$send_text .= "【" . $data[16][0] . "】" . $data[16][1];
    	
		$post_data = [
			"to" => $source_id,
			"messages" => [
				[
					"type" => "text",
				    "text" => $send_text
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
