/*
 *  Mibew Messenger common script
 *  http://sourceforge.net/projects/webim
 *
 *  Based on Prototype JavaScript framework, version 1.3.1
 *  http://prototype.conio.net/ (c) 2005 Sam Stephenson <sam@conio.net>
 */

//- getEl, myAgent, myRealAgent

//- localized

//- onComplete, showonline
//- threadParams, servl, frequency, user, threadid, token, cssfile
//- updaterOptions, url, company, agentservl, noclients, wroot, havemenu, showpopup, ignorectrl, istatus


var Class = {
  create: function() {
    return function() {
      this./**/initialize./**/apply(this, arguments);
    };
  },

  inherit: function(child,parent,body) {
	Object./**/extend(Object.extend(child.prototype, parent.prototype), body );
  }
};

Object.extend = function(destination, source) {
  for (property in source) {
    destination[property] = source[property];
  }
  return destination;
};

Object.prototype.extend = function(_object) {
  return Object.extend.apply(this, [this, _object]);
};

Function.prototype./**/bind = function(_object) {
  var __method = this;
  return function() {
    return __method.apply(_object, arguments);
  }
};

Function.prototype./**/bindAsEventListener = function(_object) {
  var __method = this;
  return function(event) {
    __method.call(_object, event || window.event);
  }
};

Number.prototype./**/toColorPart = function() {
  var digits = this.toString(16);
  if (this < 16) return '0' + digits;
  return digits;
};

var Try = {
  these: function() {
    var returnValue;

    for (var i = 0; i < arguments.length; i++) {
      var lambda = arguments[i];
      try {
        returnValue = lambda();
        break;
      } catch (e) {}
    }

    return returnValue;
  }
};

/*--------------------------------------------------------------------------*/

var PeriodicalExecuter = Class.create();
PeriodicalExecuter.prototype = {
  initialize: function(callback, frequency) {
    this.callback = callback;
    this.frequency = frequency;
    this./**/currentlyExecuting = false;

    this./**/registerCallback();
  },

  registerCallback: function() {
    setInterval(this.onTimerEvent.bind(this), this.frequency * 1000);
  },

  onTimerEvent: function() {
    if (!this.currentlyExecuting) {
      try {
        this.currentlyExecuting = true;
        this.callback();
      } finally {
        this.currentlyExecuting = false;
      }
    }
  }
};

/*--------------------------------------------------------------------------*/

function findObj( id )
{
	var x;
	if( !( x = document[ id ] ) && document.all ) x = document.all[ id ];
	if( !x && document.getElementById ) x = document.getElementById( id );
	if( !x && !document.all && document.getElementsByName )
	{
		x = document.getElementsByName( id );
		if( x.length == 0 ) return null;
		if( x.length == 1 ) return x[ 0 ];
	}

    return x;
}

if (!Array.prototype./**/push) {
  Array.prototype.push = function() {
		var startLength = this.length;
		for (var i = 0; i < arguments.length; i++)
      this[startLength + i] = arguments[i];
	  return this.length;
  };
}

function $() {
  var elems = new Array();

  for (var i = 0; i < arguments.length; i++) {
    var elem = arguments[i];
    if (typeof elem == 'string')
      elem = findObj(elem);

    if (arguments.length == 1)
      return elem;

    elems.push(elem);
  }

  return elems;
}

var Ajax = {
  getTransport: function() {
    return Try.these(
      function() {return new ActiveXObject('Msxml2.XMLHTTP')},
      function() {return new ActiveXObject('Microsoft.XMLHTTP')},
      function() {return new XMLHttpRequest()}
    ) || false;
  },

  getXml: function(_response) {
	if( _response &&
	  _response.status >= 200 &&
	  _response.status < 300 ) {
	  var xmlDoc = _response.responseXML;
	  if( xmlDoc && xmlDoc.documentElement )
	    return xmlDoc.documentElement;
	}
	return null;
  },

  getError: function(_response) {
  	return _response.statusText || "connection error N" + _response.status;
  },

  emptyFunction: function() {}
};

Ajax./**/Base = function() {};
Ajax.Base.prototype = {
  setOptions: function(_options) {
    this._options = {
      _method:       'post',
      asynchronous: true,
      parameters:   ''
    }.extend(_options || {});
  },

  getStatus: function() {
    try {
      return this.transport.status || 0;
    } catch (e) { return 0 }
  },

  responseIsSuccess: function() {
	var status = this.getStatus();
	return !status || (status >= 200 && status < 300);
  },

  responseIsFailure: function() {
    return !this.responseIsSuccess();
  }
};

