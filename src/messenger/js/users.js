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

  insertSplitter: function( _row ) {
  	var cell = _row.insertCell(-1);
  	cell.style.backgroundImage = 'url('+webimRoot+'/images/tablediv3.gif)';
  	cell.innerHTML = '<img src="'+webimRoot+'/images/free.gif" width="3" height="1" border="0" alt="">';
  },

  removeHr: function(_table, _index ) {
  	_table.deleteRow(_index+2);
  	_table.deleteRow(_index+1);
  	_table.deleteRow(_index);
  },

  insertHr: function(_table, _index) {
  	var row = _table.insertRow(_index);
  	var cell = row.insertCell(-1);
  	cell.colSpan = 13;
  	cell.height = 2;

	row = _table.insertRow(_index);
	cell = row.insertCell(-1);
  	cell.colSpan = 13;
  	cell.style.backgroundColor = '#E1E1E1';
  	cell.innerHTML = '<img src="'+webimRoot+'/images/free.gif" width="1" height="1" border="0" alt="">';

	row = _table.insertRow(_index);
	cell = row.insertCell(-1);
  	cell.colSpan = 13;
  	cell.height = 2;
  },

  popupLink: function(link, title, wndid, inner, width, height,linkclass) {
  	return '<a href="'+link+'"'+(linkclass != null ? ' class="'+linkclass+'"' : '')+' target="_blank" title="'+title+'" onclick="this.newWindow = window.open(\''+link+'\', \''+
  			wndid+'\', \'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width='+width+',height='+height+',resizable=1\');this.newWindow.focus();this.newWindow.opener=window;return false;">'+
  			inner+'</a>';
  },

  generateOneRowTable: function(content) {
  	return '<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr>' + content + '</tr></table>';
  },

  viewOpenCell: function(username,servlet,id,canview,canopen,ban,message,cantakenow) {
  		var cellsCount = 2;
  		var link = servlet+"?thread="+id;
 		var gen = '<td class="table" style="padding-left:0px; padding-right:0px;">';
		gen += HtmlGenerationUtils.popupLink( (cantakenow||!canview) ? link : link+"&viewonly=true", localized[canopen ? 0 : 1], "ImCenter"+id, username, 640, 480, ban);
		gen += '</td><td><img src="'+webimRoot+'/images/free.gif" width="5" height="1" border="0" alt=""></td>';
		if( canopen ) {
			gen += '<td width="30" align="center">';
			gen += HtmlGenerationUtils.popupLink( link, localized[0], "ImCenter"+id, '<img src="'+webimRoot+'/images/tbliclspeak.gif" width="15" height="15" border="0" alt="'+localized[0]+'">', 640, 480, null);
			gen += '</td>';
			cellsCount++;
		}
		if( canview ) {
			gen += '<td width="30" align="center">';
			gen += HtmlGenerationUtils.popupLink( link+"&viewonly=true", localized[1], "ImCenter"+id, '<img src="'+webimRoot+'/images/tbliclread.gif" width="15" height="15" border="0" alt="'+localized[1]+'">', 640, 480, null);
			gen += '</td>';
			cellsCount++;
		}
		if( message != "" ) {
			gen += '</tr><tr><td class="firstmessage" align="right" colspan="'+cellsCount+'"><a href="javascript:void(0)" title="'+message+'" onclick="alert(this.title);return false;">';
			gen += message.length > 30 ? message.substring(0,30) + '...' : message;
			gen += '</a></td>';
		}
  		return HtmlGenerationUtils.generateOneRowTable(gen);
  },
  banCell: function(id){
      return '<td width="30" align="center">'+
          HtmlGenerationUtils.popupLink( webimRoot+'/operator/ban.php?thread='+id, localized[2], "ban"+id, '<img src="'+webimRoot+'/images/ban.gif" width="15" height="15" border="0" alt="'+localized[2]+'">', 550, 440, null)+
          '</td>';
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
    this.periodicalUpdater = new Ajax.PeriodicalUpdater(this._options);
  },

  updateParams: function() {
  	return "company=" + this._options.company + "&since=" + this._options.lastrevision;
  },

  setStatus: function(msg) {
  	this._options.status.innerHTML = msg;
  },

  handleError: function(s) {
	this.setStatus( s );
  },

  updateThread: function(node) {
	var id, stateid, vstate, canview = false, canopen = false, ban = null;

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
		else if( attr.nodeName == "ban" )
			ban = attr.nodeValue;
	}

	function setcell(_table, row,id,pcontent) {
		var cell = CommonUtils.getCell( id, row, _table );
		if( cell )
			cell.innerHTML = pcontent;
	}

	var row = CommonUtils.getRow("thr"+id, this.t);
	if( stateid == "closed" ) {
		if( row ) {
			HtmlGenerationUtils.removeHr(this.t, row.rowIndex+1);
			this.t.deleteRow(row.rowIndex);
		}
		this.threadTimers[id] = null;
		return;
	}

	var vname = NodeUtils.getNodeValue(node,"name");
	var vaddr = NodeUtils.getNodeValue(node,"addr");
	var vtime = NodeUtils.getNodeValue(node,"time");
	var agent = NodeUtils.getNodeValue(node,"agent");
	var modified = NodeUtils.getNodeValue(node,"modified");
	var message = NodeUtils.getNodeValue(node,"message");
	var etc = '<td class="table">'+NodeUtils.getNodeValue(node,"useragent")+'</td>';

	if(ban != null) {
		etc = '<td class="table">'+NodeUtils.getNodeValue(node,"reason")+'</td>';
	}

	etc += HtmlGenerationUtils.banCell(id);
	etc = HtmlGenerationUtils.generateOneRowTable(etc);

	var startRow = CommonUtils.getRow(stateid, this.t);
	var endRow = CommonUtils.getRow(stateid+"end", this.t);

	if( row != null && (row.rowIndex <= startRow.rowIndex || row.rowIndex >= endRow.rowIndex ) ) {
		HtmlGenerationUtils.removeHr(this.t, row.rowIndex+1);
		this.t.deleteRow(row.rowIndex);
		this.threadTimers[id] = null;
		row = null;
	}
	if( row == null ) {
		row = this.t.insertRow(startRow.rowIndex+1);
		HtmlGenerationUtils.insertHr(this.t, startRow.rowIndex+2);
		row.id = "thr"+id;
		this.threadTimers[id] = new Array(vtime,modified,stateid);
		CommonUtils.insertCell(row, "name", "table", null, 30, HtmlGenerationUtils.viewOpenCell(vname,this._options.agentservl,id,canview,canopen,ban,message,stateid!='chat'));
		HtmlGenerationUtils.insertSplitter(row);
		CommonUtils.insertCell(row, "contid", "table", "center", null, vaddr );
		HtmlGenerationUtils.insertSplitter(row);
		CommonUtils.insertCell(row, "state", "table", "center", null, vstate );
		HtmlGenerationUtils.insertSplitter(row);
		CommonUtils.insertCell(row, "op", "table", "center", null, agent );
		HtmlGenerationUtils.insertSplitter(row);
		CommonUtils.insertCell(row, "time", "table", "center", null, this.getTimeSince(vtime) );
		HtmlGenerationUtils.insertSplitter(row);
		CommonUtils.insertCell(row, "wait", "table", "center", null, (stateid!='chat' ? this.getTimeSince(modified) : '-') );
		HtmlGenerationUtils.insertSplitter(row);
		CommonUtils.insertCell(row, "etc", "table", "center", null, etc );

		if( stateid == 'wait' || stateid == 'prio' )
			return true;
	} else {
		this.threadTimers[id] = new Array(vtime,modified,stateid);
		setcell(this.t, row,"name",HtmlGenerationUtils.viewOpenCell(vname,this._options.agentservl,id,canview,canopen,ban,message,stateid!='chat'));
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
  	function updateQueue(t,id,nclients) {
		var startRow = t.rows[id];
		var endRow = t.rows[id+"end"];
		if( startRow == null || endRow == null )
			return;
		var _status = endRow.cells["status"];
		if( _status == null )
			return;
		_status.innerHTML = (startRow.rowIndex + 1 == endRow.rowIndex) ? nclients : "";
		_status.height = (startRow.rowIndex + 1 == endRow.rowIndex) ? 30 : 10;
  	}

  	updateQueue(this.t, "wait", this._options.noclients);
  	updateQueue(this.t, "prio", this._options.noclients);
  	updateQueue(this.t, "chat", this._options.noclients);
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

  updateContent: function(root) {
	var newAdded = false;
	if( root.tagName == 'threads' ) {
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
		this.setStatus( "Up to date" );
		if( newAdded ) {
			playSound(webimRoot+'/sounds/new_user.wav');
			window.focus();
		}
	} else if( root.tagName == 'error' ) {
		this.setStatus(NodeUtils.getNodeValue(root,"descr") );
	} else {
		this.setStatus( "reconnecting" );
	}
  }
});

var webimRoot = "";

EventHelper.register(window, 'onload', function(){
  webimRoot = updaterOptions.wroot;
  new Ajax.ThreadListUpdater(({table:$("threadlist"),status:$("connstatus")}).extend(updaterOptions || {}));
});
