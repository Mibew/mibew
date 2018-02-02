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

use Mibew\Asset\AssetManagerInterface;
use Mibew\Http\Exception\NotFoundException;
use Mibew\Thread;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operator's chat window.
 */
class OperatorChatController extends AbstractController
{
    /**
     * Process chat pages.
     *
     * @param Request $request Incoming request.
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse Rendered
     *   page content or a redirect response.
     * @throws NotFoundException If the thread with specified ID and token is
     * not found.
     */
    public function indexAction(Request $request)
    {
        // Check if we should force the user to use SSL
        $ssl_redirect = $this->sslRedirect($request);
        if ($ssl_redirect !== false) {
            return $ssl_redirect;
        }

        $operator = $this->getOperator();
        $thread_id = $request->attributes->getInt('thread_id');
        $token = $request->attributes->get('token');


        $thread = Thread::load($thread_id, $token);
        if (!$thread) {
            throw new NotFoundException('The thread is not found.');
        }

        // Check if the current operator has enough permissions to use the thread
        if ($thread->agentId != $operator['operatorid'] && !is_capable(CAN_VIEWTHREADS, $operator)) {
            return $this->showErrors(array('Cannot view threads'));
        }

        $page = setup_chatview_for_operator(
            $this->getRouter(),
            $request,
            $thread,
            $operator
        );

        // Build js application options
        $page['chatOptions'] = $page['chat'];

        // Initialize client side application
        $this->getAssetManager()->attachJs('js/compiled/chat_app.js');
        $this->getAssetManager()->attachJs(
            $this->startJsApplication($request, $page),
            AssetManagerInterface::INLINE,
            1000
        );

        // Render the page with chat.
        return $this->render('chat', $page);
    }

    /**
     * Starts chat process.
     *
     * @param Request $request Incoming request.
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse Rendered
     *   page content or a redirect response.
     */
    public function startAction(Request $request)
    {
        $operator = $this->getOperator();
        $thread_id = $request->attributes->getInt('thread_id');

        // Check if the thread can be loaded.
        $thread = Thread::load($thread_id);
        if (!$thread || !isset($thread->lastToken)) {
            return $this->showErrors(array(getlocal('Wrong thread')));
        }

        $view_only = ($request->query->get('viewonly') == 'true');
        $force_take = ($request->query->get('force') == 'true');

        $try_take_over = !$view_only
            && $thread->state == Thread::STATE_CHATTING
            && $operator['operatorid'] != $thread->agentId;
        if ($try_take_over) {
            if (!is_capable(CAN_TAKEOVER, $operator)) {
                return $this->showErrors(array(getlocal('Cannot take over')));
            }

            if ($force_take == false) {
                $link = $this->generateUrl(
                    'chat_operator_start',
                    array(
                        'thread_id' => $thread_id,
                        'force' => 'true',
                    )
                );
                $page = array(
                    'user' => $thread->userName,
                    'agent' => $thread->agentName,
                    'link' => $link,
                    'title' => getlocal('Change operator'),
                );

                // Show confirmation page.
                return $this->render('confirm', $page);
            }
        }

        if (!$view_only) {
            if (!$thread->take($operator)) {
                return $this->showErrors(array(getlocal('Cannot take thread')));
            }
        } elseif (!is_capable(CAN_VIEWTHREADS, $operator)) {
            return $this->showErrors(array(getlocal('Cannot view threads')));
        }

        // Redrect the operator to initialized chat page
        $redirect_to = $this->generateUrl(
            'chat_operator',
            array(
                'thread_id' => intval($thread_id),
                'token' => urlencode($thread->lastToken),
            )
        );

        return $this->redirect($redirect_to);
    }

    /**
     * Displays error page.
     *
     * @param array $errors List of erorr messages to display.
     * @return string Rendered content of chat's error page.
     */
    protected function showErrors($errors)
    {
        $page = array(
            'errors' => $errors,
        );

        return $this->render('error', $page);
    }
}