Ajax./**/Request = Class.create();
Ajax.Request./**/Events =
  ['Uninitialized', 'Loading', 'Loaded', 'Interactive', 'Complete'];

Class.inherit( Ajax.Request, Ajax.Base, {
  initialize: function(url, _options) {
    this.transport = Ajax.getTransport();
    this.setOptions(_options);
    this.transportTimer = {};
    this.finished = false;
    this.request(url);
  },

  request: function(url) {
    var parameters = this._options.parameters || '';
    if (parameters.length > 0) parameters += '&_=';

    try {
      if (this._options._method == 'get' && parameters.length > 0)
        url += '?' + parameters;

      this.transport.open(this._options._method.toUpperCase(), url, this._options.asynchronous);

      if (this._options.asynchronous) {
        this.transport.onreadystatechange = this.onStateChange.bind(this);
        if(this._options.timeout) {
        	this.transportTimer = setTimeout(this.handleTimeout.bind(this), this._options.timeout);
        }
      }

      this.setRequestHeaders();

      var pbody = this._options.postBody ? this._options.postBody : parameters;
      this.transport.send(this._options._method == 'post' ? pbody : null);

    } catch (e) {
      this.dispatchException(e);
    }
  },

  setRequestHeaders: function() {
    var requestHeaders =
      ['X-Requested-With', 'XMLHttpRequest'];

    if (this._options._method == 'post') {
      requestHeaders.push('Content-type',
        'application/x-www-form-urlencoded');

      /* Force "Connection: close" for older Mozilla browsers to work
       * around a bug where XMLHttpRequest sends an incorrect
       * Content-length header. See Mozilla Bugzilla #246651.
       */
      if (this.transport.overrideMimeType &&
          (navigator.userAgent.match("/Gecko\/(\d{4})/") || [0,2005])[1] < 2005)
        requestHeaders.push('Connection', 'close');
    }

    if (this._options.requestHeaders)
      requestHeaders.push.apply(requestHeaders, this._options.requestHeaders);

    for (var i = 0; i < requestHeaders.length; i += 2)
      this.transport.setRequestHeader(requestHeaders[i], requestHeaders[i+1]);
  },

  onStateChange: function() {
    var readystate = this.transport.readyState;
    if (readystate != 1)
      this.respondToReadyState(this.transport.readyState);
  },

  handleTimeout: function() {
  	if(this.finished) { return; }
  	this.finished = true;
  	(this._options.onTimeout || Ajax.emptyFunction)(this);
  },

  respondToReadyState: function(readystate) {
    var event = Ajax.Request.Events[readystate];

    if (event == 'Complete') {
      try {
		if(!this.finished) {
	        this.finished = true;
	      	if(this._options.timeout) { clearTimeout(this.transportTimer); }
	      	(this._options.onComplete || Ajax.emptyFunction)(this.transport);
	    }
      } catch (e) {
        this.dispatchException(e);
      }

	  /* Avoid memory leak in MSIE: clean up the oncomplete event handler */
	  this.transport.onreadystatechange = Ajax.emptyFunction;
    }
  },

  dispatchException: function(exception) {
    (this._options.onException || Ajax.emptyFunction)(this, exception);
  }
});

var EventHelper = {
	register : function(obj, ev,func){
		var oldev = obj[ev];

		if (typeof oldev != 'function') {
			obj[ev] = func;
		} else {
			obj[ev] = function() {
				oldev();
				func();
			}
		}
	}
};

/*
   Behaviour v1.1 by Ben Nolan, June 2005. Based largely on the work
   of Simon Willison (see comments by Simon below).
   http://ripcord.co.nz/behaviour/
*/

var Behaviour = {
  list : new Array,

  register : function(sheet){
	Behaviour.list.push(sheet);
  },

  init : function(){
	EventHelper.register(window, 'onload', function(){
		Behaviour.apply();
	});
  },

  apply : function(){
	for (h=0;sheet=Behaviour.list[h];h++){
	  for (selector in sheet) {
		list = document.getElementsBySelector(selector);
		if (!list)
		  continue;
		for( i = 0; element = list[i]; i++ ) {
			sheet[selector]( element );
		}
	  }
	}
  }
};

Behaviour.init();

function getAllChildren(e) {
  // Returns all children of element. Workaround required for IE5/Windows. Ugh.
  return e.all ? e.all : e.getElementsByTagName('*');
}

