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
use Mibew\Authentication\AuthenticationManagerAwareInterface;
use Mibew\Authentication\AuthenticationManagerInterface;
use Mibew\Cache\CacheAwareInterface;
use Mibew\Mail\MailerFactoryAwareInterface;
use Mibew\Mail\MailerFactoryInterface;
use Mibew\Routing\RouterAwareInterface;
use Mibew\Routing\RouterInterface;
use Stash\Interfaces\PoolInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerResolver implements
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
     * Class constructor.
     *
     * @param RouterInterface $router Router instance.
     * @param AuthenticationManagerInterface $authentication_manager
     *   Authentication manager instance.
     * @param AssetManagerInterface $asset_manager An instance of Asset
     *   Manager.
     * @param PoolInterface $cache An instance of Cache pool.
     */
    public function __construct(
        RouterInterface $router,
        AuthenticationManagerInterface $authentication_manager,
        AssetManagerInterface $asset_manager,
        PoolInterface $cache,
        MailerFactoryInterface $mailer_factory
    ) {
        $this->router = $router;
        $this->authenticationManager = $authentication_manager;
        $this->assetManager = $asset_manager;
        $this->cache = $cache;
        $this->mailerFactory = $mailer_factory;
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
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
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
    public function setAuthenticationManager(AuthenticationManagerInterface $manager)
    {
        $this->authenticationManager = $manager;
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
            $object->setRouter($this->getRouter());
        }

        if ($object instanceof AuthenticationManagerAwareInterface) {
            $object->setAuthenticationManager($this->getAuthenticationManager());
        }

        if ($object instanceof AssetManagerAwareInterface) {
            $object->setAssetManager($this->getAssetManager());
        }

        if ($object instanceof CacheAwareInterface) {
            $object->setCache($this->getCache());
        }

        if ($object instanceof MailerFactoryAwareInterface) {
            $object->setMailerFactory($this->getMailerFactory());
        }

        return array($object, $method);
    }
}
