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

use Mibew\Asset\AssetManagerAwareInterface;
use Mibew\Asset\AssetManagerInterface;
use Mibew\Asset\Generator\UrlGeneratorInterface as AssetUrlGeneratorInterface;
use Mibew\Authentication\AuthenticationManagerAwareInterface;
use Mibew\Authentication\AuthenticationManagerInterface;
use Mibew\Cache\CacheAwareInterface;
use Mibew\Handlebars\CacheAdapter as HandlebarsCacheAdapter;
use Mibew\Handlebars\HandlebarsAwareInterface;
use Mibew\Handlebars\Helper\AssetHelper;
use Mibew\Handlebars\Helper\CsrfProtectedRouteHelper;
use Mibew\Handlebars\Helper\CssAssetsHelper;
use Mibew\Handlebars\Helper\JsAssetsHelper;
use Mibew\Handlebars\Helper\RouteHelper;
use Mibew\Mail\MailerFactoryAwareInterface;
use Mibew\Mail\MailerFactoryInterface;
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
    CacheAwareInterface,
    MailerFactoryAwareInterface
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
     * @var MailerFactoryInterface|null
     */
    protected $mailerFactory = null;

    /**
     * {@inheritdoc}
     */
    public function setRouter(RouterInterface $router)
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

        // Update cache inside the handlebars template engine if needed.
        if ($this->getStyle() instanceof HandlebarsAwareInterface) {
            $hbs = $this->getStyle()->getHandlebars();
            if ($hbs->getCache() instanceof CacheAwareInterface) {
                $hbs->getCache()->setCache($cache);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMailerFactory(MailerFactoryInterface $factory)
    {
        $this->mailerFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getMailerFactory()
    {
        return $this->mailerFactory;
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
        // Attach all default css files
        $this->getAssetManager()->attachCss('js/vendor/vex/css/vex.css', AssetManagerInterface::RELATIVE_URL, -1000);
        $this->getAssetManager()->attachCss('js/vendor/vex/css/vex-theme-default.css', AssetManagerInterface::RELATIVE_URL, -1000);

        // Attach all needed JavaScript files. This is done here to decouple
        // templates and JavaScript applications.
        $assets = array(
            // External libs
            'js/vendor/jquery/dist/jquery.min.js',
            'js/vendor/json/json2.min.js',
            'js/vendor/underscore/underscore-min.js',
            'js/vendor/backbone/backbone-min.js',
            'js/vendor/marionette/lib/backbone.marionette.min.js',
            'js/vendor/handlebars/handlebars.min.js',
            'js/vendor/vex/js/vex.combined.min.js',
            'js/vendor/validator-js/validator.min.js',
            // Client side templates
            $this->getStyle()->getFilesPath() . '/templates_compiled/client_side/templates.js',
            // Default client side application files
            'js/compiled/mibewapi.js',
            'js/compiled/default_app.js',
        );

        foreach ($assets as $asset) {
            $this->getAssetManager()->attachJs($asset, AssetManagerInterface::RELATIVE_URL, -1000);
        }

        // Localization file is added as absolute URL because URL Generator
        // already prepended base URL to its result.
        // Localization file is generated using contents from the database,
        // so it should not be attached when in the maintenance mode
        if (get_maintenance_mode() === false) {
            $this->getAssetManager()->attachJs(
                $this->generateUrl('js_translation', array('locale' => get_current_locale())),
                AssetManagerInterface::ABSOLUTE_URL,
                -1000
            );
        }

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
     * Sends an email.
     *
     * It's just a handy shortcut for
     * `$this->getMailerFactory()->getMailer()->send($message);` sequence.
     * @param \Swift_Message $message A message that should be sent.
     */
    public function sendMail(\Swift_Message $message)
    {
        $this->getMailerFactory()->getMailer()->send($message);
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
            $hbs = $style->getHandlebars();

            // Use Mibew Messenger cache to store Handlebars AST
            $hbs->setCache(new HandlebarsCacheAdapter($this->getCache()));

            // Add more helpers to template engine
            $hbs->addHelper('route', new RouteHelper($this));
            $hbs->addHelper(
                'csrfProtectedRoute',
                new CsrfProtectedRouteHelper($this)
            );
            $hbs->addHelper(
                'asset',
                new AssetHelper(
                    $this,
                    array('CurrentStyle' => $style->getFilesPath())
                )
            );
            $hbs->addHelper('jsAssets', new JsAssetsHelper($this));
            $hbs->addHelper('cssAssets', new CssAssetsHelper($this));
        }

        return $style;
    }
}
