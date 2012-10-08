/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

/**
 * This class implemets server interaction functionality
 */
ChatServer = Class.create();
/**
 * @todo Think about error handling
 */
ChatServer.prototype = {
    /**
     * @constructor
     */
    initialize: function(options) {
        var chatServer = this;

        /**
         * Update timer
         */
        this.updateTimer = null;

        /**
         * Options for the ChatServer object
         * @private
         * @todo Check onResponseError handler
         */
        this.options = {
            // Server gateway URL
            servl: "",
            // Frequency for automatic updater
            requestsFrequency: 2,
            // Call on request timeout
            onTimeout: function() {},
            // Call when transport error was caught
            onTransportError: function(e) {},
            // Call when callFunctions related error was caught
            onCallError: function(e) {},
            // Call when update related error was caught
            onUpdateError: function(e) {},
            // Call when response related error was caught
            onResponseError: function(e) {}
        }.extend(options);

        /**
         * Binds request's token and callback function
         * @type Object
         * @private
         */
        this.callbacks = {};

        /**
         * Array of periodically called functions
         * @type Array
         * @private
         */
        this.callPeriodically = [];

        /**
         * Options for an Ajax.Request object
         * @type Array
         * @private
         */
        this.ajaxOptions = {
            _method: 'post',
            asynchronous: true,
            timeout: 5000,
            onComplete: chatServer.receiveResponse.bind(chatServer),
            onException: chatServer.onTransportError.bind(chatServer),
            onTimeout: chatServer.onTimeout.bind(chatServer)
        }

        /**
         * An object of the Ajax.Request class
         * @type Ajax.Request
         * @private
         */
        this.ajaxRequest = null;

        /**
         * This buffer store requests and responses between sending packages
         * @private
         */
        this.buffer = [];

        /**
         * Contains object of registered functions handlers
         * @private
         */
        this.functions = {}

        /**
         * An instance of the MibewAPI class
         * @type MibewAPI
         * @private
         */
        this.mibewAPI = new MibewAPI(new MibewAPICoreInteraction());
    },

    /**
     * Make call to the chat server
     *
     * @param {Oblect[]} functionsList List of the function objects. See Mibew API
     * for details.
     * @param {Function} callbackFunction
     * @param {Boolean} forceSend Force requests buffer send right after call
     * @returns {Boolean} boolean true on success and false on failure
     */
    callFunctions: function(functionsList, callbackFunction, forceSend) {
        try {
            // Check function objects
            if (!(functionsList instanceof Array)) {
                throw new Error("The first arguments must be an array");
            }
            for (var i in functionsList) {
                // Filter 'Prototype' properties
                if (! functionsList.hasOwnProperty(i)) {
                    continue;
                }
                this.mibewAPI.checkFunction(functionsList[i], false);
            }

            // Generate request token
            var token = this.generateToken();
            // Store callback function
            this.callbacks[token] = callbackFunction;

            // Add request to buffer
            this.buffer.push({
                'token': token,
                'functions': functionsList
            });
            if (forceSend) {
                // Force update
                this.update();
            }
        } catch (e) {
            // Handle errors
            this.options.onCallError(e);
            return false;
        }
        return true;
    },

    /**
     * Call function at every request to build functions list
     *
     * @param {Function} functionsListBuilder Call before every request to build
     * a list of functions that must be called
     * @param {Function} callbackFunction Call after response received
     */
    callFunctionsPeriodically: function(functionsListBuilder, callbackFunction) {
        this.callPeriodically.push({
            functionsListBuilder: functionsListBuilder,
            callbackFunction: callbackFunction
        });
    },

    /**
     * Generates unique request token
     *
     * @private
     * @returns {String} Request token
     */
    generateToken: function() {
        var token;
        do {
            // Create token
            token = "wnd" +
                (new Date()).getTime().toString() +
                (Math.round(Math.random() * 50)).toString();
        // Check token uniqueness
        } while(token in this.callbacks);
        return token;
    },

    /**
     * Process request
     *
     * @param {Object} requestObject Request object. See Mibew API for details.
     * @private
     */
    processRequest: function(requestObject) {
        var context = new MibewAPIExecutionContext();

        // Get result function
        var resultFunction = this.mibewAPI.getResultFunction(
            requestObject.functions,
            this.callbacks.hasOwnProperty(requestObject.token)
        );

        if (resultFunction === null) {
            // Result function not found
            for (var i in requestObject.functions) {
                if (! requestObject.functions.hasOwnProperty(i)) {
                    continue;
                }
                // Execute functions
                this.processFunction(requestObject.functions[i], context);
                // Build and store result
                this.buffer.push(this.mibewAPI.buildResult(
                    context.getResults(),
                    requestObject.token
                ));
            }
        } else {
            // Result function found
            if (this.callbacks.hasOwnProperty(requestObject.token)) {
                // Invoke callback
                this.callbacks[requestObject.token](resultFunction.arguments);
                // Remove callback
                delete this.callbacks[requestObject.token];
            }
        }
    },

    /**
     * Process function
     *
     * @param {Object} functionObject Function object. See Mibew API for details
     * @param {MibewAPIExecutionContext} context Execution context
     * @private
     */
    processFunction: function(functionObject, context) {
        if (! this.functions.hasOwnProperty(functionObject["function"])) {
            return;
        }
        // Get function arguments with replaced refences
        var functionArguments = context.getArgumentsList(functionObject);

        var results = {};
        for (var i in this.functions[functionObject["function"]]) {
            if (! this.functions[functionObject["function"]].hasOwnProperty(i)) {
                continue;
            }
            // Get results
            results.extend(this.functions[functionObject["function"]][i](
                functionArguments
            ));
        }

        // Add function results to the execution context
        context.storeFunctionResults(functionObject, results);
    },

    /**
     * Send the request to the chat server
     *
     * @param {Object[]} requestsList Array of requests that must be sent to the
     * chat server
     * @private
     */
    sendRequests: function(requestsList) {
        // Create new AJAX request
        this.ajaxRequest = new Ajax.Request(
            this.options.servl,
            this.ajaxOptions.extend({
                parameters: 'data=' + this.mibewAPI.encodePackage(requestsList)
            })
        );
    },

    /**
     * Sets up next automatic updater iteration
     */
    runUpdater: function() {
        if (this.updateTimer == null) {
            this.update();
        }
        this.updateTimer = setTimeout(
            this.update.bind(this),
            this.options.requestsFrequency * 1000
        );
    },

    /**
     * Restarts the automatic updater
     */
    restartUpdater: function() {
        // Clear timeout
        if (this.updateTimer) {
            clearTimeout(this.updateTimer);
        }
        // Clear request onComplete callback
        if (this.ajaxRequest._options) {
            this.ajaxRequest._options.onComplete = undefined;
        }
        // Update thread
        this.update();
        // Restart updater. Try to reconnect after a while
        this.updateTimer = setTimeout(
            this.update.bind(this),
            1000
        );
    },

    /**
     * Send request for update thread and client code's requests
     * @private
     */
    update: function() {
        if (this.updateTimer) {
            clearTimeout(this.updateTimer);
        }
        for (var i = 0; i < this.callPeriodically.length; i++) {
            this.callFunctions(
                this.callPeriodically[i].functionsListBuilder(),
                this.callPeriodically[i].callbackFunction
            );
        }
        // Check buffer length
        if (this.buffer.length == 0) {
            // Rerun updater later
            this.runUpdater();
            return;
        }
        try {
            // Send requests
            this.sendRequests(this.buffer);
            // Clear requests buffer
            this.buffer = [];
        } catch (e) {
            // Handle errors
            this.options.onUpdateError(e);
        }
    },

    /**
     * Process response from the Core
     *
     * @param {String} responseObject The response object provided by
     * Ajax.Request class
     * @private
     */
    receiveResponse: function(responseObject) {
        // Do not parse empty responses
        if (responseObject.response == '') {
            this.runUpdater();
        }
        try {
            var packageObject = this.mibewAPI.decodePackage(responseObject.response);
            for (var i in packageObject.requests) {
                this.processRequest(packageObject.requests[i]);
            }
        } catch (e) {
            this.options.onResponseError(e);
        } finally {
            this.runUpdater();
        }
    },

    /**
     * Add function that can be called by the Core
     *
     * @param {String} functionName Name of the function
     * @param {Function} handler Provided function
     */
    registerFunction: function(functionName, handler) {
        if (!(functionName in this.functions)) {
            this.functions[functionName] = [];
        }
        this.functions[functionName].push(handler);
    },

    /**
     * Call on all AJAX transport errors
     * @param {Ajax.Request} transport AJAX Transport object
     * @param {Error} e Error object
     */
    onTransportError: function (transport, e) {
        this.restartUpdater();
        this.options.onTransportError(e);
    },

    /**
    * Call on all timeouts
    */
    onTimeout: function(transport) {
        this.restartUpdater();
        this.options.onTimeout()
    }
}