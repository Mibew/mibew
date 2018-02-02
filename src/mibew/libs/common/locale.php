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

use Mibew\Database;
use Mibew\Mail\Utils as MailUtils;
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * Name for the cookie to store locale code in use
 */
define('LOCALE_COOKIE_NAME', 'mibew_locale');

/**
 * Checks if a locale exists and is enabled or does not.
 *
 * @param string $locale Locale code.
 * @return boolean True if the specified locale exists and is enabled and false
 *   otherwise.
 */
function locale_is_available($locale)
{
    return in_array($locale, get_available_locales());
}

function locale_pattern_check($locale)
{
    $locale_pattern = "/^[\w-]{2,5}$/";

    return preg_match($locale_pattern, $locale);
}

/**
 * Gets available locales list.
 *
 * Returns a list of locales which exist and are enabled in the system. That
 * list is statically cached inside the function.
 *
 * @return string[] List of available locales codes.
 */
function get_available_locales()
{
    static $available_locales = null;

    if (is_null($available_locales)) {
        if (get_maintenance_mode() !== false) {
            // We cannot rely on the database during maintenance, thus we only
            // can use discovered locales as available locales.
            $available_locales = discover_locales();
        } else {
            // Get list of enabled locales from the database.
            $rows = Database::getInstance()->query(
                "SELECT code FROM {locale} WHERE enabled = 1",
                array(),
                array('return_rows' => Database::RETURN_ALL_ROWS)
            );
            $enabled_locales = array();
            foreach ($rows as $row) {
                $enabled_locales[] = $row['code'];
            }

            $fs_locales = discover_locales();

            $available_locales = array_intersect($fs_locales, $enabled_locales);
        }
    }

    return $available_locales;
}

/**
 * Returns list of all locales that are present in the file system.
 *
 * @return array List of locales codes.
 */
function discover_locales()
{
    static $list = null;

    if (is_null($list)) {
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
    }

    return $list;
}

function get_user_locale()
{
    if (isset($_COOKIE[LOCALE_COOKIE_NAME])) {
        $requested_lang = $_COOKIE[LOCALE_COOKIE_NAME];
        if (locale_pattern_check($requested_lang) && locale_is_available($requested_lang)) {
            return $requested_lang;
        }
    }

    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $requested_langs = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($requested_langs as $requested_lang) {
            if (strlen($requested_lang) > 2) {
                $requested_lang = substr($requested_lang, 0, 2);
            }

            if (locale_pattern_check($requested_lang) && locale_is_available($requested_lang)) {
                return $requested_lang;
            }
        }
    }

    return get_default_locale();
}

/**
 * Returns a value of the default locale.
 *
 * Generally, the locale returned by the function, should be used as a user
 * locale if does not provide known lang.
 *
 * In fact the function returns verified value of "default_locale" variable from
 * the system configurations file.
 *
 * @return string Locale code.
 */
function get_default_locale()
{
    static $default_locale = null;

    if (is_null($default_locale)) {
        $configs = load_system_configs();
        $is_correct = !empty($configs['default_locale'])
            && locale_pattern_check($configs['default_locale'])
            && locale_is_available($configs['default_locale']);

        $default_locale = $is_correct ? $configs['default_locale'] : 'en';
    }

    return $default_locale;
}

/**
 * Returns a value of the home locale.
 *
 * Generally, the locale returned by the function, should be used as a locale
 * for operators' native names.
 *
 * In fact the function returns verified value of "home_locale" variable from
 * the system configurations file.
 *
 * @return string Locale code.
 */
function get_home_locale()
{
    static $home_locale = null;

    if (is_null($home_locale)) {
        $configs = load_system_configs();
        $is_correct = !empty($configs['home_locale'])
            && locale_pattern_check($configs['home_locale'])
            && locale_is_available($configs['home_locale']);

        $home_locale = $is_correct ? $configs['home_locale'] : 'en';
    }

    return $home_locale;
}

/**
 * Retrieves locale for the current request.
 *
 * @return string Locale code
 */
