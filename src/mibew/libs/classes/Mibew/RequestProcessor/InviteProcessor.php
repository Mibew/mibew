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

namespace Mibew\RequestProcessor;

// Import namespaces and classes of the core
use Mibew\API\API as MibewAPI;
use Mibew\RequestProcessor\Exception\InviteProcessorException;

/**
 * Incapsulates invitation awaiting related api functions.
 *
 * Events triggered by the class (see description of the RequestProcessor class
 * for details):
 *  - inviteRequestReceived
 *  - inviteReceiveRequestError
 *  - inviteCallError
 *  - inviteFunctionCall
 *
 * Implements Singleton pattern
 */
class InviteProcessor extends ClientSideProcessor
{
    /**
     * An instance of the InviteProcessor class
     *
     * @var \Mibew\RequestProcessor\InviteProcessor
     */
    protected static $instance = null;

    /**
     * Return an instance of the InviteProcessor class.
     *
     * @return \Mibew\RequestProcessor\InviteProcessor
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Class constructor
     *
     * Do not use directly __construct method! Use
     * \Mibew\RequestProcessor\InviteProcessor::getInstance() instead!
     *
     * @todo Think about why the method is not protected
     */
    public function __construct()
    {
        parent::__construct(array(
            'signature' => '',
            'trusted_signatures' => array(''),
            'event_prefix' => 'invite'
        ));
    }

    /**
     * Creates and returns an instance of the \Mibew\API\API class.
     *
     * @return \Mibew\API\API
     */
    protected function getMibewAPIInstance()
    {
        return MibewAPI::getAPI('\\Mibew\\API\\Interaction\\InviteInteraction');
    }

    /**
     * Stub for sendAsyncRequest method.
     *
     * Actually request not send to client side. This method is ONLY STUB.
     *
     * @return boolean Always true
     */
    protected function sendAsyncRequest()
    {
        return true;
    }

    /**
     * Stub for call method.
     *
     * Actually nothing can be called at client side. This method is ONLY STUB.
     *
     * @return boolean Always false.
     */
    public function call()
    {
        return false;
    }

    /**
     * Returns visitor invitation state. API function
     *
     * @param array $args Associative array of arguments. It must contains
     *   following keys:
     *    - 'visitorId': Id of the invited visitor
     * @return array Array of results. It contains following keys:
     *    - 'invited': boolean, indicates if visitor is invited
     *    - 'threadId': thread id related to visitor or false if there is no
     *      thread
     */
    protected function apiInvitationState($args)
    {
        $operator = get_logged_in();
        if (!$operator) {
            throw new InviteProcessorException(
                "Operator not logged in!",
                InviteProcessorException::ERROR_AGENT_NOT_LOGGED_IN
            );
        }

        $invitation = invitation_state($args['visitorId']);

        return array(
            'invited' => (bool) $invitation['invited'],
            'threadId' => ($invitation['threadid'] ? $invitation['threadid'] : false),
        );
    }
}
