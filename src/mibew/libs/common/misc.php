<?php
/*
 * Copyright 2005-2014 the original author or authors.
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

function debugexit_print($var)
{
    echo "<html><body><pre>";
    print_r($var);
    echo "</pre></body></html>";
    exit;
}

function get_gifimage_size($filename)
{
    if (function_exists('gd_info')) {
        $info = gd_info();
        if (isset($info['GIF Read Support']) && $info['GIF Read Support']) {
            $img = @imagecreatefromgif($filename);
            if ($img) {
                $height = imagesy($img);
                $width = imagesx($img);
                imagedestroy($img);

                return array($width, $height);
            }
        }
    }

    return array(0, 0);
}

function js_path()
{
    return "js/compiled";
}

function div($a, $b)
{
    return ($a - ($a % $b)) / $b;
}

/**
 * Flatten array recursively.
 *
 * For example if input array is:
 * <code>
 *   $input = array(
 *     'first' => 1,
 *     'second' => array(
 *       'f' => 'value',
 *       's' => null,
 *       't' => array(
 *         'one', 'two', 'three'
 *       ),
 *       'f' => 4
 *     ),
 *     'third' => false,
 *   );
 * </code>
 * the output array will be:
 * <code>
 *   $output = array(
 *     'first' => 1,
 *     'second.f' => 'value',
 *     'second.s' => null,
 *     'second.t.0' => 'one',
 *     'second.t.1' => 'two',
 *     'second.t.2' => 'three'
 *     'second.f' => 4,
 *     'third' => false
 *   );
 * </code>
 *
 * @param array $arr Array to flatten
 * @return array
 */
function array_flatten_recursive($arr)
{
    $result = array();
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            // Flatten nested arrays
            $value = array_flatten_recursive($value);
            foreach ($value as $inner_key => $inner_value) {
                $result[$key . "." . $inner_key] = $inner_value;
            }
        } else {
            // Leave scalar values 'as is'
            $result[$key] = $value;
        }
    }

    return $result;
}

/**
 * Checks if currently processed script is installation script.
 *
 * @return boolean
 */
function installation_in_progress()
{
    if (!defined('INSTALLATION_IN_PROGRESS')) {
        return false;
    }

    return INSTALLATION_IN_PROGRESS;
}
