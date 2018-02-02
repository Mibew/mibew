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

namespace Mibew\EventDispatcher;

/**
 * This class contains a list of events that are present in the system.
 */
final class Events
{
    /**
     * A ban is created.
     *
     * This event is triggered after a ban has been created. An associative
     * array with the following items is passed to the event handlers:
     *  - "ban": an instance of {@link \Mibew\Ban} class.
     */
    const BAN_CREATE = 'banCreate';

    /**
     * A ban is updated.
     *
     * This event is triggered after a ban is saved. An associative array with
     * the following items is passed to the event handlers:
     *  - "ban": an instance of {@link \Mibew\Ban}, the state of the ban after
     *    the update.
     *  - "original_ban": an instance of {@link \Mibew\Ban}, the state of the
     *    ban before the update.
     */
    const BAN_UPDATE = 'banUpdate';

    /**
     * A ban is deleted.
     *
     * This event is triggered after a ban has been deleted. An associative
     * array with the following items is passed to the event handlers:
     *  - "id": int, deleted ban ID.
     */
    const BAN_DELETE = 'banDelete';

    /**
     * A button is generated.
     *
     * This event is triggered after a button has been generated. An associative
     * array with the following items is passed to the event handlers:
     *  - "button": an instance of {@link \Canteen\HTML5\Fragment} which
     *    represents markup of the button.
     *  - "generator": an instance of
     *    {@link \Mibew\Button\Generator\GeneratorInterface} which is used for
     *    button generation.
     */
    const BUTTON_GENERATE = 'buttonGenerate';

    /**
     * Cron is run.
     *
     * This event is triggered when cron is run. It provides an ability for
     * plugins to perform custom maintenance actions.
     */
    const CRON_RUN = 'cronRun';

    /**
     * A group is created.
     *
     * This event is triggered after a group has been created. An associative
     * array with the following items is passed to the event handlers:
     *  - "group": group's array.
     */
    const GROUP_CREATE = 'groupCreate';

    /**
     * A group is updated.
     *
     * This event is triggered after a group is saved. An associative array with
     * the following items is passed to the event handlers:
     *  - "group": array, the state of the group after update.
     *  - "original_group": array, the state of the group before update.
     */
    const GROUP_UPDATE = 'groupUpdate';

    /**
     * A group is deleted.
     *
     * This event is triggered after a group has been deleted. An associative
     * array with the following items is passed to the event handlers:
     *  - "id": int, deleted group ID.
     */
    const GROUP_DELETE = 'groupDelete';

    /**
     * Group's operators set is updated.
     *
     * This event is triggered after a set of operators related with a group has
     * been changed. An associative array with the following items is passed to
     * the event handlers:
     *  - "group": group's array.
     *  - "original_operators": array, list of operators IDs before the update.
     *  - "operators": array, list of operators IDs after the update.
     */
    const GROUP_UPDATE_OPERATORS = 'groupUpdateOperators';

    /**
     * An invitation is created.
     *
     * This event is triggered after an invitation has been created. An
     * associative array with the following items is passed to the event
     * handlers:
     *  - "invitation": an instance of {@link \Mibew\Thread} class.
     */
    const INVITATION_CREATE = 'invitationCreate';

    /**
     * An invitation is accepted.
     *
     * This event is triggered after an invitation has been accepted by a
     * visitor. An associative array with the following items is passed to the
     * event handlers:
     *  - "invitation": an instance of {@link \Mibew\Thread} class.
     */
    const INVITATION_ACCEPT = 'invitationAccept';

    /**
     * An invitation is rejected.
     *
     * This event is triggered after an invitation has been rejected by a
     * visitor. An associative array with the following items is passed to the
     * event handlers:
     *  - "invitation": an instance of {@link \Mibew\Thread} class.
     */
    const INVITATION_REJECT = 'invitationReject';

    /**
     * An invitation is ignored.
     *
     * This event is triggered after an invitation has been ignored by a
     * visitor and automatically closed by the system. An associative array with
     * the following items is passed to the event handlers:
     *  - "invitation": an instance of {@link \Mibew\Thread} class.
     */
    const INVITATION_IGNORE = 'invitationIgnore';

