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

namespace Mibew\Controller\Chat\Operator;

use Mibew\Controller\Chat\AbstractController;
use Mibew\Database;
use Mibew\Http\Exception\BadRequestException;
use Mibew\Thread;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operator's chat redirect logic.
 */
class RedirectController extends AbstractController
{
    /**
     * Process chat's redirections.
     *
     * @param Request $request Incoming request.
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse Rendered
     *   page content or a redirect response.
     * @throws BadRequestException If the thread cannot be loaded by some
     * reasons.
     */
    public function indexAction(Request $request)
    {
        // Get and validate thread id
        $thread_id = $request->query->get('thread');
        if (!preg_match("/^\d{1,10}$/", $thread_id)) {
            throw new BadRequestException('Wrong value of "thread" argument.');
        }

        // Get and validate token
        $token = $request->query->get('token');
        if (!preg_match("/^\d{1,10}$/", $token)) {
            throw new BadRequestException('Wrong value of "token" argument.');
        }

        $thread = Thread::load($thread_id, $token);
        if (!$thread) {
            throw new BadRequestException('Wrong thread.');
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
                $page['message'] = getlocal2(
                    'chat.redirected.group.content',
                    array(get_group_name($next_group))
                );
                if (!$this->redirectToGroup($thread, $next_id)) {
                    $page['errors'][] = getlocal('chat.redirect.cannot');
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
                $page['message'] = getlocal2(
                    'chat.redirected.content',
                    array(get_operator_name($next_operator))
                );
                if (!$this->redirectToOperator($thread, $next_id)) {
                    $page['errors'][] = getlocal('chat.redirect.cannot');
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
            getstring2_(
                'chat.status.operator.redirect',
                array(get_operator_name($this->getOperator())),
                $thread->locale,
                true
            )
        );

        return true;
    }

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
                    . "FROM {chatgroupoperator} "
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
            getstring2_(
                'chat.status.operator.redirect',
                array(get_operator_name($this->getOperator())),
                $thread->locale,
                true
            )
        );

        return true;
    }
}
