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
	doc.write("<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\""+Chat.webimRoot+"/chat.css\" />");
	doc.write("</head><body bgcolor='#FFFFFF' text='#000000' link='#C28400' vlink='#C28400' alink='#C28400' marginwidth='0' marginheight='0' leftmargin='0' rightmargin='0' topmargin='0' bottommargin='0'>");
	doc.write("<table width='100%' cellspacing='0' cellpadding='0' border='0'><tr><td valign='top' class='message' id='content'></td></tr></table><a id='bottom'/>");
	doc.write("</body></html>");
	doc.close();
	frm.onload = function() {
		if( frm./**/myHtml ) {
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
		frm.contentWindow.scrollTo(0,this.getDocument(frm).getElementById('content').clientHeight);
	} else if( vbottom )
		vbottom.scrollIntoView(false);
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
			? "<img src=\""+Chat.webimRoot+"/images/free.gif\" width=\"7\" height=\"1\" border=\"0\" alt=\"\" /><img src=\""
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
				playSound(Chat.webimRoot+'/sounds/new_message.wav');
			}
		}
		if( !this.focused ) {
			window.focus();
		}
	}
  },

  handleKeyDown: function(k) {
	if( k ){ ctrl=k.ctrlKey;k=k.which; } else { k=event.keyCode;ctrl=event.ctrlKey;	}
	if( this._options.message && ((k==13 && (ctrl || myRealAgent == 'opera')) || (k==10)) ) {
		var mmsg = this._options.message.value;
		if( myRealAgent == 'opera' ) {
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


HSplitter = Class.create();
HSplitter.prototype = {
  initialize: function(_options) {
	this._options = _options;
	this.captured = 0;
	if( this._options.first && this._options.second && this._options.control ) {
		this._options.control.onmousedown = this.onmousedownEvent.bind(this);
		this._options.control.onmouseup = this.onmouseupEvent.bind(this);
		this._options.control.onmousemove = this.onmouseMoveEvent.bind(this);
	}
  },

  onmousedownEvent: function(e) {
  	var ev = e || event;

	if( this._options.control.setCapture )
		this._options.control.setCapture();
	this.start_height = this._options.first.style.pixelHeight || this._options.first.clientHeight;
	this.start_offset = ev.screenY;
	this._options.maxfirst = this._options.first.style.pixelHeight + this._options.second.clientHeight - this._options.minsec;
	this.captured = 1;
  },

  onmouseupEvent: function() {
	if( this.captured ) {
		if( this._options.control.releaseCapture )
			this._options.control.releaseCapture();
		this.captured = 0;
	}
  },

  onmouseMoveEvent: function(e) {
  	var ev = e || event;

	if( this.captured ) {
		var new_height = this.start_height - (ev.screenY - this.start_offset);
		if( new_height > this._options.maxfirst )
			new_height = this._options.maxfirst;
		else if( new_height < this._options.minfirst )
			new_height = this._options.minfirst;
		if( myAgent == 'moz' )
			this._options.first.style.height=new_height+'px';
		else
			this._options.first.style.pixelHeight = new_height;
	}
  }
};

var Chat = {
  threadUpdater : {},
  hSplitter : {},

  applyName: function() {
	Chat.threadUpdater.changeName($('uname').value);
	$('changename1').style.display='none';
	$('changename2').style.display='inline';
	$('unamelink').innerHTML=$('uname').value;
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
	'a#sndmessagelnk' : function(el) {
		if( myRealAgent == 'opera' ) {
			el.innerHTML = el.innerHTML.replace('Ctrl-','');
		}
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
  Chat.webimRoot = threadParams.wroot;
  Chat.hSplitter = new HSplitter({control:$("spl1"), first:$("msgwndtd"), second:$("chatwndtd"), minfirst:30, minsec:30});
  Chat.threadUpdater = new Ajax.ChatThreadUpdater(({container:myRealAgent=='safari'?self.frames[0]:$("chatwnd"),avatar:$("avatarwnd"),message:$("msgwnd")}).extend( threadParams || {} ));
});