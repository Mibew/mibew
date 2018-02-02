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

function unicode_urldecode($url)
{
    preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);

    foreach ($a[1] as $uniord) {
        $dec = hexdec($uniord);
        $utf = '';

        if ($dec < 128) {
            $utf = chr($dec);
        } elseif ($dec < 2048) {
            $utf = chr(192 + (($dec - ($dec % 64)) / 64));
            $utf .= chr(128 + ($dec % 64));
        } else {
            $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
            $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
            $utf .= chr(128 + ($dec % 64));
        }
        $url = str_replace('%u' . $uniord, $utf, $url);
    }
    return urldecode($url);
}

function cut_string($string, $length = 75, $ellipsis = '')
{
    $result = '';
    if (strlen($string) > $length) {
        $splitstring = explode("[__cut__]", wordwrap($string, $length, "[__cut__]", true));
        $result = $splitstring[0] . $ellipsis;
    } else {
        $result = $string;
    }
    return $result;
}

/**
 * Simple HTML sanitation.
 *
 * Includes some code from the PHP StripAttributes Class For XML and HTML.
 *
 * @param string $string Target string
 * @param string $tags_level Sanitation level for tags. Available values are
 *   "high", "moderate" and "low".
 * @param string $attr_level Sanitation level for attributes. Available values
 *   are "high", "moderate" and "low".
 * @return string Sanitized string with stripped dangerous tags and attributes.
 *
 * @author David (semlabs.co.uk)
 * @copyright (c) 2009, David (semlabs.co.uk)
 * @license MIT
 * @link http://semlabs.co.uk/journal/php-strip-attributes-class-for-xml-and-html
 */
function sanitize_string($string, $tags_level = 'high', $attr_level = 'high')
{
    $sanitize_tags = array(
        'high' => '',
        'moderate' => '<span><em><strong><b><i><br>',
        'low' => '<span><em><strong><b><i><br><p><ul><ol><li><a><font><style>',
    );

    $sanitize_attributes = array(
        'high' => array(),
        'moderate' => array('class', 'href', 'rel', 'id'),
        'low' => false,
    );

    $tags_level = array_key_exists($tags_level, $sanitize_tags) ? $tags_level : 'high';
    $string = strip_tags($string, $sanitize_tags[$tags_level]);

    $attr_level = array_key_exists($attr_level, $sanitize_attributes) ? $attr_level : 'high';
    if ($sanitize_attributes[$attr_level]) {
        preg_match_all("/<([^ !\/\>\n]+)([^>]*)>/i", $string, $elements);
        foreach ($elements[1] as $key => $element) {
            if ($elements[2][$key]) {
                $new_attributes = '';
                preg_match_all(
                    "/([^ =]+)\s*=\s*[\"|']{0,1}([^\"']*)[\"|']{0,1}/i",
                    $elements[2][$key],
                    $attributes
                );

                if ($attributes[1]) {
                    foreach ($attributes[1] as $attr_key => $attr) {
                        if (in_array($attributes[1][$attr_key], $sanitize_attributes[$attr_level])) {
                            $new_attributes .= ' ' . $attributes[1][$attr_key]
                                . '="' . $attributes[2][$attr_key] . '"';
                        }
                    }
                }

                $replacement = '<' . $elements[1][$key] . $new_attributes . '>';
                $string = preg_replace(
                    '/' . sanitize_reg_escape($elements[0][$key]) . '/',
                    $replacement,
                    $string
                );

            }
        }

    }

    return $string;
}

/**
 * Remove dangerous characters from regular expression.
 *
 * @param string $string Target regular expression
 * @return string Sanitized reqular expression
 */
function sanitize_reg_escape($string)
{
    $conversions = array(
        "^" => "\^",
        "[" => "\[",
        "." => "\.",
        "$" => "\$",
        "{" => "\{",
        "*" => "\*",
        "(" => "\(",
        "\\" => "\\\\",
        "/" => "\/",
        "+" => "\+",
        ")" => "\)",
        "|" => "\|",
        "?" => "\?",
        "<" => "\<",
        ">" => "\>",
    );

    return strtr($string, $conversions);
}

/**
 * Wrapper for htmlspecialchars with single quotes conversion enabled by default
 *
 * @param string $string Target string
 * @return string Escaped string
 */
function safe_htmlspecialchars($string)
{
    $string = preg_replace('/[\x00-\x08\x0b\x0c\x0e-\x1f]/', '', $string);
    return htmlspecialchars($string, ENT_QUOTES);
}
