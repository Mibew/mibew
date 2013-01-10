/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

Ajax.PeriodicalUpdater = Class.create();
Class.inherit( Ajax.PeriodicalUpdater, Ajax.Base, {

  initialize: function(_options) {
    this.setOptions(_options);
    this._options.onComplete = this.requestComplete.bind(this);
    this._options.onException = this.handleException.bind(this);
    this._options.onTimeout = this.handleTimeout.bind(this);
    this._options.timeout = 5000;
    this.frequency = (this._options.frequency || 2);
    this.updater = {};
    this.update();
  },

  handleException: function(_request, ex) {
	if( this._options.handleError )
	  this._options.handleError("offline, reconnecting");
	this.stopUpdate();
	this.timer = setTimeout(this.update.bind(this), 1000);
  },

  handleTimeout: function(_request) {
	if( this._options.handleError )
	  this._options.handleError("timeout, reconnecting");
	this.stopUpdate();
	this.timer = setTimeout(this.update.bind(this), 1000);
  },

  stopUpdate: function() {
  	if( this.updater._options )
	    this.updater._options.onComplete = undefined;
    clearTimeout(this.timer);
  },

  update: function() {
    if( this._options.updateParams )
    	this._options.parameters = (this._options.updateParams)();
    this.updater = new Ajax.Request(this._options.url, this._options);
  },

  requestComplete: function(presponse) {
  	try {
		var xmlRoot = Ajax.getXml(presponse);
		if( xmlRoot ) {
	      (this._options.updateContent || Ajax.emptyFunction)( xmlRoot );
		} else {
		    if( this._options.handleError )
				this._options.handleError("reconnecting");
		}
	} catch(e) {
	}
    this.timer = setTimeout(this.update.bind(this), this.frequency * 1000);
  }
});

var HtmlGenerationUtils = {

  popupLink: function(link, title, wndid, inner, width, height,linkclass) {
  	return '<a href="'+link+'"'+(linkclass != null ? ' class="'+linkclass+'"' : '')+' target="_blank" title="'+title+'" onclick="this.newWindow = window.open(\''+link+'\', \''+
  			wndid+'\', \'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width='+width+',height='+height+',resizable=1\');this.newWindow.focus();this.newWindow.opener=window;return false;">'+
  			inner+'</a>';
  },

  generateOneRowTable: function(content) {
  	return '<table class="inner"><tr>' + content + '</tr></table>';
  },

  viewOpenCell: function(username,servlet,id,canview,canopen,ban,message,cantakenow,tracked,trackedlink) {
  		var link = servlet+"?thread="+id;
 		var gen = '<td>';
 		if(canopen || canview ) {
			gen += HtmlGenerationUtils.popupLink( (cantakenow||!canview) ? link : link+"&viewonly=true", localized[canopen ? 0 : 1], "ImCenter"+id, username, 640, 480, null);
		} else {
			gen += '<a href="#">' + username + '</a>';
		}
		gen += '</td>';
		if( message != "" ) {
			gen += '</tr><tr><td class="firstmessage" colspan="2"><a href="javascript:void(0)" title="'+message+'" onclick="alert(this.title);return false;">';
			gen += message.length > 30 ? message.substring(0,30) + '...' : message;
			gen += '</a></td>';
		}

  		return HtmlGenerationUtils.generateOneRowTable(gen);
  },
  viewActionsCell: function(servlet,id,canview,canopen,tracked,trackedlink) {
		var link = servlet+"?thread="+id;
		var gen = '';
		if( canopen ) {
			gen += '<td class="icon">';
			gen += HtmlGenerationUtils.popupLink( link, localized[0], "ImCenter"+id, '<img src="'+webimRoot+'/images/tbliclspeak.gif" width="15" height="15" border="0" alt="'+localized[0]+'">', 640, 480, null);
			gen += '</td>';
		}
		if( canview ) {
			gen += '<td class="icon">';
			gen += HtmlGenerationUtils.popupLink( link+"&viewonly=true", localized[1], "ImCenter"+id, '<img src="'+webimRoot+'/images/tbliclread.gif" width="15" height="15" border="0" alt="'+localized[1]+'">', 640, 480, null);
			gen += '</td>';
		}
		if ( tracked ) {
			gen += '<td class="icon">';
			gen += HtmlGenerationUtils.popupLink( trackedlink+"?thread="+id, localized[6], "ImTracked"+id, '<img src="'+webimRoot+'/images/tblictrack.gif" width="15" height="15" border="0" alt="'+localized[6]+'">', 640, 480, null);
			gen += '</td>';
		}
 		return gen;
  },
  banCell: function(id,banid){
      return '<td class="icon">'+
          HtmlGenerationUtils.popupLink( webimRoot+'/operator/ban.php?'+(banid ? 'id='+banid : 'thread='+id), localized[2], "ban"+id, '<img src="'+webimRoot+'/images/ban.gif" width="15" height="15" border="0" alt="'+localized[2]+'">', 720, 480, null)+
          '</td>';
  },
  viewVisOpenCell: function(username, inviteservlet, userid, trackedservlet, caninvite) {
  		var cellsCount = 2;
 		var gen = '<td>';
 		if(caninvite) {
			gen += HtmlGenerationUtils.popupLink( inviteservlet+"?visitor="+userid, localized[7], "ImCenter"+userid, username, 640, 480, null);
		} else {
			gen += '<a href="#">' + username + '</a>';
		}
		gen += '</td>';
		gen += '<td class="icon">';
		var tr_link = HtmlGenerationUtils.popupLink( trackedservlet+"?visitor="+userid, localized[6], "ImTracked"+userid, '<img src="'+webimRoot+'/images/tblictrack.gif" width="15" height="15" border="0" alt="'+localized[6]+'">', 640, 480, null);
		tr_link = tr_link.replace("scrollbars=0","scrollbars=1");
		gen += tr_link;
		gen += '</td>';
  		return HtmlGenerationUtils.generateOneRowTable(gen);
  }
};

