<?php
header("Content-Type: image/png");
define("HAI_WIDTH",  30);
define("HAI_HEIGHT", 40);

$haipai = $_GET["haipai"];

$img = new Imagick();
$img->newImage(HAI_WIDTH * 14, HAI_HEIGHT, new ImagickPixel("white"));
$img->setImageFormat("png");

for ($i = 0; $i < 14; $i++) {
    $haifile = "mjhai/" . substr($haipai, $i * 3, 3) . ".png";
	$hai = new Imagick($haifile);
	$img->compositeImage($hai, Imagick::COMPOSITE_DEFAULT, HAI_WIDTH * $i, 0);
}

echo($img);
