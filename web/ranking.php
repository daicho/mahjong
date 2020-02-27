<?php
    require_once("FileReader.php");

    session_start();

    // ログイン判定
    if (!isset($_SESSION["LOGIN"])) {
        header("Location: /login.php");
        exit();
    }

    // ディレクトリパス定義
    $root_dir = "https://github.com/daicho/mahjong-club/raw/master/";
    $system_dir = urlencode("成績管理システム") . "/";
    $seiseki_dir = $system_dir . urlencode("成績") . "/";

    $fileReader = new FileReader($root_dir);
    $data = $fileReader->loadCSV($seiseki_dir . urlencode("ランキング") . ".csv");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ランキング - 成績管理システム | 競技麻雀同好会</title>
    <link rel="stylesheet" href="/css/ranking.css">

    <script>
        (function(d) {
          var config = {
            kitId: 'evc7hwv',
            scriptTimeout: 3000,
            async: true
          },
          h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
        })(document);
    </script>
</head>
<body>
    <header class="header_block">
        <a href="">
        <img src="/svg/logo.svg" class="logo"alt="競技麻雀同好会のロゴ">
        </a>
    </header>

    <div class="switch">
        <div class="line"></div>

        <div class="rank" onclick="rank_click()">
            <img id="rank_img" src="/svg/rank_fill.svg" alt="">
            <p>ランキング</p>
        </div>

        <div class="man" onclick="man_click()">
            <img id="man_img" src="/svg/man_frame.svg" alt="">
            <p>参加者</p>
        </div>
    </div>
    <!-- ランキング -->
    <?php for ($j = 1; $j < count($data[2]);$j += 2) { ?>
        <section id="rank_block">
            <input id="check<?= $j ?>" class="check_flag"type="checkbox">
            <label class="rank_type" for="check<?= $j ?>">
                <p class=""><?= $data[0][$j] ?></p>
                <img src="/svg/under_arrow.svg" alt="">
            </label>
            <div class="ranking">
                    <?php 
                    $rank = 1;
                    $disprank = 1;
                    ?>
                    <?php for ($i = 2; $i < count($data); $i++) { ?>
                        <a class="name_block" href="/personal/<?= $data[$i][$j] ?>">
                            <?php if($data[$i][$j + 1] != $data[$i - 1][$j + 1]) { 
                                $disprank = $rank;
                            }
                            ?>
                            <div class="indent"></div>
                            <?php
                            if("全体" != $data[$i][$j]) {
                                $rank++;
                            ?>
                                <p class="rank_num"><?= $disprank ?></p>
                            <?php } else {?>
                                <p class="rank_totally"></p>
                            <?php } ?>
                            <p class="name"><?= $data[$i][$j] ?></p>
                            <p class="score"><?= $data[$i][$j + 1] ?></p>
                            <img class="arrow" src="/svg/arrow_trans.svg" alt="">
                        </a>
                    <?php } ?>

            </div>
        </section>
    <?php } ?>

    <script type="text/javascript" src="/js/top.js"></script>
</body>
