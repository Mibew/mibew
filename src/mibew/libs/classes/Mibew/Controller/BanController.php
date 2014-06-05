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

namespace Mibew\Controller;

use Mibew\Database;
use Mibew\Http\Exception\BadRequestException;
use Mibew\Http\Exception\NotFoundException;
use Mibew\Thread;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with banned visitors.
 */
class BanController extends AbstractController
{
    /**
     * Generates list of all banned visitors in the system.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function indexAction(Request $request)
    {
        set_csrf_token();
        setlocale(LC_TIME, getlocal('time.locale'));

        $operator = $this->getOperator();
        $page = array(
            'errors' => array(),
        );

        // Prepare list of all banned visitors
        $db = Database::getInstance();
        $blocked_list = $db->query(
            "SELECT banid, dtmtill AS till, address, comment FROM {chatban}",
            null,
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        foreach ($blocked_list as &$item) {
            $item['comment'] = $item['comment'];
        }
        unset($item);

        $page['title'] = getlocal('page_bans.title');
        $page['menuid'] = 'bans';
        $pagination = setup_pagination($blocked_list);
        $page['pagination'] = $pagination['info'];
        $page['pagination.items'] = $pagination['items'];
        $page = array_merge($page, prepare_menu($operator));

        return $this->render('bans', $page);
    }

    /**
     * Removes a ban from the database.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function deleteAction(Request $request)
    {
        csrf_check_token($request);

        $ban_id = $request->attributes->getInt('ban_id');

        // Remove ban from database
        $db = Database::getInstance();
        $db->query("DELETE FROM {chatban} WHERE banid = ?", array($ban_id));

        // Redirect the current operator to page with bans list
        return $this->redirect($this->generateUrl('bans'));
    }

    /**
     * Builds a page with form for add/edit ban.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the ban with specified ID is not found in
     *   the system.
     * @throws BadRequestException If "thread" GET param is specified but has a
     *   wrong format.
     */
    public function showEditFormAction(Request $request)
    {
        set_csrf_token();

        $operator = $this->getOperator();

        $page = array(
            'banId' => '',
            'saved' => false,
            'thread' => '',
            'threadid' => '',
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitEditForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        if ($request->attributes->has('ban_id')) {
            $ban_id = $request->attributes->getInt('ban_id');

            // Retrieve ban information from the database
            $db = Database::getInstance();
            $ban = $db->query(
                ("SELECT banid, (dtmtill - :now) AS days, address, comment "
                    . "FROM {chatban} WHERE banid = :banid"),
                array(
                    ':banid' => $ban_id,
                    ':now' => time(),
                ),
                array('return_rows' => Database::RETURN_ONE_ROW)
            );

            if (!$ban) {
                throw new NotFoundException('The ban is not found.');
            }

            $page['banId'] = $ban['banid'];
            $page['formaddress'] = $ban['address'];
            $page['formdays'] = round($ban['days'] / 86400);
            $page['formcomment'] = $ban['comment'];
        } elseif ($request->query->has('thread')) {
            // Prepopulate form using thread data
            $thread_id = $request->query->has('thread');
            if (!preg_match("/^\d{1,10}$/", $thread_id)) {
                throw new BadRequestException('Wrong value of "thread" argument.');
            }

            $thread = Thread::load($thread_id);
            if ($thread) {
                $page['thread'] = htmlspecialchars($thread->userName);
                $page['threadid'] = $thread_id;
                $page['formaddress'] = $thread->remote;
                $page['formdays'] = 15;
            }
        }

        // Override form fields from the request if it is needed
        if ($request->isMethod('POST')) {
            $page['formaddress'] = $request->request->get('address');
            $page['formdays'] = $request->request->get('days');
            $page['formcomment'] = $request->request->get('comment');
            $page['threadid'] = $request->request->get('threadid');
        }

        $page['title'] = getlocal('page_ban.title');
        $page['formaction'] = $request->getBaseUrl() . $request->getPathInfo();
        $page = array_merge($page, prepare_menu($operator, false));

        return $this->render('ban', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\BanController::showEditFormAction()} method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function submitEditFormAction(Request $request)
    {
        csrf_check_token($request);

        $operator = $this->getOperator();
        $errors = array();

        $page = array(
            'banId' => '',
            'saved' => false,
        );

        // Get form fields and validate them
        $ban_id = $request->attributes->getInt('ban_id');
        $address = $request->request->get('address');
        $days = $request->request->get('days');
        $comment = $request->request->get('comment');

        if (!$address) {
            $errors[] = no_field('form.field.address');
        }

        if (!preg_match("/^\d+$/", $days)) {
            $errors[] = wrong_field('form.field.ban_days');
        }

        if (!$comment) {
            $errors[] = no_field('form.field.ban_comment');
        }

        // Check if the ban already exists in the database
        $existing_ban = ban_for_addr($address);
        $ban_duplicate = (!$ban_id && $existing_ban)
            || ($ban_id && $existing_ban && $ban_id != $existing_ban['banid']);

        if ($ban_duplicate) {
            $ban_url = $this->generateUrl(
                'ban_edit',
                array('ban_id' => $existing_ban['banid'])
            );
            $errors[] = getlocal('ban.error.duplicate', array($address, $ban_url));
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showEditFormAction($request);
        }

        // Save ban into the database
        $db = Database::getInstance();
        $now = time();
        $till_time = $now + $days * 24 * 60 * 60;
        if (!$ban_id) {
            $db->query(
                ("INSERT INTO {chatban} (dtmcreated, dtmtill, address, comment) "
                    . "VALUES (:now,:till,:address,:comment)"),
                array(
                    ':now' => $now,
                    ':till' => $till_time,
                    ':address' => $address,
                    ':comment' => $comment,
                )
            );
        } else {
            $db->query(
                ("UPDATE {chatban} SET dtmtill = :till, address = :address, "
                    . "comment = :comment WHERE banid = :banid"),
                array(
                    ':till' => $till_time,
                    ':address' => $address,
                    ':comment' => $comment,
                    ':banid' => $ban_id,
                )
            );
        }

        // Rerender the form page
        $page['saved'] = true;
        $page['address'] = $address;
        $page['title'] = getlocal('page_ban.title');
        $page = array_merge($page, prepare_menu($operator, false));

        return $this->render('ban', $page);
    }
}