function get_current_locale()
{
    static $current_locale = null;

    if (is_null($current_locale)) {
        $locale = verify_param("locale", "/./", "");

        // Check if locale code passed in as a param is valid
        $locale_param_valid = $locale
            && locale_pattern_check($locale)
            && locale_is_available($locale);

        // Check if locale code stored in session data is valid
        $session_locale_valid = isset($_SESSION[SESSION_PREFIX . 'locale'])
            && locale_pattern_check($_SESSION[SESSION_PREFIX . 'locale'])
            && locale_is_available($_SESSION[SESSION_PREFIX . 'locale']);

        if ($locale_param_valid) {
            $_SESSION[SESSION_PREFIX . 'locale'] = $locale;
        } elseif ($session_locale_valid) {
            $locale = $_SESSION[SESSION_PREFIX . 'locale'];
        } else {
            $locale = get_user_locale();
        }

        $current_locale = $locale;
    }

    return $current_locale;
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
    foreach ($all_locales as $k) {
        $locale_info = get_locale_info($k);
        $locale_links[$k] = $locale_info ? $locale_info['name'] : $k;
    }

    return $locale_links;
}

/**
 * Returns meta data for all known locales.
 *
 * This function is deprecated. Use {get_locale_info()} instead.
 *
 * @deprecated since 2.1.0
 * @return array Associative arrays which keys are locale codes and the values
 *   are locales info.
 */
