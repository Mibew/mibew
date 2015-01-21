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

/**
 * @namespace Holds all Mibew functionality
 */
var Mibew = Mibew || {};

(function(Mibew){
    if (Mibew.Widget) {
        // It seems that the script was already loaded. We do not need to do the
        // job twice so just stop here.
        return;
    }

    /**
     * @namespace Holds objects instances
     */
    Mibew.Objects = Mibew.Objects || {};

    /**
     * Create new widget
     * @constructor
     * @todo Add options validation
     */
    Mibew.Widget = function(options) {
        /**
         * Holds all scripts that must be loaded
         * @type Object
         * @private
         */
        this.requestedScripts = {};

        /**
         * List of handlers that must be called
         * @type Array
         * @private
         */
        this.handlers = [];

        /**
         * List of dependencies between handlers and scripts
         * @type Object
         * @private
         */
        this.handlersDependencies = {};

        /**
         * URL for requests
         * @type String
         */
        this.requestURL = options.requestURL;

        /**
         * Timeout between requests to server
         * @type Number
         */
        this.requestTimeout = options.requestTimeout;

        /**
         * Name of tracking cookie
         * @type String
         */
        this.visitorCookieName = options.visitorCookieName;

        /**
         * URL of file with additional CSS rules for invitation
         * @type String
         */
        this.inviteStyle = options.inviteStyle;

        /**
         * Locale of the Widget
         * @type String
         */
        this.locale = options.locale;

        /**
         * Data that must be sent to the server with next request
         * @type Object
         * @private
         */
        this.dataToSend = {};

        // Load additional styles
        Mibew.Utils.loadStyleSheet(options.inviteStyle);
    }

    /**
     * Make request to the server.
     * @private
     */
    Mibew.Widget.prototype.makeRequest = function() {
        // Try to get user id from local cookie
        var userId = Mibew.Utils.readCookie(this.visitorCookieName);

        // Prepare GET params list
        this.dataToSend.entry = escape(document.referrer),
        this.dataToSend.locale = this.locale;
        this.dataToSend.rnd = Math.random();
        if (userId !== false) {
            this.dataToSend.user_id = userId;
        } else {
            // Enshure that there is not user_id field
            if (this.dataToSend.user_id) {
                delete this.dataToSend.user_id;
            }
        }

        Mibew.Utils.loadScript(
            this.requestURL
                + '?' + this.getQuery(),
            'mibew-response-script'
        );

        // Clean up request data
        this.dataToSend = {};
    }

    /**
     * Build GET request params list
     *
     * @returns String GET params list
     */
    Mibew.Widget.prototype.getQuery = function() {
        var query = [];
        for(var index in this.dataToSend) {
            if (! this.dataToSend.hasOwnProperty(index)) {
                continue;
            }
            query.push(index + '=' + this.dataToSend[index]);
        }
        return query.join('&');
    }

    /**
     * Send arbitrary data to the Server as GET params
     *
     * @param {Object} data List of data that should be sent to the server.
     * Properties of this object will automatically transform to GET params.
     * Values must be strings or numbers. Strings will be automatically escaped
     * before add to query.
     * Do not use properties with following names: 'entry', 'locale', 'rnd',
     * 'user_id'. They are reserved by the system and will be overridden by it.
     */
    Mibew.Widget.prototype.sendToServer = function(data) {
        for(var index in data) {
            if (! data.hasOwnProperty(index)) {
                continue;
            }

            var value = data[index];
            // Only strings and numbers can be passed to the server
            if ((typeof value !== 'string') && (typeof value !== 'number')) {
                continue;
            }
            // Escape string to add in URL later
            if (typeof value === 'string') {
                value = encodeURIComponent(value);
            }

            this.dataToSend[index] = value;
        }
    }

    /**
     * Parse server response. Called as JSONP callback function
     * @param {Object} response Data got from server
     */
    Mibew.Widget.prototype.onResponse = function(response) {
        // Create some shortcuts
        var load = response.load;
        var handlers = response.handlers;
        var data = response.data;
        var dependencies = response.dependencies;
        var context = this;

        // Update list of scripts that must be loaded
        for(var id in load){
            if (! load.hasOwnProperty(id)) {
                continue;
            }
            // Check if script already loaded
            if (! (id in this.requestedScripts)) {
                this.requestedScripts[id] = {};
                this.requestedScripts[id].url = load[id];
                this.requestedScripts[id].status = 'loading';
                this.loadScript(id);
            }
        }

        // Update list of dependencies
        for(var handler in dependencies){
            if (! dependencies.hasOwnProperty(handler)) {
                continue;
            }
            // Check if dependencies for this handler already stored
            if (! (handler in this.handlersDependencies)) {
                this.handlersDependencies[handler] = dependencies[handler];
            }
        }

        // Process all recieved handlers. Run handler if all dependencies loaded
        // and add it to handlers list otherwise.
        for (var i = 0; i < handlers.length; i++) {
            // Create shortcuts
            var handlerName = handlers[i];

            // TODO: Allow to run objects methods
            if (this.canRunHandler(handlerName)) {
                Mibew.APIFunctions[handlerName](data);
            } else {
                if (! (handlerName in this.handlers)) {
                    this.handlers[handlerName] = function(){
                        Mibew.APIFunctions[handlerName](data);
                    };
                }
            }
        }

        this.cleanUpAfterRequest();

        // Make new request after timeout
        window.setTimeout(
            function() {
                context.makeRequest();
            },
            this.requestTimeout);
    }

    /**
     * Remove dynamically loaded request script from DOM
     * @private
     */
    Mibew.Widget.prototype.cleanUpAfterRequest = function() {
        document.getElementsByTagName('head')[0]
            .removeChild(document.getElementById('mibew-response-script'));
    }

    /**
     * Load script and add handler for script onLoad event
     * @param {String} id Identifier of the script to load
     * @private
     */
    Mibew.Widget.prototype.loadScript = function(id) {
        // Store context
        var context = this;
        // Load script by adding script tag to DOM
        var script = Mibew.Utils.loadScript(this.requestedScripts[id].url, id);

        // Check if script loaded
        script.onload = function(){
            context.scriptReady(id);
        }
        // Do it in crossbrowser way
        script.onreadystatechange = function(){
            if (this.readyState == 'complete' || this.readyState == 'loaded') {
                context.scriptReady(id);
            }
        }
    }

    /**
     * Event listener for script onLoad event. Run handlers which have no
     * unload dependencies.
     * @param {String} id Identifier of the loaded script
     */
    Mibew.Widget.prototype.scriptReady = function(id) {
        this.requestedScripts[id].status = 'ready';
        for (var handlerName in this.handlers) {
            if (! this.handlers.hasOwnProperty(handlerName)) {
                continue;
            }
            if(this.canRunHandler(handlerName)){
                this.handlers[handlerName]();
                delete this.handlers[handlerName];
            }
        }
    }

    /**
     * Check if handler can be run
     */
    Mibew.Widget.prototype.canRunHandler = function(handlerName) {
        var dependencies = this.handlersDependencies[handlerName];
        // Check for dependencies
        for(var i = 0; i < dependencies.length; i++){
            if(this.requestedScripts[dependencies[i]].status != 'ready'){
                return false;
            }
        }
        return true;
    }

    /**
     * Helper function which create new widget object, store it into
     * Mibew.Objects.widget and run automatic requests.
     */
    Mibew.Widget.init = function(options) {
        Mibew.Objects.widget = new Mibew.Widget(options);
        Mibew.Objects.widget.makeRequest();
    }

    /**
     * @namespace Holds invitation stuff
     */
    Mibew.Invitation = {};

    /**
     * Create invitation popup
     * @param {Object} options Popup options. It can contain following items:
     *  - 'operatorName': String, name of the operator who invite visitor to
     *    chat;
     *  - 'avatarUrl' String, URL of operator's avatar;
     *  - 'threadUrl': String, URL of the invitation thread which must be
     *    dispaly in invitation iframe.
     *  - 'acceptCaption': String, caption for accept button.
     */
    Mibew.Invitation.create = function (options) {
        var operatorName = options.operatorName;
        var avatarUrl = options.avatarUrl;
        var threadUrl = options.threadUrl;
        var acceptCaption = options.acceptCaption;

        var popuptext = '<div id="mibew-invitation-popup" style="display: none;">';
        popuptext += '<div id="mibew-invitation-close">'
            + '<a href="javascript:void(0);" onclick="Mibew.Invitation.reject();">'
            + '&times;</a></div>';

        // Add operator avatar
        if (avatarUrl) {
            popuptext += '<div id="mibew-invitation-avatar-wrapper">'
                + '<img id="mibew-invitation-avatar" src="' + avatarUrl
                + '" title="' + operatorName
                + '" alt="' + operatorName
                + '" onclick="Mibew.Invitation.accept();" />'
                + '</div>';
        }

        // Add operator name
        if (operatorName) {
            popuptext += '<h1 onclick="Mibew.Invitation.accept();">'
                + operatorName
                + '</h1>';
        }

        // Broadcast message from the thread related with invitation into iframe
        if (threadUrl) {
            popuptext += '<iframe id="mibew-invitation-frame" '
                + 'src="' + threadUrl + '" onload="Mibew.Invitation.show();" '
                + 'frameBorder="0"></iframe>';
        }

        // Add accept button if acceptCaption set
        if (acceptCaption) {
            popuptext += '<div id="mibew-invitation-accept"'
                + ' onclick="Mibew.Invitation.accept();">'
                + acceptCaption
                + '</div>';
        }

        popuptext += '<div style="clear: both;"></div></div>';

        var invitationdiv = document.getElementById("mibew-invitation");
        if (invitationdiv) {
                invitationdiv.innerHTML = popuptext;
        }
    }

    /**
     * Display invitation popup
     */
    Mibew.Invitation.show = function() {
        var invitationPopup = document.getElementById('mibew-invitation-popup');
        if (invitationPopup) {
            Mibew.Invitation.trigger('show');
            invitationPopup.style.display = 'block';
        }
    }

    /**
     * Hide invitation popup and remove it from DOM
     */
    Mibew.Invitation.hide = function() {
        var invitationPopup = document.getElementById('mibew-invitation-popup');
        if (invitationPopup) {
            Mibew.Invitation.trigger('hide');
            invitationPopup.parentNode.removeChild(invitationPopup);
        }
    }

    /**
     * Accept invitation and open chat window
     */
    Mibew.Invitation.accept = function() {
        if (document.getElementById('mibew-agent-button')) {
            Mibew.Invitation.trigger('accept');
            document.getElementById('mibew-agent-button').onclick();
            Mibew.Invitation.hide();
        }
    }

    /**
     * Visitor rejects invitation
     */
    Mibew.Invitation.reject = function() {
        Mibew.Invitation.trigger('reject');
        Mibew.Objects.widget.sendToServer({'invitation_rejected': 1});
        Mibew.Invitation.hide();
    }

    /**
     * Contains callbacks for invitation events.
     *
     * @private
     * @type Object
     */
    var invitaionEventsCallbacks = {};

    /**
     * Attaches event handler to invitation events.
     *
     * @param {String} event Event name. Can be one of "show", "hide", "accept",
     * "reject".
     * @param {Function} callback A function that should be called when event is
     * triggered.
     */
    Mibew.Invitation.on = function(event, callback) {
        if ((typeof event !== 'string') || (typeof callback !== 'function')) {
            // Event name must be a string and callback must be a function
            return;
        }

        if (!invitaionEventsCallbacks.hasOwnProperty(event)) {
            invitaionEventsCallbacks[event] = [];
        }

        invitaionEventsCallbacks[event].push(callback);
    }

    /**
     * Triggers invitation event.
     *
     * @param {String} event Event name to trigger.
     * @param {*} data Any data that should be passed to event handler
     */
    Mibew.Invitation.trigger = function(event, data) {
        // Set default value for data argument if it is not passed in.
        if (typeof data === 'undefined') {
            data = {};
        }

        if (!invitaionEventsCallbacks.hasOwnProperty(event)) {
            // There is no callback for the event. So there is no reasons to
            // continue.
            return;
        }

        // Run callbacks one by one
        var callbacks = invitaionEventsCallbacks[event];
        for(var i = 0, length = callbacks.length; i < length; i++) {
            callbacks[i](data);
        }
    }

    /**
     * @namespace Holds functions that can be called by the Core
     */
    Mibew.APIFunctions = {};

    /**
     * Update user id. API function
     * @param {Object} response Data object from server
     */
    Mibew.APIFunctions.updateUserId = function(response) {
        Mibew.Utils.createCookie(
            Mibew.Objects.widget.visitorCookieName,
            response.user.id
        );
    }

    /**
     * Show invitation popup
     * @param {Object} data Response data passed from server
     */
    Mibew.APIFunctions.invitationCreate = function(data) {
        Mibew.Invitation.create(data.invitation);
    }

    /**
     * Close invitation popup
     */
    Mibew.APIFunctions.invitationClose = function() {
        Mibew.Invitation.hide();
    }

})(Mibew);
