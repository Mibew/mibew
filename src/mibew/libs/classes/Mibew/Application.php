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

namespace Mibew;

use Mibew\AccessControl\Check\CheckResolver;
use Mibew\Controller\ControllerResolver;
use Mibew\EventDispatcher;
use Mibew\Http\Exception\AccessDeniedException as AccessDeniedHttpException;
use Mibew\Http\Exception\HttpException;
use Mibew\Http\Exception\MethodNotAllowedException as MethodNotAllowedHttpException;
use Mibew\Http\Exception\NotFoundException as NotFoundHttpException;
use Mibew\Routing\Router;
use Mibew\Routing\RouteCollectionLoader;
use Mibew\Routing\Exception\AccessDeniedException as AccessDeniedRoutingException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\MethodNotAllowedException as MethodNotAllowedRoutingException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException as ResourceNotFoundRoutingException;

/**
 * Incapsulates whole application
 */
class Application
{
    /**
     * @var Router|null
     */
    protected $router = null;

    /**
     * @var FileLocator|null
     */
    protected $fileLocator = null;

    /**
     * @var ControllerResolver|null
     */
    protected $controllerResolver = null;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->fileLocator = new FileLocator(array(MIBEW_FS_ROOT));
        $this->router = new Router(new RouteCollectionLoader($this->fileLocator));
        $this->controllerResolver = new ControllerResolver($this->router);
        $this->accessCheckResolver = new CheckResolver();
    }

    /**
     * Handles incomming request.
     *
     * @param Request $request Incoming request
     * @return Response Resulting response
     */
    public function handleRequest(Request $request)
    {
        // Actualize request context in the internal router instance
        $context = new RequestContext();
        $context->fromRequest($request);
        $this->router->setContext($context);

        try {
            // Try to match a route, check if the client can access it and add
            // extra data to the request.
            try {
                $parameters = $this->router->matchRequest($request);
                $request->attributes->add($parameters);
                $request->attributes->set('_operator', $this->extractOperator($request));

                // Check if the user can access the page
                $access_check = $this->accessCheckResolver->getCheck($request);
                if (!call_user_func($access_check, $request)) {
                    throw new AccessDeniedRoutingException();
                }
            } catch (AccessDeniedRoutingException $e) {
                // Convert the exception to HTTP exception to process it later.
                throw new AccessDeniedHttpException();
            } catch (ResourceNotFoundRoutingException $e) {
                // Convert the exception to HTTP exception to process it later.
                throw new NotFoundHttpException();
            } catch (MethodNotAllowedRoutingException $e) {
                // Convert the exception to HTTP exception to process it later.
                throw new MethodNotAllowedHttpException();
            }

            // Get controller and perform its action to get a response.
            $controller = $this->controllerResolver->getController($request);
            $response = call_user_func($controller, $request);
        } catch (AccessDeniedHttpException $e) {
            return $this->buildAccessDeniedResponse($request);
        } catch (HttpException $e) {
            // Build response based on status code which is stored in exception
            // instance.
            $http_status = $e->getStatusCode();
            $content = Response::$statusTexts[$http_status];

            return new Response($content, $http_status);
        } catch (\Exception $e) {
            return new Response('Internal Server Error', 500);
        }

        if ($response instanceof Response) {
            return $response;
        } else {
            // Convert all content returned by a controller's action to Response
            // instance.
            return new Response((string)$response);
        }
    }

    /**
     * Extracts operator's data from the passed in request object.
     *
     * @param Request $request A request to extract operator from.
     * @return array|bool Associative array with operator's data or boolean
     *   false if there is no operator related with the request.
     *
     * @todo Remove this method when Object Oriented wrapper for an operator
     *   will be created.
     */
    protected function extractOperator(Request $request)
    {
        // Try to get operator from session.
        if (isset($_SESSION[SESSION_PREFIX . "operator"])) {
            return $_SESSION[SESSION_PREFIX . "operator"];
        }

        // Check if operator had used "remember me" feature.
        if ($request->cookies->has(REMEMBER_OPERATOR_COOKIE_NAME)) {
            $cookie_value = $request->cookies->get(REMEMBER_OPERATOR_COOKIE_NAME);
            list($login, $pwd) = preg_split('/\x0/', base64_decode($cookie_value), 2);
            $op = operator_by_login($login);
            $can_login = $op
                && isset($pwd)
                && isset($op['vcpassword'])
                && calculate_password_hash($op['vclogin'], $op['vcpassword']) == $pwd
                && !operator_is_disabled($op);
            if ($can_login) {
                $_SESSION[SESSION_PREFIX . "operator"] = $op;

                return $op;
            }
        }

        // Operator's data cannot be extracted from the request.
        return false;
    }

    /**
     * Builds response for pages with denied access
     *
     * Triggers "accessDenied' event to provide an ability for plugins to set custom response.
     * an associative array with folloing keys is passed to event listeners:
     *  - 'request': {@link Symfony\Component\HttpFoundation\Request} object.
     *
     * An event listener can attach custom response to the arguments array
     * (using "response" key) to send it to the client.
     *
     * @param Request $request Incoming request
     * @return Response
     */
    protected function buildAccessDeniedResponse(Request $request)
    {
        // Trigger fail
        $args = array(
            'request' => $request,
            'response' => false,
        );
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent('accessDenied', $args);

        if ($args['response'] && ($args['response'] instanceof Response)) {
            // If one of event listeners returned the response object send it
            // to the client.
            return $args['response'];
        }

        if ($request->attributes->get('_operator')) {
            // If the operator already logged in, display 403 page.
            return new Response('Forbidden', 403);
        }

        // Operator is not logged in. Redirect him to the login page.
        $_SESSION['backpath'] = $request->getUri();
        $response = new RedirectResponse($request->getUriForPath('/operator/login.php'));

        return $response;
    }
}