function get_locales()
{
    return array(
        'ar' => array(
            'name' => 'العربية',
            'rtl' => true,
            'time_locale' => 'ar_EG.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'be' => array(
            'name' => 'Беларуская',
            'rtl' => false,
            'time_locale' => 'be_BY.UTF8',
            'date_format' => array(
                'full' => '%d %B %Y, %H:%M',
                'date' => '%d %B %Y',
                'time' => '%H:%M',
            ),
        ),
        'bg' => array(
            'name' => 'Български',
            'rtl' => false,
            'time_locale' => 'bg_BG.UTF8',
            'date_format' => array(
                'full' => '%d %B %Y, %H:%M',
                'date' => '%d %B %Y',
                'time' => '%H:%M',
            ),
        ),
        'ca' => array(
            'name' => 'Català',
            'rtl' => false,
            'time_locale' => 'ca_ES.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y, %H:%M',
                'date' => '%B %d, %Y',
                'time' => '%H:%M',
            ),
        ),
        'cs' => array(
            'name' => 'Česky',
            'rtl' => false,
            'time_locale' => 'cs_CZ.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'da' => array(
            'name' => 'Dansk',
            'rtl' => false,
            'time_locale' => 'da_DK.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time_format' => '%I:%M %p',
            ),
        ),
        'de' => array(
            'name' => 'Deutsch',
            'rtl' => false,
            'time_locale' => 'de_DE.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %H:%M',
                'date' => '%B %d, %Y',
                'time' => '%H:%M',
            ),
        ),
        'el' => array(
            'name' => 'Ελληνικά',
            'rtl' => false,
            'time_locale' => 'el_GR.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'en' => array(
            'name' => 'English',
            'rtl' => false,
            'time_locale' => 'en_US',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'es' => array(
            'name' => 'Español',
            'rtl' => false,
            'time_locale' => 'es_ES.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %H:%M',
                'date' => '%B %d, %Y',
                'time' => '%H:%M',
            ),
        ),
        'et' => array(
            'name' => 'Eesti',
            'rtl' => false,
            'time_locale' => 'et_EE.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'fa' => array(
            'name' => 'فارسی',
            'rtl' => true,
            'time_locale' => 'fa_IR.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'fi' => array(
            'name' => 'Suomi',
            'rtl' => false,
            'time_locale' => 'fi_FI.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'fr' => array(
            'name' => 'Français',
            'rtl' => false,
            'time_locale' => 'fr_FR.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %H:%M',
                'date' => '%B %d, %Y',
                'time' => '%H:%M',
            ),
        ),
        'he' => array(
            'name' => 'עברית',
            'rtl' => true,
            'time_locale' => 'he_IL.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %H:%M',
                'date' => '%B %d, %Y',
                'time' => '%H:%M',
            ),
        ),
        'hr' => array(
            'name' => 'Hrvatski',
            'rtl' => false,
            'time_locale' => 'hr_HR.UTF8',
            'date_format' => array(
                'full' => '%d.%m.%Y %H:%M',
                'date' => '%d.%m.%Y',
                'time' => '%H:%M',
            ),
        ),
        'hu' => array(
            'name' => 'Magyar',
            'rtl' => false,
            'time_locale' => 'hu_HU.UTF8',
            'date_format' => array(
                'full' => '%Y-%B-%d %I:%M %p',
                'date' => '%Y-%B-%d',
                'time' => '%I:%M %p',
            ),
        ),
        'id' => array(
            'name' => 'Bahasa Indonesia',
            'rtl' => false,
            'time_locale' => 'id_ID.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'it' => array(
            'name' => 'Italiano',
            'rtl' => false,
            'time_locale' => 'it_IT.UTF8',
            'date_format' => array(
                'full' => '%d %b %Y, %H:%M',
                'date' => '%d %b %Y',
                'time' => '%H:%M',
            ),
        ),
        'ja' => array(
            'name' => '日本語',
            'rtl' => false,
            'time_locale' => 'ja_JP.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'ka' => array(
            'name' => 'ქართული',
            'rtl' => false,
            'time_locale' => 'ka_GE.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'kk' => array(
            'name' => 'Қазақша',
            'rtl' => false,
            'time_locale' => 'kk_KZ.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'ko' => array(
            'name' => '한국어',
            'rtl' => false,
            'time_locale' => 'ko_KR.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'ky' => array(
            'name' => 'Кыргызча',
            'rtl' => false,
            'time_locale' => 'ky_KG.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'lt' => array(
            'name' => 'Lietuvių',
            'rtl' => false,
            'time_locale' => 'lt_LT.UTF8',
            'date_format' => array(
                'full' => '%d %B %Y %H:%M',
                'date' => '%d %B %Y',
                'time' => '%H:%M',
            )
        ),
        'lv' => array(
            'name' => 'Latviešu',
            'rtl' => false,
            'time_locale' => 'lv_LV.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %H:%M',
                'date' => '%B %d, %Y',
                'time' => '%H:%M',
            ),
        ),
        'nl' => array(
            'name' => 'Nederlands',
            'rtl' => false,
            'time_locale' => 'nl_NL.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'nn' => array(
            'name' => 'Norsk nynorsk',
            'rtl' => false,
            'time_locale' => 'nn_NO.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'no' => array(
            'name' => 'Norsk bokmål',
            'rtl' => false,
            'time_locale' => 'no_NO.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'pl' => array(
            'name' => 'Polski',
            'rtl' => false,
            'time_locale' => 'pl_PL.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %H:%M',
                'date' => '%B %d, %Y',
                'time' => '%H:%M',
            ),
        ),
        'pt-pt' => array(
            'name' => 'Português',
            'rtl' => false,
            'time_locale' => 'pt_PT.UTF8',
            'date_format' => array(
                'full' => '%d %B, %Y %H:%M',
                'date' => '%d %B, %Y',
                'time' => '%H:%M',
            ),
        ),
        'pt-br' => array(
            'name' => 'Português Brasil',
            'rtl' => false,
            'time_locale' => 'pt_BR.UTF8',
            'date_format' => array(
                'full' => '%d %B, %Y %H:%M',
                'date' => '%d %B, %Y',
                'time' => '%H:%M',
            ),
        ),
        'ro' => array(
            'name' => 'Română',
            'rtl' => false,
            'time_locale' => 'ro_RO.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'ru' => array(
            'name' => 'Русский',
            'rtl' => false,
            'time_locale' => 'ru_RU.UTF8',
            'date_format' => array(
                'full' => '%d %B %Y, %H:%M',
                'date' => '%d %B %Y',
                'time' => '%H:%M',
            ),
        ),
        'sk' => array(
            'name' => 'Slovenčina',
            'rtl' => false,
            'time_locale' => 'sk_SK.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'sl' => array(
            'name' => 'Slovenščina',
            'rtl' => false,
            'time_locale' => 'sl_SI.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'sr' => array(
            'name' => 'Српски',
            'rtl' => false,
            'time_locale' => 'sr_RS.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'sv' => array(
            'name' => 'Svenska',
            'rtl' => false,
            'time_locale' => 'sv_SE.UTF8',
            'date_format' => array(
                'full' => '%B %d, %Y %H:%M',
                'date' => '%B %d, %Y',
                'time' => '%H:%M',
            ),
        ),
        'th' => array(
            'name' => 'ไทย',
            'rtl' => false,
            'time_locale' => 'th_TH.UTF8',
            'date_format' => array(
                'full' => '%d %B, %Y %I:%M %p',
                'date' => '%d %B, %Y',
                'time' => '%I:%M %p',
            ),
        ),
        'tr' => array(
            'name' => 'Türkçe',
            'rtl' => false,
            'time_locale' => 'tr_TR.UTF8',
            'date_format' => array(
                'full' => '%d.%m.%Y %H:%i',
                'date' => '%d.%m.%Y',
                'time' => '%H:%i',
            ),
        ),
        'ua' => array(
            'name' => 'Українська',
            'rtl' => false,
            'time_locale' => 'uk_UA.UTF8',
            'date_format' => array(
                'full' => '%d %B %Y, %H:%M',
                'date' => '%d %B %Y',
                'time' => '%H:%M',
            ),
        ),
        'zh-cn' => array(
            'name' => '中文',
            'rtl' => false,
            'time_locale' => 'zh_CN.UTF8',
            'date_format' => array(
                'full' => '%Y-%m-%d， %H:%M',
                'date' => '%Y-%m-%d',
                'time' => '%H:%M',
            ),
        ),
        'zh-tw' => array(
            'name' => '文言',
            'rtl' => false,
            'time_locale' => 'zh_TW.UTF8',
            'date_format' => array(
                'full' => '%Y-%m-%d， %H:%M',
                'date' => '%Y-%m-%d',
                'time' => '%H:%M',
            ),
        ),
    );
}

