/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

var FrameUtils = {

    options: {},

    getDocument: function(frm) {
        if (frm.contentDocument) {
            return frm.contentDocument;
        } else if (frm.contentWindow) {
            return frm.contentWindow.document;
        } else if (frm.document) {
            return frm.document;
        } else {
            return null;
        }
    },

    initFrame: function(frm) {
        var doc = this.getDocument(frm);
        doc.open();
        doc.write("<html><head>");
        if (this.options.cssfile) {
            doc.write("<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\""+this.options.cssfile+"\">");
        }
        doc.write("</head><body bgcolor='#FFFFFF' text='#000000' link='#C28400' vlink='#C28400' alink='#C28400'>");
        doc.write("<table width='100%' cellspacing='0' cellpadding='0' border='0'><tr><td valign='top' class='message' id='content'></td></tr></table><a id='bottom' name='bottom'></a>");
        doc.write("</body></html>");
        doc.close();
        frm.onload = function() {
            if (frm.myHtml) {
                FrameUtils.getDocument(frm).getElementById('content').innerHTML += frm.myHtml;
                FrameUtils.scrollDown(frm);
            }
        };
    },

    insertIntoFrame: function(frm, htmlcontent) {
        var vcontent = this.getDocument(frm).getElementById('content');
        if (vcontent == null) {
            if (!frm.myHtml) {
                frm.myHtml = "";
            }
            frm.myHtml += htmlcontent;
        } else {
            vcontent.innerHTML += htmlcontent;
        }
    },

    scrollDown: function(frm) {
        var vbottom = this.getDocument(frm).getElementById('bottom');
        if (myAgent == 'opera') {
            try {
                frm.contentWindow.scrollTo(0,this.getDocument(frm).getElementById('content').clientHeight);
            } catch(e) {}
        }
        if (vbottom) {
            vbottom.scrollIntoView(false);
        }
    }
};

