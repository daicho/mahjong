<?php
    class FileReader {
        protected $root;

        public function __construct($root) {
            $this->root = $root;
        }

        // テキストファイルを読み込み
        public function loadText($path) {
            $url = $this->root . $path . "?" . date("YmdHis");
            return file_get_contents($url);
        }

        // CSVファイルを読み込み
        public function loadCSV($path) {
            $url = $this->root . $path . "?" . date("YmdHis");
            $temp_path = "temp/" . rand();
            $contents = file_get_contents($url);

            if ($contents) {
                // シフトJIS→UTF-8に変換
                file_put_contents($temp_path, mb_convert_encoding($contents, "UTF-8", "SJIS"));

                $csv = new SplFileObject($temp_path);
                $csv->setFlags(SplFileObject::DROP_NEW_LINE | SplFileObject::READ_CSV);

                // 配列に格納
                foreach ($csv as $row)
                    if (!is_null($row[0]))
                        $data[] = $row;
            } else {
                return false;
            }

            unlink($temp_path);

            return $data;
        }
    }
