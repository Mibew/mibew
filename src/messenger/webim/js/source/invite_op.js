/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

Ajax.InviteUpdater = Class.create();
Class.inherit( Ajax.InviteUpdater, Ajax.Base, {

  initialize: function(_options) {
    this.setOptions(_options);
    this._options.onComplete = this.requestComplete.bind(this);
    this._options.onException = this.handleException.bind(this);
    this._options.onTimeout = this.handleTimeout.bind(this);
    this._options.updateParams = this.updateParams.bind(this);
    this._options.handleError = this.handleError.bind(this);
    this._options.updateContent = this.updateContent.bind(this);
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
  },

  updateParams: function() {
  	return "visitor=" + this._options.visitor;
  },

  handleError: function(s) {
  },

  updateContent: function(root) {
	if( root.tagName == 'invitation' ) {
	    var invited = NodeUtils.getNodeValue(root, "invited");
	    var threadid = NodeUtils.getNodeValue(root, "threadid");
	    if (invited == "0") {
		this.stopUpdate();
		window.close();
	    }
	    else if (threadid != "0") {
		this.stopUpdate();
		window.name = 'ImCenter' + threadid;
		window.location=this._options.agentservl + '?thread=' + threadid;

	    }
	}
  }

});


EventHelper.register(window, 'onload', function(){
  new Ajax.InviteUpdater(({}).extend(updaterOptions || {}));
});
