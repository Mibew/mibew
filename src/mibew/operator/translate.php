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

// Import namespaces and classes of the core
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();
force_password($operator);
csrf_check_token();

$source = verify_param("source", "/^[\w-]{2,5}$/", DEFAULT_LOCALE);
$target = verify_param("target", "/^[\w-]{2,5}$/", CURRENT_LOCALE);
$string_id = verify_param("key", "/^[_\.\w]+$/", "");

if (!isset($messages[$source])) {
    load_messages($source);
}
$lang1 = $messages[$source];
if (!isset($messages[$target])) {
    load_messages($target);
}
$lang2 = $messages[$target];

$page = array(
    'lang1' => $source,
    'lang2' => $target,
    'title1' => (isset($lang1["localeid"]) ? $lang1["localeid"] : $source),
    'title2' => (isset($lang2["localeid"]) ? $lang2["localeid"] : $target),
    'errors' => array(),
);

$page_style = new PageStyle(PageStyle::getCurrentStyle());

if ($string_id) {
    $translation = isset($lang2[$string_id]) ? $lang2[$string_id] : "";
    if (isset($_POST['translation'])) {

        $translation = get_param('translation');
        if (!$translation) {
            $page['errors'][] = no_field("form.field.translation");
        }

        if (count($page['errors']) == 0) {
            save_message($target, $string_id, $translation);

            $page['saved'] = true;
            $page['title'] = getlocal("page.translate.title");
            $page = array_merge(
                $page,
                prepare_menu($operator, false)
            );
            $page_style->render('translate', $page);
            exit;
        }
    }

    $page['saved'] = false;
    $page['key'] = $string_id;
    $page['target'] = $target;
    $page['formoriginal'] = isset($lang1[$string_id]) ? $lang1[$string_id] : "<b><unknown></b>";
    $page['formtranslation'] = $translation;
    $page['title'] = getlocal("page.translate.title");
    $page = array_merge(
        $page,
        prepare_menu($operator, false)
    );
    $page_style->render('translate', $page);
    exit;
}

$locales_list = array();
$all_locales = get_available_locales();
foreach ($all_locales as $loc) {
    $locales_list[] = array("id" => $loc, "name" => getlocal_("localeid", $loc));
}

$show = verify_param("show", "/^(all|s1|s2|s3)$/", "all");

$result = array();
$all_keys = array_keys($lang1);
if ($show == 's1') {
    $all_keys = array_intersect($all_keys, locale_load_id_list('level1'));
} elseif ($show == 's2') {
    $all_keys = array_intersect($all_keys, locale_load_id_list('level2'));
} elseif ($show == 's3') {
    $all_keys = array_diff($all_keys, locale_load_id_list('level1'), locale_load_id_list('level2'));
}

foreach ($all_keys as $key) {
    $t_source = htmlspecialchars($lang1[$key]);
    if (isset($lang2[$key])) {
        $value = htmlspecialchars($lang2[$key]);
    } else {
        $value = "<font color=\"#c13030\"><b>absent</b></font>";
    }
    $result[] = array(
        'id' => $key,
        'l1' => $t_source,
        'l2' => $value,
        'idToPage' => $key,
        'l1ToPage' => $t_source,
        'l2ToPage' => $value,
    );
}

$order = verify_param("sort", "/^(id|l1)$/", "id");
if ($order == 'id') {
    usort(
        $result,
        function ($a, $b) {
            return strcmp($a['id'], $b['id']);
        }
    );
} elseif ($order == 'l1') {
    usort(
        $result,
        function ($a, $b) {
            return strcmp($a['l1'], $b['l1']);
        }
    );
}

$pagination = setup_pagination($result, 100);
$page['pagination'] = $pagination['info'];
$page['pagination.items'] = $pagination['items'];

$page['formtarget'] = $target;
$page['formsource'] = $source;
$page['availableLocales'] = $locales_list;
$page['availableOrders'] = array(
    array("id" => "id", "name" => getlocal("translate.sort.key")),
    array("id" => "l1", "name" => getlocal("translate.sort.lang")),
);
$page['formsort'] = $order;
$page['showOptions'] = array(
    array("id" => "all", "name" => getlocal("translate.show.all")),
    array("id" => "s1", "name" => getlocal("translate.show.forvisitor")),
    array("id" => "s2", "name" => getlocal("translate.show.foroperator")),
    array("id" => "s3", "name" => getlocal("translate.show.foradmin")),
);
$page['formshow'] = $show;

$page['title'] = getlocal("page.translate.title");
$page['menuid'] = "translate";

$page = array_merge($page, prepare_menu($operator));

$page_style->render('translate_list', $page);
