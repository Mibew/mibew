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
use Mibew\Database;

$pagination_spacing = "&nbsp;&nbsp;&nbsp;";
$links_on_page = 5;

function generate_pagination_link($page, $title)
{
	$lnk = $_SERVER['REQUEST_URI'];
	$href = preg_replace("/\?page=\d+\&/", "?", preg_replace("/\&page=\d+/", "", $lnk));
	$href .= strstr($href, "?") ? "&page=$page" : "?page=$page";
	return "<a href=\"" . htmlspecialchars($href) . "\" class=\"pagelink\">$title</a>";
}

function generate_pagination_image($id, $alt)
{
	global $mibewroot;
	return "<img src=\"$mibewroot/images/$id.gif\" border=\"0\" alt=\"" . htmlspecialchars($alt) . "\"/>";
}

function prepare_pagination($items_count, $default_items_per_page = 15)
{
	global $page;

	if ($items_count) {
		$items_per_page = verifyparam("items", "/^\d{1,3}$/", $default_items_per_page);
		if ($items_per_page < 2)
			$items_per_page = 2;

		$total_pages = div($items_count + $items_per_page - 1, $items_per_page);
		$curr_page = verifyparam("page", "/^\d{1,6}$/", 1);

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

/**
 * Selects rows from database taking pagination into account.
 * 
 * @global array $page
 * @param string $fields Selected fields
 * @param string $table Table name in database
 * @param string $conditions Where close
 * @param string $order Order clause
 * @param string $countfields Field, substituted in SQL COUNT function
 * @param array $values Associative array of substituted values. Keys are named placeholders in the 
 *   query(see \Mibew\Database::query() and its $values parameter description)
 * 
 * @see \Mibew\Database::query()
 */
function select_with_pagintation($fields, $table, $conditions, $order, $countfields, $values)
{
	global $page;
	$db = Database::getInstance();

	list($count) = $db->query(
		"select count(". ($countfields ? $countfields : "*") .") from {$table} " .
		"where " . (count($conditions)  ? implode(" and ", $conditions) : "") .
		($order ? " " . $order : ""),
		$values,
		array(
			'return_rows' => Database::RETURN_ONE_ROW,
			'fetch_type' => Database::FETCH_NUM
		)
	);

	prepare_pagination($count);
	if ($count) {
		$p = $page['pagination'];
		$limit = $p['limit'];
		$page['pagination.items'] = $db->query(
			"select {$fields} from {$table} " .
			"where " . (count($conditions)  ? implode(" and ", $conditions) : "") .
			($order ? " " . $order : "") . " " . $limit,
			$values,
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);
	} else {
		$page['pagination.items'] = array();
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
		$result .= "<div class='pagination'>";
		$curr_page = $pagination['page'];

		$minPage = max($curr_page - $links_on_page, 1);
		$maxPage = min($curr_page + $links_on_page, $pagination['total']);

		if ($curr_page > 1) {
			$result .= generate_pagination_link($curr_page - 1, generate_pagination_image("prevpage", getlocal("tag.pagination.previous"))) . $pagination_spacing;
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
			$result .= $pagination_spacing . generate_pagination_link($curr_page + 1, generate_pagination_image("nextpage", getlocal("tag.pagination.next")));
		}
		$result .= "</div>";
	}
	return $result;
}

?>