Ajax.ThreadListUpdater = Class.create();
Class.inherit( Ajax.ThreadListUpdater, Ajax.Base, {

  initialize: function(_options) {
    this.setOptions(_options);
    this._options.updateParams = this.updateParams.bind(this);
    this._options.handleError = this.handleError.bind(this);
    this._options.updateContent = this.updateContent.bind(this);
    this._options.lastrevision = 0;
    this.threadTimers = new Object();
    this.delta = 0;
    this.t = this._options.table;
    this.t2 = this._options.visitors_table;
    this.periodicalUpdater = new Ajax.PeriodicalUpdater(this._options);
    this.old_visitors = new Object();
    this.visitors = new Object();
    this.visitorTimers = new Object();
  },

  updateParams: function() {
  	return "since=" + this._options.lastrevision + "&status=" + this._options.istatus + (this._options.showonline ? "&showonline=1" : "") + (this._options.showvisitors ? "&showvisitors=1" : "");
  },

  setStatus: function(msg) {
  	this._options.status.innerHTML = msg;
  },

  handleError: function(s) {
	this.setStatus( s );
  },

  updateThread: function(node) {
	var id, stateid, vstate, canview = false, canopen = false, canban = false, ban = null, banid = null;

	for( var i = 0; i < node.attributes.length; i++ ) {
		var attr = node.attributes[i];
		if( attr.nodeName == "id" )
			id = attr.nodeValue;
		else if( attr.nodeName == "stateid" )
			stateid = attr.nodeValue;
		else if( attr.nodeName == "state" )
			vstate = attr.nodeValue;
		else if( attr.nodeName == "canopen" )
			canopen = true;
		else if( attr.nodeName == "canview" )
			canview = true;
		else if( attr.nodeName == "canban" )
			canban = true;
		else if( attr.nodeName == "ban" )
			ban = attr.nodeValue;
		else if( attr.nodeName == "banid" )
			banid = attr.nodeValue;
	}

	function setcell(_table, row,id,pcontent) {
		var cell = CommonUtils.getCell( id, row, _table );
		if( cell )
			cell.innerHTML = pcontent;
	}

	var row = CommonUtils.getRow("thr"+id, this.t);
	if( stateid == "closed" ) {
		if( row ) {
			this.t.deleteRow(row.rowIndex);
		}
		this.threadTimers[id] = null;
		return;
	}

	var vname = NodeUtils.getNodeValue(node,"name");
	var actions = HtmlGenerationUtils.viewActionsCell(this._options.agentservl,id,canview,canopen,this._options.showvisitors, this._options.trackedservl);
	var vaddr = NodeUtils.getNodeValue(node,"addr");
	var vtime = NodeUtils.getNodeValue(node,"time");
	var agent = NodeUtils.getNodeValue(node,"agent");
	var modified = NodeUtils.getNodeValue(node,"modified");
	var message = NodeUtils.getNodeValue(node,"message");
	var etc = '<td>'+NodeUtils.getNodeValue(node,"useragent")+'</td>';

	if(ban != null) {
		etc = '<td>'+NodeUtils.getNodeValue(node,"reason")+'</td>';
	}

	if(canban) {
		actions += HtmlGenerationUtils.banCell(id,banid);
	}
	actions = HtmlGenerationUtils.generateOneRowTable(actions);
	etc = HtmlGenerationUtils.generateOneRowTable(etc);

	var startRow = CommonUtils.getRow("t"+stateid, this.t);
	var endRow = CommonUtils.getRow("t"+stateid+"end", this.t);

	if( row != null && (row.rowIndex <= startRow.rowIndex || row.rowIndex >= endRow.rowIndex ) ) {
		this.t.deleteRow(row.rowIndex);
		this.threadTimers[id] = null;
		row = null;
	}
	if( row == null ) {
		row = this.t.insertRow(startRow.rowIndex+1);
		row.className = (ban == "blocked" && stateid != "chat") ? "ban" : "in"+stateid;
		row.id = "thr"+id;
		this.threadTimers[id] = new Array(vtime,modified,stateid);
		CommonUtils.insertCell(row, "name", "visitor", null, null, HtmlGenerationUtils.viewOpenCell(vname,this._options.agentservl,id,canview,canopen,ban,message,stateid!='chat',this._options.showvisitors, this._options.trackedservl));
		CommonUtils.insertCell(row, "actions", "visitor", "center", null, actions);
		CommonUtils.insertCell(row, "contid", "visitor", "center", null, vaddr );
		CommonUtils.insertCell(row, "state", "visitor", "center", null, vstate );
		CommonUtils.insertCell(row, "op", "visitor", "center", null, agent );
		CommonUtils.insertCell(row, "time", "visitor", "center", null, this.getTimeSince(vtime) );
		CommonUtils.insertCell(row, "wait", "visitor", "center", null, (stateid!='chat' ? this.getTimeSince(modified) : '-') );
		CommonUtils.insertCell(row, "etc", "visitor", "center", null, etc );

		if( stateid == 'wait' || stateid == 'prio' )
			return true;
	} else {
		this.threadTimers[id] = new Array(vtime,modified,stateid);
		row.className = (ban == "blocked" && stateid != "chat") ? "ban" : "in"+stateid;
		setcell(this.t, row,"name",HtmlGenerationUtils.viewOpenCell(vname,this._options.agentservl,id,canview,canopen,ban,message,stateid!='chat',this._options.showvisitors, this._options.trackedservl));
		setcell(this.t, row, "actions", actions);
		setcell(this.t, row,"contid",vaddr);
		setcell(this.t, row,"state",vstate);
		setcell(this.t, row,"op",agent);
		setcell(this.t, row,"time",this.getTimeSince(vtime));
		setcell(this.t, row,"wait",(stateid!='chat' ? this.getTimeSince(modified) : '-'));
		setcell(this.t, row,"etc",etc);
	}
	return false;
  },

  updateQueueMessages: function() {
  	function queueNotEmpty(t,id) {
		var startRow = $(id);
		var endRow = $(id+"end");
		if( startRow == null || endRow == null ) {
			return false;
		}
		return startRow.rowIndex+1 < endRow.rowIndex;
  	}
	var _status = $("statustd");
	if( _status) {
		var notempty = queueNotEmpty(this.t, "twait") || queueNotEmpty(this.t, "tprio") || queueNotEmpty(this.t, "tchat");
		_status.innerHTML = notempty ? "" : this._options.noclients;
		_status.height = notempty ? 5 : 30;
	}
  },

  getTimeSince: function(srvtime) {
	var secs = Math.floor(((new Date()).getTime()-srvtime-this.delta)/1000);
	var minutes = Math.floor(secs/60);
	var prefix = "";
	secs = secs % 60;
	if( secs < 10 )
		secs = "0" + secs;
	if( minutes >= 60 ) {
		var hours = Math.floor(minutes/60);
		minutes = minutes % 60;
		if( minutes < 10 )
			minutes = "0" + minutes;
		prefix = hours + ":";
	}

	return prefix + minutes+":"+secs;
  },

  updateTimers: function() {
	for (var i in this.threadTimers) {
		if (this.threadTimers[i] != null) {
			var value = this.threadTimers[i];
			var row = CommonUtils.getRow("thr"+i, this.t);
			if( row != null ) {
				function setcell(_table, row,id,pcontent) {
					var cell = CommonUtils.getCell( id, row, _table );
					if( cell )
						cell.innerHTML = pcontent;
				}
				setcell(this.t, row,"time",this.getTimeSince(value[0]));
				setcell(this.t, row,"wait",(value[2]!='chat' ? this.getTimeSince(value[1]) : '-'));
			}
		}
	}
  },
  
  updateThreads: function(root) {
	var newAdded = false;
	var _time = NodeUtils.getAttrValue(root, "time");
	var _revision = NodeUtils.getAttrValue(root, "revision" );
	
	if( _time )
		this.delta = (new Date()).getTime() - _time;
	if( _revision )
		this._options.lastrevision = _revision;
	
	for( var i = 0; i < root.childNodes.length; i++ ) {
		var node = root.childNodes[i];
		if( node.tagName == 'thread' )
			if( this.updateThread(node) )
				newAdded = true;
	}
	this.updateQueueMessages();
	this.updateTimers();
	this.setStatus(this._options.istatus ? localized[8] : localized[9]);
	if( newAdded ) {
		playSound(webimRoot+'/sounds/new_user.wav');
		window.focus();
		if(updaterOptions.showpopup) {
			alert(localized[5]);
		}
	}
  },
  
  updateOperators: function(root) {
  	var div = $('onlineoperators');
  	if (!div)
  		return;

	var names = [];
	
	for( var i = 0; i < root.childNodes.length; i++ ) {
		var node = root.childNodes[i];
		if(node.tagName != 'operator')
			continue;
		
		var name = NodeUtils.getAttrValue(node, 'name');
		var isAway = NodeUtils.getAttrValue(node, 'away') != null;
		
		names[names.length] = 
			'<img src="'+webimRoot+'/images/op'+(isAway ? 'away' : 'online')+
					'.gif" width="12" height="12" border="0" alt="'+localized[1]+'"> '+ name;
	}

	div.innerHTML = names.join(', ');
  },

  updateVisitorsTimers: function() {
	for (var i in this.visitorTimers) {
		if (this.visitorTimers[i] != null) {
			var value = this.visitorTimers[i];
			var row = CommonUtils.getRow("vis"+i, this.t2);
			if( row != null ) {
				function setcell(_table, row,id,pcontent) {
					var cell = CommonUtils.getCell( id, row, _table );
					if( cell )
						cell.innerHTML = pcontent;
				}
				setcell(this.t2, row,"time",this.getTimeSince(value[0]));
				setcell(this.t2, row,"modified",this.getTimeSince(value[1]));
				if (value[2] != null)
				    setcell(this.t2, row,"invitationtime",this.getTimeSince(value[2]));
			}
		}
	}
  },

  updateVisitor: function(node) {
	var id, invited = false;

	for( var i = 0; i < node.attributes.length; i++ ) {
		var attr = node.attributes[i];
		if( attr.nodeName == "id" )
			id = attr.nodeValue;
	}

	function setcell(_table, row,id,pcontent) {
		var cell = CommonUtils.getCell( id, row, _table );
		if( cell )
			cell.innerHTML = pcontent;
	}

	var addr = NodeUtils.getNodeValue(node,"addr");
	var username = NodeUtils.getNodeValue(node,"username");
	var useragent = NodeUtils.getNodeValue(node,"useragent");
	var time = NodeUtils.getNodeValue(node,"time");
	var modified = NodeUtils.getNodeValue(node,"modified");

	var invitations = NodeUtils.getNodeValue(node,"invitations");
	var chats = NodeUtils.getNodeValue(node,"chats");

	var operator = null;
	var invitationtime = null;
	var invitation = node.getElementsByTagName("invitation")[0];
	for( var i = 0; i < invitation.childNodes.length; i++ ) {
		var childnode = invitation.childNodes[i];
		if( childnode.tagName == 'operator' ) {
		    operator = childnode.firstChild.nodeValue;
		}
		else if ( childnode.tagName == 'invitationtime' ) {
		    invitationtime = childnode.firstChild.nodeValue;
		}
	}
	var state = (operator == null) ? 'free' : 'invited';

	var row = CommonUtils.getRow("vis"+id, this.t2);

	var startRow = CommonUtils.getRow("vis" + state, this.t2);
	var endRow = CommonUtils.getRow("vis" + state + "end", this.t2);

	if( row != null && (row.rowIndex <= startRow.rowIndex || row.rowIndex >= endRow.rowIndex ) ) {

	    this.t2.deleteRow(row.rowIndex);
	    this.visitorTimers[id] = null;
	    row = null;
	}

	if (row == null) {
	    row = this.t2.insertRow(startRow.rowIndex+1);
	    row.id = "vis"+id;
	    this.visitorTimers[id] = new Array(time, modified, invitationtime);
	    CommonUtils.insertCell(row, "username", "visitor", null, null, HtmlGenerationUtils.viewVisOpenCell(username, this._options.inviteservl, id, this._options.trackedservl, operator==null));
	    CommonUtils.insertCell(row, "addr", "visitor", "center", null, addr);
	    CommonUtils.insertCell(row, "time", "visitor", "center", null, this.getTimeSince(time) );
	    CommonUtils.insertCell(row, "modified", "visitor", "center", null, this.getTimeSince(modified) );
	    CommonUtils.insertCell(row, "operator", "visitor", "center", null, (operator != null) ? operator : '-');
	    CommonUtils.insertCell(row, "invitationtime", "visitor", "center", null, (operator != null ? this.getTimeSince(invitationtime) : '-') );
	    CommonUtils.insertCell(row, "invitations", "visitor", "center", null, invitations + ' / ' + chats);
	    CommonUtils.insertCell(row, "useragent", "visitor", "center", null, useragent);
	}
	else {
	    this.visitorTimers[id] = new Array(time, modified, invitationtime);
	    setcell(this.t2, row, "username",HtmlGenerationUtils.viewVisOpenCell(username, this._options.inviteservl, id, this._options.trackedservl, operator==null));
	    setcell(this.t2, row, "addr", addr);
	    setcell(this.t2, row, "operator", (operator != null) ? operator : '-');
	    setcell(this.t2, row, "time", this.getTimeSince(time) );
	    setcell(this.t2, row, "modified", this.getTimeSince(modified) );
	    setcell(this.t2, row, "invitationtime", (operator != null ? this.getTimeSince(invitationtime) : '-') );
	    setcell(this.t2, row, "invitations", invitations + ' / ' + chats);
	    setcell(this.t2, row, "useragent", useragent);
	}

	this.visitors[id] = 1;

	return false;
  },

  removeOldVisitors: function() {
	for (id in this.old_visitors) {
	    if (this.visitors[id] === undefined) {
		var row = CommonUtils.getRow("vis"+id, this.t2);
		if( row ) {
			this.t2.deleteRow(row.rowIndex);
		}
		this.visitorTimers[id] = null;
	    }
	}
  },

  updateVisitorsList: function(visitors) {
	var _status = $("visstatustd");
	if( _status) {
		_status.innerHTML = (visitors > 0) ? "" : this._options.novisitors;
		_status.height = (visitors > 0) ? 5 : 30;
	}
  },

  updateVisitors: function(root) {

	this.old_visitors = this.visitors;
	this.visitors = new Object();

	var visitors_cnt = 0;
	for( var i = 0; i < root.childNodes.length; i++ ) {
		var node = root.childNodes[i];
		if( node.tagName == 'visitor' ) {
			visitors_cnt++;
			this.updateVisitor(node);
		}
	}
	this.updateVisitorsTimers();
	this.removeOldVisitors();
	this.updateVisitorsList(visitors_cnt);
  },

  updateContent: function(root) {
	if( root.tagName == 'update' ) {
		for( var i = 0; i < root.childNodes.length; i++ ) {
			var node = root.childNodes[i];
			
			if (node.tagName == 'threads') {
				this.updateThreads(node);
			} else if (node.tagName == 'operators') {
				this.updateOperators(node);
			} else if (node.tagName == 'visitors') {
				this.updateVisitors(node);
			}
		}
	} else if( root.tagName == 'error' ) {
		this.setStatus(NodeUtils.getNodeValue(root,"descr") );
	} else {
		this.setStatus( "reconnecting" );
	}
  }
});

function togglemenu() {
if($("sidebar") && $("wcontent") && $("togglemenu")) {
  if($("wcontent").className == "contentnomenu") {
    $("sidebar").style.display = "block";
    $("wcontent").className = "contentinner";
    $("togglemenu").innerHTML = localized[4];
  } else {
    $("sidebar").style.display = "none";
    $("wcontent").className = "contentnomenu"; 
    $("togglemenu").innerHTML = localized[3];
  }
}
}

var webimRoot = "";

Behaviour.register({
	'#togglemenu' : function(el) {
		el.onclick = function() {
			togglemenu();
		};
	}
});

EventHelper.register(window, 'onload', function(){
  webimRoot = updaterOptions.wroot;
  new Ajax.ThreadListUpdater(({table:$("threadlist"),status:$("connstatus"),istatus:0,visitors_table:$("visitorslist")}).extend(updaterOptions || {}));
  if(!updaterOptions.havemenu) {
	  togglemenu();
  }	 
});
