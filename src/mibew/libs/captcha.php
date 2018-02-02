<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Check if captcha is loadable or not by cheking captcha requirements
 *
 * @return bool reqirements are saticfird or not
 */
function can_show_captcha()
{
    return extension_loaded("gd");
}
/**
 * Using characters and numbers to generate captcha code
 *
 * @return string captcha code
 */
function gen_captcha()
{
    $symbols = 'abcdefghijkmnpqrstuvwxyz123456789';
    $string = '';
    for ($i = 0; $i < 5; $i++) {
        $string .= substr($symbols, mt_rand(0, strlen($symbols)), 1);
    }

    return $string;
}
/**
 * Send captcha image directly to output
 *
 * @param string $security_code String to show on captcha
 * @param $return boolean Indicates if the image should be sent to the browser
 *   or returned as function result.
 * @return string Image content if $return argument is set to true.
 */
function draw_captcha($security_code, $return = false)
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


    if ($return) {
        // Get image content using output bufferezation
        ob_start();
        ImageJpeg($image);
        $result = ob_get_clean();
    } else {
        //Tell the browser what kind of file is come in
        header("Content-Type: image/jpeg");

        //Output the newly created image in jpeg format
        ImageJpeg($image);
    }

    //Free up resources
    ImageDestroy($image);

    if ($return) {
        return $result;
    }
}
