/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t){e.Regions={},e.Popup={},e.Popup.open=function(e,t,i){t=t.replace(/[^A-z0-9_]+/g,"");var n=window.open(e,t,i);n.focus(),n.opener=window},e.Utils.updateTimers=function(e,i){e.find(i).each(function(){var e=t(this).data("timestamp");if(e){var i=Math.round((new Date).getTime()/1e3)-e,n=i%60,s=Math.floor(i/60)%60,o=Math.floor(i/3600),a=[];o>0&&a.push(o),a.push(10>s?"0"+s:s),a.push(10>n?"0"+n:n),t(this).html(a.join(":"))}})}}(Mibew,jQuery),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
MibewAPIUsersInteraction=function(){this.mandatoryArguments=function(){return{"*":{agentId:null,"return":{},references:{}},result:{errorCode:0}}},this.getReservedFunctionsNames=function(){return["result"]}},MibewAPIUsersInteraction.prototype=new MibewAPIInteraction,/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.Agent=e.Models.User.extend({defaults:t.extend({},e.Models.User.prototype.defaults,{id:null,isAgent:!0,away:!1}),away:function(){this.setAvailability(!1)},available:function(){this.setAvailability(!0)},setAvailability:function(t){var i=t?"available":"away",n=this;e.Objects.server.callFunctions([{"function":i,arguments:{agentId:this.id,references:{},"return":{}}}],function(e){0==e.errorCode&&n.set({away:!t})},!0)}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){var i=[],n=e.Models.QueuedThread=e.Models.Thread.extend({defaults:t.extend({},e.Models.Thread.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",agentName:"",canOpen:!1,canView:!1,canBan:!1,ban:!1,totalTime:0,waitingTime:0,firstMessage:null}),initialize:function(){for(var t=this,i=[],s=n.getControls(),o=0,a=s.length;a>o;o++)i.push(new s[o]({thread:t}));this.set({controls:new e.Collections.Controls(i)})}},{addControl:function(e){i.push(e)},getControls:function(){return i}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.StatusPanel=e.Models.Base.extend({defaults:{message:""},setStatus:function(e){this.set({message:e})},changeAgentStatus:function(){var t=e.Objects.Models.agent;t.get("away")?t.available():t.away()}})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){var i=[],n=e.Models.Visitor=e.Models.User.extend({defaults:t.extend({},e.Models.User.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",firstTime:0,lastTime:0,invitations:0,chats:0,invitationInfo:!1}),initialize:function(){for(var t=this,i=[],s=n.getControls(),o=0,a=s.length;a>o;o++)i.push(new s[o]({visitor:t}));this.set({controls:new e.Collections.Controls(i)})}},{addControl:function(e){i.push(e)},getControls:function(){return i}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){e.Collections.Agents=t.Collection.extend({model:e.Models.Agent,comparator:function(e){return e.get("name")},initialize:function(){var t=e.Objects.Models.agent;e.Objects.server.callFunctionsPeriodically(function(){return[{"function":"updateOperators",arguments:{agentId:t.id,"return":{operators:"operators"},references:{}}}]},i.bind(this.updateOperators,this))},updateOperators:function(e){this.set(e.operators)}})}(Mibew,Backbone,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){e.Collections.Threads=t.Collection.extend({model:e.Models.QueuedThread,initialize:function(){this.revision=0;var t=this,n=e.Objects.Models.agent;e.Objects.server.callFunctionsPeriodically(function(){return[{"function":"currentTime",arguments:{agentId:n.id,"return":{time:"currentTime"},references:{}}},{"function":"updateThreads",arguments:{agentId:n.id,revision:t.revision,"return":{threads:"threads",lastRevision:"lastRevision"},references:{}}}]},i.bind(this.updateThreads,this))},comparator:function(e){var t={field:e.get("waitingTime").toString()};return this.trigger("sort:field",e,t),t.field},updateThreads:function(t){if(0==t.errorCode){if(t.threads.length>0){var i;i=t.currentTime?Math.round((new Date).getTime()/1e3)-t.currentTime:0;for(var n=0,s=t.threads.length;s>n;n++)t.threads[n].totalTime=parseInt(t.threads[n].totalTime)+i,t.threads[n].waitingTime=parseInt(t.threads[n].waitingTime)+i;this.trigger("before:update:threads",t.threads);var o=e.Models.Thread.prototype.STATE_CLOSED,a=e.Models.Thread.prototype.STATE_LEFT,r=[];this.set(t.threads,{remove:!1,sort:!1}),r=this.filter(function(e){return e.get("state")==o||e.get("state")==a}),r.length>0&&this.remove(r),this.sort(),this.trigger("after:update:threads")}this.revision=t.lastRevision}}})}(Mibew,Backbone,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){e.Collections.Visitors=t.Collection.extend({model:e.Models.Visitor,initialize:function(){var t=e.Objects.Models.agent;e.Objects.server.callFunctionsPeriodically(function(){return[{"function":"currentTime",arguments:{agentId:t.id,"return":{time:"currentTime"},references:{}}},{"function":"updateVisitors",arguments:{agentId:t.id,"return":{visitors:"visitors"},references:{}}}]},i.bind(this.updateVisitors,this))},comparator:function(e){var t={field:e.get("firstTime").toString()};return this.trigger("sort:field",e,t),t.field},updateVisitors:function(e){if(0==e.errorCode){var t;t=e.currentTime?Math.round((new Date).getTime()/1e3)-e.currentTime:0;for(var i=0,n=e.visitors.length;n>i;i++)e.visitors[i].lastTime=parseInt(e.visitors[i].lastTime)+t,e.visitors[i].firstTime=parseInt(e.visitors[i].firstTime)+t;this.trigger("before:update:visitors",e.visitors),this.reset(e.visitors),this.trigger("after:update:visitors")}}})}(Mibew,Backbone,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){e.Views.Agent=t.Marionette.ItemView.extend({template:i.templates.agent,tagName:"span",className:"agent",modelEvents:{change:"render"},initialize:function(){this.isModelFirst=!1,this.isModelLast=!1},serializeData:function(){var e=this.model.toJSON();return e.isFirst=this.isModelFirst,e.isLast=this.isModelLast,e}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){e.Views.NoThreads=t.Marionette.ItemView.extend({template:i.templates.no_threads,initialize:function(e){this.tagName=e.tagName}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){e.Views.NoVisitors=t.Marionette.ItemView.extend({template:i.templates.no_visitors,initialize:function(e){this.tagName=e.tagName}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Views.QueuedThread=e.Views.CompositeBase.extend({template:t.templates.queued_thread,itemView:e.Views.Control,itemViewContainer:".thread-controls",className:"thread",modelEvents:{change:"render"},events:{"click .open-dialog":"openDialog","click .view-control":"viewDialog","click .track-control":"showTrack","click .ban-control":"showBan","click .geo-link":"showGeoInfo","click .first-message a":"showFirstMessage"},initialize:function(){this.lastStyles=[]},serializeData:function(){var t=this.model,i=e.Objects.Models.page,n=t.toJSON();return n.stateDesc=this.stateToDesc(t.get("state")),n.chatting=t.get("state")==t.STATE_CHATTING,n.tracked=i.get("showVisitors"),n.firstMessage&&(n.firstMessagePreview=n.firstMessage.length>30?n.firstMessage.substring(0,30)+"...":n.firstMessage),n},stateToDesc:function(t){var i=e.Localization;return t==this.model.STATE_QUEUE?i.get("In queue"):t==this.model.STATE_WAITING?i.get("Waiting for operator"):t==this.model.STATE_CHATTING?i.get("In chat"):t==this.model.STATE_CLOSED?i.get("Closed"):t==this.model.STATE_LOADING?i.get("Loading"):""},showGeoInfo:function(){var t=this.model.get("userIp");if(t){var i=e.Objects.Models.page,n=i.get("geoLink").replace("{ip}",t);e.Popup.open(n,"ip"+t,i.get("geoWindowParams"))}},openDialog:function(){var e=this.model;if(e.get("canOpen")||e.get("canView")){var t=!e.get("canOpen");this.showDialogWindow(t)}},viewDialog:function(){this.showDialogWindow(!0)},showDialogWindow:function(t){var i=this.model,n=i.id,s=e.Objects.Models.page;e.Popup.open(s.get("agentLink")+"/"+n+(t?"?viewonly=true":""),"ImCenter"+n,s.get("chatWindowParams"))},showTrack:function(){var t=this.model.id,i=e.Objects.Models.page;e.Popup.open(i.get("trackedLink")+"?thread="+t,"ImTracked"+t,i.get("trackedUserWindowParams"))},showBan:function(){var t=this.model,i=t.get("ban"),n=e.Objects.Models.page;e.Popup.open(n.get("banLink")+"/"+(i!==!1?i.id+"/edit":"add?thread="+t.id),"ImBan"+i.id,n.get("banWindowParams"))},showFirstMessage:function(){var e=this.model.get("firstMessage");e&&alert(e)}})}(Mibew,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){e.Views.StatusPanel=t.Marionette.ItemView.extend({template:i.templates.status_panel,modelEvents:{change:"render"},ui:{changeStatus:"#change-status"},events:{"click #change-status":"changeAgentStatus"},initialize:function(){e.Objects.Models.agent.on("change",this.render,this)},changeAgentStatus:function(){this.model.changeAgentStatus()},serializeData:function(){var t=this.model.toJSON();return t.agent=e.Objects.Models.agent.toJSON(),t}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Views.Visitor=e.Views.CompositeBase.extend({template:t.templates.visitor,itemView:e.Views.Control,itemViewContainer:".visitor-controls",className:"visitor",modelEvents:{change:"render"},events:{"click .invite-link":"inviteUser","click .geo-link":"showGeoInfo","click .track-control":"showTrack"},inviteUser:function(){if(!this.model.get("invitationInfo")){var t=this.model.id,i=e.Objects.Models.page;e.Popup.open(i.get("inviteLink")+"?visitor="+t,"ImCenter"+t,i.get("inviteWindowParams"))}},showTrack:function(){var t=this.model.id,i=e.Objects.Models.page;e.Popup.open(i.get("trackedLink")+"?visitor="+t,"ImTracked"+t,i.get("trackedVisitorWindowParams"))},showGeoInfo:function(){var t=this.model.get("userIp");if(t){var i=e.Objects.Models.page,n=i.get("geoLink").replace("{ip}",t);e.Popup.open(n,"ip"+t,i.get("geoWindowParams"))}}})}(Mibew,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Views.AgentsCollection=e.Views.CollectionBase.extend({itemView:e.Views.Agent,className:"agents-collection",collectionEvents:{"sort add remove reset":"render"},initialize:function(){this.on("itemview:before:render",this.updateIndexes,this)},updateIndexes:function(e){var t=this.collection,i=e.model;i&&(e.isModelFirst=0==t.indexOf(i),e.isModelLast=t.indexOf(i)==t.length-1)}})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){e.Views.ThreadsCollection=e.Views.CompositeBase.extend({template:t.templates.threads_collection,itemView:e.Views.QueuedThread,itemViewContainer:"#threads-container",emptyView:e.Views.NoThreads,className:"threads-collection",collectionEvents:{sort:"render","sort:field":"createSortField",add:"threadAdded"},itemViewOptions:function(t){var i=e.Objects.Models.page;return{tagName:i.get("threadTag"),collection:t.get("controls")}},initialize:function(){window.setInterval(i.bind(this.updateTimers,this),2e3),this.on("itemview:before:render",this.updateStyles,this),this.on("composite:collection:rendered",this.updateTimers,this)},updateStyles:function(e){var t=this.collection,i=e.model,n=this;if(i.id){var s=this.getQueueCode(i),o=!1,a=!1,r=t.filter(function(e){return n.getQueueCode(e)==s});if(r.length>0&&(a=r[0].id==i.id,o=r[r.length-1].id==i.id),e.lastStyles.length>0){for(var l=0,d=e.lastStyles.length;d>l;l++)e.$el.removeClass(e.lastStyles[l]);e.lastStyles=[]}var c=(s!=this.QUEUE_BAN?"in":"")+this.queueCodeToString(s);e.lastStyles.push(c),a&&e.lastStyles.push(c+"-first"),o&&e.lastStyles.push(c+"-last");for(var l=0,d=e.lastStyles.length;d>l;l++)e.$el.addClass(e.lastStyles[l])}},updateTimers:function(){e.Utils.updateTimers(this.$el,".timesince")},createSortField:function(e,t){var i=this.getQueueCode(e)||"Z";t.field=i.toString()+"_"+e.get("waitingTime").toString()},threadAdded:function(){var t=e.Objects.Models.page.get("mibewRoot");"undefined"!=typeof t&&(t+="/sounds/new_user",e.Utils.playSound(t)),e.Objects.Models.page.get("showPopup")&&this.once("render",function(){alert(e.Localization.get("A new visitor is waiting for an answer."))})},getQueueCode:function(e){var t=e.get("state");return 0!=e.get("ban")&&t!=e.STATE_CHATTING?this.QUEUE_BAN:t==e.STATE_QUEUE||t==e.STATE_LOADING?this.QUEUE_WAITING:t==e.STATE_CLOSED||t==e.STATE_LEFT?this.QUEUE_CLOSED:t==e.STATE_WAITING?this.QUEUE_PRIO:t==e.STATE_CHATTING?this.QUEUE_CHATTING:!1},queueCodeToString:function(e){return e==this.QUEUE_PRIO?"prio":e==this.QUEUE_WAITING?"wait":e==this.QUEUE_CHATTING?"chat":e==this.QUEUE_BAN?"ban":e==this.QUEUE_CLOSED?"closed":""},QUEUE_PRIO:1,QUEUE_WAITING:2,QUEUE_CHATTING:3,QUEUE_BAN:4,QUEUE_CLOSED:5})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){e.Views.VisitorsCollection=e.Views.CompositeBase.extend({template:t.templates.visitors_collection,itemView:e.Views.Visitor,itemViewContainer:"#visitors-container",emptyView:e.Views.NoVisitors,className:"visitors-collection",collectionEvents:{sort:"render"},itemViewOptions:function(t){var i=e.Objects.Models.page;return{tagName:i.get("visitorTag"),collection:t.get("controls")}},initialize:function(){window.setInterval(i.bind(this.updateTimers,this),2e3),this.on("composite:collection:rendered",this.updateTimers,this)},updateTimers:function(){e.Utils.updateTimers(this.$el,".timesince")}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,i){var n=0,s=function(){n++,10==n&&(alert(e.Localization.get("Network problems detected. Please refresh the page.")),n=0)},o=new t.Marionette.Application;o.addRegions({agentsRegion:"#agents-region",statusPanelRegion:"#status-panel-region",threadsRegion:"#threads-region",visitorsRegion:"#visitors-region"}),o.addInitializer(function(t){e.PluginOptions=t.plugins||{};var n=e.Objects,a=e.Objects.Models,r=e.Objects.Collections;n.server=new e.Server(i.extend({interactionType:MibewAPIUsersInteraction,onTimeout:s,onTransportError:s},t.server)),a.page=new e.Models.Page(t.page),a.agent=new e.Models.Agent(t.agent),r.threads=new e.Collections.Threads,o.threadsRegion.show(new e.Views.ThreadsCollection({collection:r.threads})),t.page.showVisitors&&(r.visitors=new e.Collections.Visitors,o.visitorsRegion.show(new e.Views.VisitorsCollection({collection:r.visitors}))),a.statusPanel=new e.Models.StatusPanel,o.statusPanelRegion.show(new e.Views.StatusPanel({model:a.statusPanel})),t.page.showOnlineOperators&&(r.agents=new e.Collections.Agents,o.agentsRegion.show(new e.Views.AgentsCollection({collection:r.agents}))),n.server.callFunctionsPeriodically(function(){return[{"function":"update",arguments:{"return":{},references:{},agentId:a.agent.id}}]},function(){})}),o.on("start",function(){e.Objects.server.runUpdater()}),e.Application=o}(Mibew,Backbone,_);