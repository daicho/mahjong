<?php
define("ACCESS_TOKEN", "p92VGjm5eJxJdzbz/cwIu3ErYj0pTf50tFV/ESKg2mLCHi0fHPvuPSaQ0pKXHspeB9tO+CK/7VPcjJpbPLZ61tZzAe6uq4HLBDmHY4+3YVAKI/BQcsuRbt5OISbA3AzUZV+gUZ7uOpADST8FR2L9HwdB04t89/1O/w1cDnyilFU=");
define("USER_ID", "U370e9c2081a4b305e756946b5f6313a5");

$rank_str = ["通算スコア", "平均スコア", "平均順位", "トップ率", "ふっとび率", "アガリ率", "放銃率", "平均アガリ点", "平均放銃点", "副露率"];

// APIから送信されてきたJSONを取得
$line_json = file_get_contents("php://input");
$line_obj = json_decode($line_json);
$event = $line_obj->events[0];
$event_type = $event->type;
$source_id = $event->source->userId;
$replytoken = $event->replyToken;

/*
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
*/

if ($event_type == "message") {
    $message_type = $event->message->type;

    if ($message_type == "text") {
        $message_text = $event->message->text;

        $send_text = "";

        if ($message_text == "使い方") {
            $send_text .= "(名前)";
            for ($i = 0; $i < count($rank_str); $i++)
                $send_text .= "\n" . $rank_str[$i];
        }

        for ($i = 0; $i < count($rank_str); $i++) {
            if ($message_text == $rank_str[$i]) {
                $fname = "https://raw.githubusercontent.com/daicho/mahjong/master/" . urlencode("三人麻雀") . "/" . urlencode("成績") . "/" . urlencode("ランキング") . ".csv";
                $myfname = "record/ランキング.csv";

                if (file_get_contents($fname)) {
                    file_put_contents($myfname, mb_convert_encoding(file_get_contents($fname), 'UTF-8', 'SJIS'));

                    $csv = new SplFileObject($myfname);
                    $csv->setFlags(SplFileObject::READ_CSV);

                    foreach ($csv as $row) {
                        if (!is_null($row[0]))
                            $data[] = $row;
                    }

                    $send_text .= $data[0][$i * 2 + 1];
                    for ($j = 1; $j < count($data); $j++)
                        $send_text .= "\n" . "【" . $data[$j][$i * 2 + 1] . "】" . $data[$j][$i * 2 + 2];
                }
            }
        }

        $fname = "https://raw.githubusercontent.com/daicho/mahjong/master/" . urlencode("三人麻雀") . "/" . urlencode("成績") . "/" . urlencode($message_text) . ".csv";
        $graph_url = "https://raw.githubusercontent.com/daicho/mahjong/master/" . urlencode("三人麻雀") . "/" . urlencode("グラフ") . "/" . urlencode($message_text) . ".png";
        $myfname = "record/" . $message_text . ".csv";

        if (file_get_contents($fname)) {
            file_put_contents($myfname, mb_convert_encoding(file_get_contents($fname), 'UTF-8', 'SJIS'));

            $csv = new SplFileObject($myfname);
            $csv->setFlags(SplFileObject::READ_CSV);

            foreach ($csv as $row) {
                if (!is_null($row[0]))
                    $data[] = $row;
            }

            $send_text .= $data[0][1] . "\n";
            $send_text .= "【" . $data[1][0]  . "】" . $data[1][1]  . "\n";
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
        }

        if ($send_text != "") {
            $post_data = [
                "replyToken" => $replytoken,
                "messages" => [
                    [
                        "type" => "text",
                        "text" => $send_text
                    ],
                	[
                        "type" => "image",
                        "originalContentUrl" => $graph_url,
                        "previewImageUrl" => $graph_url
                    ]
                ]
            ];

            // curlを使用してメッセージを返信する
            $ch = curl_init("https://api.line.me/v2/bot/message/reply");
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
}
