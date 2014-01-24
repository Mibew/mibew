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

$pagination_spacing = "&nbsp;&nbsp;&nbsp;";
$links_on_page = 5;

function generate_pagination_link($page, $title, $raw = false)
{
	$lnk = $_SERVER['REQUEST_URI'];
	$href = preg_replace("/\?page=\d+\&/", "?", preg_replace("/\&page=\d+/", "", $lnk));
	$href .= strstr($href, "?") ? "&page=$page" : "?page=$page";
	return "<a href=\"" . safe_htmlspecialchars($href) . "\" class=\"pagelink\">" . ($raw ? $title : safe_htmlspecialchars($title)) . "</a>";
}

function generate_pagination_image($id, $alt)
{
	global $mibewroot;
	return "<img src=\"$mibewroot/images/$id.gif\" border=\"0\" alt=\"" . safe_htmlspecialchars($alt) . "\"/>";
}

function prepare_pagination($items_count, $default_items_per_page = 15)
{
	global $page;

	$items_count = intval($items_count);

	if ($items_count) {
		$items_per_page = intval(verifyparam("items", "/^\d{1,3}$/", $default_items_per_page));
		if ($items_per_page < 2)
			$items_per_page = 2;

		$total_pages = div($items_count + $items_per_page - 1, $items_per_page);
		$curr_page = intval(verifyparam("page", "/^\d{1,6}$/", 1));

		if ($curr_page < 1)
			$curr_page = 1;
		if ($curr_page > $total_pages)
			$curr_page = $total_pages;

		$start_index = ($curr_page - 1) * $items_per_page;
		$end_index = min($start_index + $items_per_page, $items_count);
		$page['pagination'] =
				array("page" => $curr_page, "items" => $items_per_page, "total" => $total_pages,
					  "count" => $items_count, "start" => $start_index, "end" => $end_index,
					  "limit" => "LIMIT $start_index," . ($end_index - $start_index));
	} else {
		$page['pagination'] = true;
	}
}

function setup_pagination($items, $default_items_per_page = 15)
{
	global $page;
	prepare_pagination($items ? count($items) : 0, $default_items_per_page);
	if ($items && count($items) > 0) {
		$p = $page['pagination'];
		$page['pagination.items'] = array_slice($items, $p['start'], $p['end'] - $p['start']);
	} else {
		$page['pagination.items'] = false;
	}
}

function select_with_pagintation($fields, $table, $conditions, $order, $countfields, $link)
{
	global $page;
	$count = db_rows_count($table, $conditions, $countfields, $link);
	prepare_pagination($count);
	if ($count) {
		$p = $page['pagination'];
		$limit = $p['limit'];
		$page['pagination.items'] = select_multi_assoc(db_build_select($fields, $table, $conditions, $order) . " " . $limit, $link);
	} else {
		$page['pagination.items'] = false;
	}
}

function setup_empty_pagination()
{
	global $page;
	$page['pagination.items'] = false;
	$page['pagination'] = false;
}

function generate_pagination($pagination, $bottom = true)
{
	global $pagination_spacing, $links_on_page;
	$result = getlocal2("tag.pagination.info",
						array($pagination['page'], $pagination['total'], $pagination['start'] + 1, $pagination['end'], $pagination['count'])) . "<br/>";

	if ($pagination['total'] > 1) {
		if (!$bottom) {
			$result = "";
		} else {
			$result .= "<br/>";
		}
		$result .= "<div class=\"pagination\">";
		$curr_page = $pagination['page'];

		$minPage = max($curr_page - $links_on_page, 1);
		$maxPage = min($curr_page + $links_on_page, $pagination['total']);

		if ($curr_page > 1) {
			$result .= generate_pagination_link($curr_page - 1, generate_pagination_image("prevpage", getlocal("tag.pagination.previous")), true) . $pagination_spacing;
		}

		for ($i = $minPage; $i <= $maxPage; $i++) {
			$title = abs($curr_page - $i) >= $links_on_page && $i != 1 ? "..." : $i;
			if ($i != $curr_page)
				$result .= generate_pagination_link($i, $title);
			else
				$result .= "<span class=\"pagecurrent\">$title</span>";
			if ($i < $maxPage)
				$result .= $pagination_spacing;
		}

		if ($curr_page < $pagination['total']) {
			$result .= $pagination_spacing . generate_pagination_link($curr_page + 1, generate_pagination_image("nextpage", getlocal("tag.pagination.next")), true);
		}
		$result .= "</div>";
	}
	return $result;
}

?>