/**
 * Returns locale info by its code.
 *
 * @param string $locale
 * @return array|false Associative array of locale info or boolean false if the
 *   locale is unknown. Locale info array contains the following keys:
 *     - name: string, human readable locale name.
 *     - rtl: boolean, indicates with the locale uses right-to-left
 *       writing mode.
 *     - time_locale: string, locale code which is used in {@link setlocale()}
 *       function to set the correct date/time formatting.
 *     - date_format: array, list of available date formats. Each key of the
 *       array is format name and each value is a format string for
 *       {@link strftime()} function.
 */
function get_locale_info($locale)
{
    $cache = get_locale_info_cache();

    if (!isset($cache[$locale])) {
        if (get_maintenance_mode() === false) {
            // Load local info from the database
            $info = Database::getInstance()->query(
                'SELECT * FROM {locale} WHERE code = :code',
                array(':code' => $locale),
                array('return_rows' => Database::RETURN_ONE_ROW)
            );

            $cache[$locale] = $info
                ? array(
                    'name' => $info['name'],
                    'rtl' => (bool)$info['rtl'],
                    'time_locale' => $info['time_locale'],
                    'date_format' => unserialize($info['date_format'])
                )
                : false;
        } else {
            // Either installation or update is performed. Try to get locale
            // info from its config file.
            $config_path = MIBEW_FS_ROOT . '/locales/' . $locale . '/config.yml';
            $info = read_locale_config($config_path);

            $cache[$locale] = $info
                ? $info +  array(
                    'name' => $locale,
                    'rtl' => false,
                    'time_locale' => 'en_US',
                    'date_format' => array(
                        'full' => '%B %d, %Y %I:%M %p',
                        'date' => '%B %d, %Y',
                        'time' => '%I:%M %p',
                    ),
                )
                : false;
        }
    }

    return $cache[$locale];
}

/**
 * Updates locale meta info.
 *
 * @param string $locale Code of the locale to update.
 * @param array $info Associative array of locale's info. This array should
 * contain the follwing keys:
 *  - "name"
 *  - "rtl"
 *  - "time_locale"
 *  - "date_format"
 * See description of {@link get_locale_info()} function for keys meaning.
 * @return boolean True if the info is updated and false otherwise.
 * @throws \InvalidArgumentException If the $info array is invalid.
 */
