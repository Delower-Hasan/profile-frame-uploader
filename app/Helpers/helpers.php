<?php
/**
 * Add frame to profile picture
 * @param  string $sourcePath Path to profile picture
 * @param  int    $design     Frame to use
 * @return binary             Binary data of framed profile picture
 */
function makeDP($sourcePath, $design = 1){

  // if(in_array($design, array(0, 1, 2)))
  //   $design = "frame-$design.png";
  // else
  //   exit;

  $design = "frame-$design.png";


  $src = imagecreatefromstring(file_get_contents($sourcePath));
  $fg = imagecreatefrompng(public_path('frames/'.$design));

  list($width, $height) = getimagesize($sourcePath);

  $croppedFG = imagecreatetruecolor($width, $height);

  $background = imagecolorallocate($croppedFG, 0, 0, 0);
  // removing the black from the placeholder
  imagecolortransparent($croppedFG, $background);

  imagealphablending($croppedFG, false);
  imagesavealpha($croppedFG, true);

  imagecopyresized($croppedFG, $fg, 0, 0, 0, 0, $width, $height, 750, 750);

  // Start merging
  $out = imagecreatetruecolor($width, $height);
  imagecopyresampled($out, $src, 0, 0, 0, 0, $width, $height, $width, $height);
  imagecopyresampled($out, $croppedFG, 0, 0, 0, 0, $width, $height, $width, $height);

  ob_start();
  imagepng($out);
  $image = ob_get_clean();
  return $image;
}