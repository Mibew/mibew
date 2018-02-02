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

use Mibew\Ban;
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
        $operator = $this->getOperator();
        $page = array(
            'errors' => array(),
        );

        // Prepare list of all banned visitors
        $blocked_list = array();
        foreach (Ban::all() as $ban) {
            $blocked_list[] = array(
                'banid' => $ban->id,
                'created' => $ban->created,
                'till' => $ban->till,
                'address' => $ban->address,
                'comment' => $ban->comment,
            );
        }

        $page['title'] = getlocal('Ban List');
        $page['menuid'] = 'bans';
        $pagination = setup_pagination($blocked_list);
        $page['pagination'] = $pagination['info'];
        $page['pagination.items'] = $pagination['items'];
        $page = array_merge($page, prepare_menu($operator));

        $this->getAssetManager()->attachJs('js/compiled/bans.js');

        return $this->render('bans', $page);
    }

    /**
     * Removes a ban from the database.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the ban with specified ID is not found in
     *   the system.
     */
    public function deleteAction(Request $request)
    {
        csrf_check_token($request);

        $ban_id = $request->attributes->getInt('ban_id');

        // Check if the ban exists
        $ban = Ban::load($ban_id);
        if (!$ban) {
            throw new NotFoundException('The ban is not found.');
        }

        // Remove the ban
        $ban->delete();

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
            $ban = Ban::load($ban_id);
            if (!$ban) {
                throw new NotFoundException('The ban is not found.');
            }

            $page['banId'] = $ban->id;
            $page['formaddress'] = $ban->address;
            $page['formdays'] = $ban->till > 0 ? round(($ban->till - time()) / 86400) : 0;
            $page['formcomment'] = $ban->comment;
        } elseif ($request->query->has('thread')) {
            // Prepopulate form using thread data
            $thread_id = $request->query->get('thread');
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

        $page['title'] = getlocal('Block address');
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
     * @throws NotFoundException If the ban with specified ID is not found in
     *   the system.
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
            $errors[] = no_field('Visitor\'s Address');
        }

        if (!preg_match("/^\d+$/", $days)) {
            $errors[] = wrong_field('Days');
        }

        if (!$comment) {
            $errors[] = no_field('Comment');
        }

        // Check if the ban already exists in the database
        $existing_ban = Ban::loadByAddress($address);
        $ban_duplicate = (!$ban_id && $existing_ban)
            || ($ban_id && $existing_ban && $ban_id != $existing_ban->id);

        if ($ban_duplicate) {
            $ban_url = $this->generateUrl(
                'ban_edit',
                array('ban_id' => $existing_ban->id)
            );
            $errors[] = getlocal('The specified address is already in use. Click <a href="{1}">here</a> if you want to edit it.', array($address, $ban_url));
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showEditFormAction($request);
        }

        // Save ban into the database
        if (!$ban_id) {
            $ban = new Ban();
            $ban->created = time();
        } else {
            $ban = Ban::load($ban_id);
            if (!$ban) {
                throw new NotFoundException('The ban is not found.');
            }
        }
        $ban->till = $days > 0 ? time() + $days * 24 * 60 * 60 : 0;
        $ban->address = $address;
        $ban->comment = $comment;
        $ban->save();

        // Rerender the form page
        $page['saved'] = true;
        $page['address'] = $address;
        $page['title'] = getlocal('Block address');
        $page = array_merge($page, prepare_menu($operator, false));

        return $this->render('ban', $page);
    }
}
