<?php
// Image placeholder generator
header('Content-Type: image/png');

$text = isset($_GET['text']) ? $_GET['text'] : 'Coffee';
$width = isset($_GET['w']) ? intval($_GET['w']) : 400;
$height = isset($_GET['h']) ? intval($_GET['h']) : 300;
$bg_color = isset($_GET['bg']) ? $_GET['bg'] : '8B4513';
$text_color = isset($_GET['color']) ? $_GET['color'] : 'FFFFFF';

// Create image
$image = imagecreatetruecolor($width, $height);

// Parse colors
list($r, $g, $b) = sscanf($bg_color, "%02x%02x%02x");
$bg = imagecolorallocate($image, $r, $g, $b);

list($r, $g, $b) = sscanf($text_color, "%02x%02x%02x");
$text_color = imagecolorallocate($image, $r, $g, $b);

// Fill background
imagefill($image, 0, 0, $bg);

// Add coffee cup icon
$cup_color = imagecolorallocate($image, 210, 105, 30);
$handle_color = imagecolorallocate($image, 139, 69, 19);

// Draw coffee cup
$cup_height = $height * 0.4;
$cup_width = $width * 0.4;
$cup_x = ($width - $cup_width) / 2;
$cup_y = ($height - $cup_height) / 2;

// Cup body
imagefilledrectangle($image, $cup_x, $cup_y, $cup_x + $cup_width, $cup_y + $cup_height, $cup_color);

// Cup handle
imagefilledellipse($image, $cup_x + $cup_width + 20, $cup_y + $cup_height/2, 30, 40, $handle_color);

// Steam
$steam_color = imagecolorallocate($image, 255, 255, 255);
for ($i = 0; $i < 3; $i++) {
    $x = $cup_x + $cup_width/2 - 20 + $i * 20;
    $y = $cup_y - 20;
    imagefilledellipse($image, $x, $y, 15, 25, $steam_color);
}

// Add text
$font = 5; // Built-in font
$text_width = imagefontwidth($font) * strlen($text);
$text_x = ($width - $text_width) / 2;
$text_y = $cup_y + $cup_height + 30;

imagestring($image, $font, $text_x, $text_y, strtoupper($text), $text_color);

// Output image
imagepng($image);
imagedestroy($image);
?>