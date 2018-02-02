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

// Initialize libraries
require_once(dirname(__FILE__) . '/libs/init.php');

use Mibew\Application;
use Mibew\Authentication\AuthenticationManager;
use Mibew\Cache\CacheFactory;
use Mibew\Mail\MailerFactory;
use Mibew\Plugin\PluginManager;
use Mibew\Routing\Router;
use Mibew\Routing\Loader\CacheLoader;
use Mibew\Routing\Loader\PluginLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\YamlFileLoader;

$configs = load_system_configs();

// Prepare the cache
$cache_factory = new CacheFactory($configs['cache']);
// For now directory for cache files cannot be changed via the configs file.
// TODO: Evaluate possibility of using custom cache directory.
$cache_factory->setOption('path', MIBEW_FS_ROOT . '/cache/stash');

// Run plugins
if (get_maintenance_mode() === false) {
    $plugin_manager = PluginManager::getInstance();
    $plugin_manager->setCache($cache_factory->getCache());
    $plugin_manager->loadPlugins($configs['plugins']);
}

// The main route loader which loads nothig but works as a cache proxy for other
// loaders.
$route_loader = new CacheLoader($cache_factory->getCache());
// Real loaders are attached via the resolver.
$loader_resolver = new LoaderResolver(array(
    $route_loader,
    new YamlFileLoader(new FileLocator(array(MIBEW_FS_ROOT))),
    new PluginLoader(),
));

$router = new Router($route_loader, 'configs/routing.yml');

$application = new Application($router, new AuthenticationManager());
$application->setCache($cache_factory->getCache());

// Use custom config-dependent mailer factory
$application->setMailerFactory(new MailerFactory($configs['mailer']));

// Process request
$request = Request::createFromGlobals();
$response = $application->handleRequest($request);

// Send response to the user
$response->send();