function set_locale_info($locale, $info)
{
    // Make sure $info array is correct
    $missed_keys = array_diff(
        array(
            'name',
            'rtl',
            'time_locale',
            'date_format'
        ),
        array_keys($info)
    );

    if (count($missed_keys) > 0) {
        throw new \InvalidArgumentException(sprintf(
            'These fields are missed: "%s".',
            implode('", "', $missed_keys)
        ));
    }

    $success = Database::getInstance()->query(
        ('UPDATE {locale} SET name = :name, rtl = :rtl, '
            . 'time_locale = :time_locale, date_format = :date_format '
            . 'WHERE code = :code'),
        array(
            ':code' => $locale,
            ':name' => $info['name'],
            ':rtl' => $info['rtl'] ? 1 : 0,
            ':time_locale' => $info['time_locale'],
            ':date_format' => serialize($info['date_format'])
        )
    );

    if (!$success) {
        return false;
    }

    // Update the cache.
    $cache = get_locale_info_cache();
    $cache[$locale] = $info;

    return true;
}

/**
 * Retrieves locales info cache.
 *
 * @return array Locales info cached. It's stored as static variable inside the
 * function.
 */
function &get_locale_info_cache()
{
    static $cache = array();

    return $cache;
}

/**
 * Loads localized messages for the specified locale.
 *
 * In common case messages will be loaded from the database but if the
 * installation is runnig they will be loaded from files.
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
        $messages[$locale] = array();

        if (get_maintenance_mode() !== false) {
            // Load localized strings from files because we cannot rely on the
            // database during maintenance.
            $locale_file = MIBEW_FS_ROOT . "/locales/{$locale}/translation.po";
            $locale_data = read_locale_file($locale_file);

            $messages[$locale] = $locale_data['messages'];
        } else {
            // Load localizations from the database
            $messages[$locale] = load_db_messages($locale);
        }
    }

    return $messages[$locale];
}

/**
 * Loads localized messages from the database for the specified locale.
 *
 * @param string $locale Name of a locale whose messages should be loaded.
 * @return array Localized messages array
 */
function load_db_messages($locale)
{
    // Load localizations from the database
    $db = Database::getInstance();
    $db_messages = $db->query(
        'SELECT * FROM {translation} WHERE locale = ?',
        array($locale),
        array(
            'return_rows' => Database::RETURN_ALL_ROWS
        )
    );

    $messages = array();
    foreach ($db_messages as $message) {
        $messages[$message['source']] = $message['translation'];
    }

    return $messages;
}

/**
 * Imports localized messages from the specified file to the specified locale.
 *
 * @param string $locale Traget locale code.
 * @param string $file Full path to translation file.
 * @param boolean $override Indicates if messages should be overridden or not.
 */
