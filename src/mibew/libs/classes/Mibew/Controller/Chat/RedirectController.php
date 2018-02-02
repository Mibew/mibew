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

namespace Mibew\Controller\Chat;

use Mibew\Database;
use Mibew\Http\Exception\BadRequestException;
use Mibew\Http\Exception\NotFoundException;
use Mibew\Thread;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operator's chat redirect logic.
 */
class RedirectController extends AbstractController
{
    /**
     * Renders a page with redirections links.
     *
     * @param Request $request Incoming request.
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse Rendered
     *   page content or a redirect response.
     * @throws NotFoundException If the thread with specified ID and token is
     * not found.
     */
    public function showRedirectionLinksAction(Request $request)
    {
        // Check if we should force the user to use SSL
        $ssl_redirect = $this->sslRedirect($request);
        if ($ssl_redirect !== false) {
            return $ssl_redirect;
        }

        $operator = $this->getOperator();
        $thread_id = $request->attributes->get('thread_id');
        $token = $request->attributes->get('token');

        $thread = Thread::load($thread_id, $token);
        if (!$thread) {
            throw new NotFoundException('The thread is not found.');
        }

        if ($thread->agentId != $operator['operatorid']) {
            $page = array('errors' => array('Can redirect only own threads.'));

            return $this->render('error', $page);
        }

        $page = array_merge_recursive(
            setup_chatview_for_operator(
                $this->getRouter(),
                $request,
                $thread,
                $operator
            ),
            setup_redirect_links(
                $this->getRouter(),
                $thread_id,
                $operator,
                $token
            )
        );

        // Render the page with redirection links.
        return $this->render('redirect', $page);
    }

    /**
     * Process chat thread redirection.
     *
     * @param Request $request Incoming request.
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse Rendered
     *   page content or a redirect response.
     * @throws NotFoundException If the thread with specified ID and token is
     * not found.
     * @throws BadRequestException If one or more arguments have a wrong format.
     */
    public function redirectAction(Request $request)
    {
        $thread_id = $request->attributes->get('thread_id');
        $token = $request->attributes->get('token');

        $thread = Thread::load($thread_id, $token);
        if (!$thread) {
            throw new NotFoundException('The thread is not found.');
        }

        $page = array(
            'errors' => array(),
        );

        if ($request->query->has('nextGroup')) {
            // The thread was redirected to a group.
            $next_id = $request->query->get('nextGroup');
            if (!preg_match("/^\d{1,10}$/", $next_id)) {
                throw new BadRequestException('Wrong value of "nextGroup" argument.');
            }
            $next_group = group_by_id($next_id);

            if ($next_group) {
                $page['message'] = getlocal(
                    'The visitor has been placed in a priorty queue of the group {0}.',
                    array(get_group_name($next_group))
                );
                if (!$this->redirectToGroup($thread, (int)$next_id)) {
                    $page['errors'][] = getlocal('You are not chatting with the visitor.');
                }
            } else {
                $page['errors'][] = 'Unknown group';
            }
        } else {
            // The thread was redirected to an operator.
            $next_id = $request->query->get('nextAgent');
            if (!preg_match("/^\d{1,10}$/", $next_id)) {
                throw new BadRequestException('Wrong value of "nextAgent" argument.');
            }
            $next_operator = operator_by_id($next_id);

            if ($next_operator) {
                $page['message'] = getlocal(
                    'The visitor has been placed in the priorty queue of the operator {0}.',
                    array(get_operator_name($next_operator))
                );
                if (!$this->redirectToOperator($thread, $next_id)) {
                    $page['errors'][] = getlocal('You are not chatting with the visitor.');
                }
            } else {
                $page['errors'][] = 'Unknown operator';
            }
        }

        $page = array_merge_recursive($page, setup_logo());

        if (count($page['errors']) > 0) {
            return $this->render('error', $page);
        } else {
            return $this->render('redirected', $page);
        }
    }

    /**
     * Redirects a chat thread to the group with the specified ID.
     *
     * @param \Mibew\Thread $thread Chat thread to redirect.
     * @param int $group_id ID of the target group.
     * @return boolean True if the thread was redirected and false on failure.
     */
    protected function redirectToGroup(Thread $thread, $group_id)
    {
        if ($thread->state != Thread::STATE_CHATTING) {
            // We can redirect only threads which are in proggress now.
            return false;
        }

        // Redirect the thread
        $thread->state = Thread::STATE_WAITING;
        $thread->nextAgent = 0;
        $thread->groupId = $group_id;
        $thread->agentId = 0;
        $thread->agentName = '';
        $thread->save();

        // Send notification message
        $thread->postMessage(
            Thread::KIND_EVENTS,
            getlocal(
                'Operator {0} redirected you to another operator. Please wait a while.',
                array(get_operator_name($this->getOperator())),
                $thread->locale,
                true
            )
        );

        return true;
    }

    /**
     * Redirects a chat thread to the operator with the specified ID.
     *
     * @param \Mibew\Thread $thread Chat thread to redirect.
     * @param int $group_id ID of the target operator.
     * @return boolean True if the thread was redirected and false on failure.
     */
    protected function redirectToOperator(Thread $thread, $operator_id)
    {
        if ($thread->state != Thread::STATE_CHATTING) {
            // We can redirect only threads which are in proggress now.
            return false;
        }

        // Redirect the thread
        $thread->state = Thread::STATE_WAITING;
        $thread->nextAgent = $operator_id;
        $thread->agentId = 0;

        // Check if the target operator belongs to the current thread's group.
        // If not reset the current thread's group.
        if ($thread->groupId != 0) {
            $db = Database::getInstance();
            list($groups_count) = $db->query(
                ("SELECT count(*) AS count "
                    . "FROM {operatortoopgroup} "
                    . "WHERE operatorid = ? AND groupid = ?"),
                array($operator_id, $thread->groupId),
                array(
                    'return_rows' => Database::RETURN_ONE_ROW,
                    'fetch_type' => Database::FETCH_NUM,
                )
            );
            if ($groups_count === 0) {
                $thread->groupId = 0;
            }
        }

        $thread->save();

        // Send notification message
        $thread->postMessage(
            Thread::KIND_EVENTS,
            getlocal(
                'Operator {0} redirected you to another operator. Please wait a while.',
                array(get_operator_name($this->getOperator())),
                $thread->locale,
                true
            )
        );

        return true;
    }
}
