<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
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
	return "<a href=\"$href\" class=\"pagelink\">$title</a>";
}

function generate_pagination_image($id) {
	global $webimroot;
	return "<img src=\"$webimroot/images/$id.gif\" border=\"0\"/>";
}

function setup_pagination($items) {
	global $page;

	if( $items ) {
		$items_per_page = verifyparam("items", "/^\d{1,3}$/", 2);
		if( $items_per_page < 2 )
			$items_per_page = 2;
	
		$total_pages = div(count($items) + $items_per_page - 1, $items_per_page);
		$curr_page = verifyparam("page", "/^\d{1,6}$/", 1);
	
		if( $curr_page < 1 )
			$curr_page = 1;
		if( $curr_page > $total_pages )
			$curr_page = $total_pages;
	
		$start_index = ($curr_page-1)*$items_per_page;
		$end_index = min($start_index+$items_per_page, count($items));
	    $page['pagination.items'] = array_slice($items, $start_index, $end_index-$start_index);
	    $page['pagination'] = 
	    	array(  "page" => $curr_page, "items" => $items_per_page, "total" => $total_pages, 
	    			"count" => count($items), "start" => $start_index, "end" => $end_index );
	} else {
    	$page['pagination.items'] = false;
    	$page['pagination'] = true;
	}
}

function setup_empty_pagination() {
	global $page;
    $page['pagination.items'] = false;
    $page['pagination'] = false;
}

function generate_pagination($pagination) {
	global $pagination_spacing, $links_on_page;
	$result = getstring2("tag.pagination.info",
		array($pagination['page'],$pagination['total'],$pagination['start']+1,$pagination['end'],$pagination['count']))."<br/>";

	if( $pagination['total'] > 1 ) {
		$result.="<br/><div class='pagination'>";
		$curr_page = $pagination['page'];

		$minPage = max( $curr_page - $links_on_page, 1 );
		$maxPage = min( $curr_page + $links_on_page, $pagination['total'] );
		
		if( $curr_page > 1 ) {
			$result .= generate_pagination_link($curr_page-1, generate_pagination_image("prevpage")).$pagination_spacing;
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
			$result .= $pagination_spacing.generate_pagination_link($curr_page+1, generate_pagination_image("nextpage"));
		}
		$result.="</div>";
	}
	return $result;
}

?>