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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains actions which are related with invitations.
 */
class InvitationController extends AbstractController
{
    /**
     * Invites a visitor to chat.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function inviteAction(Request $request)
    {
        $operator = $this->getOperator();

        // Get visitor ID from the request and check it
        $visitor_id = $request->query->get('visitor');
        if (!preg_match("/^\d{1,10}$/", $visitor_id)) {
            throw new BadRequestException('Wrong format of visitor param.');
        }

        // Try to invite the visitor
        $thread = invitation_invite($visitor_id, $operator);
        if (!$thread) {
            return new Response('Invitation failed!');
        }

        // Open chat window for operator
        $redirect_to = $this->generateUrl(
            'chat_operator',
            array(
                'thread_id' => intval($thread->id),
                'token' => urlencode($thread->lastToken),
            )
        );

        return $this->redirect($redirect_to);
    }
}