    /**
     * An operator cannot be authenticated.
     *
     * This event is triggered if an operator cannot be authenticated by the
     * system. It provides an ability for plugins to implement custom
     * authentication logic. An associative array with the following items is
     * passed to the event handlers:
     *  - "operator": array, if a plugin has extracted operator from the request
     *    it should set operator's data to this field.
     *  - "request": {@link \Symfony\Component\HttpFoundation\Request},
     *    incoming request. Can be used by a plugin to extract an operator.
     */
    const OPERATOR_AUTHENTICATE = 'operatorAuthenticate';

    /**
     * An operator logged in.
     *
     * This event is triggered after an operator logged in using system login
     * form. An associative array with the following items is passed to the
     * event handlers:
     *  - "operator": array of the logged in operator info;
     *  - "remember": boolean, indicates if system should remember operator.
     */
    const OPERATOR_LOGIN = 'operatorLogin';

    /**
     * An operator logged out.
     *
     * This event is triggered after an operator is logged out.
     */
    const OPERATOR_LOGOUT = 'operatorLogout';

    /**
     * An operator is created.
     *
     * This event is triggered after an operator has been created. An
     * associative array with the following items is passed to the event
     * handlers:
     *  - "operator": operator's array.
     */
    const OPERATOR_CREATE = 'operatorCreate';

    /**
     * An operator is updated.
     *
     * This event is triggered after an operator is saved. An associative array
     * with the following items is passed to the event handlers:
     *  - "operator": array, the state of the operator after update.
     *  - "original_operator": array, the state of the operator before update.
     */
    const OPERATOR_UPDATE = 'operatorUpdate';

    /**
     * An operator is deleted.
     *
     * This event is triggered after an operator has been deleted. An
     * associative array with the following items is passed to the event
     * handlers:
     *  - "id": int, deleted operator ID.
     */
    const OPERATOR_DELETE = 'operatorDelete';

    /**
     * CSS assets are attached to a page.
     *
     * This event is triggered before CSS assets are attached to a page. It
     * provides an ability for plugins to add custom CSS files (or inline
     * styles) to a page. An associative array with the following items is
     * passed to the event handlers:
     *  - "request": {@link \Symfony\Component\HttpFoundation\Request}, a
     *    request instance. CSS files will be attached to the requested page.
     *  - "css": array of assets. Each asset can be either a string with
     *    relative URL of a CSS file or an array with "content", "type" and
     *    "weight" items. See
     *    {@link \Mibew\Asset\AssetManagerInterface::getCssAssets()} for details
     *    of their meaning. Modify this array to add or remove additional CSS
     *    files.
     */
    const PAGE_ADD_CSS = 'pageAddCss';

    /**
     * JavaScript assets are attached to a page.
     *
     * This event is triggered before JavaScript assets are attached to a page.
     * It provides an ability for plugins to add custom JavaScript files (or
     * inline scripts) to a page. An associative array with the following items
     * is passed to the event handlers:
     *  - "request": {@link \Symfony\Component\HttpFoundation\Request}, a
     *    request instance. JavaScript files will be attached to the requested
     *    page.
     *  - "js": array of assets. Each asset can be either a string with
     *    relative URL of a JavaScript file or an array with "content",
     *    "type" and "weight" items. See
     *    {@link \Mibew\Asset\AssetManagerInterface::getJsAssets()} for details
     *    of their meaning. Modify this array to add or remove additional
     *    JavaScript files.
     */
    const PAGE_ADD_JS = 'pageAddJs';

    /**
     * Options of JavaScript plugins are attached to a page.
     *
     * This event is triggered before options of JavaScript plugins are attached
     * to a page. It provides an ability for plugins to pass some data to the
     * client side. An associative array with the following items is passed to
     * the event handlers:
     *  - "request": {@link \Symfony\Component\HttpFoundation\Request}, a
     *    request instance. Plugins will work at the requested page.
     *  - "plugins": associative array, whose keys are plugins names and values
     *    are plugins options. Modify this array to add or change plugins
     *    options.
     */
    const PAGE_ADD_JS_PLUGIN_OPTIONS = 'pageAddJsPluginOptions';

