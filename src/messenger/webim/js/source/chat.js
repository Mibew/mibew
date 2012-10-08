/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

var FrameUtils = {
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
	doc.write("<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\""+Chat.cssfile+"\">");
	doc.write("</head><body bgcolor='#FFFFFF' text='#000000' link='#C28400' vlink='#C28400' alink='#C28400'>");
	doc.write("<table width='100%' cellspacing='0' cellpadding='0' border='0'><tr><td valign='top' class='message' id='content'></td></tr></table><a id='bottom' name='bottom'></a>");
	doc.write("</body></html>");
	doc.close();
	frm.onload = function() {
		if( frm.myHtml ) {
			FrameUtils.getDocument(frm).getElementById('content').innerHTML += frm.myHtml;
			FrameUtils.scrollDown(frm);
		}
	};
  },

  insertIntoFrame: function(frm, htmlcontent) {
	var vcontent = this.getDocument(frm).getElementById('content');
	if( vcontent == null ) {
		if( !frm.myHtml ) frm.myHtml = "";
		frm.myHtml += htmlcontent;
	} else {
		vcontent.innerHTML += htmlcontent;
	}
  },

  scrollDown: function(frm) {
	var vbottom = this.getDocument(frm).getElementById('bottom');
	if( myAgent == 'opera' ) {
	    try {
		 frm.contentWindow.scrollTo(0,this.getDocument(frm).getElementById('content').clientHeight);
	    } catch(e) {}
	}
	if( vbottom ) {
		vbottom.scrollIntoView(false);
	}
  }
};

