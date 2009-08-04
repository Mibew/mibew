<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

$pagination_spacing = "&nbsp;&nbsp;&nbsp;";
$links_on_page = 5;

function generate_pagination_link($page,$title) {
	$lnk = $_SERVER['REQUEST_URI'];
	$href = preg_replace("/\?page=\d+\&/", "?", preg_replace("/\&page=\d+/", "", $lnk));
	$href .= strstr($href,"?") ? "&page=$page" : "?page=$page";
	return "<a href=\"".htmlspecialchars($href)."\" class=\"pagelink\">$title</a>";
}

function generate_pagination_image($id,$alt) {
	global $webimroot;
	return "<img src=\"$webimroot/images/$id.gif\" border=\"0\" alt=\"".htmlspecialchars($alt)."\"/>";
}

function prepare_pagination($items_count,$default_items_per_page=15) {
	global $page;

	if( $items_count ) {
		$items_per_page = verifyparam("items", "/^\d{1,3}$/", $default_items_per_page);
		if( $items_per_page < 2 )
			$items_per_page = 2;

		$total_pages = div($items_count + $items_per_page - 1, $items_per_page);
		$curr_page = verifyparam("page", "/^\d{1,6}$/", 1);

		if( $curr_page < 1 )
			$curr_page = 1;
		if( $curr_page > $total_pages )
			$curr_page = $total_pages;

		$start_index = ($curr_page-1)*$items_per_page;
		$end_index = min($start_index+$items_per_page, $items_count);
		$page['pagination'] =
			array(  "page" => $curr_page, "items" => $items_per_page, "total" => $total_pages,
					"count" => $items_count, "start" => $start_index, "end" => $end_index,
					"limit" => "LIMIT $start_index,".($end_index - $start_index) );
	} else {
		$page['pagination'] = true;
	}
}

function setup_pagination($items,$default_items_per_page=15) {
	global $page;
	prepare_pagination($items ? count($items) : 0, $default_items_per_page);
	if($items && count($items) > 0) {
		$p = $page['pagination'];
		$page['pagination.items'] = array_slice($items, $p['start'], $p['end']-$p['start']);
	} else {
		$page['pagination.items'] = false;
	}
}

function setup_empty_pagination() {
	global $page;
	$page['pagination.items'] = false;
	$page['pagination'] = false;
}

function generate_pagination($pagination,$bottom=true) {
	global $pagination_spacing, $links_on_page;
	$result = getlocal2("tag.pagination.info",
		array($pagination['page'],$pagination['total'],$pagination['start']+1,$pagination['end'],$pagination['count']))."<br/>";

	if( $pagination['total'] > 1 ) {
		if(!$bottom) {
			$result = "";
		} else {
			$result .= "<br/>";
		}
		$result.="<div class='pagination'>";
		$curr_page = $pagination['page'];

		$minPage = max( $curr_page - $links_on_page, 1 );
		$maxPage = min( $curr_page + $links_on_page, $pagination['total'] );

		if( $curr_page > 1 ) {
			$result .= generate_pagination_link($curr_page-1, generate_pagination_image("prevpage", getlocal("tag.pagination.previous"))).$pagination_spacing;
		}

		for($i = $minPage; $i <= $maxPage; $i++ ) {
			$title = abs($curr_page-$i) >= $links_on_page && $i != 1 ? "..." : $i;
			if( $i != $curr_page)
				$result .= generate_pagination_link($i, $title);
			else
				$result .= "<span class=\"pagecurrent\">$title</span>";
			if( $i < $maxPage )
				$result .= $pagination_spacing;
		}

		if( $curr_page < $pagination['total'] ) {
			$result .= $pagination_spacing.generate_pagination_link($curr_page+1, generate_pagination_image("nextpage", getlocal("tag.pagination.next")));
		}
		$result.="</div>";
	}
	return $result;
}

?>