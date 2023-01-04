<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <title>I am イーソー君</title>
</head>
<form method="post" action="./isokun.php">
  <p>内容<br>
    <textarea name="text" cols="30" rows="5"></textarea>
  </p>
  <p>
    <input type="submit" value="送信" style="width:80px; height:30px">
  </p>
</form>

<?php
$groupid = "C9651813f9ebbaa6aa5beb0df65da63b6";
//$groupid = "U370e9c2081a4b305e756946b5f6313a5";
$access_token = "p92VGjm5eJxJdzbz/cwIu3ErYj0pTf50tFV/ESKg2mLCHi0fHPvuPSaQ0pKXHspeB9tO+CK/7VPcjJpbPLZ61tZzAe6uq4HLBDmHY4+3YVAKI/BQcsuRbt5OISbA3AzUZV+gUZ7uOpADST8FR2L9HwdB04t89/1O/w1cDnyilFU=";

$send_text = $_POST["text"];

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
