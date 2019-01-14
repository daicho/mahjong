<?php
$groupid = "C9651813f9ebbaa6aa5beb0df65da63b6";
//$groupid = "U370e9c2081a4b305e756946b5f6313a5";
$access_token = "p92VGjm5eJxJdzbz/cwIu3ErYj0pTf50tFV/ESKg2mLCHi0fHPvuPSaQ0pKXHspeB9tO+CK/7VPcjJpbPLZ61tZzAe6uq4HLBDmHY4+3YVAKI/BQcsuRbt5OISbA3AzUZV+gUZ7uOpADST8FR2L9HwdB04t89/1O/w1cDnyilFU=";

echo("改行…%0a\n");

$send_text = $_GET["text"];

$post_data = [
	"to" => $groupid,
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
    "Authorization:Bearer " . $access_token
));
$result = curl_exec($ch);
curl_close($ch);
