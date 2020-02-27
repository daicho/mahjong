<?php
    require_once("FileReader.php");

    session_start();

    // ログイン判定
    if (!isset($_SESSION["LOGIN"])) {
        header("Location: /login.php");
        exit();
    }

    // GET
    $name = $_GET["name"];

    // ディレクトリパス定義
    $root_dir = "https://github.com/daicho/mahjong-club/raw/master/";
    $system_dir = urlencode("成績管理システム") . "/";
    $seiseki_dir = $system_dir . urlencode("成績") . "/";
    
    $fileReader = new FileReader($root_dir);
    $data = $fileReader->loadCSV($seiseki_dir . urlencode($name) . ".csv");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $name ?>の成績 - 成績管理システム | 競技麻雀同好会</title>
    <link rel="stylesheet" href="/css/person.css">
    <script src="/Chart.js/Chart.min.js"></script>
</head>
<body>
    <h1>競技麻雀同好会 成績管理システム</h1>
    <h2><?= $name ?></h2>
    <div class="personal-container">
        <div class="personal-table">
            <h3>成績</h3>
            <table>
                <?php
                for ($i = 1; $i <= 31; $i++) {
                    echo "<tr>";
                    for ($j = 0; $j <= 2; $j++) {
                        echo "<td>";
                        if ($j != 2 || $i < 23 || 28 < $i)
                            echo $data[$i][$j];
                        echo "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
        <div class="personal-table">
            <h3>役</h3>
                <table>
                <?php
                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i][4] == "") break;

                    echo "<tr>";
                    for ($j = 5; $j <= 8; $j++) {
                        echo $i == 0 ? "<th>" : "<td>";
                        echo $data[$i][$j];
                        echo $i == 0 ? "</th>" : "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
        <div class="personal-table">
            <h3>アガリ翻数</h3>
            <table>
                <?php
                for ($i = 33; $i <= 46; $i++) {
                    echo "<tr>";
                    for ($j = 0; $j <= 2; $j++) {
                        echo $i == 33 ? "<th>" : "<td>";
                        echo $data[$i][$j];
                        echo $i == 33 ? "</th>" : "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        <!--</div>
        <div class="personal-table turnBack">-->
            <h3>局別収支</h3>
            <table>
                <?php
                for ($i = 0; $i <= 8; $i++) {
                    echo "<tr>";
                    for ($j = 13; $j <= 15; $j++) {
                        echo $i == 0 ? "<th>" : "<td>";
                        echo $data[$i][$j];
                        echo $i == 0 ? "</th>" : "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        <!--</div>
        <div class="personal-table turnBack">-->
            <h3>開始位置別スコア</h3>
            <table>
                <?php
                for ($i = 10; $i <= 14; $i++) {
                    echo "<tr>";
                    for ($j = 13; $j <= 15; $j++) {
                        echo $i == 10 ? "<th>" : "<td>";
                        echo $data[$i][$j];
                        echo $i == 10 ? "</th>" : "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        <!--</div>
        <div class="personal-table turnBack">-->
            <h3>相性</h3>
            <table>
                <?php
                for ($i = 16; $i <= count($data); $i++) {
                    if ($data[$i][13] == "") break;

                    echo "<tr>";
                    for ($j = 13; $j <= 15; $j++) {
                        echo $i == 16 ? "<th>" : "<td>";
                        echo $data[$i][$j];
                        echo $i == 16 ? "</th>" : "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    <div class="graph">        
        <div  class="personal-graph">
            <h3>スコア</h3>
            <canvas id="score" ></canvas>
        </div>
        <div class="personal-graph",style="width:100%;">
            <h3>局別スコア</h3>
            <canvas id="byStationScore"></canvas>
        </div>
        <div class="personal-graph",style="width:100%;">
            <h3>あがり翻数</h3>
            <canvas id="fanScore"></canvas>
        </div>
    </div>
    </div>
    <script>
        var ctx = document.getElementById("score").getContext("2d");
        var myScore = new Chart(ctx, {
            type: "line",
            data: {
                labels: [
                    <?php
                    for ($i = 1; $i < count($data); $i++) {
                        if ($data[$i][10] == "") break;
                        echo "'" . $data[$i][10] . "', ";
                    }
                    ?>
                ],
                datasets: [{
                    label: "スコア",
                    data: [
                        <?php
                        for ($i = 1; $i < count($data); $i++) {
                            if ($data[$i][10] == "") break;
                            echo str_replace("±", "", $data[$i][11]) . ", ";
                        }
                        ?>
                    ],
                    borderColor: "rgba(255, 99, 132, 1)",
                    borderWidth: 4,
                    lineTension: 0,
                    fill: false,
                    pointBackgroundColor: "rgba(0, 0, 0, 0)",
                    pointBorderColor: "rgba(0, 0, 0, 0)"
                }]
            }
        });
    
        let ctxByStation = document.getElementById("byStationScore").getContext("2d");
        let myScoreByStation  = new Chart(ctxByStation, {
            type: "line",
            data:{
                labels: [
                    <?php
                    for ($i = 1; $i < count($data); $i++) {
                        if ($data[$i][13] == "") break;
                        echo "'" . $data[$i][13] . "', ";
                    }
                    ?>
                ],
                datasets: [{
                    label: "局別スコア",
                    data: [
                        <?php
                        for ($i = 1; $i < count($data); $i++) {
                            if ($data[$i][13] == "") break;
                            echo str_replace("±", "", $data[$i][14]) . ", ";
                        }
                        ?>
                    ],
                    borderColor: "rgba(255, 99, 132, 1)",
                    borderWidth: 4,
                    lineTension: 0,
                    fill: false,
                    pointBackgroundColor: "rgba(0, 0, 0, 0)",
                    pointBorderColor: "rgba(0, 0, 0, 0)"
                }]

            }
        });


        let ctxfan = document.getElementById("fanScore").getContext("2d");
        let myFanScore  = new Chart(ctxfan, {
            type: "bar",
            data:{
                labels: [
                    <?php
                    for ($i = 34; $i < count($data); $i++) {
                        if ($data[$i][0] == "") break;
                        echo "'" . $data[$i][0] . "', ";
                    }
                    ?>
                ],
                datasets: [{
                    label: "あがり翻数",
                    data: [
                        <?php
                        for ($i = 34; $i < count($data); $i++) {
                            if ($data[$i][0] == "") break;
                            echo str_replace("回", "", $data[$i][1]) . ", ";
                        }
                        ?>
                    ],
                    borderColor: "rgba(255, 99, 132, 1)",
                    backgroundColor:"rgba(255,99,132,0.3)",
                    borderWidth: 4,
                }]

            }
        });
    </script>
</body>
