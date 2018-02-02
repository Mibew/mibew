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

use Mibew\Http\Exception\NotFoundException;
use Mibew\Plugin\PluginInfo;
use Mibew\Plugin\PluginManager;
use Mibew\Plugin\Utils as PluginUtils;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with plugins management.
 */
class PluginController extends AbstractController
{
    /**
     * Generates list of all plugins in the system.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function indexAction(Request $request)
    {
        $page = array(
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass errors from another actions.
            'errors' => $request->attributes->get('errors', array()),
        );

        $page['plugins'] = $this->buildPluginsList();
        $page['title'] = getlocal('Plugins');
        $page['menuid'] = 'plugins';
        $page = array_merge($page, prepare_menu($this->getOperator()));

        $this->getAssetManager()->attachJs('js/compiled/plugins.js');

        return $this->render('plugins', $page);
    }

    /**
     * Enables a plugin.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the plugin with specified name is not found
     *   in the system.
     */
    public function enableAction(Request $request)
    {
        csrf_check_token($request);

        $plugin_name = $request->attributes->get('plugin_name');

        if (!PluginUtils::pluginExists($plugin_name)) {
            throw new NotFoundException('The plugin is not found.');
        }

        // Enable the plugin
        if (!PluginManager::getInstance()->enable($plugin_name)) {
            $error = getlocal(
                'Plugin "{0}" cannot be enabled.',
                array($plugin_name)
            );
            $request->attributes->set('errors', array($error));

            // The plugin cannot be enabled by some reasons. Just rebuild
            // index page and show errors there.
            return $this->indexAction($request);
        }

        // Plugins can have own routing files and when the plugin becomes
        // enabled its routes should become enabled too. So the cache is cleared
        // to make sure the routes set is up to date.
        $this->getCache()->getItem('routing/resources')->clear();

        return $this->redirect($this->generateUrl('plugins'));
    }

    /**
     * Disables a plugin.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the plugin with specified name is not found
     *   in the system.
     */
    public function disableAction(Request $request)
    {
        csrf_check_token($request);

        $plugin_name = $request->attributes->get('plugin_name');

        if (!PluginUtils::pluginExists($plugin_name)) {
            throw new NotFoundException('The plugin is not found.');
        }

        // Disable the plugin
        if (!PluginManager::getInstance()->disable($plugin_name)) {
            $error = getlocal(
                'Plugin "{0}" cannot be disabled.',
                array($plugin_name)
            );
            $request->attributes->set('errors', array($error));

            // The plugin cannot be disabled by some reasons. Just rebuild
            // index page and show errors there.
            return $this->indexAction($request);
        }

        // Plugins can have own routing files and when the plugin becomes
        // disabled its routes should become disabled too. So the cache is
        // cleared to make sure the routes set is up to date.
        $this->getCache()->getItem('routing/resources')->clear();

        return $this->redirect($this->generateUrl('plugins'));
    }

    /**
     * Uninstalls a plugin.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the plugin with specified name is not found
     *   in the system.
     */
    public function uninstallAction(Request $request)
    {
        csrf_check_token($request);

        $plugin_name = $request->attributes->get('plugin_name');

        if (!PluginUtils::pluginExists($plugin_name)) {
            throw new NotFoundException('The plugin is not found.');
        }

        // Uninstall the plugin
        if (!PluginManager::getInstance()->uninstall($plugin_name)) {
            $error = getlocal(
                'Plugin "{0}" cannot be uninstalled.',
                array($plugin_name)
            );
            $request->attributes->set('errors', array($error));

            // The plugin cannot be uninstalled by some reasons. Just rebuild
            // index page and show errors there.
            return $this->indexAction($request);
        }

        return $this->redirect($this->generateUrl('plugins'));
    }

    /**
     * Updates a plugin.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the plugin with specified name is not found
     *   in the system.
     */
    public function updateAction(Request $request)
    {
        csrf_check_token($request);

        $plugin_name = $request->attributes->get('plugin_name');

        if (!PluginUtils::pluginExists($plugin_name)) {
            throw new NotFoundException('The plugin is not found.');
        }

        // Update the plugin
        if (!PluginManager::getInstance()->update($plugin_name)) {
            $error = getlocal(
                'Plugin "{0}" cannot be updated.',
                array($plugin_name)
            );
            $request->attributes->set('errors', array($error));

            // The plugin cannot be updated by some reasons. Just rebuild
            // index page and show errors there.
            return $this->indexAction($request);
        }

        return $this->redirect($this->generateUrl('plugins'));
    }

    /**
     * Builds plugins list that will be passed to templates engine.
     *
     * @return array
     */
    protected function buildPluginsList()
    {
        $plugins = array();
        foreach (PluginUtils::discoverPlugins() as $plugin_name) {
            $plugin = new PluginInfo($plugin_name);
            $plugins[] = array(
                'name' => $plugin_name,
                'version' => $plugin->isInstalled() ? $plugin->getInstalledVersion() : $plugin->getVersion(),
                'dependencies' => array_merge($plugin->getSystemRequirements(), $plugin->getDependencies()),
                'enabled' => $plugin->isEnabled(),
                'installed' => $plugin->isInstalled(),
                'needsUpdate' => $plugin->needsUpdate(),
                'canBeEnabled' => $plugin->canBeEnabled(),
                'canBeDisabled' => $plugin->canBeDisabled(),
                'canBeUninstalled' => $plugin->canBeUninstalled(),
                'canBeUpdated' => $plugin->canBeUpdated(),
                'state' => $this->getPluginState($plugin),
            );
        }

        return $plugins;
    }

    /**
     * Gets string representation of the current plugin state.
     *
     * @param PluginInfo $plugin Plugin to get state for.
     * @return string Human readable representation of plugin's state.
     */
    protected function getPluginState(PluginInfo $plugin)
    {
        if (!$plugin->isEnabled()) {
            // The plugin is just disabled
            return getlocal('disabled');
        }

        if (PluginManager::getInstance()->hasPlugin($plugin->getName())) {
            // The plugin is enabled and works well
            return getlocal('working');
        }

        // The plugin is enabled but something is wrong.
        if ($plugin->needsUpdate()) {
            // The plugin is not working because it needs to be updated.
            return getlocal('needs update');
        }

        // Actually we do not know why the plugin does not work. The only thing
        // that can be said is the plugin was not initialized correctly by some
        // reasons.
        return getlocal('not initialized');
    }
}
