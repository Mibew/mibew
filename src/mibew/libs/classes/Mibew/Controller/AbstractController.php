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

use Mibew\Routing\Router;
use Mibew\Routing\RouterAwareInterface;
use Mibew\Style\StyleInterface;
use Mibew\Style\PageStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * A base class for all controllers.
 */
abstract class AbstractController implements RouterAwareInterface
{
    /**
     * @var Router|null
     */
    protected $router = null;

    /**
     * @var StyleInterface|null
     */
    protected $style = null;

    /**
     * {@inheritdoc}
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route The name of the route.
     * @param mixed $parameters An array of parameters.
     * @param bool|string $referenceType The type of reference (one of the
     *   constants in UrlGeneratorInterface).
     *
     * @return string The generated URL.
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->getRouter()->generate($route, $parameters, $referenceType);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url The URL to redirect to.
     * @param int $status The status code to use for the Response.
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a rendered template.
     *
     * @param string $template Name of the template.
     * @param array $parameters An array of parameters to pass to the template.
     *
     * @return string The rendered template
     */
    public function render($template, array $parameters = array())
    {
        // TODO: Remove bufferization after all pages will be replaced with
        // controllers and direct output will be removed from the *Style classes.
        ob_start();
        $this->getStyle()->render($template, $parameters);
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Returns style object related with the controller.
     *
     * The method can be overridden in child classes to implement style choosing
     * logic.
     *
     * @return StyleInterface
     * @todo Update the method after rewriting style choosing logic
     */
    protected function getStyle()
    {
        if (is_null($this->style)) {
            $this->style = new PageStyle(PageStyle::getDefaultStyle());
        }

        return $this->style;
    }
}
