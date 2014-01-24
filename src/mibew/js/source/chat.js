/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2014 Mibew Messenger Community
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
	doc.write("</head><body bgcolor=\"#FFFFFF\" text=#000000\" link=\"#C28400\" vlink=\"#C28400\" alink=\"#C28400\">");
	doc.write("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\" class=\"message\" id=\"content\"></td></tr></table><a id=\"bottom\" name=\"bottom\"></a>");
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

Ajax.ChatThreadUpdater = Class.create();
Class.inherit( Ajax.ChatThreadUpdater, Ajax.Base, {

  initialize: function(_options) {
    this.setOptions(_options);
    this._options.onComplete = this.requestComplete.bind(this);
    this._options.onException = this.handleException.bind(this);
    this._options.onTimeout = this.handleTimeout.bind(this);
    this._options.timeout = 5000;
    this.updater = {};
    this.frequency = (this._options.frequency || 2);
    this.lastupdate = 0;
    this.cansend = true;
    this.skipNextsound = true;
    this.focused = true;
    this.ownThread = this._options.message != null;
	FrameUtils.initFrame(this._options.container);
    if( this._options.message ) {
		this._options.message.onkeydown = this.handleKeyDown.bind(this);
		this._options.message.onfocus = (function() { this.focused = true; }).bind(this);
		this._options.message.onblur = (function() { this.focused = false; }).bind(this)
	}
    this.update();
  },

  handleException: function(_request, ex) {
	this.setStatus("offline, reconnecting");
	this.stopUpdate();
	this.timer = setTimeout(this.update.bind(this), 1000);
  },

  handleTimeout: function(_request) {
  	this.setStatus("timeout, reconnecting");
	this.stopUpdate();
	this.timer = setTimeout(this.update.bind(this), 1000);
  },

  updateOptions: function(act) {
    this._options.parameters = 'act='+act+'&thread=' + (this._options.threadid || 0) +
			'&token=' + (this._options.token || 0)+
		'&lastid=' + (this._options.lastid || 0);
    if( this._options.user )
	this._options.parameters += "&user=true";
	if( act == 'refresh' && this._options.message && this._options.message.value != '' )
	this._options.parameters += "&typed=1";
  },

  enableInput: function(val) {
	if( this._options.message )
		this._options.message.disabled = !val;
  },

  stopUpdate: function() {
    this.enableInput(true);
	if( this.updater._options )
	    this.updater._options.onComplete = undefined;
    clearTimeout(this.timer);
  },

  update: function() {
    this.updateOptions("refresh");
    this.updater = new Ajax.Request(this._options.servl, this._options);
  },

  requestComplete: function(_response) {
    try {
        this.enableInput(true);
	this.cansend = true;
	var xmlRoot = Ajax.getXml(_response);
        if( xmlRoot && xmlRoot.tagName == 'thread' ) {
          this.updateContent( xmlRoot );
	} else {
	  this.handleError(_response, xmlRoot, 'refresh messages failed');
	}
	} catch (e) {
    }
    this.skipNextsound = false;
    this.timer = setTimeout(this.update.bind(this), this.frequency * 1000);
  },

  postMessage: function(msg) {
    if( msg == "" || !this.cansend) {
		return;
    }
    this.cansend = false;
    this.stopUpdate();
    this.skipNextsound = true;
    this.updateOptions("post");
    var postOptions = {}.extend(this._options);
    postOptions.parameters += "&message=" + encodeURIComponent(msg);
    postOptions.onComplete = (function(presponse) {
	this.requestComplete( presponse );
	if( this._options.message ) {
		this._options.message.value = '';
		this._options.message.focus();
	}
    }).bind(this);
    if( myRealAgent != 'opera' )
	this.enableInput(false);
    this.updater = new Ajax.Request(this._options.servl, postOptions);
  },

  changeName: function(newname) {
    this.skipNextsound = true;
    new Ajax.Request(this._options.servl, {parameters:'act=rename&thread=' + (this._options.threadid || 0) +
	'&token=' + (this._options.token || 0) + '&name=' + encodeURIComponent(newname)});

  },

  onThreadClosed: function(_response) {
	var xmlRoot = Ajax.getXml(_response);
    if( xmlRoot && xmlRoot.tagName == 'closed' ) {
	  setTimeout('window.close()', 2000);
	} else {
	  this.handleError(_response, xmlRoot, 'cannot close');
	}
  },

  closeThread: function() {
	var _params = 'act=close&thread=' + (this._options.threadid || 0) + '&token=' + (this._options.token || 0);
	if( this._options.user )
	_params += "&user=true";
    new Ajax.Request(this._options.servl, {parameters:_params, onComplete: this.onThreadClosed.bind(this)});
  },

  processMessage: function(_target, message) {
	var destHtml = NodeUtils.getNodeText(message);
	FrameUtils.insertIntoFrame(_target, destHtml );
  },

  showTyping: function(istyping) {
	if( $("typingdiv") ) {
		$("typingdiv").style.display=istyping ? 'inline' : 'none';
	}
  },

  setupAvatar: function(avatar) {
	var imageLink = NodeUtils.getNodeText(avatar);
	if( this._options.avatar && this._options.user ) {
		this._options.avatar.innerHTML = imageLink != ""
			? "<img src=\""+Chat.mibewRoot+"/images/free.gif\" width=\"7\" height=\"1\" border=\"0\" alt=\"\" /><img src=\""
				+imageLink+ "\" border=\"0\" alt=\"\"/>"
			: "";
	}
  },

  updateContent: function(xmlRoot) {
	var haveMessage = false;

	var result_div = this._options.container;
	var _lastid = NodeUtils.getAttrValue(xmlRoot, "lastid");
	if( _lastid ) {
		this._options.lastid = _lastid;
	}

	var typing = NodeUtils.getAttrValue(xmlRoot, "typing");
	if( typing ) {
		this.showTyping(typing == '1');
	}

	var canpost = NodeUtils.getAttrValue(xmlRoot, "canpost");
	if( canpost ) {
		if( canpost == '1' && !this.ownThread || this.ownThread && canpost != '1' ) {
			window.location.href = window.location.href;
		}
	}

	for( var i = 0; i < xmlRoot.childNodes.length; i++ ) {
		var node = xmlRoot.childNodes[i];
		if( node.tagName == 'message' ) {
        	haveMessage = true;
			this.processMessage(result_div, node);
		} else if( node.tagName == 'avatar' ) {
			this.setupAvatar(node);
        }
	}
	if(window.location.search.indexOf('trace=on')>=0) {
		var val = "updated";
		if(this.lastupdate > 0) {
			var seconds = ((new Date()).getTime() - this.lastupdate)/1000;
			val = val + ", " + seconds + " secs";
			if(seconds > 10) {
				alert(val);
			}
		}
		this.lastupdate = (new Date()).getTime();
		this.setStatus(val);
	} else {
		this.clearStatus();
	}
	if( haveMessage ) {
		FrameUtils.scrollDown(this._options.container);
		if(!this.skipNextsound) {
			var tsound = $('soundimg');
			if(tsound == null || tsound.className.match(new RegExp("\\bisound\\b")) ) {
				playSound(Chat.mibewRoot+'/sounds/new_message.wav');
			}
		}
		if( !this.focused ) {
			window.focus();
		}
	}
  },

  isSendkey: function(ctrlpressed, key) {
	  return ((key==13 && (ctrlpressed || this._options.ignorectrl)) || (key==10));
  },

  handleKeyDown: function(k) {
	if( k ){ ctrl=k.ctrlKey;k=k.which; } else { k=event.keyCode;ctrl=event.ctrlKey;	}
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

  handleError: function(_response, xmlRoot, _action) {
	if( xmlRoot && xmlRoot.tagName == 'error' ) {
	  this.setStatus(NodeUtils.getNodeValue(xmlRoot,"descr"));
	} else {
	  this.setStatus("reconnecting");
	}
  },

  showStatusDiv: function(k) {
  	if( $("engineinfo") ) {
		$("engineinfo").style.display='inline';
		$("engineinfo").innerHTML = k;
  	}
  },

  setStatus: function(k) {
	if( this.statusTimeout )
		clearTimeout(this.statusTimeout);
	this.showStatusDiv(k);
	this.statusTimeout = setTimeout(this.clearStatus.bind(this), 4000);
  },

  clearStatus: function() {
	$("engineinfo").style.display='none';
  }
});


var Chat = {
  threadUpdater : {},

  applyName: function() {
	if ( !$('uname').value.match(/^\s*$/) ) {
	    Chat.threadUpdater.changeName($('uname').value);
	    $('changename1').style.display='none';
	    $('changename2').style.display='inline';
	    $('unamelink').innerHTML = htmlescape($('uname').value);
	}
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
				message.value = this.options[this.selectedIndex].innerText || this.options[this.selectedIndex].innerHTML;
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
		    Chat.threadUpdater.stopUpdate();
			Chat.threadUpdater.update();
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

EventHelper.register(window, 'onload', function(){
  Chat.mibewRoot = threadParams.wroot;
  Chat.cssfile = threadParams.cssfile;
  Chat.threadUpdater = new Ajax.ChatThreadUpdater(({ignorectrl:-1,container:myRealAgent=='safari'?self.frames[0]:$("chatwnd"),avatar:$("avatarwnd"),message:$("msgwnd")}).extend( threadParams || {} ));
});