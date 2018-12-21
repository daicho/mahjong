<?php
header("Content-Type: image/png");
define("HAI_WIDTH",  30);
define("HAI_HEIGHT", 40);

for ($i = 1; $i <= 108; $i++)
	$hai[] = $i;

shuffle($hai);
$haipai = array_slice($hai, 0, 14);
sort($haipai);

$img = new Imagick();
$img->newImage(HAI_WIDTH * 14, HAI_HEIGHT, new ImagickPixel("white"));
$img->setImageFormat("png");

for ($i = 0; $i < 14; $i++) {
	$hai = new Imagick(sprintf("mjhai/%03d.png", $haipai[$i]));
	$img->compositeImage($hai, Imagick::COMPOSITE_DEFAULT, HAI_WIDTH * $i, 0);
}

echo($img);
