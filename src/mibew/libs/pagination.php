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

/**
 * String used to separate pagination links from each other
 */
define('PAGINATION_SPACING', "&nbsp;&nbsp;&nbsp;");

/**
 * Count of pagination links on the page
 */
define('PAGINATION_LINKS_ON_PAGE', 5);

/**
 * Builds HTML markup for pagination link based on currently requested page.
 *
 * @param int $page Page number
 * @param string $title Link title
 * @return string HTML markup
 */
function generate_pagination_link($page, $title)
{
    $lnk = $_SERVER['REQUEST_URI'];
    $href = preg_replace("/\?page=\d+\&/", "?", preg_replace("/\&page=\d+/", "", $lnk));
    $href .= strstr($href, "?") ? "&page=$page" : "?page=$page";

    return "<a href=\"" . htmlspecialchars($href) . "\" class=\"pagelink\">$title</a>";
}

/**
 * Builds HTML markup for pagination arrow
 *
 * The resulting markup is a div tag with specifed class and title.
 *
 * @param string $class Name of the CSS class which should be used.
 * @param string $title Value of an 'title' attribute of the div tag.
 * @return string HTML markup
 */
function generate_pagination_arrow($class, $title)
{
    return '<div class="' . $class . '" title="' . htmlspecialchars($title) . '"></div>';
}

/**
 * Returns information about pagination.
 *
 * @param int $items_count Count of items which are separated by pages.
 * @param int $default_items_per_page Count of items per page.
 * @return array|boolean Associative array of pagination info or FALSE if the
 * info array cannot be build. Info array contatins the following keys:
 *   - page: int, number of current page.
 *   - total: int, total pages count.
 *   - items: int, items per page.
 *   - count: int, total items count.
 *   - start: int, index of item to start from.
 *   - end: int, index of item to end at.
 */
function pagination_info($items_count, $default_items_per_page = 15)
{
    if ($items_count) {
        $items_per_page = verify_param("items", "/^\d{1,3}$/", $default_items_per_page);
        if ($items_per_page < 2) {
            $items_per_page = 2;
        }

        $total_pages = div($items_count + $items_per_page - 1, $items_per_page);
        $curr_page = verify_param("page", "/^\d{1,6}$/", 1);

        if ($curr_page < 1) {
            $curr_page = 1;
        }
        if ($curr_page > $total_pages) {
            $curr_page = $total_pages;
        }

        $start_index = ($curr_page - 1) * $items_per_page;
        $end_index = min($start_index + $items_per_page, $items_count);

        return array(
            "page" => $curr_page,
            "items" => $items_per_page,
            "total" => $total_pages,
            "count" => $items_count,
            "start" => $start_index,
            "end" => $end_index,
        );
    } else {
        return false;
    }
}

/**
 * Prepare all info that needed to build paginated items
 *
 * @param array $items Items which are separated by pages.
 * @param int $default_items_per_page Count of items per page.
 * @return array Associative array of with the following keys:
 *   - info: array, pagination info. See description of the result of
 *     pagination_info function for details.
 *   - items: slice of items to display.
 */
function setup_pagination($items, $default_items_per_page = 15)
{
    if (count($items) > 0) {
        $info = pagination_info(count($items), $default_items_per_page);
        if ($info) {
            $items_slice = array_slice(
                $items,
                $info['start'],
                $info['end'] - $info['start']
            );

            return array(
                'info' => $info,
                'items' => $items_slice,
            );
        }
    }

    return array(
        'info' => false,
        'items' => false,
    );
}

/**
 * Builds HTML markup for pagination pager.
 *
 * @param array $pagination Pagination info. See description of the result of
 * pagination_info function for details.
 * @param bool $bottom Indicates if pager will be displayed at the bottom of a
 * page.
 * @return string HTML markup
 */
function generate_pagination($pagination, $bottom = true)
{
    $result = getlocal(
        'Page {0} of {1}, {2}-{3} from {4}',
        array(
            $pagination['page'],
            $pagination['total'],
            $pagination['start'] + 1,
            $pagination['end'],
            $pagination['count'],
        )
    ) . "<br/>";

    if ($pagination['total'] > 1) {
        if (!$bottom) {
            $result = "";
        } else {
            $result .= "<br/>";
        }
        $result .= "<div class='pagination'>";
        $curr_page = $pagination['page'];

        $min_page = max($curr_page - PAGINATION_LINKS_ON_PAGE, 1);
        $max_page = min($curr_page + PAGINATION_LINKS_ON_PAGE, $pagination['total']);

        if ($curr_page > 1) {
            $result .= generate_pagination_link(
                $curr_page - 1,
                generate_pagination_arrow(
                    "prev-page",
                    getlocal("previous")
                )
            ) . PAGINATION_SPACING;
        }

        for ($i = $min_page; $i <= $max_page; $i++) {
            $title = (abs($curr_page - $i) >= PAGINATION_LINKS_ON_PAGE && $i != 1) ? "..." : $i;
            if ($i != $curr_page) {
                $result .= generate_pagination_link($i, $title);
            } else {
                $result .= "<span class=\"pagecurrent\">$title</span>";
            }
            if ($i < $max_page) {
                $result .= PAGINATION_SPACING;
            }
        }

        if ($curr_page < $pagination['total']) {
            $result .= PAGINATION_SPACING . generate_pagination_link(
                $curr_page + 1,
                generate_pagination_arrow(
                    "next-page",
                    getlocal("next")
                )
            );
        }
        $result .= "</div>";
    }

    return $result;
}
