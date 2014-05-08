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

namespace Mibew\Controller;

use Symfony\Component\HttpFoundation\Request;
use Mibew\Routing\Router;
use Mibew\Routing\RouterAwareInterface;

class ControllerResolver
{
    /**
     * @var Router|null
     */
    protected $router = null;

    /**
     * Class constructor.
     *
     * @param Router $router Router instance.
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Resolves controller by request.
     *
     * @param Request $request Incoming request.
     * @return callable
     * @throws \InvalidArgumentException If the controller cannot be resolved.
     */
    public function getController(Request $request)
    {
        // Get controller name from the request
        $controller = $request->attributes->get('_controller');
        if (!$controller) {
            throw new \InvalidArgumentException('The "_controller" parameter is missed.');
        }

        // Build callable for specified controller
        $callable = $this->createController($controller);

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf(
                'Controller "%s" for URI "%s" is not callable.',
                $controller,
                $request->getPathInfo()
            ));
        }

        return $callable;
    }

    /**
     * Builds controller callable by its full name.
     *
     * @param string $controller Full controller name in "<Class>::<method>"
     *   format.
     * @return callable Controller callable
     * @throws \InvalidArgumentException
     */
    protected function createController($controller)
    {
        if (strpos($controller, '::') === false) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to find controller "%s".',
                $controller
            ));
        }

        list($class, $method) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $object = new $class();
        if ($object instanceof RouterAwareInterface) {
            $object->setRouter($this->router);
        }

        return array($object, $method);
    }
}
