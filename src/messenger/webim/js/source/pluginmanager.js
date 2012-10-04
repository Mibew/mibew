/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

/**
 * Create an instance of plugin manager
 * @constructor
 */
var PluginManager = function() {

    /**
     * Contains all added plugins
     * @type Array
     * @private
     */
    var pluginsList = {}

    /**
     * Add plugin to internal plugins list
     *
     * @param {String} pluginName Name of the added plugin. Uses to get plugin
     * by the PluginManager.getPlugin() method
     * @param {Object} plugin A plugin object
     */
    this.addPlugin = function(pluginName, plugin) {
        pluginsList[pluginName] = plugin;
    }

    /**
     * Get plugin object from internal storage
     *
     * @returns {Object|Boolean} Plugin object if it was added by the
     * PluginManager.addPlugin method and boolean false otherwise.
     */
    this.getPlugin = function(pluginName) {
        if (pluginsList[pluginName]) {
            return pluginsList[pluginName];
        }
        return false;
    }
}