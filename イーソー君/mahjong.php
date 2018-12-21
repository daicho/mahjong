<?php
define("ACCESS_TOKEN", "p92VGjm5eJxJdzbz/cwIu3ErYj0pTf50tFV/ESKg2mLCHi0fHPvuPSaQ0pKXHspeB9tO+CK/7VPcjJpbPLZ61tZzAe6uq4HLBDmHY4+3YVAKI/BQcsuRbt5OISbA3AzUZV+gUZ7uOpADST8FR2L9HwdB04t89/1O/w1cDnyilFU=");
define("USER_ID", "U370e9c2081a4b305e756946b5f6313a5");

$rank_str = ["通算スコア", "平均スコア", "平均順位", "トップ率", "ふっとび率", "アガリ率", "放銃率", "平均アガリ点", "平均放銃点", "リーチ率", "副露率", "リーチ成功率", "副露成功率"];

$unsei = [
    [
        "str" => "役満級",
        "pckid" => 2,
        "stkid" => 172
    ],
    [
        "str" => "倍満級",
        "pckid" => 2,
        "stkid" => 164
    ],
    [
        "str" => "満貫級",
        "pckid" => 2,
        "stkid" => 171
    ],
    [
        "str" => "リーチのみ級",
        "pckid" => 2,
        "stkid" => 175
    ],
    [
        "str" => "ノーテン級",
        "pckid" => 2,
        "stkid" => 525
    ],
    [
        "str" => "ダブロン振り込み級",
        "pckid" => 2,
        "stkid" => 174
    ],
    [
        "str" => "ふっとび級",
        "pckid" => 2,
        "stkid" => 173
    ]
];

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

        // 使い方
        if ($message_text == "使い方") {
            // 対応コマンドの一覧を送信
            $send_text = "(名前)";
            $send_text .= "\n" . "占って";

            for ($i = 0; $i < count($rank_str); $i++)
                $send_text .= "\n" . $rank_str[$i];

            $messages = [
                [
                    "type" => "text",
                    "text" => $send_text
                ]
            ];
        }

        // 占って
        if (preg_match("/(運勢|占|うらな)/", $message_text)) {
            $unsei_rand = mt_rand(0, count($unsei) - 1);

            $messages = [
                [
                    "type" => "text",
                    "text" => "あなたの運勢は「" . $unsei[$unsei_rand]["str"] . "」です"
                ],
                [
                    "type" => "sticker",
                    "packageId" => $unsei[$unsei_rand]["pckid"],
                    "stickerId" => $unsei[$unsei_rand]["stkid"]
                ]
            ];
        }

        // 配牌
        if (preg_match("/(配牌|はいぱい)/", $message_text)) {
        	$haipai_url = "https://lolipop-dp26251191.ssl-lolipop.jp/line/haipai.php?" . date("YmdHis");

            $messages = [
                [
                    "type" => "image",
                    "originalContentUrl" => $haipai_url,
                    "previewImageUrl" => $haipai_url
                ]
            ];
        }

        // ランキング
        for ($i = 0; $i < count($rank_str); $i++) {
            // ランキングのいずれかに一致したら
            if ($message_text == $rank_str[$i]) {
                $fname = "https://raw.githubusercontent.com/daicho/mahjong/master/" . urlencode("三人麻雀") . "/" . urlencode("成績") . "/" . urlencode("ランキング") . ".csv?" . date("YmdHis");
                $myfname = "record/ランキング.csv";

                if (file_get_contents($fname)) {
                	if (is_null($data)) {
	                    // CSVを読み込み
	                    file_put_contents($myfname, mb_convert_encoding(file_get_contents($fname), 'UTF-8', 'SJIS'));

	                    $csv = new SplFileObject($myfname);
	                    $csv->setFlags(SplFileObject::READ_CSV);

	                    foreach ($csv as $row) {
	                        if (!is_null($row[0]))
	                            $data[] = $row;
	                    }
	                }

                    // ランキングを送信
                    $send_text = $data[0][$i * 2 + 1];
                    for ($j = 1; $j < count($data); $j++)
                        $send_text .= "\n" . "【" . $data[$j][$i * 2 + 1] . "】" . $data[$j][$i * 2 + 2];

                    $messages = [
                    	[
	                        "type" => "text",
	                        "text" => $send_text
	                    ]
                    ];
                }
            }
        }

        // 成績
        $fname = "https://raw.githubusercontent.com/daicho/mahjong/master/" . urlencode("三人麻雀") . "/" . urlencode("成績") . "/" . urlencode($message_text) . ".csv?" . date("YmdHis");
        $graph_url = "https://raw.githubusercontent.com/daicho/mahjong/master/" . urlencode("三人麻雀") . "/" . urlencode("グラフ") . "/" . urlencode($message_text) . ".png?" . date("YmdHis");
        $myfname = "record/" . $message_text . ".csv";

        // 名前が存在したら
        if ($message_text != "ランキング" && file_get_contents($fname)) {
            // CSVを読み込み
            file_put_contents($myfname, mb_convert_encoding(file_get_contents($fname), 'UTF-8', 'SJIS'));

            $csv = new SplFileObject($myfname);
            $csv->setFlags(SplFileObject::READ_CSV);

            foreach ($csv as $row) {
                if (!is_null($row[0]))
                    $data[] = $row;
            }

            // 成績を送信
            $send_text = $data[0][1] . "\n";
            $send_text .= "【" . $data[1][0]  . "】" . $data[1][1]  . "\n";
            $send_text .= "【" . $data[2][0]  . "】" . $data[2][1]  . "\n";
            $send_text .= "【" . $data[3][0]  . "】" . $data[3][1]  . "\n";
            $send_text .= "【" . $data[4][0]  . "】" . $data[4][1]  . "\n";
            $send_text .= "【" . $data[5][0]  . "】" . $data[5][1]  . "\n";
            $send_text .= "【" . $data[6][0]  . "】" . $data[6][1]  . " / " . $data[6][2]  . "\n";
            $send_text .= "【" . $data[7][0]  . "】" . $data[7][1]  . " / " . $data[7][2]  . "\n";
            $send_text .= "【" . $data[8][0]  . "】" . $data[8][1]  . " / " . $data[8][2]  . "\n";
            $send_text .= "【" . $data[9][0]  . "】" . $data[9][1]  . " / " . $data[9][2]  . "\n";
            $send_text .= "【" . $data[10][0] . "】" . $data[10][1] . " / " . $data[10][2] . "\n";
            $send_text .= "【" . $data[11][0] . "】" . $data[11][1] . " / " . $data[11][2] . "\n";
            $send_text .= "【" . $data[12][0] . "】" . $data[12][1] . " / " . $data[12][2] . "\n";
            $send_text .= "【" . $data[13][0] . "】" . $data[13][1] . " / " . $data[13][2] . "\n";
            $send_text .= "【" . $data[14][0] . "】" . $data[14][1] . " / " . $data[14][2] . "\n";
            $send_text .= "【" . $data[14][0] . "】" . $data[14][1] . " / " . $data[14][2] . "\n";
            $send_text .= "【" . $data[15][0] . "】" . $data[15][1] . " / " . $data[15][2] . "\n";
            $send_text .= "【" . $data[16][0] . "】" . $data[16][1] . " / " . $data[16][2] . "\n";
            $send_text .= "【" . $data[17][0] . "】" . $data[17][1] . " / " . $data[17][2] . "\n";
            $send_text .= "【" . $data[18][0] . "】" . $data[18][1] . "\n";
            $send_text .= "【" . $data[19][0] . "】" . $data[19][1];

            $messages = [
                [
                    "type" => "text",
                    "text" => $send_text
                ],
                [
                    "type" => "image",
                    "originalContentUrl" => $graph_url,
                    "previewImageUrl" => $graph_url
                ]
            ];
        }

        if (!is_null($messages)) {
            // 送信データ
            $post_data = [
                "replyToken" => $replytoken,
                "messages" => $messages
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
