<?php
// Check if required variables exist
if (!isset($_GET['hid'])) {
    exit;
}

$hashId = strtolower($_GET['hid']);
$width = (int) $_GET['w'];
$height = (int) $_GET['h'];

// Check if hash is really a hash string
if (!(strlen($hashId) == 40 && ctype_xdigit($hashId))) {
    exit;
}

// This shouldn't happen normally, but just in case
if (!file_exists('uploads/' . $hashId . '.simg')) {
    exit;
}

if (isset($_GET['del']) && $_GET['del'] == 'true') {
    unlink('uploads/' . $hashId . '.simg');
    exit;
} else if (!isset($_GET['w']) || !isset($_GET['h'])) {
    exit;
}

// Check length and width to make sure they are correct
if ($width < 0 || $height < 0) {
    exit;
}

list($oriWidth, $oriHeight) = getimagesize('uploads/' . $hashId . '.simg');

$im = @imagecreatefrompng('uploads/' . $hashId . '.simg');

// Verification check
if (!$im) {
    exit;
}

// Create new blank image and copy the original in it
$newImg = imagecreatetruecolor($width, $height);

imagecopyresampled($newImg, $im, 0, 0, 0, 0, $width, $height,
    $oriWidth, $oriHeight);

header('Content-type: image/png');
imagepng($newImg);

imagedestroy($newImg);
imagedestroy($im);

// Delete the image
unlink('uploads/' . $hashId . '.simg');
