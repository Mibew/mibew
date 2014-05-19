/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,e){b.Regions={};b.Popup={};b.Popup.open=function(a,c,d){c=c.replace(/[^A-z0-9_]+/g,"");a=window.open(a,c,d);a.focus();a.opener=window};b.Utils.updateTimers=function(a,c){a.find(c).each(function(){var d=e(this).data("timestamp");if(d){var a=Math.round((new Date).getTime()/1E3)-d,d=a%60,c=Math.floor(a/60)%60,a=Math.floor(a/3600),b=[];0<a&&b.push(a);b.push(10>c?"0"+c:c);b.push(10>d?"0"+d:d);e(this).html(b.join(":"))}})}})(Mibew,jQuery);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
MibewAPIUsersInteraction=function(){this.mandatoryArguments=function(){return{"*":{agentId:null,"return":{},references:{}},result:{errorCode:0}}};this.getReservedFunctionsNames=function(){return["result"]}};MibewAPIUsersInteraction.prototype=new MibewAPIInteraction;
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,b){a.Models.Agent=a.Models.User.extend({defaults:b.extend({},a.Models.User.prototype.defaults,{id:null,isAgent:!0,away:!1}),away:function(){this.setAvailability(!1)},available:function(){this.setAvailability(!0)},setAvailability:function(c){var b=this;a.Objects.server.callFunctions([{"function":c?"available":"away",arguments:{agentId:this.id,references:{},"return":{}}}],function(a){0==a.errorCode&&b.set({away:!c})},!0)}})})(Mibew,_);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,c){var b=[],f=a.Models.QueuedThread=a.Models.Thread.extend({defaults:c.extend({},a.Models.Thread.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",agentName:"",canOpen:!1,canView:!1,canBan:!1,ban:!1,totalTime:0,waitingTime:0,firstMessage:null}),initialize:function(){for(var e=[],b=f.getControls(),d=0,c=b.length;d<c;d++)e.push(new b[d]({thread:this}));this.set({controls:new a.Collections.Controls(e)})}},{addControl:function(a){b.push(a)},getControls:function(){return b}})})(Mibew,
_);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b){b.Models.StatusPanel=b.Models.Base.extend({defaults:{message:""},setStatus:function(a){this.set({message:a})},changeAgentStatus:function(){var a=b.Objects.Models.agent;a.get("away")?a.available():a.away()}})})(Mibew);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,c){var b=[],f=a.Models.Visitor=a.Models.User.extend({defaults:c.extend({},a.Models.User.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",firstTime:0,lastTime:0,invitations:0,chats:0,invitationInfo:!1}),initialize:function(){for(var e=[],b=f.getControls(),d=0,c=b.length;d<c;d++)e.push(new b[d]({visitor:this}));this.set({controls:new a.Collections.Controls(e)})}},{addControl:function(a){b.push(a)},getControls:function(){return b}})})(Mibew,_);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,c,d){b.Collections.Agents=c.Collection.extend({model:b.Models.Agent,comparator:function(a){return a.get("name")},initialize:function(){var a=b.Objects.Models.agent;b.Objects.server.callFunctionsPeriodically(function(){return[{"function":"updateOperators",arguments:{agentId:a.id,"return":{operators:"operators"},references:{}}}]},d.bind(this.updateOperators,this))},updateOperators:function(a){this.set(a.operators)}})})(Mibew,Backbone,_);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(c,e,f){c.Collections.Threads=e.Collection.extend({model:c.Models.QueuedThread,initialize:function(){this.revision=0;var a=this,b=c.Objects.Models.agent;c.Objects.server.callFunctionsPeriodically(function(){return[{"function":"currentTime",arguments:{agentId:b.id,"return":{time:"currentTime"},references:{}}},{"function":"updateThreads",arguments:{agentId:b.id,revision:a.revision,"return":{threads:"threads",lastRevision:"lastRevision"},references:{}}}]},f.bind(this.updateThreads,this))},comparator:function(a){var b=
{field:a.get("waitingTime").toString()};this.trigger("sort:field",a,b);return b.field},updateThreads:function(a){if(0==a.errorCode){if(0<a.threads.length){var b;b=a.currentTime?Math.round((new Date).getTime()/1E3)-a.currentTime:0;for(var d=0,e=a.threads.length;d<e;d++)a.threads[d].totalTime=parseInt(a.threads[d].totalTime)+b,a.threads[d].waitingTime=parseInt(a.threads[d].waitingTime)+b;this.trigger("before:update:threads",a.threads);var f=c.Models.Thread.prototype.STATE_CLOSED,g=c.Models.Thread.prototype.STATE_LEFT;
b=[];this.set(a.threads,{remove:!1,sort:!1});b=this.filter(function(a){return a.get("state")==f||a.get("state")==g});0<b.length&&this.remove(b);this.sort();this.trigger("after:update:threads")}this.revision=a.lastRevision}}})})(Mibew,Backbone,_);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,e,f){b.Collections.Visitors=e.Collection.extend({model:b.Models.Visitor,initialize:function(){var a=b.Objects.Models.agent;b.Objects.server.callFunctionsPeriodically(function(){return[{"function":"currentTime",arguments:{agentId:a.id,"return":{time:"currentTime"},references:{}}},{"function":"updateVisitors",arguments:{agentId:a.id,"return":{visitors:"visitors"},references:{}}}]},f.bind(this.updateVisitors,this))},comparator:function(a){var c={field:a.get("firstTime").toString()};this.trigger("sort:field",
a,c);return c.field},updateVisitors:function(a){if(0==a.errorCode){var c;c=a.currentTime?Math.round((new Date).getTime()/1E3)-a.currentTime:0;for(var d=0,b=a.visitors.length;d<b;d++)a.visitors[d].lastTime=parseInt(a.visitors[d].lastTime)+c,a.visitors[d].firstTime=parseInt(a.visitors[d].firstTime)+c;this.trigger("before:update:visitors",a.visitors);this.reset(a.visitors);this.trigger("after:update:visitors")}}})})(Mibew,Backbone,_);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,c,d){b.Views.Agent=c.Marionette.ItemView.extend({template:d.templates.agent,tagName:"span",className:"agent",modelEvents:{change:"render"},initialize:function(){this.isModelLast=this.isModelFirst=!1},serializeData:function(){var a=this.model.toJSON();a.isFirst=this.isModelFirst;a.isLast=this.isModelLast;return a}})})(Mibew,Backbone,Handlebars);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,b,c){a.Views.NoThreads=b.Marionette.ItemView.extend({template:c.templates.no_threads,initialize:function(a){this.tagName=a.tagName}})})(Mibew,Backbone,Handlebars);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,b,c){a.Views.NoVisitors=b.Marionette.ItemView.extend({template:c.templates.no_visitors,initialize:function(a){this.tagName=a.tagName}})})(Mibew,Backbone,Handlebars);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(d,e){d.Views.QueuedThread=d.Views.CompositeBase.extend({template:e.templates.queued_thread,itemView:d.Views.Control,itemViewContainer:".thread-controls",className:"thread",modelEvents:{change:"render"},events:{"click .open-dialog":"openDialog","click .view-control":"viewDialog","click .track-control":"showTrack","click .ban-control":"showBan","click .geo-link":"showGeoInfo","click .first-message a":"showFirstMessage"},initialize:function(){this.lastStyles=[]},serializeData:function(){var a=
this.model,b=d.Objects.Models.page,c=a.toJSON();c.stateDesc=this.stateToDesc(a.get("state"));c.chatting=a.get("state")==a.STATE_CHATTING;c.tracked=b.get("showVisitors");c.firstMessage&&(c.firstMessagePreview=30<c.firstMessage.length?c.firstMessage.substring(0,30)+"...":c.firstMessage);return c},stateToDesc:function(a){var b=d.Localization;return a==this.model.STATE_QUEUE?b.get("chat.thread.state_wait"):a==this.model.STATE_WAITING?b.get("chat.thread.state_wait_for_another_agent"):a==this.model.STATE_CHATTING?
b.get("chat.thread.state_chatting_with_agent"):a==this.model.STATE_CLOSED?b.get("chat.thread.state_closed"):a==this.model.STATE_LOADING?b.get("chat.thread.state_loading"):""},showGeoInfo:function(){var a=this.model.get("userIp");if(a){var b=d.Objects.Models.page,c=b.get("geoLink").replace("{ip}",a);d.Popup.open(c,"ip"+a,b.get("geoWindowParams"))}},openDialog:function(){var a=this.model;if(a.get("canOpen")||a.get("canView"))a=!a.get("canOpen"),this.showDialogWindow(a)},viewDialog:function(){this.showDialogWindow(!0)},
showDialogWindow:function(a){var b=this.model.id,c=d.Objects.Models.page;d.Popup.open(c.get("agentLink")+"?thread="+b+(a?"&viewonly=true":""),"ImCenter"+b,c.get("chatWindowParams"))},showTrack:function(){var a=this.model.id,b=d.Objects.Models.page;d.Popup.open(b.get("trackedLink")+"?thread="+a,"ImTracked"+a,b.get("trackedUserWindowParams"))},showBan:function(){var a=this.model,b=a.get("ban"),c=d.Objects.Models.page;d.Popup.open(c.get("banLink")+"?"+(!1!==b?"id="+b.id:"thread="+a.id),"ImBan"+b.id,
c.get("banWindowParams"))},showFirstMessage:function(){var a=this.model.get("firstMessage");a&&alert(a)}})})(Mibew,Handlebars);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,c,d){a.Views.StatusPanel=c.Marionette.ItemView.extend({template:d.templates.status_panel,modelEvents:{change:"render"},ui:{changeStatus:"#change-status"},events:{"click #change-status":"changeAgentStatus"},initialize:function(){a.Objects.Models.agent.on("change",this.render,this)},changeAgentStatus:function(){this.model.changeAgentStatus()},serializeData:function(){var b=this.model.toJSON();b.agent=a.Objects.Models.agent.toJSON();return b}})})(Mibew,Backbone,Handlebars);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,d){a.Views.Visitor=a.Views.CompositeBase.extend({template:d.templates.visitor,itemView:a.Views.Control,itemViewContainer:".visitor-controls",className:"visitor",modelEvents:{change:"render"},events:{"click .invite-link":"inviteUser","click .geo-link":"showGeoInfo","click .track-control":"showTrack"},inviteUser:function(){if(!this.model.get("invitationInfo")){var b=this.model.id,c=a.Objects.Models.page;a.Popup.open(c.get("inviteLink")+"?visitor="+b,"ImCenter"+b,c.get("inviteWindowParams"))}},
showTrack:function(){var b=this.model.id,c=a.Objects.Models.page;a.Popup.open(c.get("trackedLink")+"?visitor="+b,"ImTracked"+b,c.get("trackedVisitorWindowParams"))},showGeoInfo:function(){var b=this.model.get("userIp");if(b){var c=a.Objects.Models.page,d=c.get("geoLink").replace("{ip}",b);a.Popup.open(d,"ip"+b,c.get("geoWindowParams"))}}})})(Mibew,Handlebars);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a){a.Views.AgentsCollection=a.Views.CollectionBase.extend({itemView:a.Views.Agent,className:"agents-collection",collectionEvents:{"sort add remove reset":"render"},initialize:function(){this.on("itemview:before:render",this.updateIndexes,this)},updateIndexes:function(a){var b=this.collection,c=a.model;c&&(a.isModelFirst=0==b.indexOf(c),a.isModelLast=b.indexOf(c)==b.length-1)}})})(Mibew);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(d,h,k){d.Views.ThreadsCollection=d.Views.CompositeBase.extend({template:h.templates.threads_collection,itemView:d.Views.QueuedThread,itemViewContainer:"#threads-container",emptyView:d.Views.NoThreads,className:"threads-collection",collectionEvents:{sort:"render","sort:field":"createSortField",add:"threadAdded"},itemViewOptions:function(a){return{tagName:d.Objects.Models.page.get("threadTag"),collection:a.get("controls")}},initialize:function(){window.setInterval(k.bind(this.updateTimers,
this),2E3);this.on("itemview:before:render",this.updateStyles,this);this.on("composite:collection:rendered",this.updateTimers,this)},updateStyles:function(a){var b=this.collection,c=a.model,d=this;if(c.id){var e=this.getQueueCode(c),f=!1,g=!1,b=b.filter(function(a){return d.getQueueCode(a)==e});0<b.length&&(g=b[0].id==c.id,f=b[b.length-1].id==c.id);if(0<a.lastStyles.length){c=0;for(b=a.lastStyles.length;c<b;c++)a.$el.removeClass(a.lastStyles[c]);a.lastStyles=[]}c=(e!=this.QUEUE_BAN?"in":"")+this.queueCodeToString(e);
a.lastStyles.push(c);g&&a.lastStyles.push(c+"-first");f&&a.lastStyles.push(c+"-last");c=0;for(b=a.lastStyles.length;c<b;c++)a.$el.addClass(a.lastStyles[c])}},updateTimers:function(){d.Utils.updateTimers(this.$el,".timesince")},createSortField:function(a,b){var c=this.getQueueCode(a)||"Z";b.field=c.toString()+"_"+a.get("waitingTime").toString()},threadAdded:function(){var a=d.Objects.Models.page.get("mibewRoot");"undefined"!==typeof a&&d.Utils.playSound(a+"/sounds/new_user");if(d.Objects.Models.page.get("showPopup"))this.once("render",
function(){alert(d.Localization.get("pending.popup_notification"))})},getQueueCode:function(a){var b=a.get("state");return!1!=a.get("ban")&&b!=a.STATE_CHATTING?this.QUEUE_BAN:b==a.STATE_QUEUE||b==a.STATE_LOADING?this.QUEUE_WAITING:b==a.STATE_CLOSED||b==a.STATE_LEFT?this.QUEUE_CLOSED:b==a.STATE_WAITING?this.QUEUE_PRIO:b==a.STATE_CHATTING?this.QUEUE_CHATTING:!1},queueCodeToString:function(a){return a==this.QUEUE_PRIO?"prio":a==this.QUEUE_WAITING?"wait":a==this.QUEUE_CHATTING?"chat":a==this.QUEUE_BAN?
"ban":a==this.QUEUE_CLOSED?"closed":""},QUEUE_PRIO:1,QUEUE_WAITING:2,QUEUE_CHATTING:3,QUEUE_BAN:4,QUEUE_CLOSED:5})})(Mibew,Handlebars,_);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,b,c){a.Views.VisitorsCollection=a.Views.CompositeBase.extend({template:b.templates.visitors_collection,itemView:a.Views.Visitor,itemViewContainer:"#visitors-container",emptyView:a.Views.NoVisitors,className:"visitors-collection",collectionEvents:{sort:"render"},itemViewOptions:function(b){return{tagName:a.Objects.Models.page.get("visitorTag"),collection:b.get("controls")}},initialize:function(){window.setInterval(c.bind(this.updateTimers,this),2E3);this.on("composite:collection:rendered",
this.updateTimers,this)},updateTimers:function(){a.Utils.updateTimers(this.$el,".timesince")}})})(Mibew,Handlebars,_);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,h,j){var c=0,g=function(){c++;10==c&&(alert(a.Localization.get("pending.errors.network")),c=0)},b=new h.Marionette.Application;b.addRegions({agentsRegion:"#agents-region",statusPanelRegion:"#status-panel-region",threadsRegion:"#threads-region",visitorsRegion:"#visitors-region"});b.addInitializer(function(d){a.PluginOptions=d.plugins||{};var c=a.Objects,f=a.Objects.Models,e=a.Objects.Collections;c.server=new a.Server(j.extend({interactionType:MibewAPIUsersInteraction,onTimeout:g,onTransportError:g},
d.server));f.page=new a.Models.Page(d.page);f.agent=new a.Models.Agent(d.agent);e.threads=new a.Collections.Threads;b.threadsRegion.show(new a.Views.ThreadsCollection({collection:e.threads}));d.page.showVisitors&&(e.visitors=new a.Collections.Visitors,b.visitorsRegion.show(new a.Views.VisitorsCollection({collection:e.visitors})));f.statusPanel=new a.Models.StatusPanel;b.statusPanelRegion.show(new a.Views.StatusPanel({model:f.statusPanel}));d.page.showOnlineOperators&&(e.agents=new a.Collections.Agents,
b.agentsRegion.show(new a.Views.AgentsCollection({collection:e.agents})));c.server.callFunctionsPeriodically(function(){return[{"function":"update",arguments:{"return":{},references:{},agentId:f.agent.id}}]},function(){})});b.on("start",function(){a.Objects.server.runUpdater()});a.Application=b})(Mibew,Backbone,_);
