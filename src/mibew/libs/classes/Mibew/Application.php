<?php
/*
 * This file is a part of Mibew Messenger.
 *
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
use Mibew\Asset\AssetUrlGenerator;
use Mibew\Authentication\AuthenticationManagerInterface;
use Mibew\Authentication\AuthenticationManagerAwareInterface;
use Mibew\Controller\ControllerResolver;
use Mibew\EventDispatcher;
use Mibew\Http\CookieFactory;
use Mibew\Http\CookieFactoryAwareInterface;
use Mibew\Http\Exception\AccessDeniedException as AccessDeniedHttpException;
use Mibew\Http\Exception\HttpException;
use Mibew\Http\Exception\MethodNotAllowedException as MethodNotAllowedHttpException;
use Mibew\Http\Exception\NotFoundException as NotFoundHttpException;
use Mibew\Routing\RouterAwareInterface;
use Mibew\Routing\RouterInterface;
use Mibew\Routing\Exception\AccessDeniedException as AccessDeniedRoutingException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\MethodNotAllowedException as MethodNotAllowedRoutingException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException as ResourceNotFoundRoutingException;

/**
 * Incapsulates whole application
 */
class Application implements RouterAwareInterface, AuthenticationManagerAwareInterface
{
    /**
     * @var RouterInterface|null
     */
    protected $router = null;

    /**
     * @var ControllerResolver|null
     */
    protected $controllerResolver = null;

    /**
     * @var CheckResolver|null
     */
    protected $accessCheckResolver = null;

    /**
     * @var AuthenticationManagerInterface|null
     */
    protected $authenticationManager = null;

    /**
     * @var AssetUrlGenerator|null
     */
    protected $assetUrlGenerator = null;

    /**
     * Class constructor.
     *
     * @param RouterInterface $router Appropriate router instance.
     * @param AuthenticationManagerInterface $manager Appropriate authentication
     *   manager.
     */
    public function __construct(RouterInterface $router, AuthenticationManagerInterface $manager)
    {
        $this->router = $router;
        $this->authenticationManager = $manager;
        $this->assetUrlGenerator = new AssetUrlGenerator();
        $this->controllerResolver = new ControllerResolver(
            $this->router,
            $this->authenticationManager
        );
        $this->controllerResolver->setAssetUrlGenerator($this->assetUrlGenerator);
        $this->accessCheckResolver = new CheckResolver($this->authenticationManager);
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
        $this->getRouter()->setContext($context);

        $authentication_manager = $this->getAuthenticationManager();
        // Actualize cookie factory in the authentication manager if it is
        // needed.
        if ($authentication_manager instanceof CookieFactoryAwareInterface) {
            $cookie_factory = CookieFactory::fromRequest($request);
            $authentication_manager->setCookieFactory($cookie_factory);
        }
        $authentication_manager->setOperatorFromRequest($request);

        // Actualize AssetUrlGenerator
        $this->assetUrlGenerator->setRequest($request);

        try {
            // Try to match a route, check if the client can access it and add
            // extra data to the request.
            try {
                $parameters = $this->getRouter()->matchRequest($request);
                $request->attributes->add($parameters);

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
            $response = $this->buildAccessDeniedResponse($request);
        } catch (HttpException $e) {
            // Build response based on status code which is stored in exception
            // instance.
            $http_status = $e->getStatusCode();
            $content = Response::$statusTexts[$http_status];

            $response = new Response($content, $http_status);
        } catch (\Exception $e) {
            $response = new Response('Internal Server Error', 500);
        }

        if (!($response instanceof Response)) {
            // Convert all content returned by a controller's action to Response
            // instance.
            $response = new Response((string)$response);
        }

        // Attach operator's authentication info to the response to distinguish
        // him in the next requests.
        $authentication_manager->attachOperatorToResponse($response);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;

        // Update router in internal objects
        if (!is_null($this->controllerResolver)) {
            $this->controllerResolver->setRouter($router);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthenticationManager(AuthenticationManagerInterface $manager)
    {
        $this->authenticationManager = $manager;

        // Update authentication manager in internal objects
        if (!is_null($this->controllerResolver)) {
            $this->controllerResolver->setAuthenticationManager($manager);
        }

        if (!is_null($this->accessCheckResolver)) {
            $this->accessCheckResolver->setAuthenticationManager($manager);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
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

        if ($this->authenticationManager->getOperator()) {
            // If the operator already logged in, display 403 page.
            return new Response('Forbidden', 403);
        }

        // Operator is not logged in. Redirect him to the login page.
        $_SESSION['backpath'] = $request->getUri();
        $response = new RedirectResponse($this->getRouter()->generate('login'));

        return $response;
    }
}
