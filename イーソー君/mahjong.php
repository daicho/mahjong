<?php
define("ACCESS_TOKEN", "p92VGjm5eJxJdzbz/cwIu3ErYj0pTf50tFV/ESKg2mLCHi0fHPvuPSaQ0pKXHspeB9tO+CK/7VPcjJpbPLZ61tZzAe6uq4HLBDmHY4+3YVAKI/BQcsuRbt5OISbA3AzUZV+gUZ7uOpADST8FR2L9HwdB04t89/1O/w1cDnyilFU=");
define("USER_ID", "U370e9c2081a4b305e756946b5f6313a5");

$rank_str = [
    "通算スコア",
    "平均スコア",
    "平均順位",
    "トップ率",
    "ラス率",
    "アガリ率",
    "放銃率",
    "平均アガリ点",
    "平均放銃点",
    "ふっとび率",
    "ふっとばし率",
    "逆転率",
    "リーチ率",
    "副露率",
    "リーチ成功率",
    "副露成功率",
    "ツモ率",
    "最大ドラ数",
    "最大点数",
    "チョンボ"
];

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
            $send_text .= "\n" . "(名前) 役";
            $send_text .= "\n" . "(名前) 相性";
            $send_text .= "\n" . "(名前) (項目名)";
            $send_text .= "\n" . "占って";
            $send_text .= "\n" . "配牌";
            $send_text .= "\n" . "清一色";
            $send_text .= "\n" . "相関";

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

        $haipai_flag = false;

        // 配牌
        if ($message_text == "配牌") {
        	$haipai_flag = true;
            for ($i = 0; $i < 108; $i++)
                $hai[] = $i / 4 + 1;
        }

        // 清一色
        if ($message_text == "清一色" || $message_text == "チンイツ") {
        	$haipai_flag = true;
            for ($i = 36; $i < 72; $i++)
                $hai[] = $i / 4 + 1;
        }

        if ($haipai_flag) {
            shuffle($hai);
            $haipai = array_slice($hai, 0, 14);
            sort($haipai);

            $haipai_url = "https://lolipop-dp26251191.ssl-lolipop.jp/line/haipai.php?haipai=";

            for ($i = 0; $i < 14; $i++)
                $haipai_url .= sprintf("%03d", $haipai[$i]);

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
                    if ($data[1][$i * 2 + 1] == "")
                    	$send_text = $data[0][$i * 2 + 1];
                    else
                    	$send_text = $data[0][$i * 2 + 1] . " [" . $data[1][$i * 2 + 1] . "]";

                    for ($j = 2; $j < count($data); $j++)
                        $send_text .= "\n【" . $data[$j][$i * 2 + 1] . "】" . $data[$j][$i * 2 + 2];

                    $messages = [
                        [
                            "type" => "text",
                            "text" => $send_text
                        ]
                    ];
                }
            }
        }

        // 相関係数
        if ($message_text == "相関") {
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

                $send_text = "相関";
                for ($i = 5; $i < count($data); $i += 2)
                    $send_text .= "\n【" . $data[0][$i] . "】" . $data[1][$i];

                $messages = [
                    [
                        "type" => "text",
                        "text" => $send_text
                    ]
                ];
            }
        }

        // 成績
        $record = "https://raw.githubusercontent.com/daicho/mahjong/master/" . urlencode("三人麻雀") . "/" . urlencode("成績") . "/";
        $fname = $record . urlencode(str_replace([" 役", " 相性"], ["", ""], $message_text)) . ".csv?" . date("YmdHis");
        $graph_score = $record . urlencode($message_text) . "-Score.png?" . date("YmdHis");
        $graph_kyoku = $record . urlencode($message_text) . "-Kyoku.png?" . date("YmdHis");
        $myfname = "record/" . $message_text . ".csv";

        // 名前が存在したら
        if (file_get_contents($fname)) {
            // CSVを読み込み
            file_put_contents($myfname, mb_convert_encoding(file_get_contents($fname), 'UTF-8', 'SJIS'));

            $csv = new SplFileObject($myfname);
            $csv->setFlags(SplFileObject::READ_CSV);

            foreach ($csv as $row) {
                if (!is_null($row[0]))
                    $data[] = $row;
            }

			if (strpos($message_text, "役")) {
                // 役出現率を送信
                $send_text = $data[0][1] . " 役";
                for ($i = 1; $i <= 50; $i++)
                    $send_text .= "\n【" . $data[$i][5] ."】" . $data[$i][6] . " / " . $data[$i][7] . " (" . $data[$i][8] . ")";

                $messages = [
                    [
                        "type" => "text",
                        "text" => $send_text
                    ]
                ];
			} else if (strpos($message_text, "相性")) {
                // 相性を送信
                $send_text = $data[0][1] . " 相性";
                for ($i = 9; $data[$i][13] != ""; $i++)
                    $send_text .= "\n【" . $data[$i][13] ."】" . $data[$i][14] . " (" . $data[$i][16] . ")";

                $messages = [
                    [
                        "type" => "text",
                        "text" => $send_text
                    ]
                ];
			} else {
	            // 成績を送信
	            $send_text = $data[0][1] . "\n";
	            $send_text .= "【" . $data[1][0]  . "】" . $data[1][1]  . "\n";
	            $send_text .= "【" . $data[2][0]  . "】" . $data[2][1]  . "\n";

	            if ($message_text != "全体") {
		            $send_text .= "【" . $data[3][0]  . "】" . $data[3][1]  . "\n";
		            $send_text .= "【" . $data[4][0]  . "】" . $data[4][1]  . "\n";
		            $send_text .= "【" . $data[5][0]  . "】" . $data[5][1]  . "\n";
		            $send_text .= "【" . $data[6][0]  . "】" . $data[6][1]  . " / " . $data[6][2]  . "\n";
		            $send_text .= "【" . $data[7][0]  . "】" . $data[7][1]  . " / " . $data[7][2]  . "\n";
		            $send_text .= "【" . $data[8][0]  . "】" . $data[8][1]  . " / " . $data[8][2]  . "\n";
		        }

	            $send_text .= "【" . $data[9][0]  . "】" . $data[9][1]  . " / " . $data[9][2]  . "\n";
	            $send_text .= "【" . $data[10][0] . "】" . $data[10][1] . " / " . $data[10][2] . "\n";
	            $send_text .= "【" . $data[11][0] . "】" . $data[11][1] . " / " . $data[11][2] . "\n";
	            $send_text .= "【" . $data[12][0] . "】" . $data[12][1] . " / " . $data[12][2] . "\n";
	            $send_text .= "【" . $data[13][0] . "】" . $data[13][1] . " / " . $data[13][2] . "\n";
	            $send_text .= "【" . $data[14][0] . "】" . $data[14][1] . " / " . $data[14][2] . "\n";
	            $send_text .= "【" . $data[15][0] . "】" . $data[15][1] . " / " . $data[15][2] . "\n";
	            $send_text .= "【" . $data[16][0] . "】" . $data[16][1] . " / " . $data[16][2] . "\n";
	            $send_text .= "【" . $data[17][0] . "】" . $data[17][1] . " / " . $data[17][2] . "\n";
	            $send_text .= "【" . $data[18][0] . "】" . $data[18][1] . " / " . $data[18][2] . "\n";
	            $send_text .= "【" . $data[19][0] . "】" . $data[19][1] . " / " . $data[19][2] . "\n";
	            $send_text .= "【" . $data[20][0] . "】" . $data[20][1] . "\n";
	            $send_text .= "【" . $data[21][0] . "】" . $data[21][1] . "\n";
	            $send_text .= "【" . $data[22][0] . "】" . $data[22][1] . "\n";
	            $send_text .= "【" . $data[23][0] . "】" . $data[23][1] . "\n";
	            $send_text .= "【" . $data[24][0] . "】" . $data[24][1] . " / " . $data[24][2];

	            $messages = [
	                [
	                    "type" => "text",
	                    "text" => $send_text
	                ]
	            ];

	            if ($message_text != "全体") {
	            	$messages[] = [
	                    "type" => "image",
	                    "originalContentUrl" => $graph_score,
	                    "previewImageUrl" => $graph_score
	                ];

	                $messages[] = [
	                    "type" => "image",
	                    "originalContentUrl" => $graph_kyoku,
	                    "previewImageUrl" => $graph_kyoku
	                ];
		        }
		    }
        }

		// グラフ
        $gfile = $record . str_replace("+", "%20", urlencode($message_text)) . ".png?" . date("YmdHis");

        // グラフ名が存在したら
        if (file_get_contents($gfile)) {

            $messages = [
                [
                    "type" => "image",
                    "originalContentUrl" => $gfile,
                    "previewImageUrl" => $gfile
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
