/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, MibewAPI, $, _) {

    /**
     * This class implemets server interaction functionality
     * @todo May be make all private properties and methods _really_ private
     */
    Mibew.Server = function(options) {

        /**
         * Update timer
         */
        this.updateTimer = null;

        /**
         * Options for the Server object
         * @private
         */
        this.options = _.extend({
            // Server gateway URL
            url: "",
            // Time between requests (in seconds)
            requestsFrequency: 2,
            // Pause before restarting updater using Server.restartUpdater
            // function (in seconds)
            reconnectPause: 1,
            // Call on request timeout
            onTimeout: function() {},
            // Call when transport error was caught
            onTransportError: function() {},
            // Call when callFunctions related error was caught
            onCallError: function(e) {},
            // Call when update related error was caught
            onUpdateError: function(e) {},
            // Call when response related error was caught
            onResponseError: function(e) {}
        }, options);

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
         * An object of the jqXHR class
         * @type jqXHR
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
        this.mibewAPI = new MibewAPI(new this.options['interactionType']());
    }

    /**
     * Make call to the server
     *
     * @param {Oblect[]} functionsList List of the function objects. See
     * Mibew API for details.
     * @param {Function} callbackFunction
     * @param {Boolean} forceSend Force requests buffer send right after call
     * @returns {Boolean} boolean true on success and false on failure
     */
    Mibew.Server.prototype.callFunctions = function(functionsList, callbackFunction, forceSend) {
        try {
            // Check function objects
            if (!(functionsList instanceof Array)) {
                throw new Error("The first arguments must be an array");
            }
            // TODO: Try to use 'for' loop instead of 'for in' loop
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
    }

    /**
     * Call function at every request to build functions list
     *
     * @param {Function} functionsListBuilder Call before every request to build
     * a list of functions that must be called
     * @param {Function} callbackFunction Call after response received
     */
    Mibew.Server.prototype.callFunctionsPeriodically = function(functionsListBuilder, callbackFunction) {
        this.callPeriodically.push({
            functionsListBuilder: functionsListBuilder,
            callbackFunction: callbackFunction
        });
    }

    /**
     * Generates unique request token
     *
     * @private
     * @returns {String} Request token
     */
    Mibew.Server.prototype.generateToken = function() {
        var token;
        do {
            // Create random token
            token = "wnd" +
                (new Date()).getTime().toString() +
                (Math.round(Math.random() * 50)).toString();
        // Check token uniqueness
        } while(token in this.callbacks);
        return token;
    }

    /**
     * Process request
     *
     * @param {Object} requestObject Request object. See Mibew API for details.
     * @private
     */
    Mibew.Server.prototype.processRequest = function(requestObject) {
        var context = new MibewAPIExecutionContext();

        // Get result function
        var resultFunction = this.mibewAPI.getResultFunction(
            requestObject.functions,
            this.callbacks.hasOwnProperty(requestObject.token)
        );

        if (resultFunction === null) {
            // Result function not found
            // TODO: Try to use 'for' loop instead of 'for in' loop
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
    }

    /**
     * Process function
     *
     * @param {Object} functionObject Function object. See Mibew API for details
     * @param {MibewAPIExecutionContext} context Execution context
     * @private
     */
    Mibew.Server.prototype.processFunction = function(functionObject, context) {
        if (! this.functions.hasOwnProperty(functionObject["function"])) {
            return;
        }
        // Get function arguments with replaced refences
        var functionArguments = context.getArgumentsList(functionObject);

        var results = {};
        // TODO: Try to use 'for' loop instead of 'for in' loop
        for (var i in this.functions[functionObject["function"]]) {
            if (! this.functions[functionObject["function"]].hasOwnProperty(i)) {
                continue;
            }
            // Get results
            results = _.extend(results, this.functions[functionObject["function"]][i](
                functionArguments
            ));
        }

        // Add function results to the execution context
        context.storeFunctionResults(functionObject, results);
    }

    /**
     * Send the request to the server
     *
     * @param {Object[]} requestsList Array of requests that must be sent to the
     * server
     * @private
     */
    Mibew.Server.prototype.sendRequests = function(requestsList) {
        var self = this;
        // Create new AJAX request
        this.ajaxRequest = $.ajax({
            url: self.options.url,
            timeout: 5000,
            async: true,
            cache: false,
            type: 'POST',
            dataType: 'text',
            data: {
                data: this.mibewAPI.encodePackage(requestsList)
            },
            success: _.bind(self.receiveResponse, self),
            error: _.bind(self.onError, self)
        });
    }

    /**
     * Sets up next automatic updater iteration
     */
    Mibew.Server.prototype.runUpdater = function() {
        if (this.updateTimer == null) {
            this.update();
        }
        this.updateTimer = setTimeout(
            _.bind(this.update, this),
            this.options.requestsFrequency * 1000
        );
    }

    /**
     * Restarts the automatic updater
     */
    Mibew.Server.prototype.restartUpdater = function() {
        // Clear timeout
        if (this.updateTimer) {
            clearTimeout(this.updateTimer);
        }
        // Abort current AJAX request if it exist
        if (this.ajaxRequest) {
            this.ajaxRequest.abort();
        }
        // Update thread
        this.update();
        // Restart updater. Try to reconnect after a while
        this.updateTimer = setTimeout(
            _.bind(this.update, this),
            this.options.reconnectPause * 1000
        );
    }

    /**
     * Send request for update thread and client code's requests
     * @private
     */
    Mibew.Server.prototype.update = function() {
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
    }

    /**
     * Process response from the Server
     *
     * @param {String} data The response text
     * @param {String} textStatus Request status
     * @param {jqXHR} jqXHR jQuery AJAX request object
     * @private
     */
    Mibew.Server.prototype.receiveResponse = function(data, textStatus, jqXHR) {
        // Do not parse empty responses
        if (data == '') {
            this.runUpdater();
        }
        try {
            var packageObject = this.mibewAPI.decodePackage(data);
            // TODO: Try to use 'for' loop instead of 'for in' loop
            // or use hasOwnProperty method
            for (var i in packageObject.requests) {
                this.processRequest(packageObject.requests[i]);
            }
        } catch (e) {
            this.options.onResponseError(e);
        } finally {
            this.runUpdater();
        }
    }

    /**
     * Add function that can be called by the Server
     *
     * @param {String} functionName Name of the function
     * @param {Function} handler Provided function
     */
    Mibew.Server.prototype.registerFunction = function(functionName, handler) {
        if (!(functionName in this.functions)) {
            this.functions[functionName] = [];
        }
        this.functions[functionName].push(handler);
    }

    /**
     * Call on AJAX errors
     *
     * @param {jqXHR} jqXHR jQuery AJAX request object
     * @param {String} textStatus Request status
     * @param {String} errorThrown Contain text part HTTP status if HTTP error
     * occurs
     */
    Mibew.Server.prototype.onError = function(jqXHR, textStatus, errorThrown) {
        if (textStatus != 'abort') {
            this.restartUpdater();
            if (textStatus == 'timeout') {
                this.options.onTimeout();
            } else if (textStatus == 'error') {
                this.options.onTransportError();
            }
        }
    }

})(Mibew, MibewAPI, $, _);