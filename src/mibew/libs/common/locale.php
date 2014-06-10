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

use Mibew\Database;
use Mibew\Plugin\Manager as PluginManager;
use Symfony\Component\Translation\Loader\PoFileLoader;

/**
 * Name for the cookie to store locale code in use
 */
define('LOCALE_COOKIE_NAME', 'mibew_locale');

// Test and set default locales

/**
 * Verified value of the $default_locale configuration parameter (see
 * "libs/default_config.php" for details)
 */
define(
    'DEFAULT_LOCALE',
    locale_pattern_check($default_locale) && locale_exists($default_locale) ? $default_locale : 'en'
);

/**
 * Verified value of the $home_locale configuration parameter (see
 * "libs/default_config.php" for details)
 */
define(
    'HOME_LOCALE',
    locale_pattern_check($home_locale) && locale_exists($home_locale) ? $home_locale : 'en'
);

/**
 * Code of the current system locale
 */
define('CURRENT_LOCALE', get_locale());

function locale_exists($locale)
{
    return file_exists(MIBEW_FS_ROOT . "/locales/$locale/translation.po");
}

function locale_pattern_check($locale)
{
    $locale_pattern = "/^[\w-]{2,5}$/";

    return preg_match($locale_pattern, $locale);
}

function get_available_locales()
{
    $list = array();
    $folder = MIBEW_FS_ROOT . '/locales';
    if ($handle = opendir($folder)) {
        while (false !== ($file = readdir($handle))) {
            if (locale_pattern_check($file) && is_dir("$folder/$file")) {
                $list[] = $file;
            }
        }
        closedir($handle);
    }
    sort($list);

    return $list;
}

function get_user_locale()
{
    if (isset($_COOKIE[LOCALE_COOKIE_NAME])) {
        $requested_lang = $_COOKIE[LOCALE_COOKIE_NAME];
        if (locale_pattern_check($requested_lang) && locale_exists($requested_lang)) {
            return $requested_lang;
        }
    }

    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $requested_langs = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($requested_langs as $requested_lang) {
            if (strlen($requested_lang) > 2) {
                $requested_lang = substr($requested_lang, 0, 2);
            }

            if (locale_pattern_check($requested_lang) && locale_exists($requested_lang)) {
                return $requested_lang;
            }
        }
    }

    if (locale_pattern_check(DEFAULT_LOCALE) && locale_exists(DEFAULT_LOCALE)) {
        return DEFAULT_LOCALE;
    }

    return 'en';
}

function get_locale()
{
    $locale = verify_param("locale", "/./", "");

    // Check if locale code passed in as a param is valid
    $locale_param_valid = $locale
        && locale_pattern_check($locale)
        && locale_exists($locale);

    // Check if locale code stored in session data is valid
    $session_locale_valid = isset($_SESSION['locale'])
        && locale_pattern_check($_SESSION['locale'])
        && locale_exists($_SESSION['locale']);

    if ($locale_param_valid) {
        $_SESSION['locale'] = $locale;
    } elseif ($session_locale_valid) {
        $locale = $_SESSION['locale'];
    } else {
        $locale = get_user_locale();
    }

    setcookie(LOCALE_COOKIE_NAME, $locale, time() + 60 * 60 * 24 * 1000, MIBEW_WEB_ROOT . "/");

    return $locale;
}

function get_locale_links()
{
    // Get list of available locales
    $locale_links = array();
    $all_locales = get_available_locales();
    if (count($all_locales) < 2) {
        return null;
    }

    // Attache locale names
    $locale_names = get_locale_names();
    foreach ($all_locales as $k) {
        $locale_links[$k] = isset($locale_names[$k]) ? $locale_names[$k] : $k;
    }

    return $locale_links;
}

/**
 * Returns list of human readable locale names.
 *
 * @return array
 */
function get_locale_names()
{
    return array(
        'ar' => 'العربية',
        'be' => 'Беларуская',
        'bg' => 'Български',
        'ca' => 'Català',
        'cs' => 'Česky',
        'da' => 'Dansk',
        'de' => 'Deutsch',
        'el' => 'Ελληνικά',
        'en' => 'English',
        'es' => 'Español',
        'et' => 'Eesti',
        'fa' => 'فارسی',
        'fi' => 'Suomi',
        'fr' => 'Français',
        'he' => 'עברית',
        'hr' => 'Hrvatski',
        'hu' => 'Magyar',
        'id' => 'Bahasa Indonesia',
        'it' => 'Italiano',
        'ja' => '日本語',
        'ka' => 'ქართული',
        'kk' => 'Қазақша',
        'ko' => '한국어',
        'ky' => 'Кыргызча',
        'lt' => 'Lietuvių',
        'lv' => 'Latviešu',
        'nl' => 'Nederlands',
        'nn' => 'Norsk nynorsk',
        'no' => 'Norsk bokmål',
        'pl' => 'Polski',
        'pt-pt' => 'Português',
        'pt-br' => 'Português Brasil',
        'ro' => 'Română',
        'ru' => 'Русский',
        'sk' => 'Slovenčina',
        'sl' => 'Slovenščina',
        'sr' => 'Српски',
        'sv' => 'Svenska',
        'th' => 'ไทย',
        'tr' => 'Türkçe',
        'ua' => 'Українська',
        'zh-cn' => '中文',
        'zh-tw' => '文言',
    );
}

