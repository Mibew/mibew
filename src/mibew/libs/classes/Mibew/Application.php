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

use Mibew\Routing\Router;
use Mibew\Routing\RouteCollectionLoader;
use Mibew\Routing\Exception\AccessDeniedException;
use Mibew\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Config\FileLocator;

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
            // Try to match route
            $parameters = $this->router->matchRequest($request);
            $request->attributes->add($parameters);

            // Get controller
            $controller = $this->controllerResolver->getController($request);

            // Execute the controller's action and get response.
            $response = call_user_func($controller, $request);
        } catch(AccessDeniedException $e) {
            return new Response('Forbidden', 403);
        } catch (ResourceNotFoundException $e) {
            return new Response('Not Found', 404);
        } catch (MethodNotAllowedException $e) {
            return new Response('Method Not Allowed', 405);
        } catch (Exception $e) {
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
}
