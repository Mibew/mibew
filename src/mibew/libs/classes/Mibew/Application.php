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

namespace Mibew;

use Mibew\AccessControl\Check\CheckResolver;
use Mibew\Asset\AssetManager;
use Mibew\Asset\AssetManagerInterface;
use Mibew\Authentication\AuthenticationManagerInterface;
use Mibew\Authentication\AuthenticationManagerAwareInterface;
use Mibew\Cache\CacheAwareInterface;
use Mibew\Controller\ControllerResolver;
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Http\CookieFactory;
use Mibew\Http\CookieFactoryAwareInterface;
use Mibew\Http\Exception\AccessDeniedException as AccessDeniedHttpException;
use Mibew\Http\Exception\HttpException;
use Mibew\Http\Exception\MethodNotAllowedException as MethodNotAllowedHttpException;
use Mibew\Http\Exception\NotFoundException as NotFoundHttpException;
use Mibew\Mail\MailerFactory;
use Mibew\Mail\MailerFactoryAwareInterface;
use Mibew\Mail\MailerFactoryInterface;
use Mibew\Routing\RouterAwareInterface;
use Mibew\Routing\RouterInterface;
use Mibew\Routing\Exception\AccessDeniedException as AccessDeniedRoutingException;
use Stash\Interfaces\PoolInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\MethodNotAllowedException as MethodNotAllowedRoutingException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException as ResourceNotFoundRoutingException;

/**
 * Incapsulates whole application
 */