    /**
     * Access for resource is denied.
     *
     * This event is triggered if the access for resource is denied. An
     * associative array with the following items is passed to the event
     * handlers:
     *  - "request": {@link Symfony\Component\HttpFoundation\Request}, incoming
     *    request object.
     *  - "response": {@link Symfony\Component\HttpFoundation\Response}, if a
     *    plugin wants to send a custom response to the client it should attach
     *    a response object to this field.
     */
    const RESOURCE_ACCESS_DENIED = 'resourceAccessDenied';

    /**
     * Resource is not found.
     *
     * This event is triggered if a resource is not found. An
     * associative array with the following items is passed to the event
     * handlers:
     *  - "request": {@link Symfony\Component\HttpFoundation\Request}, incoming
     *    request object.
     *  - "response": {@link Symfony\Component\HttpFoundation\Response}, if a
     *    plugin wants to send a custom response to the client it should attach
     *    a response object to this field.
     */
    const RESOURCE_NOT_FOUND = 'resourceNotFound';

    /**
     * Routes collection is loaded and ready to use.
     *
     * This event is triggered after all routes are loaded. It provides an
     * ability for plugins to alter routes collection before it will be used. An
     * associative array with the following items is passed to the event
     * handlers:
     *  - "routes" an instance of
     *    {@link Symfony\Component\Routing\RouteCollection} class.
     */
    const ROUTES_ALTER = 'routesAlter';

    /**
     * A function was called at client side "thread" application.
     *
     * This event is triggered when an API a function is called at client side
     * in the "thread" application, but the system is not aware of this function.
     *
     * Plugins can implement custom API functions by attaching handlers to the
     * event. If a plugin wants to return some results, it should use "results"
     * element of the event arguments array (see below).
     *
     * An associative array with the following items is passed to the event
     * handlers:
     *  - "request_processor": an instance of
     *    {\Mibew\RequestProcessor\ThreadProcessor} which processes the current
     *    call.
     *   - "function": string, name of the function that was called.
     *   - "arguments": associative array of arguments that was passed to the
     *     function.
     *   - "results": array, list of function results.
     *
     * Here is an example of the event handler:
     * <code>
     * public function callHandler(&$function)
     * {
     *     // Check that the function we want to implement is called.
     *     if ($function['function'] == 'microtime') {
     *         // Check some function's arguments.
     *         $as_float = empty($function['arguments']['as_float'])
     *             ? false
     *             : $function['arguments']['as_float'];
     *         // Invoke the function and return the results.
     *         $function['results']['time'] = microtime($as_float);
     *     }
     * }
     * </code>
     */
    const THREAD_FUNCTION_CALL = 'threadFunctionCall';

    /**
     * A thread is created.
     *
     * This event is triggered after a thread has been created. An associative
     * array with the following items is passed to the event handlers:
     *  - "thread": an instance of {@link \Mibew\Thread}.
     */
    const THREAD_CREATE = 'threadCreate';

    /**
     * Thread is updated.
     *
     * This event is triggered after a thread is saved. An associative array
     * with the following items is passed to the event handlers:
     *  - "thread": an instance of {@link \Mibew\Thread}, state of the thread
     *    after the update.
     *  - "original_thread": an instance of {@link \Mibew\Thread}, state of the
     *    thread before the update.
     */
    const THREAD_UPDATE = 'threadUpdate';

    /**
     * A thread is deleted.
     *
     * This event is triggered after a thread has been deleted. An associative
     * array with the following items is passed to the event handlers:
     *  - "id": int, deleted thread ID.
     */
    const THREAD_DELETE = 'threadDelete';

    /**
     * A thread is closed.
     *
     * This event is triggered after a thread has been closed. An associative
     * array with the following items is passed to the event handlers:
     *  - "thread": an instance of {@link \Mibew\Thread}.
     */
    const THREAD_CLOSE = 'threadClose';