ChatView = Class.create();
ChatView.prototype = {
    /**
     * Status timeout identifier
     * @type Number
     * @private
     */
    statusTimeout: null,

    /**
     * Contains localized strings. Properties names are language key and
     * properties values are localized strings
     * @type Object
     */
    localizedStrings: {},

    /**
     * Contains predefined answers configurable from administrative interface
     * @type Array
     */
    predefinedAnswers: [],

    /**
     * Messages container DOM element
     * @type Object
     */
    messageContainer: null,

    /**
     * Create an instance of ChatView
     * @constructor
     */
    initialize: function(localizedStrings, predefinedAnswers) {
        this.localizedStrings = localizedStrings || {};
        this.predefinedAnswers = predefinedAnswers || [];

        this.messageContainer = (myRealAgent == 'safari')
            ? self.frames[0]
            : $("chatwnd");
        FrameUtils.initFrame(this.messageContainer);
    },

    /**
     * Get localized string by language key
     * @param {String} key Language key
     * @returns {Boolean|String} Returns boolean FALSE if string with specified
     * key is undefined and localized string otherwise
     */
    getLocaleString: function(key) {
        if (typeof this.localizedStrings[key] == 'undefined') {
            return false;
        }
        return this.localizedStrings[key];
    },

    /**
     * Enables or disables input field
     * @param {Boolean} val Use boolean true for enable input and false
     * otherwise
     */
    enableInput: function(val) {
        var message = $('msgwnd');
        if (message) {
            message.disabled = !val;
        }
    },

    /**
     * Clear message input element and set focus to it
     */
    clearInput: function() {
        var message = $('msgwnd');
        if(message) {
            message.value = '';
            message.focus();
        }
    },

    /**
     * Displays status div and sets the status string into it
     * @param {String} k Status string
     */
    showStatusDiv: function(k) {
        if ($("engineinfo")) {
            $("engineinfo").style.display = 'inline';
            $("engineinfo").innerHTML = k;
        }
    },

    /**
     * Sets the status
     * @param {String} k Status string
     */
    setStatus: function(k) {
        if (this.statusTimeout) {
            clearTimeout(this.statusTimeout);
        }
        this.showStatusDiv(k);
        this.statusTimeout = setTimeout(this.clearStatus.bind(this), 4000);
    },

    /**
     * Hide the status string
     */
    clearStatus: function() {
        $("engineinfo").style.display='none';
    },

    /**
     * Displays typing status
     * @param {Boolean} istyping Indicates the other side of conversation is
     * typing a message or not
     */
    showTyping: function(istyping) {
        if( $("typingdiv") ) {
            $("typingdiv").style.display = istyping ? 'inline' : 'none';
        }
    },

    /**
     * Updates operator's avatar
     * @param {String} root Base path
     * @param {String} imageLink New avatar URL
     */
    updateAvatar: function(root, imageLink) {
        var avatar = "";
        if (imageLink != "") {
            avatar = '<img src="'+root+'/images/free.gif" width="7" height="1" border="0" alt="" />' +
                '<img src="'+imageLink+'" border="0" alt=""/>';
        }
        $("avatarwnd").innerHTML = avatar;
    },

    /**
     * Display all messages at the message window
     * @param {Array} messages Messages array
     */
    displayMessages: function(messages) {
        // Output messages
        for (var i = 0; i < messages.length; i++) {
            this.outputMessage(messages[i]);
        }
        // There are some new messages
        if (messages.length > 0) {
            FrameUtils.scrollDown(this.messageContainer);
        }
    },

    /**
     * Add message to the message window
     * @param {String} message HTML message to insert
     * @private
     */
    outputMessage: function(message) {
        FrameUtils.insertIntoFrame(this.messageContainer, message);
    },

    /**
     * Show new user name input
     */
    showNameField: function() {
        $('changename1').style.display='inline';
        $('changename2').style.display='none';
    },

    /**
     * Hide new user name input
     */
    hideNameField: function() {
        $('changename1').style.display='none';
        $('changename2').style.display='inline';
    },

    /**
     * Update user name in chat window
     * @param {String} name New user's name
     */
    updateUserName: function(name) {
        $('unamelink').innerHTML = htmlescape(name);
    },

    /**
     * Change sound button state.
     *
     * @param {Boolean} enable TRUE if sound enabled and FALSE otherwise
     */
    changeSoundButtonState: function(enable) {
        var tsound = $('soundimg');
        if (enable) {
            tsound.className = "tplimage isound";
        } else {
            tsound.className = "tplimage inosound";
        }
        var messagePane = $('msgwnd');
        if(messagePane) {
            messagePane.focus();
        }
    },

    /**
     * Add predefined answer to message input element and set focus to it.
     *
     * @param {Number} answerIndex Index of predefined answer
     */
    displayPredefinedAnswer: function(answerIndex) {
        var message = $('msgwnd');
        message.value = this.predefinedAnswers[answerIndex];
        message.focus();
    },

    /**
     * Set selectedIndex property of the select box DOM element passed as
     * argument to zero.
     * @param {Object} elem Select box DOM element
     */
    resetSelectedIndex: function(elem) {
        elem.selectedIndex = 0;
    }
}

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

Behaviour.register({
    '#postmessage a' : function(el) {
        el.onclick = function() {
            var message = $('msgwnd');
            if (message) {
                chatController.postMessage(message.value);
            }
        };
    },

    'select#predefined' : function(el) {
        el.onchange = function() {
            chatController.selectPredefinedAnswer(this);
        };
    },

    'div#changename2 a' : function(el) {
        el.onclick = function() {
            chatController.showNameField();
            return false;
        };
    },

    'div#changename1 a' : function(el) {
        el.onclick = function() {
            chatController.applyName();
            return false;
        };
    },

    'div#changename1 input#uname' : function(el) {
        el.onkeydown = function(e) {
            var ev = e || event;
            if( ev.keyCode == 13 ) {
                chatController.applyName();
            }
        };
    },

    'a#refresh' : function(el) {
        el.onclick = function() {
            chatController.refresh();
        };
    },

    'a#togglesound' : function(el) {
        el.onclick = function() {
            chatController.toggleSound();
        };
    },

    'a.closethread' : function(el) {
        el.onclick = function() {
            chatController.closeThread();
        };
    }
});

var pluginManager = new PluginManager();
var chatController;

EventHelper.register(window, 'onload', function(){
    FrameUtils.options.cssfile = chatParams.cssfile;
    var chatServer = new ChatServer(chatParams.serverParams);
    var thread = new Thread(chatParams.threadParams);
    chatParams.initPlugins(pluginManager, thread, chatServer);
    var chatView = new ChatView(
        chatParams.localizedStrings,
        chatParams.predefinedAnswers || []
    );
    chatController = new ChatController(
        chatServer,
        chatView,
        thread,
        {ignorectrl: -1}.extend(chatParams.controllerParams || {})
    );
});