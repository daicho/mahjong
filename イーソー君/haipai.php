<?php
header("Content-Type: image/png");
define("HAI_WIDTH",  30);
define("HAI_HEIGHT", 40);

$haipai = $_GET["haipai"];
$hai_cnt = floor(strlen($haipai) / 3);

$img = new Imagick();
$img->newImage(HAI_WIDTH * $hai_cnt, HAI_HEIGHT, new ImagickPixel("white"));
$img->setImageFormat("png");

for ($i = 0; $i < $hai_cnt; $i++) {
    $haifile = "mjhai/" . substr($haipai, $i * 3, 3) . ".png";
	$hai = new Imagick($haifile);
	$img->compositeImage($hai, Imagick::COMPOSITE_DEFAULT, HAI_WIDTH * $i, 0);
}

echo($img);