    /**
     * A message is posted.
     *
     * This event is triggered before a message has been posted to thread. It
     * provides an ability for plugins to alter message, its kind or options. An
     * associative array with the following items is passed to the event
     * handlers:
     *  - "thread": an instance of {@link \Mibew\Thread}.
     *  - "message_kind": int, message kind.
     *  - "message_body": string, message body.
     *  - "message_options": associative array, list of options passed to
     *    {@link \Mibew\Thread::postMessage()} method as the third argument.
     */
    const THREAD_POST_MESSAGE = 'threadPostMessage';

    /**
     * Related with a thread messages are loaded.
     *
     * This event is triggered after messages related with a thread are loaded.
     * It provides an ability for plugins to alter messages set. An associative
     * array with the following items is passed to the event handlers:
     *  - "thread": an instance of {@link \Mibew\Thread}.
     *  - "messages": array, list of messages. Each message is an associative
     *    array. See {@link \Mibew\Thread::getMessages()} return value for
     *    details of its structure.
     */
    const THREAD_GET_MESSAGES_ALTER = 'threadGetMessagesAlter';

    /**
     * User is ready to chat.
     *
     * This event is triggered after the thread is created, the user passed
     * pre-chat survey and all system messages are sent to him. This event is
     * not triggered if there are no online operators and the chat cannot be
     * started. An associative array with the following items is passed to the
     * event handlers:
     *  - "thread": an instance of {@link \Mibew\Thread}.
     */
    const THREAD_USER_IS_READY = 'threadUserIsReady';

    /**
     * Threads list is ready to be sent to client.
     *
     * This event is triggered before the threads list is sent to the "users"
     * client side application. It provide an ability to alter the list. A
     * plugin can attach some fields to each thread or completeley replace the
     * whole list. An associative array with the following items is passed to
     * the event handlers:
     *   - "threads": array of threads data arrays.
     */
    const USERS_UPDATE_THREADS_ALTER = 'usersUpdateThreadsAlter';

    /**
     * Load custom on site visitors list.
     *
     * This event is triggered before the list of on site visitors is loaded for
     * sending to the "users" client side application. It provide an ability for
     * plugins to load, sort and limit visitors list. An associative array with
     * the following items is passed to the event handlers:
     *   - "visitors": array of visitors data arrays. Each visitor array must
     *     contain at least the following keys: "id", "userName", "userAgent",
     *     "userIp", "remote", "firstTime", "lastTime", "invitations",
     *     "chats", "invitationInfo". If there are no visitors an empty array
     *     should be used.
     *
     * If the "visitors" item was not set by a plugin the default system loader
     * will be used.
     */
    const USERS_UPDATE_VISITORS_LOAD = 'usersUpdateVisitorsLoad';

    /**
     * On site visitors list is ready to be sent to client.
     *
     * This event is triggered before the on site visitors list is sent to the
     * "users" client application. It provide an ability to alter the list.
     * A plugin can attach some fields to each visitor or completeley replace
     * the whole list. An associative array with the following items is passed
     * to the event handlers:
     *   - "visitors": array of visitors data arrays.
     */
    const USERS_UPDATE_VISITORS_ALTER = 'usersUpdateVisitorsAlter';

    /**
     * A function was called at client side "users" application.
     *
     * This event is triggered when an API a function is called at client side
     * in the "users" application, but the system is not aware of this function.
     *
     * Plugins can implement custom API functions by attaching handlers to the
     * event. If a plugin wants to return some results, it should use "results"
     * element of the event arguments array (see below).
     *
     * An associative array with the following items is passed to the event
     * handlers:
     *  - "request_processor": an instance of
     *    {\Mibew\RequestProcessor\UsersProcessor} which processes the current
     *    call.
     *   - "function": string, name of the function that was called.
     *   - "arguments": associative array of arguments that was passed to the
     *     function.
     *   - "results": array, list of function results.
     *
     * Here is an example of the event handler:
     * <code>
     * public function callHandler(&$function)
     * {
     *     // Check that the function we want to implement is called.
     *     if ($function['function'] == 'microtime') {
     *         // Check some function's arguments.
     *         $as_float = empty($function['arguments']['as_float'])
     *             ? false
     *             : $function['arguments']['as_float'];
     *         // Invoke the function and return the results.
     *         $function['results']['time'] = microtime($as_float);
     *     }
     * }
     * </code>
     */
    const USERS_FUNCTION_CALL = 'usersFunctionCall';

