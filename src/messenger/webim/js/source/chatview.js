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