function import_messages($locale, $file, $override = false)
{
    $available_messages = load_db_messages($locale);
    $locale_data = read_locale_file($file);

    foreach ($locale_data['messages'] as $source => $translation) {
        if (isset($available_messages[$source]) && !$override) {
            continue;
        }

        save_message($locale, $source, $translation);
    }
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
 * @param string|null $locale Target locale code. If null is passed in the
 *   current locale will be used.
 * @param boolean $raw Indicates if the result should be sanitized or not.
 * @return string Localized text.
 */
function getlocal($text, $params = null, $locale = null, $raw = false)
{
    if (is_null($locale)) {
        $locale = get_current_locale();
    }

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

    // The string is not localized, save it to the database to provide an
    // ability to translate it from the UI later. At the same time we cannot
    // rely on the database during maintenance, thus we should check the
    // current system state.
    if (get_maintenance_mode() === false) {
        save_message($locale, $string, $string);
    }

    // One can change english strings from the UI. Try to use these strings.
    if ($locale != 'en') {
        return get_localized_string($string, 'en');
    }

    // The string is not localized at all. Use it "as is".
    return $string;
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
    static $available_messages = array();

    if (empty($available_messages[$locale])) {
        $available_messages[$locale] = load_db_messages($locale);
    }

    // Prepare the value to save in the database.
    $translation = str_replace("\r", "", trim($value));

    $db = Database::getInstance();
    if (array_key_exists($key, $available_messages[$locale])) {
        // The string is already in the database. Update it.
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
        // There is no such string in the database. Create it.
        $db->query(
            ('INSERT INTO {translation} (locale, source, translation, hash) '
                . 'VALUES (:locale, :key, :translation, :hash)'),
            array(
                ':locale' => $locale,
                ':key' => $key,
                ':translation' => $translation,
                ':hash' => sha1($locale . '##'. $key),
            )
        );
        // The message is now in the database. Next time it should be updated.
        $available_messages[$locale][$key] = $value;
    }
}

/**
 * Enables specified locale.
 *
 * @param string $locale Locale code according to RFC 5646.
 */
function enable_locale($locale)
{
    $db = Database::getInstance();

    // Check if the locale exists in the database
    list($count) = $db->query(
        "SELECT COUNT(*) FROM {locale} WHERE code = :code",
        array(':code' => $locale),
        array(
            'return_rows' => Database::RETURN_ONE_ROW,
            'fetch_type' => Database::FETCH_NUM,
        )
    );

    if ($count == 0) {
        // The locale does not exist in the database. Create it.
        Database::getInstance()->query(
            ('INSERT INTO {locale} (code, enabled) VALUES(:code, :enabled)'),
            array(
                ':code' => $locale,
                ':enabled' => 1,
            )
        );

        // Import all locale-related info (translations, mail, templates,
        // formats...) to databse.
        import_locale_content($locale);
    } else {
        // The locale exists in the database. Update it.
        $db->query(
            "UPDATE {locale} SET enabled = :enabled WHERE code = :code",
            array(
                ':enabled' => 1,
                ':code' => $locale,
            )
        );
    }
}

/**
 * Disables specified locale.
 *
 * @param string $locale Locale code according to RFC 5646.
 */
function disable_locale($locale)
{
    Database::getInstance()->query(
        "UPDATE {locale} SET enabled = :enabled WHERE code = :code",
        array(
            ':enabled' => 0,
            ':code' => $locale,
        )
    );
}

/**
 * Imports all locale's content (messages, mail templates, configs) to database.
 *
 * This function does not create the locale in database so you have to create it
 * by yourself.
 *
 * @param string $locale Code of the locale to import.
 */
function import_locale_content($locale)
{
    $config = (read_locale_config(MIBEW_FS_ROOT . '/locales/' . $locale . '/config.yml') ?: array())
        + array(
            'name' => $locale,
            'rtl' => false,
            'time_locale' => 'en_US',
            'date_format' => array(
                'full' => '%B %d, %Y %I:%M %p',
                'date' => '%B %d, %Y',
                'time' => '%I:%M %p',
            ),
        );

    Database::getInstance()->query(
        ('UPDATE {locale} SET '
            . 'name = :name, rtl = :rtl, time_locale = :time_locale,'
            . 'date_format = :date_format '
            . 'WHERE code = :code'),
        array(
            ':code' => $locale,
            ':name' => $config['name'],
            ':rtl' => $config['rtl'] ? 1 : 0,
            ':time_locale' => $config['time_locale'],
            ':date_format' => serialize($config['date_format'])
        )
    );

    // Import localized messages to the just created locale
    import_messages(
        $locale,
        MIBEW_FS_ROOT . '/locales/' . $locale . '/translation.po',
        true
    );

    // Import canned messages for the locale if they exist in the locale's
    // files.
    $canned_messages_file = MIBEW_FS_ROOT . '/locales/' . $locale . '/canned_messages.yml';
    if (is_readable($canned_messages_file)) {
        import_canned_messages($locale, $canned_messages_file);
    }

    // Import mail templates for the locale if they exist in the locale's
    // files.
    $mail_templates_file = MIBEW_FS_ROOT . '/locales/' . $locale . '/mail_templates.yml';
    if (is_readable($mail_templates_file)) {
        MailUtils::importTemplates($locale, $mail_templates_file);
    }
}

/**
 * Reads locale's config files.
 *
 * @param string $path Path of the file to read.
 * @return boolean|array Boolean false if the file is not found and associative
 * configs array otherwise.
 */
function read_locale_config($path)
{
    if (!is_readable($path)) {
        return false;
    }

    $parser = new YamlParser();
    $config = $parser->parse(file_get_contents($path));

    return $config;
}
