<?php
/*  [external]

	This is PHP file that generates CAPTCHA image for the How to Create CAPTCHA
	Protection using PHP and AJAX Tutorial

	You may use this code in your own projects as long as this
	copyright is left in place.  All code is provided AS-IS.
	This code is distributed in the hope that it will be useful,
 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
	
	For the rest of the code visit http://www.WebCheatSheet.com

	Copyright 2006 WebCheatSheet.com
*/

function can_show_captcha()
{
	return extension_loaded("gd");
}

function gen_captcha()
{
	$md5_hash = md5(rand(0, 9999));
	return substr($md5_hash, 15, 5);
}

function draw_captcha($security_code)
{

	//Set the image width and height
	$width = 100;
	$height = 25;

	//Create the image resource
	$image = ImageCreate($width, $height);
	if (function_exists('imageantialias')) {
		imageantialias($image, true);
	}

	//We are making three colors, white, black and gray
	$white = ImageColorAllocate($image, 255, 255, 255);
	$black = ImageColorAllocate($image, 15, 50, 15);
	$grey = ImageColorAllocate($image, 204, 204, 204);
	$ellipsec = ImageColorAllocate($image, 0, 100, 60);

	//Make the background black
	ImageFill($image, 0, 0, $black);
	imagefilledellipse($image, 56, 15, 30, 17, $ellipsec);

	//Add randomly generated string in white to the image
	ImageString($image, 5, 30, 4, $security_code, $white);

	//Throw in some lines to make it a little bit harder for any bots to break
	ImageRectangle($image, 0, 0, $width - 1, $height - 1, $grey);
	imageline($image, 0, $height / 2 + 3, $width, $height / 2 + 5, $grey);
	imageline($image, $width / 2 - 14, 0, $width / 2 + 7, $height, $grey);


	//Tell the browser what kind of file is come in
	header("Content-Type: image/jpeg");

	//Output the newly created image in jpeg format
	ImageJpeg($image);

	//Free up resources
	ImageDestroy($image);
}

?>