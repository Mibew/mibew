/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
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
         * Contains periodically called functions
         * @type Object
         * @private
         */
        this.callPeriodically = {};

        /**
         * Id of the last added periodically called function
         * @type Number
         * @private
         */
        this.callPeriodicallyLastId = 0;

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
         * @type Object
         * @private
         */
        this.functions = {}

        /**
         * Id of the last registered function
         * @type Number
         * @private
         */
        this.functionsLastId = 0;

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
            for (var i = 0; i < functionsList.length; i++) {
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
     * @returns {Number} Id of added functions
     */
    Mibew.Server.prototype.callFunctionsPeriodically = function(functionsListBuilder, callbackFunction) {
        this.callPeriodicallyLastId++;
        this.callPeriodically[this.callPeriodicallyLastId] = {
            functionsListBuilder: functionsListBuilder,
            callbackFunction: callbackFunction
        };
        return this.callPeriodicallyLastId;
    }

    /**
     * Stop calling function at every request.
     *
     * @param {Number} id Id of the periodically called function, returned by
     * Mibew.Server.callFunctionsPeriodically method
     */
    Mibew.Server.prototype.stopCallFunctionsPeriodically = function(id) {
        if (id in this.callPeriodically) {
            delete this.callPeriodically[id];
        }
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
     * Start automatic updater
     */
    Mibew.Server.prototype.runUpdater = function() {
        this.update();
    }

    /**
     * Call Mibew.Server.update after specified timeout
     * @param {Number} time Timeout in seconds
     * @private
     */
    Mibew.Server.prototype.updateAfter = function(time) {
        this.updateTimer = setTimeout(
            _.bind(this.update, this),
            time * 1000
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
        // Restart updater. Try to reconnect after a while
        this.updateAfter(this.options.reconnectPause);
    }

    /**
     * Send request to server
     * @private
     */
    Mibew.Server.prototype.update = function() {
        if (this.updateTimer) {
            clearTimeout(this.updateTimer);
        }
        for (var i in this.callPeriodically) {
            if (! this.callPeriodically.hasOwnProperty(i)) {
                continue;
            }
            this.callFunctions(
                this.callPeriodically[i].functionsListBuilder(),
                this.callPeriodically[i].callbackFunction
            );
        }
        // Check buffer length
        if (this.buffer.length == 0) {
            // Rerun updater later
            this.updateAfter(this.options.requestsFrequency);
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
            this.updateAfter(this.options.requestsFrequency);
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
            this.updateAfter(this.options.requestsFrequency);
        }
    }

    /**
     * Add function that can be called by the Server
     *
     * @param {String} functionName Name of the function
     * @param {Function} handler Provided function
     * @returns {Number} Id of registered function
     */
    Mibew.Server.prototype.registerFunction = function(functionName, handler) {
        this.functionsLastId++;
        if (!(functionName in this.functions)) {
            this.functions[functionName] = {};
        }
        this.functions[functionName][this.functionsLastId] = handler;
        return this.functionsLastId;
    }

    /**
     * Remove function that can be called by Server
     *
     * @param {Number} id Id of function returned by
     * Mibew.Server.registerFunction method
     */
    Mibew.Server.prototype.unregisterFunction = function(id) {
        for (var i in this.functions) {
            if (! this.functions.hasOwnProperty(i)) {
                continue;
            }
            if (id in this.functions[i]) {
                delete this.functions[i][id];
            }
            if (_.isEmpty(this.functions[i])) {
                delete this.functions[i];
            }
        }
    },

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