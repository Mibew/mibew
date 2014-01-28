<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

function escape_with_cdata($text)
{
    return "<![CDATA[" . str_replace("]]>", "]]>]]&gt;<![CDATA[", $text) . "]]>";
}