/**
 * Load localized messages id some service locale info.
 *
 * Messages are statically cached.
 *
 * @param string $locale Name of a locale whose messages should be loaded.
 * @return array Localized messages array
 */
function load_messages($locale)
{
    static $messages = array();

    if (!isset($messages[$locale])) {
        // Load core localization
        $locale_file = MIBEW_FS_ROOT . "/locales/{$locale}/translation.po";
        $locale_data = read_locale_file($locale_file);

        $messages[$locale] = $locale_data['messages'];

        // Plugins are unavailable on system installation
        if (!installation_in_progress()) {
            // Load active plugins localization
            $plugins_list = array_keys(PluginManager::getAllPlugins());

            foreach ($plugins_list as $plugin_name) {
                // Build plugin path
                list($vendor_name, $plugin_short_name) = explode(':', $plugin_name, 2);
                $plugin_name_parts = explode('_', $plugin_short_name);
                $locale_file = MIBEW_FS_ROOT
                    . "/plugins/" . ucfirst($vendor_name) . "/Mibew/Plugin/"
                    . implode('', array_map('ucfirst', $plugin_name_parts))
                    . "/locales/{$locale}/translation.po";

                // Get localized strings
                if (is_readable($locale_file)) {
                    $locale_data = read_locale_file($locale_file);
                    // array_merge used to provide an ability for plugins to override
                    // localized strings
                    $messages[$locale] = array_merge(
                        $messages[$locale],
                        $locale_data['messages']
                    );
                }
            }

            // Load localizations from the database
            $db = Database::getInstance();
            $db_messages = $db->query(
                'SELECT * FROM {translation} WHERE locale = ?',
                array($locale),
                array(
                    'return_rows' => Database::RETURN_ALL_ROWS
                )
            );

            foreach ($db_messages as $message) {
                $messages[$locale][$message['source']] = $message['translation'];
            }
        }
    }

    return $messages[$locale];
}

/**
 * Read and parse locale file.
 *
 * @param string $path Locale file path
 * @return array Associative array with following keys:
 *  - 'messages': associative array of localized strings. The keys of the array
 *    are localization keys and the values of the array are localized strings.
 *    All localized strings are encoded in UTF-8.
 */
function read_locale_file($path)
{
    // Set default values
    $messages = array();

    $loader = new PoFileLoader();
    // At this point locale name (the second argument of the "load" method) has
    // no sense, so an empty string is passed in.
    $messages = $loader->load($path, '');

    return array(
        'messages' => $messages->all('messages'),
    );
}

/**
 * Returns localized string.
 *
 * @param string $text A text which should be localized
 * @param array $params Indexed array with placeholders.
 * @param string $locale Target locale code.
 * @param boolean $raw Indicates if the result should be sanitized or not.
 * @return string Localized text.
 */
function getlocal($text, $params = null, $locale = CURRENT_LOCALE, $raw = false)
{
    $string = get_localized_string($text, $locale);

    if ($params) {
        for ($i = 0; $i < count($params); $i++) {
            $string = str_replace("{" . $i . "}", $params[$i], $string);
        }
    }

    return $raw ? $string : sanitize_string($string, 'low', 'moderate');
}

/**
 * Return localized string by its key and locale.
 *
 * Do not use this function manually because it is for internal use only and may
 * be removed soon. Use {@link getlocal()} function instead.
 *
 * @access private
 * @param string $string Localization string key.
 * @param string $locale Target locale code.
 * @return string Localized string.
 */
function get_localized_string($string, $locale)
{
    $localized = load_messages($locale);
    if (isset($localized[$string])) {
        return $localized[$string];
    }
    if ($locale != 'en') {
        return get_localized_string($string, 'en');
    }

    return "!" . $string;
}

/* prepares for Javascript string */
function get_local_for_js($text, $params)
{
    $string = get_localized_string($text, CURRENT_LOCALE);
    $string = str_replace("\"", "\\\"", str_replace("\n", "\\n", $string));
    for ($i = 0; $i < count($params); $i++) {
        $string = str_replace("{" . $i . "}", $params[$i], $string);
    }

    return sanitize_string($string, 'low', 'moderate');
}

/**
 * Saves a localized string to the database.
 *
 * @param string $locale Locale code.
 * @param string $key String key.
 * @param string $value Translated string.
 */
function save_message($locale, $key, $value)
{
    $db = Database::getInstance();

    // Check if the string is already in the database.
    list($count) = $db->query(
        'SELECT COUNT(*) FROM {translation} WHERE locale = :locale AND source = :key',
        array(
            ':locale' => $locale,
            ':key' => $key,
        ),
        array(
            'return_rows' => Database::RETURN_ONE_ROW,
            'fetch_type' => Database::FETCH_NUM,
        )
    );
    $exists = ($count != 0);

    // Prepare the value to save in the database.
    $translation = str_replace("\r", "", trim($value));

    if ($exists) {
        // There is no such string in the database. Create it.
        $db->query(
            ('UPDATE {translation} SET translation = :translation '
                . 'WHERE locale = :locale AND source = :key'),
            array(
                ':locale' => $locale,
                ':key' => $key,
                ':translation' => $translation,
            )
        );
    } else {
        // The string is already in the database. Update it.
        $db->query(
            ('INSERT INTO {translation} (locale, source, translation) '
                . 'VALUES (:locale, :key, :translation)'),
            array(
                ':locale' => $locale,
                ':key' => $key,
                ':translation' => $translation,
            )
        );
    }
}
