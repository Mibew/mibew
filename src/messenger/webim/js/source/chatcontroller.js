/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

ChatController = Class.create();
ChatController.prototype = {
    /**
     * Additional options
     * @type Object
     * @private
     */
    options: {},

    /**
     * An instance of the Thread class
     * @type Thread
     */
    thread: null,

    /**
     * An instance of the ChatServer class
     * @type ChatServer
     */
    server: null,

    /**
     * An instance of the ChatView class
     * @type ChatView
     */
    view: null,

    /**
     * Indicates if user can post messages
     * @type Boolean
     */
    cansend: true,

    /**
     * Indicates if next message's sound must be skipped
     * @type Boolean
     */
    skipNextSound: true,

    /**
     * Indicates if message input area ihn focus
     * @type Boolean
     */
    focused: true,

    /**
     * Message input DOM element
     * @type Object
     */
    message: null,

    /**
     * Indicates the thread belong to this operator
     * @type Boolean
     */
    ownThread: null,

    /**
     * Create an instance of ChatController
     * @constructor
     * @param {ChatServer} chatServer An instance of ChatServer class
     * @param {Thread} thread Thread object
     * @param {ChatView} chatView A Chat view object
     * @param {Object} options Additional configuration options
     * @todo Add error handlers to chatServer
     * @todo Think about code format
     */
    initialize: function(chatServer, chatView, thread, options) {

        this.options = options;
        this.thread = thread;
        this.server = chatServer;
        this.view = chatView;

        this.message = $('msgwnd');

        this.ownThread = this.message != null;

        if (this.message) {
            this.message.onkeydown = this.handleKeyDown.bind(this);
            this.message.onfocus = (function() {this.focused = true;}).bind(this);
            this.message.onblur = (function() {this.focused = false;}).bind(this);
        }

        // Add periodic functions
        this.server.callFunctionsPeriodically(
            this.updateFunctionBuilder.bind(this),
            this.updateChatState.bind(this)
        );

        // Register functions
        this.server.registerFunction(
            'updateMessages',
            this.updateMessages.bind(this)
        );
        this.server.registerFunction(
            'setupAvatar',
            this.setupAvatar.bind(this)
        );

        this.server.runUpdater();
    },

    /**
     * Exception handler. Updates status message
     */
    handleException: function(e) {
        this.view.setStatus("offline, reconnecting");
        this.view.enableInput(true);
    },

    /**
     * Timeout handler. Updates status message
     */
    handleTimeout: function() {
        this.view.setStatus("timeout, reconnecting");
        this.view.enableInput(true);
    },

    /**
     * Load new messages by restarting thread updater.
     */
    refresh: function() {
        this.server.restartUpdater();
    },

    /**
     * Sends message to the chat server
     * @param {String} msg Message for send
     */
    postMessage: function(msg) {
        // Check if message can be sent
        if(msg == "" || !this.cansend) {
            return;
        }
        // Disable message sending
        this.cansend = false;
        // Disable next sound
        this.skipNextSound = true;
        // Disable input
        if(myRealAgent != 'opera') {
            this.view.enableInput(false);
        }
        // Post message
        this.server.callFunctions(
            [{
                "function": "post",
                "arguments": {
                    "references": {},
                    "return": {},
                    "message": msg,
                    "threadId": this.thread.threadid,
                    "token": this.thread.token,
                    "user": this.thread.user
                }
            }],
            (function(){
                this.view.enableInput(true);
                this.cansend = true;
                this.view.clearInput();
            }).bind(this),
            true
        );
    },

    /**
     * Change user name
     * @param {String} newname A new user name
     */
    changeName: function(newname) {
        this.skipNextSound = true;
        this.server.callFunctions(
            [{
                "function": "rename",
                "arguments": {
                    "references": {},
                    "return": {},
                    "threadId": this.thread.threadid,
                    "token": this.thread.token,
                    "name": newname
                }
            }],
            (function(args){
                if (args.errorCode) {
                    this.handleError(args, 'cannot rename');
                }
            }).bind(this),
            true
        );
    },

    /**
     * Send request for close chat to the core
     */
    closeThread: function() {
        // Show confirmation message if can
        if (this.view.getLocaleString('closeConfirmation')) {
            if (! confirm(this.view.getLocaleString('closeConfirmation'))) {
                return;
            }
        }
        // Send request
        this.server.callFunctions(
            [{
                "function": "close",
                "arguments": {
                    "references": {},
                    "return": {"closed": "closed"},
                    "threadId": this.thread.threadid,
                    "token": this.thread.token,
                    "lastId": this.thread.lastid,
                    "user": this.thread.user
                }
            }],
            this.onThreadClosed.bind(this),
            true
        );
    },

    /**
     * Callback function for close chat request.
     *
     * Close chat window if closing success or warn on fail
     */
    onThreadClosed: function(args) {
        if (args.closed) {
            window.close();
        } else {
            this.handleError(args, 'cannot close');
        }
    },

    /**
     * Update operator's avatar
     * @param {Array} args Array of arguments passed from the core
     */
    setupAvatar: function(args) {
        if ($("avatarwnd") && this.thread.user) {
            this.view.updateAvatar(this.options.webimRoot, args.imageLink);
        }
    },

    /**
    * Add new messages to chat window
    * @param {Object} args object of function arguments passed from the server
    */
    updateMessages: function(args){
        // Update last message id
        if (args.lastId) {
            this.thread.lastid = args.lastId;
        }
        // Add messages
        this.view.displayMessages(args.messages);
        // Clear status string
        this.view.clearStatus();
        // There are some new messages
        if (args.messages.length > 0) {
            if (!this.skipNextSound) {
                var tsound = $('soundimg');
                if (tsound == null || tsound.className.match(new RegExp("\\bisound\\b"))) {
                    playSound(this.options.webimRoot+'/sounds/new_message.wav');
                }
            }
            if (!this.focused) {
                window.focus();
            }
        }
        this.skipNextSound = false;
    },

    /**
    * Build update function to call at the core
    */
    updateFunctionBuilder: function() {
        return [
            {
                "function": "update",
                "arguments": {
                    "return": {'typing': 'typing', 'canPost': 'canPost'},
                    "references": {},
                    "threadId": this.thread.threadid,
                    "token": this.thread.token,
                    "lastId": this.thread.lastid,
                    "typed": (this.message && this.message.value != ''),
                    "user": this.thread.user
                }
            }
        ];
    },

    /**
     * Set current chat state message
     * @param {Array} args Array of arguments passed from the core
     */
    updateChatState: function(args) {
        if (args.errorCode) {
            // Something went wrong
            this.handleError(args, 'refresh failed');
            return;
        }
        // Update typing indicator
        if (typeof args.typing != 'undefined') {
            this.view.showTyping(args.typing);
        }

        // Check if user can post messages
        if (typeof args.canPost != 'undefined') {
            if ((args.canPost && !this.ownThread) || (this.ownThread && ! args.canPost)) {
                // Refresh the page
                window.location.href = window.location.href;
            }
        }
    },

    /**
     * Check if send key (Enter or Ctrl+Enter) pressed
     * @param {Boolean} ctrlpressed Indicates ctrl key is pressed or not
     * @param {Number} key Key code
     */
    isSendkey: function(ctrlpressed, key) {
        return ((key==13 && (ctrlpressed || this.options.ignorectrl)) || (key==10));
    },

    /**
     * Key down handler
     *
     * @param {Object} k Event object
     */
    handleKeyDown: function(k) {
        if (k) {
            ctrl=k.ctrlKey;
            k=k.which;
        } else {
            k=event.keyCode;
            ctrl=event.ctrlKey;
        }
        if (this.message && this.isSendkey(ctrl, k)) {
            var mmsg = this.message.value;
            if (this.options.ignorectrl) {
                mmsg = mmsg.replace(/[\r\n]+$/,'');
            }
            this.postMessage(mmsg);
            return false;
        }
        return true;
    },

    /**
     * Update status message
     *
     * @param {Object} args Array of arguments. Must contain 'errorCode' and
     * 'errorMessage' keys
     * @param {String} descr Error description
     */
    handleError: function(args, descr) {
        if (args.errorCode) {
            this.view.setStatus(args.errorMessage);
        } else {
            this.view.setStatus('reconnecting');
        }
    },

    /**
     * Apply new user's name
     */
    applyName: function() {
        this.changeName($('uname').value);
        this.view.hideNameField();
        this.view.updateUserName($('uname').value);
    },

    /**
     * Displays field for new user's name
     */
    showNameField: function() {
        this.view.showNameField();
    },

    /**
     * Predefined Answer select event handler.
     *
     * Add selected predefined answer to message input and reset predefined
     * answers select box.
     *
     * @param {Object} answerSelect Predefined answer DOM element
     */
    selectPredefinedAnswer: function(answerSelect) {
        var index = answerSelect.selectedIndex;
        if(index != 0) {
            this.view.displayPredefinedAnswer(index-1);
            this.view.resetSelect(answerSelect);
        }
    },

    /**
     * Toggle sound button
     */
    toggleSound: function() {
        var tsound = $('soundimg');
        if(!tsound) {
            return;
        }
        if(tsound.className.match(new RegExp("\\bisound\\b"))) {
            this.view.changeSoundButtonState(false);
        } else {
            this.view.changeSoundButtonState(true);
        }
    }
}