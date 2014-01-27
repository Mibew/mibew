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

// Import namespaces and classes of the core
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/pagination.php');

$operator = check_login();
force_password($operator);
csrfchecktoken();

$source = verifyparam("source", "/^[\w-]{2,5}$/", DEFAULT_LOCALE);
$target = verifyparam("target", "/^[\w-]{2,5}$/", CURRENT_LOCALE);
$stringid = verifyparam("key", "/^[_\.\w]+$/", "");

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
	'title1' => isset($lang1["localeid"]) ? $lang1["localeid"] : $source,
	'title2' => isset($lang2["localeid"]) ? $lang2["localeid"] : $target,
	'errors' => array(),
);

$page_style = new PageStyle(PageStyle::currentStyle());

if ($stringid) {
	$translation = isset($lang2[$stringid]) ? $lang2[$stringid] : "";
	if (isset($_POST['translation'])) {

		$translation = getparam('translation');
		if (!$translation) {
			$page['errors'][] = no_field("form.field.translation");
		}

		if (count($page['errors']) == 0) {
			save_message($target, $stringid, $translation);

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
	$page['key'] = $stringid;
	$page['target'] = $target;
	$page['formoriginal'] = isset($lang1[$stringid]) ? $lang1[$stringid] : "<b><unknown></b>";
	$page['formtranslation'] = $translation;
	$page['title'] = getlocal("page.translate.title");
	$page = array_merge(
		$page,
		prepare_menu($operator, false)
	);
	$page_style->render('translate', $page);
	exit;
}

$localesList = array();
$allLocales = get_available_locales();
foreach ($allLocales as $loc) {
	$localesList[] = array("id" => $loc, "name" => getlocal_("localeid", $loc));
}

$show = verifyparam("show", "/^(all|s1|s2|s3)$/", "all");

$result = array();
$allkeys = array_keys($lang1);
if ($show == 's1') {
	$allkeys = array_intersect($allkeys, locale_load_idlist('level1'));
} else if ($show == 's2') {
	$allkeys = array_intersect($allkeys, locale_load_idlist('level2'));
} else if ($show == 's3') {
	$allkeys = array_diff($allkeys, locale_load_idlist('level1'), locale_load_idlist('level2'));
}

foreach ($allkeys as $key) {
	if ($key != 'output_charset') {
		$tsource = htmlspecialchars($lang1[$key]);
		if (isset($lang2[$key])) {
			$value = htmlspecialchars($lang2[$key]);
		} else {
			$value = "<font color=\"#c13030\"><b>absent</b></font>";
		}
		$result[] = array(
			'id' => $key,
			'l1' => $tsource,
			'l2' => $value);
	}
}

$order = verifyparam("sort", "/^(id|l1)$/", "id");
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
$page['availableLocales'] = $localesList;
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

$page = array_merge(
	$page,
	prepare_menu($operator)
);

$page_style->render('translatelist', $page);

?>