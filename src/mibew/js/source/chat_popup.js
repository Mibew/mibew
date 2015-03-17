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

(function(Mibew) {
    if (Mibew.ChatPopup) {
        // It seems that this file was already loaded. We do not need to do the
        // job twice.
        return;
    }

    /**
     * @namespace Holds all functionality related with chat popups
     */
    Mibew.ChatPopup = {};

    /**
     * @namespace Holds objects instances
     */
    Mibew.Objects = Mibew.Objects || {};

    /**
     * @namespace Holds all popups instances.
     */
    Mibew.Objects.ChatPopups = {};

    /**
     * @namespace Holds utility functions
     */
    Mibew.Utils = {};

    /**
     * Create a cookie for the second level domain with path equals to '/'.
     *
     * @param {String} name Cookie name
     * @param {String} value Cookie value
     * @param {Date} expires Indicates when the cookie expires. If the value is
     * omitted a session cookie will be created.
     */
    Mibew.Utils.createCookie = function(name, value, expires) {
        var domain = /([^\.]+\.[^\.]+)$/.exec(document.location.hostname);
        document.cookie = "" + name + "=" + value + "; "
            + "path=/; "
            + (domain ? ("domain=" + domain[1] + "; ") : '')
            + (expires ?  ('expires=' + expires.toUTCString() + '; ') : '');
    }

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
    }

    /**
     * Deletes cookie.
     *
     * @param {String} name Name of the cookie that should be deleted.
     */
    Mibew.Utils.deleteCookie = function(name) {
        Mibew.Utils.createCookie(name, '', (new Date(0)));
    }

    /**
     * Sets correct prototypes chain.
     *
     * This function is based on the logic used in Object.create method.
     * Unfortunately not all target browsers support this function thus it
     * should be implemented here.
     *
     * Warning: this methods completely rewrites prototype of "ctor" argument.
     *
     * @type {Function}
     * @param {Function} ctor An object constructor which prototype should be
     * updated.
     * @param {Function} superCtor An object constructor which prototype should
     * be used.
     */
    Mibew.Utils.inherits = (function() {
        // Tmp function is defined in closure because in such case only one
        // temporary function will be kept in memory regardless of inherits
        // function calls number.
        var Tmp = function() {};

        return function(ctor, superCtor) {
            Tmp.prototype = superCtor.prototype;
            ctor.prototype = new Tmp();
            Tmp.prototype = null;
            ctor.prototype.constructor = ctor;
        }
    })();

    /**
     * Attaches an event listener to the target object's event.
     *
     * This method uses native "addEventListener" in modern browsers and a
     * workaround for IE < 9.
     *
     * @param {Object} target The object which fires the event.
     * @param {String} eventName Name of the event.
     * @param {Function} listener The function that should be triggered.
     */
    Mibew.Utils.addEventListener = function(target, eventName, listener) {
        if (target.addEventListener) {
            // A regular browser is used
            target.addEventListener(eventName, listener, false);
        } else {
            if (target.attachEvent) {
                // This is needed for IE < 9
                target.attachEvent(
                    'on' + eventName,
                    // The closure is used to use valid this reference in the
                    // listener.
                    function (event) {
                        listener.call(target, event);
                    });
            }
        }
    };

    /**
     * Loads CSS file and attach it to DOM.
     *
     * @param {String} url URL of the CSS that should be loaded.
     * @param {String} [id] ID of the DOM element that will be created. Can be
     * omitted.
     * @returns {Element} Appended DOM item.
     */
    Mibew.Utils.loadStyleSheet = function(url, id) {
        var styleSheet = document.createElement('link');
        styleSheet.setAttribute('rel', 'stylesheet');
        styleSheet.setAttribute('type', 'text/css');
        styleSheet.setAttribute('href', url);
        if (id) {
            styleSheet.setAttribute('id', id);
        }
        document.getElementsByTagName('head')[0].appendChild(styleSheet);

        return styleSheet;
    }

    /**
     * Loads JavaScript file and attach it to DOM.
     *
     * @param {String} url URL of the JavaScript file that should be loaded.
     * @param {String} [id] ID of the DOM element that will be created. Can be
     * omitted.
     * @returns {Element} Appended DOM item.
     */
    Mibew.Utils.loadScript = function(url, id) {
        var script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        script.setAttribute('src', url);
        if (id) {
            script.setAttribute('id', id);
        }
        document.getElementsByTagName('head')[0].appendChild(script);

        return script;
    }

    /**
     * Initialize a proper chat popup.
     *
     * This is a helper function which choose which popup (iframe or window)
     * should be created, create it and store into Mibew.Objects.ChatPopups
     * hash.
     *
     * @param {Object} options List of popup options.
     */
    Mibew.ChatPopup.init = function(options) {
        var canUseIFrame = (window.postMessage && options.preferIFrame),
            Popup = canUseIFrame ? Mibew.ChatPopup.IFrame : Mibew.ChatPopup.Window;

        Mibew.Objects.ChatPopups[options.id] = new Popup(options);
    }

    /**
     * A constructor for base (abstract) popup object.
     *
     * @constructor
     * @param {Object} options A list of popup options.
     */
    var BasePopup = function(options) {
        /**
         * Unique ID of the popup.
         * @type {String}
         */
        this.id = options.id;

        /**
         * Chat initialization URL.
         * @type {String}
         */
        this.url = options.url;

        /**
         * Width of the popup in pixels.
         * @type {Number}
         */
        this.width = options.width;

        /**
         * Height of the popup in pixels.
         * @type {Number}
         */
        this.height = options.height;

        /**
         * Indicats if the popup should be resizable.
         *
         * It can be appliedonly for window popup.
         *
         * @type {Boolean}
         */
        this.resizable = options.resizable || false;

        /**
         * Contains URL of JavaScript file that loads css file for IFrame popup.
         *
         * @type {String}
         */
        this.styleLoader = options.styleLoader;

        /**
         * Indicates if special actions should be done to fix problems with
         * mod_security.
         * @type {Boolean}
         */
        this.modSecurity = options.modSecurity || false;
    }

    /**
     * Builds an URL that initializes a chat.
     *
     * @returns {String} Chat URL.
     */
    BasePopup.prototype.buildChatUrl = function() {
        var href = document.location.href,
            referrer = document.referrer;

        if (this.modSecurity) {
            href = href.replace('http://','').replace('https://','');
            referrer = referrer.replace('http://','').replace('https://','');
        }

        return this.url
            + ((this.url.indexOf('?') === -1) ? '?' : '&') + 'url=' + encodeURIComponent(href)
            + '&referrer=' + encodeURIComponent(referrer);
    }

    /**
     * Constructs IFrame popup.
     *
     * @constructor
     * @extends BasePopup
     * @param {Object} options List of popup options.
     */
    Mibew.ChatPopup.IFrame = function(options) {
        // Call parent constructor.
        BasePopup.call(this, options);

        /**
         * Popup iframe DOM Element.
         * @type {Node}
         */
        this.iframe = null;

        /**
         * Indicates if the popup is opened.
         * @type {Boolean}
         */
        this.isOpened = false;

        // Load default styles. These styles hide the popup while real styles
        // are loading.
        this.attachDefaultStyles();
        // Load extra style sheets.
        Mibew.Utils.loadScript(this.styleLoader);

        // Check if the popup should be reopened.
        var openedChatUrl = Mibew.Utils.readCookie('mibew-chat-frame-' + this.id);
        if (openedChatUrl) {
            // The chat was not closed so the popup should be reopened when a
            // new page is visited.
            this.open(openedChatUrl);
        }
    }

    // Set correct prototype chain for IFrame popup.
    Mibew.Utils.inherits(Mibew.ChatPopup.IFrame, BasePopup);

    /**
     * Attaches default styles to the DOM.
     *
     * This function do its job only once no matter how many times it is called.
     *
     * @type {Function}
     */
    Mibew.ChatPopup.IFrame.prototype.attachDefaultStyles = (function() {
        var executed = false;

        return function() {
            if (executed) {
                // The function was already called. Just do nothing.
                return;
            }

            executed = true;

            var style = document.createElement('style'),
                // These rules hides the popup while real styles are loading.
                css = '.mibew-chat-frame {height: 0px; width: 0px;}';

            style.setAttribute('type', 'text/css');
            if (style.styleSheet){
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            document.getElementsByTagName('head')[0].appendChild(style);
        };
    })();

    /**
     * Opens the popup.
     *
     * @param {String} [url] The URL that should be opened in the popup. If the
     * value is omitted, the chat initialization URL will be loaded.
     */
    Mibew.ChatPopup.IFrame.prototype.open = function(url) {
        if (this.isOpened) {
            // Do not open the popup twice.
            return;
        }

        if (!this.iframe) {
            // Create new iframe.
            // There is a bug in IE <= 7 that make "name" attribute unchangeble
            // for elements that already exist. Thus a temporary div is used
            // here as a workaround.
            var tmpDiv = document.createElement('div');
            tmpDiv.innerHTML = '<iframe name="mibewChat' + this.id + '"></iframe>';

            this.iframe = tmpDiv.getElementsByTagName('iframe')[0];
            this.iframe.setAttribute('id', 'mibew-chat-frame-' + this.id);
            this.iframe.className = 'mibew-chat-frame';
            this.iframe.setAttribute('frameBorder', 0);
            this.iframe.style.display = 'none';
            document.getElementsByTagName('body')[0].appendChild(this.iframe);
        }

        this.iframe.style.display = 'block';
        this.iframe.src = url || this.buildChatUrl();
        this.isOpened = true;
    }

    /**
     * Closes the popup.
     */
    Mibew.ChatPopup.IFrame.prototype.close = function() {
        if (!this.isOpened) {
            // A popup that was not opened thus it cannot be closed.
            return;
        }

        this.iframe.style.display = 'none';
        this.iframe.src = '';
        this.isOpened = false;
        Mibew.Utils.deleteCookie('mibew-chat-frame-' + this.id);
    }

    /**
     * Constructs Window popup.
     *
     * @constructor
     * @extends BasePopup
     * @param {Object} options List of popup options.
     */
    Mibew.ChatPopup.Window = function(options) {
        BasePopup.call(this, options);

        this.window = null;
    }

    // Set correct prototype chain for Window popup.
    Mibew.Utils.inherits(Mibew.ChatPopup.Window, BasePopup);

    /**
     * Opens the popup.
     *
     * @param {String} [url] The URL that should be opened in the popup. If the
     * value is omitted, the chat initialization URL will be loaded.
     */
    Mibew.ChatPopup.Window.prototype.open = function(url) {
        this.window = window.open(
            url || this.buildChatUrl(),
            'mibewChat' + this.id,
            this.getWindowParams()
        );
        this.window.focus();
        this.window.opener = window;
    }

    /**
     * Closes the popup.
     */
    Mibew.ChatPopup.Window.prototype.close = function() {
        if (!this.window) {
            // There is nothing to close.
            return;
        }

        this.window.close();
        this.window = null;
    }

    /**
     * Builds window params string.
     *
     * Generated params string can be used in window.open method as the third
     * argument.
     *
     * @protected
     * @returns {String}
     */
    Mibew.ChatPopup.Window.prototype.getWindowParams = function() {
        return [
            'toolbar=0',
            'scrollbars=0',
            'location=0',
            'status=1',
            'menubar=0',
            'width=' + this.width.toString(),
            'height=' + this.height.toString(),
            'resizable=' + (this.resizable ? '1' : '0')
        ].join(',');
    }

    // Attach a listener to window's "message" event to get the url of the chat
    // which is opened in iframe popup.
    Mibew.Utils.addEventListener(window, 'message', function(event) {
        var matches = /^mibew-chat-started\:mibewChat([0-9A-Za-z]+)\:(.*)$/.exec(event.data);

        if (matches) {
            Mibew.Utils.createCookie('mibew-chat-frame-' + matches[1], matches[2]);
        }
    });

    // Attach a listener to window's "message" event to close the iframe when
    // the chat is closed.
    Mibew.Utils.addEventListener(window, 'message', function(event) {
        var popups = Mibew.Objects.ChatPopups,
            matches = /^mibew-chat-closed\:mibewChat([0-9A-Za-z]+)$/.exec(event.data);

        if (matches && popups[matches[1]]) {
            popups[matches[1]].close();
        }
    });

})(Mibew);
