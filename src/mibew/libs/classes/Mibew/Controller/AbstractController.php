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

namespace Mibew\Controller;

use Mibew\Asset\AssetManagerAwareInterface;
use Mibew\Asset\AssetManagerInterface;
use Mibew\Asset\Generator\UrlGeneratorInterface as AssetUrlGeneratorInterface;
use Mibew\Authentication\AuthenticationManagerAwareInterface;
use Mibew\Authentication\AuthenticationManagerInterface;
use Mibew\Cache\CacheAwareInterface;
use Mibew\Handlebars\HandlebarsAwareInterface;
use Mibew\Handlebars\Helper\AdditionalJsHelper;
use Mibew\Handlebars\Helper\AssetHelper;
use Mibew\Handlebars\Helper\CsrfProtectedRouteHelper;
use Mibew\Handlebars\Helper\CssAssetsHelper;
use Mibew\Handlebars\Helper\RouteHelper;
use Mibew\Routing\RouterAwareInterface;
use Mibew\Routing\RouterInterface;
use Mibew\Style\StyleInterface;
use Mibew\Style\PageStyle;
use Stash\Interfaces\PoolInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * A base class for all controllers.
 */
abstract class AbstractController implements
    RouterAwareInterface,
    AuthenticationManagerAwareInterface,
    AssetManagerAwareInterface,
    CacheAwareInterface
{
    /**
     * @var RouterInterface|null
     */
    protected $router = null;

    /**
     * @var AuthenticationManagerInterface|null
     */
    protected $authenticationManager = null;

    /**
     * @var StyleInterface|null
     */
    protected $style = null;

    /**
     * @var AssetManagerInterface|null
     */
    protected $assetManager = null;

    /**
     * @var PoolInterface|null;
     */
    protected $cache = null;

    /**
     * {@inheritdoc}
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;

        // Update router in the style helpers
        if (!is_null($this->style) && $this->style instanceof HandlebarsAwareInterface) {
            $handlebars = $this->style->getHandlebars();
            if ($handlebars->hasHelper('route')) {
                $handlebars->getHelper('route')->setRouter($router);
            }
            if ($handlebars->hasHelper('csrfProtectedRoute')) {
                $handlebars->getHelper('csrfProtectedRoute')->setRouter($router);
            }
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
    public function setAssetManager(AssetManagerInterface $manager)
    {
        $this->assetManager = $manager;

        // Update URL generator in the style helpers
        if (!is_null($this->style) && $this->style instanceof HandlebarsAwareInterface) {
            $handlebars = $this->style->getHandlebars();
            if ($handlebars->hasHelper('asset')) {
                $handlebars->getHelper('asset')->setAssetUrlGenerator($manager->getUrlGenerator());
            }
            if ($handlebars->hasHelper('additionalJs')) {
                $handlebars->getHelper('additionalJs')->setAssetManager($manager);
            }
            if ($handlebars->hasHelper('cssAssets')) {
                $handlebars->getHelper('cssAssets')->setAssetManager($manager);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetManager()
    {
        return $this->assetManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(PoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route The name of the route.
     * @param mixed $parameters An array of parameters.
     * @param bool|string $reference_type The type of reference (one of the
     *   constants in UrlGeneratorInterface).
     *
     * @return string The generated URL.
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->getRouter()->generate($route, $parameters, $reference_type);
    }

    /**
     * Generates an HTTPS URL from the given parameters.
     *
     * @param string $route The name of the route.
     * @param mixed $parameters An array of parameters.
     * @param bool|string $reference_type The type of reference (one of the
     *   constants in UrlGeneratorInterface).
     *
     * @return string The generated URL.
     */
    public function generateSecureUrl($route, $parameters = array(), $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->getRouter()->generateSecure($route, $parameters, $reference_type);
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
        return $this->getStyle()->render($template, $parameters);
    }

    /**
     * Returns the current operator.
     *
     * @return array Operator's data
     */
    public function getOperator()
    {
        return $this->getAuthenticationManager()->getOperator();
    }

    /**
     * Generates URL for an asset with the specified relative path.
     *
     * @param string $relative_path Relative path of an asset.
     * @param bool|string $reference_type Indicates what type of URL should be
     *   generated. It is equal to one of the
     *   {@link AssetUrlGeneratorInterface} constants.
     * @return string Asset URL.
     */
    public function asset($relative_path, $reference_type = AssetUrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->getAssetManager()->getUrlGenerator()
            ->generate($relative_path, $reference_type);
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
            $this->style = $this->prepareStyle(new PageStyle(PageStyle::getDefaultStyle()));
        }

        return $this->style;
    }

    /**
     * Prepares a style right after creation.
     *
     * One can use this method to add custom helpers to a template engine using
     * by the style.
     *
     * @param StyleInterface $style A style that should be prepared.
     * @return StyleInterface A ready to use style instance.
     */
    protected function prepareStyle(StyleInterface $style)
    {
        if ($style instanceof HandlebarsAwareInterface) {
            // Add more helpers to template engine
            $style->getHandlebars()->addHelper(
                'route',
                new RouteHelper($this->getRouter())
            );
            $style->getHandlebars()->addHelper(
                'csrfProtectedRoute',
                new CsrfProtectedRouteHelper($this->getRouter())
            );
            $style->getHandlebars()->addHelper(
                'asset',
                new AssetHelper(
                    $this->getAssetManager()->getUrlGenerator(),
                    array('CurrentStyle' => $style->getFilesPath())
                )
            );
            $style->getHandlebars()->addHelper(
                'additionalJs',
                new AdditionalJsHelper($this->getAssetManager())
            );
            $style->getHandlebars()->addHelper(
                'cssAssets',
                new CssAssetsHelper($this->getAssetManager())
            );
        }

        return $style;
    }
}
