/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
Ajax.InviteUpdater=Class.create();
Class.inherit(Ajax.InviteUpdater,Ajax.Base,{initialize:function(a){this.setOptions(a);this._options.onComplete=this.requestComplete.bind(this);this._options.onException=this.handleException.bind(this);this._options.onTimeout=this.handleTimeout.bind(this);this._options.updateParams=this.updateParams.bind(this);this._options.handleError=this.handleError.bind(this);this._options.updateContent=this.updateContent.bind(this);this._options.timeout=5E3;this.frequency=this._options.frequency||2;this.updater=
{};this.update()},handleException:function(){this._options.handleError&&this._options.handleError("offline, reconnecting");this.stopUpdate();this.timer=setTimeout(this.update.bind(this),1E3)},handleTimeout:function(){this._options.handleError&&this._options.handleError("timeout, reconnecting");this.stopUpdate();this.timer=setTimeout(this.update.bind(this),1E3)},stopUpdate:function(){if(this.updater._options)this.updater._options.onComplete=void 0;clearTimeout(this.timer)},update:function(){if(this._options.updateParams)this._options.parameters=
this._options.updateParams();this.updater=new Ajax.Request(this._options.url,this._options)},requestComplete:function(a){try{var b=Ajax.getXml(a);b?(this._options.updateContent||Ajax.emptyFunction)(b):this._options.handleError&&this._options.handleError("reconnecting")}catch(c){}this.timer=setTimeout(this.update.bind(this),this.frequency*1E3)},updateParams:function(){return"visitor="+this._options.visitor},handleError:function(){},updateContent:function(a){if(a.tagName=="invitation"){var b=NodeUtils.getNodeValue(a,
"invited"),a=NodeUtils.getNodeValue(a,"threadid");if(b=="0")this.stopUpdate(),window.close();else if(a!="0")this.stopUpdate(),window.name="ImCenter"+a,window.location=this._options.agentservl+"?thread="+a}}});EventHelper.register(window,"onload",function(){new Ajax.InviteUpdater({}.extend(updaterOptions||{}))});