ChatThreadUpdater = Class.create();
ChatThreadUpdater.prototype = {
  /**
   * Create an instance of ChatThreadUpdater
   * @constructor
   * @param {ChatServer} chatServer An instance of ChatServer class
   * @param {Thread} thread Thread object
   * @param {Object} options Additional configuration options
   * @todo Add error handlers to chatServer
   * @todo Think about code format
   */
  initialize: function(chatServer, thread, options) {
    /**
     * Additional options
     * @type Object
     * @private
     */
    this._options = options;

    /**
     * An instance of the Thread class
     * @type Thread
     */
    this.thread = thread;

    /**
     * An instance of the ChatServer class
     * @type ChatServer
     */
    this.chatServer = chatServer;

    /**
     * Indicates if user can post messages
     * @type Boolean
     */
    this.cansend = true;

    /**
     * Indicates if next message's sound must be skipped
     * @type Boolean
     */
    this.skipNextsound = true;

    /**
     * Indicates if message input area ihn focus
     * @type Boolean
     */
    this.focused = true;

    /**
     * Indicates the thread belong to this operator
     * @type Boolean
     */
    this.ownThread = this._options.message != null;

    FrameUtils.initFrame(this._options.container);
    if (this._options.message) {
        this._options.message.onkeydown = this.handleKeyDown.bind(this);
        this._options.message.onfocus = (function() {this.focused = true;}).bind(this);
        this._options.message.onblur = (function() {this.focused = false;}).bind(this);
    }

    // Add periodic functions
    this.chatServer.callFunctionsPeriodically(
        this.updateFunctionBuilder.bind(this),
        this.updateChatState.bind(this)
    );

    // Register functions
    this.chatServer.registerFunction(
        'updateMessages',
        this.updateMessages.bind(this)
    );
    this.chatServer.registerFunction(
        'setupAvatar',
        this.setupAvatar.bind(this)
    );

    this.chatServer.runUpdater();
  },

  /**
   * Exception handler. Updates status message
   */
  handleException: function(e) {
        this.setStatus("offline, reconnecting");
        this.enableInput(true);
  },

  /**
   * Timeout handler. Updates status message
   */
  handleTimeout: function() {
        this.setStatus("timeout, reconnecting");
        this.enableInput(true);
  },

  /**
   * Enables or disables input field
   * @param {Boolean} val Use boolean true for enable input and false otherwise
   */
  enableInput: function(val) {
      if( this._options.message ) {
          this._options.message.disabled = !val;
      }
  },

  /**
   * Load new messages by restarting thread updater.
   */
  refresh: function() {
    this.chatServer.restartUpdater();
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
      this.skipNextsound = true;
      // Disable input
      if(myRealAgent != 'opera') {
          this.enableInput(false);
      }
      // Post message
      this.chatServer.callFunctions(
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
            this.enableInput(true);
            this.cansend = true;
            this.skipNextsound = false;
            if(this._options.message) {
                this._options.message.value = '';
                this._options.message.focus();
            }
        }).bind(this),
        true
      );
  },

  /**
   * Change user name
   * @param {String} newname A new user name
   */
  changeName: function(newname) {
      this.skipNextsound = true;
      this.chatServer.callFunctions(
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
      if(this._options.localizedStrings.closeConfirmation){
          if(! confirm(this._options.localizedStrings.closeConfirmation)){
              return;
          }
      }
      // Send request
      this.chatServer.callFunctions(
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
   * Add message to the message window
   * @param {Object} _target Target DOM element
   * @param {String} message HTML message to insert
   */
  processMessage: function(_target, message) {
      FrameUtils.insertIntoFrame(_target, message);
  },

  /**
   * Displays typing status
   * @param {Boolean} istyping Indicates the other side of conversation is
   * typing a message or not
   */
  showTyping: function(istyping) {
  	if( $("typingdiv") ) {
		$("typingdiv").style.display=istyping ? 'inline' : 'none';
  	}
  },

  /**
   * Update operator's avatar
   * @param {Array} args Array of arguments passed from the core
   */
  setupAvatar: function(args) {
      if (this._options.avatar && this.thread.user) {
          this._options.avatar.innerHTML = args.imageLink != ""
              ? "<img src=\""+this._options.webimRoot+"/images/free.gif\" width=\"7\" height=\"1\" border=\"0\" alt=\"\" /><img src=\""
                    +args.imageLink+ "\" border=\"0\" alt=\"\"/>"
              : "";
	}
  },

  /**
   * Add new messages to chat window
   * @param {Object} args object of function arguments passed from the server
   * @todo Fix skipNextSound
   */
  updateMessages: function(args){
      // Update last message id
      if (args.lastId) {
          this.thread.lastid = args.lastId;
      }
      // Add messages
      for (var i = 0; i < args.messages.length; i++) {
          // TODO: Add template engine
          this.processMessage(this._options.container, args.messages[i]);
      }
      // Clear status string
      this.clearStatus();
      // There are some new messages
      if (args.messages.length > 0) {
          FrameUtils.scrollDown(this._options.container);
          if (!this.skipNextsound) {
              var tsound = $('soundimg');
              if (tsound == null || tsound.className.match(new RegExp("\\bisound\\b"))) {
                  playSound(this._options.webimRoot+'/sounds/new_message.wav');
              }
          }
          if (!this.focused) {
              window.focus();
          }
      }
      this.skipNextsound = false;
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
                  "typed": (this._options.message && this._options.message.value != ''),
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
          this.showTyping(args.typing);
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
	  return ((key==13 && (ctrlpressed || this._options.ignorectrl)) || (key==10));
  },

  /**
   * Key down handler
   *
   * @param {Object} k Event object
   */
  handleKeyDown: function(k) {
	if( k ){ctrl=k.ctrlKey;k=k.which;} else {k=event.keyCode;ctrl=event.ctrlKey;}
	if( this._options.message && this.isSendkey(ctrl, k) ) {
		var mmsg = this._options.message.value;
		if( this._options.ignorectrl ) {
			mmsg = mmsg.replace(/[\r\n]+$/,'');
		}
		this.postMessage( mmsg );
		return false;
	}
	return true;
  },

  /**
   * Update status message
   *
   * @param {Array} args Array of arguments. Must contain 'errorCode' and
   * 'errorMessage' keys
   * @param {String} descr Error description
   */
  handleError: function(args, descr) {
      if (args.errorCode) {
          this.setStatus(args.errorMessage);
      } else {
          this.setStatus('reconnecting');
      }
  },

  /**
   * Displays status div and sets the status string into it
   *
   * @param {String} k Status string
   */
  showStatusDiv: function(k) {
  	if( $("engineinfo") ) {
		$("engineinfo").style.display='inline';
		$("engineinfo").innerHTML = k;
  	}
  },

  /**
   * Sets the status
   *
   * @param {String} k Status string
   */
  setStatus: function(k) {
	if( this.statusTimeout )
		clearTimeout(this.statusTimeout);
	this.showStatusDiv(k);
	this.statusTimeout = setTimeout(this.clearStatus.bind(this), 4000);
  },

  /**
   * Hide the status string
   */
  clearStatus: function() {
	$("engineinfo").style.display='none';
  }
}

var Chat = {
  threadUpdater : {},

  applyName: function() {
	Chat.threadUpdater.changeName($('uname').value);
	$('changename1').style.display='none';
	$('changename2').style.display='inline';
	$('unamelink').innerHTML = htmlescape($('uname').value);
  },

  showNameField: function() {
	$('changename1').style.display='inline';
	$('changename2').style.display='none';
  }
};

Behaviour.register({
	'#postmessage a' : function(el) {
		el.onclick = function() {
			var message = $('msgwnd');
			if( message )
				Chat.threadUpdater.postMessage(message.value);
		};
	},
	'select#predefined' : function(el) {
		el.onchange = function() {
			var message = $('msgwnd');
			if(this.selectedIndex!=0) {
				message.value = Chat.predefinedAnswers[this.selectedIndex-1];
			}
			this.selectedIndex = 0;
			message.focus();
		};
	},
	'div#changename2 a' : function(el) {
		el.onclick = function() {
			Chat.showNameField();
			return false;
		};
	},
	'div#changename1 a' : function(el) {
		el.onclick = function() {
			Chat.applyName();
			return false;
		};
	},
	'div#changename1 input#uname' : function(el) {
		el.onkeydown = function(e) {
			var ev = e || event;
			if( ev.keyCode == 13 ) {
				Chat.applyName();
			}
		};
	},
	'a#refresh' : function(el) {
		el.onclick = function() {
                    Chat.threadUpdater.refresh();
		};
	},
	'a#togglesound' : function(el) {
		el.onclick = function() {
			var tsound = $('soundimg');
			if(!tsound) {
				return;
			}
			if(tsound.className.match(new RegExp("\\bisound\\b"))) {
				tsound.className = "tplimage inosound";
			} else {
				tsound.className = "tplimage isound";
			}
			var messagePane = $('msgwnd');
			if(messagePane)
				messagePane.focus();
		};
	},
	'a.closethread' : function(el) {
		el.onclick = function() {
			Chat.threadUpdater.closeThread();
		};
	}
});

var pluginManager = new PluginManager();

EventHelper.register(window, 'onload', function(){
  var chatServer = new ChatServer(chatParams.serverParams);
  var thread = new Thread(chatParams.threadParams);
  chatParams.initPlugins(pluginManager, thread, chatServer);
  Chat.cssfile = chatParams.cssfile;
  Chat.predefinedAnswers = chatParams.predefinedAnswers || [];
  Chat.localizedStrings = chatParams.localizedStrings;
  Chat.threadUpdater = new ChatThreadUpdater(
    chatServer,
    thread,
    {
        ignorectrl: -1,
        container: myRealAgent=='safari'?self.frames[0]:$("chatwnd"),
        avatar: $("avatarwnd"),
        message: $("msgwnd")
    }.extend(chatParams.threadUpdaterParams || {})
  );
});