class Application implements
    RouterAwareInterface,
    AuthenticationManagerAwareInterface,
    CacheAwareInterface,
    MailerFactoryAwareInterface
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
     * @var AssetManagerInterface|null
     */
    protected $assetManager = null;

    /**
     * @var PoolInterface|null;
     */
    protected $cache = null;

    /**
     * @var MailerFactoryInterface|null;
     */
    protected $mailerFactory = null;

    /**
     * Class constructor.
     *
     * @param RouterInterface $router Appropriate router instance.
     * @param AuthenticationManagerInterface $manager Appropriate authentication
     *   manager.
     */
    public function __construct(RouterInterface $router, AuthenticationManagerInterface $manager)
    {
        $this->setRouter($router);
        $this->setAuthenticationManager($manager);
    }

    /**
     * Handles incoming request.
     *
     * @param Request $request Incoming request
     * @return Response Resulting response
     */
    public function handleRequest(Request $request)
    {
        $this->prepareRequest($request);

        try {
            // Try to match a route, check if the client can access it and add
            // extra data to the request.
            try {
                $parameters = $this->getRouter()->matchRequest($request);
                $request->attributes->add($parameters);

                // Check if the user can access the page
                $access_check = $this->getAccessCheckResolver()->getCheck($request);
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
            $controller = $this->getControllerResolver()->getController($request);
            $response = call_user_func($controller, $request);
        } catch (AccessDeniedHttpException $e) {
            $response = $this->buildAccessDeniedResponse($request);
        } catch (NotFoundHttpException $e) {
            $response = $this->buildNotFoundResponse($request);
        } catch (HttpException $e) {
            // Build response based on status code which is stored in exception
            // instance.
            $http_status = $e->getStatusCode();
            $content = Response::$statusTexts[$http_status];

            $response = new Response($content, $http_status);
        } catch (\Exception $e) {
            trigger_error(
                sprintf(
                    'Application stopped because of uncaught exception %s "%s" (%s:%u)',
                    get_class($e),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                ),
                E_USER_WARNING
            );
            $response = new Response('Internal Server Error', 500);
        }

        if (!($response instanceof Response)) {
            // Convert all content returned by a controller's action to Response
            // instance.
            $response = new Response((string)$response);
        }

        $this->finalizeRequest($request, $response);

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
     * {@inheritdoc}
     */
    public function setCache(PoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        if (is_null($this->cache)) {
            $driver = new \Stash\Driver\Ephemeral();
            $this->cache = new \Stash\Pool($driver);
        }

        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function setMailerFactory(MailerFactoryInterface $factory)
    {
        $this->mailerFactory = $factory;

        if (!is_null($this->controllerResolver)) {
            $this->controllerResolver->setMailerFactory($factory);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMailerFactory()
    {
        if (is_null($this->mailerFactory)) {
            $this->mailerFactory = new MailerFactory();
        }

        return $this->mailerFactory;
    }

    /**
     * Returns an instance of Controller Resolver related with the application.
     *
     * @return ControllerResolver
     */
    protected function getControllerResolver()
    {
        if (is_null($this->controllerResolver)) {
            $this->controllerResolver = new ControllerResolver(
                $this->getRouter(),
                $this->getAuthenticationManager(),
                $this->getAssetManager(),
                $this->getCache(),
                $this->getMailerFactory()
            );
        }

        return $this->controllerResolver;
    }

    /**
     * Returns an instance of Asset Manager related with the application.
     *
     * @return AssetManagerInterface
     */
    protected function getAssetManager()
    {
        if (is_null($this->assetManager)) {
            $this->assetManager = new AssetManager();
        }

        return $this->assetManager;
    }

    /**
     * Returns an instance of Access Check Resolver related with the
     * application.
     *
     * @return CheckResolver
     */
    protected function getAccessCheckResolver()
    {
        if (is_null($this->accessCheckResolver)) {
            $this->accessCheckResolver = new CheckResolver($this->getAuthenticationManager());
        }

        return $this->accessCheckResolver;
    }

    /**
     * Prepare request to be processed.
     *
     * @param Request $request Fresh incoming request.
     */
    protected function prepareRequest(Request $request)
    {
        // Actualize request context in the internal router instance
        $context = new RequestContext();
        $context->fromRequest($request);
        $this->getRouter()->setContext($context);

        $authentication_manager = $this->getAuthenticationManager();
        // Actualize cookie factory in the authentication manager if it is
        // needed.
        if ($authentication_manager instanceof CookieFactoryAwareInterface) {
            $authentication_manager->setCookieFactory(CookieFactory::fromRequest($request));
        }
        $authentication_manager->setOperatorFromRequest($request);

        // Actualize AssetUrlGenerator
        $this->getAssetManager()->setRequest($request);
    }

    /**
     * Finalize the request and make sure that the response is in compliance
     * with it.
     *
     * @param Request $request The processed request.
     * @param Response $response The response which should be set to the client.
     */
    protected function finalizeRequest(Request $request, Response $response)
    {
        // Attach operator's authentication info to the response to distinguish
        // him in the next requests.
        $this->getAuthenticationManager()->attachOperatorToResponse($response);

        // Cache user's locale in the cookie. The code below should be treated
        // as a temporary workaround.
        // TODO: Move the following lines to Locales Manager when object
        // oriented approach for locales will be implemented.
        $response->headers->setCookie(CookieFactory::fromRequest($request)->createCookie(
            LOCALE_COOKIE_NAME,
            get_current_locale(),
            time() + 60 * 60 * 24 * 1000
        ));

        $response->prepare($request);
    }

    /**
     * Builds response for a page if access to it is denied.
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::RESOURCE_ACCESS_DENIED}
     * event.
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
        $dispatcher->triggerEvent(Events::RESOURCE_ACCESS_DENIED, $args);

        if ($args['response'] && ($args['response'] instanceof Response)) {
            // If one of event listeners returned the response object send it
            // to the client.
            return $args['response'];
        }

        if ($this->getAuthenticationManager()->getOperator()) {
            // If the operator already logged in, display 403 page.
            return new Response('Forbidden', 403);
        }

        // Operator is not logged in. Use the correct backpath to redirect him
        // operator after login.
        if ($request->attributes->get('_route') == 'users_update') {
            // Do not use "users" client application gateway as the backpath.
            // Use the awaiting visitors page instead.
            $_SESSION[SESSION_PREFIX . 'backpath'] = $this->getRouter()->generate('users');
        } else {
            // Just use the current URI as the backpath.
            $_SESSION[SESSION_PREFIX . 'backpath'] = $request->getUri();
        }
        // Redirect the operator to the login page.
        $response = new RedirectResponse($this->getRouter()->generate('login'));

        return $response;
    }

    /**
     * Builds response for a not found page.
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::RESOURCE_NOT_FOUND}
     * event.
     *
     * @param Request $request Incoming request
     * @return Response
     */
    protected function buildNotFoundResponse(Request $request)
    {
        // Trigger fail
        $args = array(
            'request' => $request,
            'response' => false,
        );
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent(Events::RESOURCE_NOT_FOUND, $args);

        if ($args['response'] && ($args['response'] instanceof Response)) {
            // If one of event listeners returned the response object send it
            // to the client.
            return $args['response'];
        }

        return new Response('Not Found', 404);
    }
}