document.getElementsBySelector = function(selector) {
  // Attempt to fail gracefully in lesser browsers
  if (!document.getElementsByTagName) {
    return new Array();
  }
  // Split selector in to tokens
  var tokens = selector.split(' ');
  var currentContext = new Array(document);
  for (var i = 0; i < tokens.length; i++) {
    token = tokens[i].replace(/^\s+/,'').replace(/\s+$/,'');;
    if (token.indexOf('#') > -1) {
      // Token is an ID selector
      var bits = token.split('#');
      var tag_name = bits[0];
      var id = bits[1];
      var element = document.getElementById(id);
      if (element == null || tag_name && element.nodeName.toLowerCase() != tag_name ) {
        // tag with that ID not found, return false
        return new Array();
      }
      // Set currentContext to contain just this element
      currentContext = new Array(element);
      continue; // Skip to next token
    }
    if (token.indexOf('.') > -1) {
      // Token contains a class selector
      var bits = token.split('.');
      var tag_name = bits[0];
      var class_name = bits[1];
      if (!tag_name) {
        tag_name = '*';
      }
      // Get elements matching tag, filter them for class selector
      var found = new Array;
      var foundCount = 0;
      for (var h = 0; h < currentContext.length; h++) {
        var elements;
        if (tag_name == '*') {
            elements = getAllChildren(currentContext[h]);
        } else {
            elements = currentContext[h].getElementsByTagName(tag_name);
        }
        if( elements == null )
        	continue;
        for (var j = 0; j < elements.length; j++) {
          found[foundCount++] = elements[j];
        }
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      for (var k = 0; k < found.length; k++) {
        if (found[k].className && found[k].className.match(new RegExp("\\b"+class_name+"\\b"))) {
          currentContext[currentContextIndex++] = found[k];
        }
      }
      continue; // Skip to next token
    }

    // [evgeny] code for attribute selection is removed...

    if (!currentContext[0]){
    	return;
    }

    // If we get here, token is JUST an element (not a class or ID selector)
    tag_name = token;
    var found = new Array;
    var foundCount = 0;
    for (var h = 0; h < currentContext.length; h++) {
      var elements = currentContext[h].getElementsByTagName(tag_name);
      for (var j = 0; j < elements.length; j++) {
        found[foundCount++] = elements[j];
      }
    }
    currentContext = found;
  }
  return currentContext;
};

var NodeUtils = {

  getNodeValue: function(parent,name) {
	var nodes = parent.getElementsByTagName( name );
	if( nodes.length == 0 )
		return "";
	nodes = nodes[0].childNodes;
	var reslt = "";
	for( i = 0; i < nodes.length; i++ )
		reslt += nodes[i].nodeValue;
	return reslt;
  },

  getNodeText: function(_node) {
	var _nodes = _node.childNodes;
	var _text = "";
	for( i = 0; i < _nodes.length; i++ )
		_text += _nodes[i].nodeValue;
	return _text;
  },

  getAttrValue: function(parent,name) {
	for( k=0; k < parent.attributes.length; k++ )
		if( parent.attributes[k].nodeName == name )
			return parent.attributes[k].nodeValue;
	return null;
  }
};

var CommonUtils = {
  getRow: function(_id,_table) {
  	var _row = _table.rows[_id];
  	if( _row != null )
  		return _row;
  	if( _table.rows['head'] != null )
  		return null;

  	for( k=0; k < _table.rows.length; k++ ) {
  		if( _table.rows[k].id == _id )
  			return _table.rows[k];
  	}
  	return null;
  },

  getCell: function(_id,_row,_table) {
  	var _cell = _row.cells[_id];
  	if( _cell != null )
  		return _cell;
  	if( _table.rows['head'] != null )
  		return null;
  	for( k=0; k < _row.cells.length; k++ ) {
  		if( _row.cells[k].id == _id )
  			return _row.cells[k];
  	}
  	return null;
  },

  insertCell: function(_row,_id,_className,_align,_height, _inner) {
  	var cell = _row.insertCell(-1);
  	cell.id = _id;
  	if(_align)
  		cell.align = _align;
  	cell.className = _className;
  	if(_height)
  		cell.height = _height;
  	cell.innerHTML = _inner;
  }
};

function playSound(wav_file) {
  var player = document.createElement("div");
  var agt = navigator.userAgent.toLowerCase();
  if(agt.indexOf('opera') != -1) {
  	player.style = "position: absolute; left: 0px; top: -200px;";
  }
  document.body.appendChild(player);
  player.innerHTML = '<embed src="'+wav_file+'" hidden="true" autostart="true" loop="false">';
}

function htmlescape(str) {
	return str.replace('&','&amp;').replace('<','&lt;').replace('>','&gt;').replace('"','&quot;');
}