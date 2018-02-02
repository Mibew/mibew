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

namespace Mibew\Controller;

use Mibew\Http\Exception\BadRequestException;
use Mibew\Settings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Display all statistics-related pages
 */
class StatisticsController extends AbstractController
{
    const TYPE_BY_DATE = 'by-date';
    const TYPE_BY_PAGE = 'by-page';
    const TYPE_BY_OPERATOR = 'by-operator';

    /**
     * Generates a page with statistics info.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        $operator = $this->getOperator();
        $statistics_type = $request->attributes->get('type');

        $page = array();
        $page['operator'] = get_operator_name($operator);
        $page['availableDays'] = range(1, 31);
        $page['availableMonth'] = get_month_selection(
            time() - 400 * 24 * 60 * 60,
            time() + 50 * 24 * 60 * 60
        );
        $page['showresults'] = false;
        $page['type'] = $statistics_type;
        $page['showbydate'] = ($statistics_type == self::TYPE_BY_DATE);
        $page['showbyagent'] = ($statistics_type == self::TYPE_BY_OPERATOR);
        $page['showbypage'] = ($statistics_type == self::TYPE_BY_PAGE);

        $cron_uri = $this->generateUrl(
            'cron',
            array('cron_key' => Settings::get('cron_key')),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $page['pageDescription'] = getlocal(
            'From this page you can generate a variety of usage reports. Last time statistics was calculated {0}. You can calculate it <a href="{1}" target="_blank">manually</a>.',
            array(
                date_to_text(Settings::get('_last_cron_run')),
                $cron_uri,
            )
        );

        $page['show_invitations_info'] = (bool) Settings::get('enabletracking');
        $page['errors'] = array();

        // Get and validate time interval
        $time_interval = $this->extractTimeInterval($request);
        $start = $time_interval['start'];
        $end = $time_interval['end'];
        if ($start > $end) {
            $page['errors'][] = getlocal('You have selected From date after Till date');
        }

        $page = array_merge(
            $page,
            set_form_date($start, 'start'),
            set_form_date($end - 24 * 60 * 60, 'end')
        );

        // Get statistics info
        if ($statistics_type == self::TYPE_BY_DATE) {
            $statistics = get_by_date_statistics($start, $end);
            $page['reportByDate'] = $statistics['records'];
            $page['reportByDateTotal'] = $statistics['total'];
        } elseif ($statistics_type == self::TYPE_BY_OPERATOR) {
            $page['reportByAgent'] = get_by_operator_statistics($start, $end);
        } elseif ($statistics_type == self::TYPE_BY_PAGE) {
            $page['reportByPage'] = get_by_page_statistics($start, $end);
        }

        $page['showresults'] = count($page['errors']) == 0;
        $page['title'] = getlocal("Statistics");
        $page['menuid'] = "statistics";
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('statistics', $page);
    }

    /**
     * Builds list of the statistics tabs.
     *
     * @param Request $request Current request.
     * @return array Tabs list. The keys of the array are tabs titles and the
     *   values are tabs URLs.
     */
    protected function buildTabs(Request $request)
    {
        $tabs = array();
        $args = $request->query->all();
        $type = $request->attributes->get('type');

        $tabs[getlocal('Usage statistics for each date')] = $type != self::TYPE_BY_DATE
            ? $this->generateUrl('statistics', ($args + array('type' => self::TYPE_BY_DATE)))
            : '';

        $tabs[getlocal('Threads by operator')] = $type != self::TYPE_BY_OPERATOR
            ? $this->generateUrl('statistics', ($args + array('type' => self::TYPE_BY_OPERATOR)))
            : '';

        if (Settings::get('enabletracking')) {
            $tabs[getlocal('Chat threads by page')] = $type != self::TYPE_BY_PAGE
                ? $this->generateUrl('statistics', ($args + array('type' => self::TYPE_BY_PAGE)))
                : '';
        }

        return $tabs;
    }

    /**
     * Extracts start and end timestamps from the interval related with the
     * request.
     *
     * @param Request $request Incoming request
     * @return array Associative array with the following keys:
     *  - "start": int, timestamp for beginning of the interval.
     *  - "end": int, timestamp for ending of the interval.
     */
    protected function extractTimeInterval(Request $request)
    {
        if ($request->query->has('startday')) {
            // The request contains info about interval.
            $start_day = $request->query->get('startday');
            $start_month = $request->query->get('startmonth');
            $end_day = $request->query->get('endday');
            $end_month = $request->query->get('endmonth');

            // Check if all necessary info is specified.
            $bad_request = !preg_match("/^\d+$/", $start_day)
                || !preg_match("/^\d{2}.\d{2}$/", $start_month)
                || !preg_match("/^\d+$/", $end_day)
                || !preg_match("/^\d{2}.\d{2}$/", $end_month);
            if ($bad_request) {
                throw new BadRequestException();
            }

            return array(
                'start' => get_form_date($start_day, $start_month),
                'end' => get_form_date($end_day, $end_month) + 24 * 60 * 60,
            );
        }

        // The request does not contain info about interval. Use defaults.
        $curr = getdate(time());
        if ($curr['mday'] < 7) {
            // Use previous month if it is the first week of the month
            if ($curr['mon'] == 1) {
                $month = 12;
                $year = $curr['year'] - 1;
            } else {
                $month = $curr['mon'] - 1;
                $year = $curr['year'];
            }

            $start = mktime(0, 0, 0, $month, 1, $year);
            $end = mktime(0, 0, 0, $month, date('t', $start), $year) + 24 * 60 * 60;

            return array(
                'start' => $start,
                'end' => $end,
            );
        }

        // Use the current month
        return array(
            'start' => mktime(0, 0, 0, $curr['mon'], 1, $curr['year']),
            'end' => time() + 24 * 60 * 60,
        );
    }
}
