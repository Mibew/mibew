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

function date_diff_to_text($seconds)
{
    $minutes = div($seconds, 60);
    $seconds = $seconds % 60;
    if ($minutes < 60) {
        return sprintf("%02d:%02d", $minutes, $seconds);
    } else {
        $hours = div($minutes, 60);
        $minutes = $minutes % 60;

        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
}

function get_month_selection($from_time, $to_time)
{
    // Use correct months names and over translatable date/time strings.
    $locale_info = get_locale_info(get_current_locale());
    setlocale(LC_TIME, $locale_info['time_locale']);

    $start = getdate($from_time);
    $month = $start['mon'];
    $year = $start['year'];
    $result = array();
    do {
        $current = mktime(0, 0, 0, $month, 1, $year);
        $result[date("m.y", $current)] = strftime("%B, %Y", $current);
        $month++;
        if ($month > 12) {
            $month = 1;
            $year++;
        }
    } while ($current < $to_time);

    return $result;
}

function get_form_date($day, $month)
{
    if (preg_match('/^(\d{2}).(\d{2})$/', $month, $matches)) {
        return mktime(0, 0, 0, $matches[1], $day, $matches[2]);
    }

    return 0;
}

function set_form_date($utime, $prefix)
{
    return array(
        "form${prefix}day" => date("d", $utime),
        "form${prefix}month" => date("m.y", $utime),
    );
}

function date_to_text($unixtime)
{
    if ($unixtime < 60 * 60 * 24 * 30) {
        return getlocal("Never");
    }

    $then = getdate($unixtime);
    $now = getdate();

    if ($then['yday'] == $now['yday'] && $then['year'] == $now['year']) {
        return getlocal("Today at {0}", array(format_date($unixtime, 'time')));
    } elseif (($then['yday'] + 1) == $now['yday'] && $then['year'] == $now['year']) {
        return getlocal("Yesterday at {0}", array(format_date($unixtime, 'time')));
    } else {
        return format_date($unixtime, 'full');
    }
}

/**
 * Format date according to passed in type and locale.
 *
 * @param int $timestamp Unix timestamp
 * @param string $format Format name. Can be one of "full", "date", "time".
 * @param string|null $locale Locale code. If null is passed in the current
 *   locale will be used.
 * @return string Formatted date.
 * @throws \InvalidArgumentException If $type argument has wrong value.
 */
function format_date($timestamp, $format, $locale = null)
{
    if (is_null($locale)) {
        $locale = get_current_locale();
    }

    // Get locale info
    $locale_info = get_locale_info($locale);
    $date_format = $locale_info['date_format'];

    if (!isset($date_format[$format])) {
        throw new \InvalidArgumentException('Wrong value of the $format argument.');
    }

    // Use correct months names and over translatable date/time strings.
    setlocale(LC_TIME, $locale_info['time_locale']);

    return strftime($date_format[$format], $timestamp);
}