    /**
     * Visitor is created.
     *
     * This event is triggered when a visitor is tracked by the widget for the
     * first time. An associative array with the following items is passed to
     * the event handlers:
     *   - "visitor": array, list of visitor's info. See returned value of
     *     {@link track_get_visitor_by_id()} function for details of its
     *     structure.
     */
    const VISITOR_CREATE = 'visitorCreate';

    /**
     * Visitor is tracked by the system.
     *
     * This event is triggered every time a visitor is tracked by the widget. An
     * associative array with the following items is passed to the event
     * handlers:
     *   - "visitor": array, list of visitor's info. See returned value of
     *     {@link track_get_visitor_by_id()} function for details of its
     *     structure.
     */
    const VISITOR_TRACK = 'visitorTrack';

    /**
     * Old visitors are deleted.
     *
     * This event is triggered after old visitors are deleted. An associative
     * array with the following items is passed to the event handlers:
     *   - "visitors": array, list of removed visitors' IDs.
     */
    const VISITOR_DELETE_OLD = 'visitorDeleteOld';

    /**
     * Widget response is ready.
     *
     * This event is triggered every time the widget data is ready to be sent.
     * An associative array with the following items is passed to the event
     * listeners:
     *  - "visitor": array, visitor's info.
     *  - "request": an instance of
     *    {@link \Symfony\Component\HttpFoundation\Request} which represents
     *    incoming request.
     *  - "response": array, set of data that will be sent to the widget. See
     *    description of its structure and use case below.
     *  - "route_url_generator": an instance of
     *    {@link \Mibew\Routing\Generator\SecureUrlGeneratorInterface}.
     *  - "asset_url_generator": an instance of
     *    {@link \Mibew\Asset\Generator\UrlGeneratorInterface}.
     *
     * This event can be used to do something at page the visitor is currenlty
     * browsing.
     *
     * For example we can call a function every time the widget
     * get the response from the server. Here is the event listener code from a
     * plugin:
     * <code>
     * public function callHandler(&$args)
     * {
     *     // This is just a shortcut for URL generator.
     *     $g = $args['asset_url_generator'];
     *
     *     // The external libraries can be loaded before the function will be
     *     // called. There can be as many libraries as needed (even none).
     *     // The keys of the "load" array are libraries IDs and values are
     *     // their URLs.
     *     $args['response']['load']['the_lib'] = 'http://example.com/lib.js';
     *     $args['response']['load']['the_func'] = $g->generate($this->getFilesPath() . '/func.js');
     *
     *     // The "handlers" array contains a list of functions that should be
     *     // called.
     *     $args['response']['handlers'][] = 'usefulFunc';
     *
     *     // The "dependencies" array lists all libraries a function depend on.
     *     // In this example "usefulFunc" depends on libraries with "the_lib"
     *     // and "the_func" IDs.
     *     $args['response']['dependencies']['usefulFunc'] = array('the_lib', 'the_func');
     *
     *     // Some extra data can be passed to the function.
     *     $args['response']['data']['usefulFunc'] = array('time' => microtime(true));
     * }
     * </code>
     *
     * Here is the JavaScript part of the example:
     * <code>
     * (function(Mibew) {
     *     // Notice the full function name. All callable functions must be
     *     // defined as properties of Mibew.APIFunctions object.
     *     Mibew.APIFunctions.usefulFunc = function(data) {
     *         // Do some job here.
     *         console.dir(data.usefulFunc);
     *     }
     * })(Mibew);
     * </code>
     */
    const WIDGET_RESPONSE_ALTER = 'widgetResponseAlter';
}
