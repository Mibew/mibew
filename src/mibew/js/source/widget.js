/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * @namespace Holds all Mibew functionality
 */
var Mibew = {};

(function(Mibew){

    /**
     * @namespace Holds objects instances
     */
    Mibew.Objects = {};

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
         * List of dependences between handlers and scripts
         * @type Object
         * @private
         */
        this.handlersDependences = {};

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
        var styleSheet = document.createElement('link');
        styleSheet.setAttribute('rel', 'stylesheet');
        styleSheet.setAttribute('type', 'text/css');
        styleSheet.setAttribute('href', options.inviteStyle);
        document.getElementsByTagName('head')[0].appendChild(styleSheet);
    };

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

        this.doLoadScript(
            this.requestURL
                + '?' + this.getQuery(),
            'responseScript'
        );

        // Clean up request data
        this.dataToSend = {};
    };

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
    };

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
    };

    /**
     * Parse server response. Called as JSONP callback function
     * @param {Object} response Data got from server
     */
    Mibew.Widget.prototype.onResponse = function(response) {
        // Create some shortcuts
        var load = response.load;
        var handlers = response.handlers;
        var data = response.data;
        var dependences = response.dependences;
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

        // Update list of dependences
        for(var handler in dependences){
            if (! dependences.hasOwnProperty(handler)) {
                continue;
            }
            // Check if dependences for this handler already stored
            if (! (handler in this.handlersDependences)) {
                this.handlersDependences[handler] = dependences[handler];
            }
        }

        // Process all recieved handlers. Run handler if all dependences loaded
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
    };

    /**
     * Remove dynamically loaded request script from DOM
     * @private
     */
    Mibew.Widget.prototype.cleanUpAfterRequest = function() {
        document.getElementsByTagName('head')[0]
            .removeChild(document.getElementById('responseScript'));
    };

    /**
     * Load script and add handler for script onLoad event
     * @param {String} id Identifier of the script to load
     * @private
     */
    Mibew.Widget.prototype.loadScript = function(id) {
        // Store context
        var context = this;
        // Load script by adding script tag to DOM
        var script = this.doLoadScript(this.requestedScripts[id].url, id);

        // Check if script loaded
        script.onload = function(){
            context.scriptReady(id);
        };
        // Do it in crossbrowser way
        script.onreadystatechange = function(){
            if (this.readyState == 'complete' || this.readyState == 'loaded') {
                context.scriptReady(id);
            }
        }
    };

    /**
     * Dynamically add script tag to DOM
     * @param {String} url URL of the script to load
     * @param {String} id Identifier of the script to load
     * @private
     */
    Mibew.Widget.prototype.doLoadScript = function(url, id) {
        var script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        script.setAttribute('src', url);
        script.setAttribute('id', id);
        document.getElementsByTagName('head')[0].appendChild(script);
        return script;
    };

    /**
     * Event listener for script onLoad event. Run handlers which have no
     * unload dependences.
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
    };

    /**
     * Check if handler can be run
     */
    Mibew.Widget.prototype.canRunHandler = function(handlerName) {
        var dependences = this.handlersDependences[handlerName];
        // Check for dependencess
        for(var i = 0; i < dependences.length; i++){
            if(this.requestedScripts[dependences[i]].status != 'ready'){
                return false;
            }
        }
        return true;
    };

    /**
     * Helper function which create new widget object, store it into
     * Mibew.Objects.widget and run automatic requests.
     */
    Mibew.Widget.init = function(options) {
        Mibew.Objects.widget = new Mibew.Widget(options);
        Mibew.Objects.widget.makeRequest();
    };


    /**
     * @namespace Holds utility functions
     */
    Mibew.Utils = {};

    /**
     * Create session cookie for top level domain with path equals to '/'.
     *
     * @param {String} name Cookie name
     * @param {String} value Cookie value
     */
    Mibew.Utils.createCookie = function(name, value) {
        var domainParts = /([^\.]+\.[^\.]+)$/.exec(document.location.hostname);
        var domain = domainParts[1];
        document.cookie = "" + name + "=" + value + "; "
            + "path=/; "
            + (domain ? ("domain=" + domain + ";") : '');
    };

    /**
     * Try to read cookie.
     *
     * @param {String} name Cookie name
     * @returns {String|Boolean} Cookie value or boolean false if cookie with
     * specified name does not exist
     */
    Mibew.Utils.readCookie = function(name) {
        var cookies = document.cookie.split('; ');
        var nameForSearch = name + '=';
        var value = false;
        for (var i = 0; i < cookies.length; i++) {
            if (cookies[i].indexOf(nameForSearch) != -1) {
                value = cookies[i].substr(nameForSearch.length);
                break;
            }
        }
        return value;
    };


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

        var popuptext = '<div id="mibewinvitationpopup" style="display: none;">';
        popuptext += '<div id="mibewinvitationclose">'
            + '<a href="javascript:void(0);" onclick="Mibew.Invitation.reject();">'
            + '&times;</a></div>';

        // Add operator name
        if (operatorName) {
            popuptext += '<h1 onclick="Mibew.Invitation.accept();">'
                + operatorName
                + '</h1>';
        }

        // Add operator avatar
        if (avatarUrl) {
            popuptext += '<img id="mibewinvitationavatar" src="' + avatarUrl
                + '" title="' + operatorName
                + '" alt="' + operatorName
                + '" onclick="Mibew.Invitation.accept();" />';
        }

        // Broadcast message from the thread related with invitation into iframe
        if (threadUrl) {
            popuptext += '<iframe id="mibewinvitationframe" '
                + 'src="' + threadUrl + '" onload="Mibew.Invitation.show();" '
                + 'frameBorder="0"></iframe>';
        }

        // Add accept button if acceptCaption set
        if (acceptCaption) {
            popuptext += '<div id="mibewinvitationaccept"'
                + ' onclick="Mibew.Invitation.accept();">'
                + acceptCaption
                + '</div>';
        }

        popuptext += '<div style="clear: both;"></div></div>';

        var invitationdiv = document.getElementById("mibewinvitation");
        if (invitationdiv) {
                invitationdiv.innerHTML = popuptext;
        }
    };

    /**
     * Display invitation popup
     */
    Mibew.Invitation.show = function() {
        var invitationPopup = document.getElementById('mibewinvitationpopup');
        if (invitationPopup) {
            invitationPopup.style.display = 'block';
        }
    };

    /**
     * Hide invitation popup and remove it from DOM
     */
    Mibew.Invitation.hide = function() {
        var invitationPopup = document.getElementById('mibewinvitationpopup');
        if (invitationPopup) {
            invitationPopup.parentNode.removeChild(invitationPopup);
        }
    };

    /**
     * Accept invitation and open chat window
     */
    Mibew.Invitation.accept = function() {
        if (document.getElementById('mibewAgentButton')) {
            document.getElementById('mibewAgentButton').onclick();
            Mibew.Invitation.hide();
        }
    };

    /**
     * Visitor rejects invitation
     */
    Mibew.Invitation.reject = function() {
        Mibew.Objects.widget.sendToServer({'invitation_rejected': 1});
        Mibew.Invitation.hide();
    };


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
    };

    /**
     * Show invitation popup
     * @param {Object} data Response data passed from server
     */
    Mibew.APIFunctions.invitationCreate = function(data) {
        Mibew.Invitation.create(data.invitation);
    };

    /**
     * Close invitation popup
     */
    Mibew.APIFunctions.invitationClose = function() {
        Mibew.Invitation.hide();
    }

})(Mibew);
