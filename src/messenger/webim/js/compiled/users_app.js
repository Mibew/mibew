/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(){var a=Handlebars.template,b=Handlebars.templates=Handlebars.templates||{};b.agent=a(function(a,b,c,d,e){function l(a,b){return"away"}function m(a,b){return"online"}function n(a,b){var d,e;return e={hash:{},data:b},i((d=c.L10n,d?d.call(a,"pending.status.away",e):h.call(a,"L10n","pending.status.away",e)))}function o(a,b){var d,e;return e={hash:{},data:b},i((d=c.L10n,d?d.call(a,"pending.status.online",e):h.call(a,"L10n","pending.status.online",e)))}function p(a,b){return","}this.compilerInfo=[2,">= 1.0.0-rc.3"],c=c||a.helpers,e=e||{};var f="",g,h=c.helperMissing,i=this.escapeExpression,j=this,k="function";f+='<span class="agent-status-',g=c["if"].call(b,b.away,{hash:{},inverse:j.program(3,m,e),fn:j.program(1,l,e),data:e});if(g||g===0)f+=g;f+=' inline-block" title="',g=c["if"].call(b,b.away,{hash:{},inverse:j.program(7,o,e),fn:j.program(5,n,e),data:e});if(g||g===0)f+=g;f+='"></span>',(g=c.name)?g=g.call(b,{hash:{},data:e}):(g=b.name,g=typeof g===k?g.apply(b):g),f+=i(g),g=c.unless.call(b,b.isLast,{hash:{},inverse:j.noop,fn:j.program(9,p,e),data:e});if(g||g===0)f+=g;return f}),b.no_threads=a(function(a,b,c,d,e){this.compilerInfo=[2,">= 1.0.0-rc.3"],c=c||a.helpers,e=e||{};var f="",g,h,i=c.helperMissing,j=this.escapeExpression;return f+='<td class="no-threads" colspan="8">',h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"clients.no_clients",h):i.call(b,"L10n","clients.no_clients",h)))+"</td>",f}),b.no_visitors=a(function(a,b,c,d,e){this.compilerInfo=[2,">= 1.0.0-rc.3"],c=c||a.helpers,e=e||{};var f="",g,h,i=c.helperMissing,j=this.escapeExpression;return f+='<td class="no-visitors" colspan="9">',h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.no_visitors",h):i.call(b,"L10n","visitors.no_visitors",h)))+"</td>",f}),b.status_panel=a(function(a,b,c,d,e){function m(a,b){var d,e;return e={hash:{},data:b},j((d=c.L10n,d?d.call(a,"pending.status.away",e):i.call(a,"L10n","pending.status.away",e)))}function n(a,b){var d,e;return e={hash:{},data:b},j((d=c.L10n,d?d.call(a,"pending.status.online",e):i.call(a,"L10n","pending.status.online",e)))}function o(a,b){var d,e;return e={hash:{},data:b},j((d=c.L10n,d?d.call(a,"pending.status.setonline",e):i.call(a,"L10n","pending.status.setonline",e)))}function p(a,b){var d,e;return e={hash:{},data:b},j((d=c.L10n,d?d.call(a,"pending.status.setaway",e):i.call(a,"L10n","pending.status.setaway",e)))}this.compilerInfo=[2,">= 1.0.0-rc.3"],c=c||a.helpers,e=e||{};var f="",g,h,i=c.helperMissing,j=this.escapeExpression,k="function",l=this;f+='<div id="connstatus">',(g=c.message)?g=g.call(b,{hash:{},data:e}):(g=b.message,g=typeof g===k?g.apply(b):g),f+=j(g),h=c["if"].call(b,(g=b.agent,g==null||g===!1?g:g.away),{hash:{},inverse:l.program(3,n,e),fn:l.program(1,m,e),data:e});if(h||h===0)f+=h;f+='</div><div id="connlinks"><a href="javascript:void(0);" id="change-status">',h=c["if"].call(b,(g=b.agent,g==null||g===!1?g:g.away),{hash:{},inverse:l.program(7,p,e),fn:l.program(5,o,e),data:e});if(h||h===0)f+=h;return f+="</a></div>",f}),b.threads_collection=a(function(a,b,c,d,e){this.compilerInfo=[2,">= 1.0.0-rc.3"],c=c||a.helpers,e=e||{};var f="",g,h,i=c.helperMissing,j=this.escapeExpression;return f+='<table class="awaiting" border="0">\n<thead>\n<tr>\n    <th class="first">',h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"pending.table.head.name",h):i.call(b,"L10n","pending.table.head.name",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"pending.table.head.actions",h):i.call(b,"L10n","pending.table.head.actions",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"pending.table.head.contactid",h):i.call(b,"L10n","pending.table.head.contactid",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"pending.table.head.state",h):i.call(b,"L10n","pending.table.head.state",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"pending.table.head.operator",h):i.call(b,"L10n","pending.table.head.operator",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"pending.table.head.total",h):i.call(b,"L10n","pending.table.head.total",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"pending.table.head.waittime",h):i.call(b,"L10n","pending.table.head.waittime",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"pending.table.head.etc",h):i.call(b,"L10n","pending.table.head.etc",h)))+'</th>\n</tr>\n</thead>\n<tbody id="threads-container">\n\n</tbody>\n</table>',f}),b.visitors_collection=a(function(a,b,c,d,e){this.compilerInfo=[2,">= 1.0.0-rc.3"],c=c||a.helpers,e=e||{};var f="",g,h,i=c.helperMissing,j=this.escapeExpression;return f+='<table id="visitorslist" class="awaiting" border="0">\n<thead>\n<tr>\n    <th class="first">',h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.table.head.name",h):i.call(b,"L10n","visitors.table.head.name",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.table.head.actions",h):i.call(b,"L10n","visitors.table.head.actions",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.table.head.contactid",h):i.call(b,"L10n","visitors.table.head.contactid",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.table.head.firsttimeonsite",h):i.call(b,"L10n","visitors.table.head.firsttimeonsite",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.table.head.lasttimeonsite",h):i.call(b,"L10n","visitors.table.head.lasttimeonsite",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.table.head.invited.by",h):i.call(b,"L10n","visitors.table.head.invited.by",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.table.head.invitationtime",h):i.call(b,"L10n","visitors.table.head.invitationtime",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.table.head.invitations",h):i.call(b,"L10n","visitors.table.head.invitations",h)))+"</th>\n    <th>",h={hash:{},data:e},f+=j((g=c.L10n,g?g.call(b,"visitors.table.head.etc",h):i.call(b,"L10n","visitors.table.head.etc",h)))+'</th>\n</tr>\n</thead>\n<tbody id="visitors-container">\n</tbody>\n</table>',f}),b.visitor=a(function(a,b,c,d,e){function n(a,b){var d="",e,f,g;return d+='<a href="javascript:void(0);" class="invite-link" title="',g={hash:{},data:b},d+=k((e=c.L10n,e?e.call(a,"pending.table.invite",g):j.call(a,"L10n","pending.table.invite",g)))+'">',(f=c.userName)?f=f.call(a,{hash:{},data:b}):(f=a.userName,f=typeof f===l?f.apply(a):f),d+=k(f)+"</a>",d}function o(a,b){var d;return(d=c.userName)?d=d.call(a,{hash:{},data:b}):(d=a.userName,d=typeof d===l?d.apply(a):d),k(d)}function p(a,b){var d="",e;return d+='<a href="javascript:void(0);" class="geo-link" title="GeoLocation">',(e=c.remote)?e=e.call(a,{hash:{},data:b}):(e=a.remote,e=typeof e===l?e.apply(a):e),d+=k(e)+"</a>",d}function q(a,b){var d;return(d=c.remote)?d=d.call(a,{hash:{},data:b}):(d=a.remote,d=typeof d===l?d.apply(a):d),k(d)}function r(a,b){var c;return k((c=(c=a.invitationInfo,c==null||c===!1?c:c.agentName),typeof c===l?c.apply(a):c))}function s(a,b){return"-"}function t(a,b){var c="",d;return c+='<span class="timesince" data-timestamp="'+k((d=(d=a.invitationInfo,d==null||d===!1?d:d.time),typeof d===l?d.apply(a):d))+'"></span>',c}this.compilerInfo=[2,">= 1.0.0-rc.3"],c=c||a.helpers,e=e||{};var f="",g,h,i,j=c.helperMissing,k=this.escapeExpression,l="function",m=this;f+='<td class="visitor">\n    ',g=c.unless.call(b,b.invitationInfo,{hash:{},inverse:m.program(3,o,e),fn:m.program(1,n,e),data:e});if(g||g===0)f+=g;f+='\n</td>\n<td class="visitor">\n    <div class="default-visitor-controls inline-block">\n        <div class="control track-control inline-block" title="',i={hash:{},data:e},f+=k((g=c.L10n,g?g.call(b,"pending.table.tracked",i):j.call(b,"L10n","pending.table.tracked",i)))+'"></div>\n    </div>\n    <div class="visitor-controls inline-block"></div>\n</td>\n<td class="visitor">',h=c["if"].call(b,b.userIp,{hash:{},inverse:m.program(7,q,e),fn:m.program(5,p,e),data:e});if(h||h===0)f+=h;f+='</td>\n<td class="visitor"><span class="timesince" data-timestamp="',(h=c.firstTime)?h=h.call(b,{hash:{},data:e}):(h=b.firstTime,h=typeof h===l?h.apply(b):h),f+=k(h)+'"></span></td>\n<td class="visitor"><span class="timesince" data-timestamp="',(h=c.lastTime)?h=h.call(b,{hash:{},data:e}):(h=b.lastTime,h=typeof h===l?h.apply(b):h),f+=k(h)+'"></span></td>\n<td class="visitor">',h=c["if"].call(b,b.invitationInfo,{hash:{},inverse:m.program(11,s,e),fn:m.program(9,r,e),data:e});if(h||h===0)f+=h;f+='</td>\n<td class="visitor">',h=c["if"].call(b,b.invitationInfo,{hash:{},inverse:m.program(11,s,e),fn:m.program(13,t,e),data:e});if(h||h===0)f+=h;return f+='</td>\n<td class="visitor">',(h=c.invitations)?h=h.call(b,{hash:{},data:e}):(h=b.invitations,h=typeof h===l?h.apply(b):h),f+=k(h)+" / ",(h=c.chats)?h=h.call(b,{hash:{},data:e}):(h=b.chats,h=typeof h===l?h.apply(b):h),f+=k(h)+'</td>\n<td class="visitor">',(h=c.userAgent)?h=h.call(b,{hash:{},data:e}):(h=b.userAgent,h=typeof h===l?h.apply(b):h),f+=k(h)+"</td>",f}),b.queued_thread=a(function(a,b,c,d,e){function l(a,b){var d,e;return e={hash:{},data:b},i((d=c.L10n,d?d.call(a,"pending.table.speak",e):h.call(a,"L10n","pending.table.speak",e)))}function m(a,b){var d,e;return e={hash:{},data:b},i((d=c.L10n,d?d.call(a,"pending.table.view",e):h.call(a,"L10n","pending.table.view",e)))}function n(a,b){var d="",e,f;return f={hash:{},data:b},d+=i((e=c.L10n,e?e.call(a,"chat.client.spam.prefix",f):h.call(a,"L10n","chat.client.spam.prefix",f)))+"&nbsp;",d}function o(a,b){var d="",e;return d+='<div class="first-message"><a href="javascript:void(0);" title="',(e=c.firstMessage)?e=e.call(a,{hash:{},data:b}):(e=a.firstMessage,e=typeof e===j?e.apply(a):e),d+=i(e)+'">',(e=c.firstMessagePreview)?e=e.call(a,{hash:{},data:b}):(e=a.firstMessagePreview,e=typeof e===j?e.apply(a):e),d+=i(e)+"</a></div>",d}function p(a,b){var d="",e,f;return d+='\n            <div class="control open-dialog open-control inline-block" title="',f={hash:{},data:b},d+=i((e=c.L10n,e?e.call(a,"pending.table.speak",f):h.call(a,"L10n","pending.table.speak",f)))+'"></div>\n        ',d}function q(a,b){var d="",e,f;return d+='\n            <div class="control view-control inline-block" title="',f={hash:{},data:b},d+=i((e=c.L10n,e?e.call(a,"pending.table.view",f):h.call(a,"L10n","pending.table.view",f)))+'"></div>\n        ',d}function r(a,b){var d="",e,f;return d+='\n            <div class="control track-control inline-block" title="',f={hash:{},data:b},d+=i((e=c.L10n,e?e.call(a,"pending.table.tracked",f):h.call(a,"L10n","pending.table.tracked",f)))+'"></div>\n        ',d}function s(a,b){var d="",e,f;return d+='\n            <div class="control ban-control inline-block" title="',f={hash:{},data:b},d+=i((e=c.L10n,e?e.call(a,"pending.table.ban",f):h.call(a,"L10n","pending.table.ban",f)))+'"></div>\n        ',d}function t(a,b){var d="",e;return d+='<a href="javascript:void(0);" class="geo-link" title="GeoLocation">',(e=c.remote)?e=e.call(a,{hash:{},data:b}):(e=a.remote,e=typeof e===j?e.apply(a):e),d+=i(e)+"</a>",d}function u(a,b){var d;return(d=c.remote)?d=d.call(a,{hash:{},data:b}):(d=a.remote,d=typeof d===j?d.apply(a):d),i(d)}function v(a,b){var d="",e;return d+='<span class="timesince" data-timestamp="',(e=c.waitingTime)?e=e.call(a,{hash:{},data:b}):(e=a.waitingTime,e=typeof e===j?e.apply(a):e),d+=i(e)+'"></span>',d}function w(a,b){return"-"}function x(a,b){var c;return i((c=(c=a.ban,c==null||c===!1?c:c.reason),typeof c===j?c.apply(a):c))}function y(a,b){var d;return(d=c.userAgent)?d=d.call(a,{hash:{},data:b}):(d=a.userAgent,d=typeof d===j?d.apply(a):d),i(d)}this.compilerInfo=[2,">= 1.0.0-rc.3"],c=c||a.helpers,e=e||{};var f="",g,h=c.helperMissing,i=this.escapeExpression,j="function",k=this;f+='<td class="visitor">\n    <div><a href="javascript:void(0);" class="user-name open-dialog" title="',g=c["if"].call(b,b.canOpen,{hash:{},inverse:k.program(3,m,e),fn:k.program(1,l,e),data:e});if(g||g===0)f+=g;f+='">',g=c["if"].call(b,b.ban,{hash:{},inverse:k.noop,fn:k.program(5,n,e),data:e});if(g||g===0)f+=g;(g=c.userName)?g=g.call(b,{hash:{},data:e}):(g=b.userName,g=typeof g===j?g.apply(b):g),f+=i(g)+"</a></div>\n    ",g=c["if"].call(b,b.firstMessage,{hash:{},inverse:k.noop,fn:k.program(7,o,e),data:e});if(g||g===0)f+=g;f+='\n</td>\n<td class="visitor">\n    <div class="default-thread-controls inline-block">\n        ',g=c["if"].call(b,b.canOpen,{hash:{},inverse:k.noop,fn:k.program(9,p,e),data:e});if(g||g===0)f+=g;f+="\n        ",g=c["if"].call(b,b.canView,{hash:{},inverse:k.noop,fn:k.program(11,q,e),data:e});if(g||g===0)f+=g;f+="\n        ",g=c["if"].call(b,b.tracked,{hash:{},inverse:k.noop,fn:k.program(13,r,e),data:e});if(g||g===0)f+=g;f+="\n        ",g=c["if"].call(b,b.canBan,{hash:{},inverse:k.noop,fn:k.program(15,s,e),data:e});if(g||g===0)f+=g;f+='\n    </div>\n    <div class="thread-controls inline-block"></div>\n</td>\n<td class="visitor">',g=c["if"].call(b,b.userIp,{hash:{},inverse:k.program(19,u,e),fn:k.program(17,t,e),data:e});if(g||g===0)f+=g;f+='</td>\n<td class="visitor">',(g=c.stateDesc)?g=g.call(b,{hash:{},data:e}):(g=b.stateDesc,g=typeof g===j?g.apply(b):g),f+=i(g)+'</td>\n<td class="visitor">',(g=c.agentName)?g=g.call(b,{hash:{},data:e}):(g=b.agentName,g=typeof g===j?g.apply(b):g),f+=i(g)+'</td>\n<td class="visitor"><span class="timesince" data-timestamp="',(g=c.totalTime)?g=g.call(b,{hash:{},data:e}):(g=b.totalTime,g=typeof g===j?g.apply(b):g),f+=i(g)+'"></span></td>\n<td class="visitor">',g=c.unless.call(b,b.chatting,{hash:{},inverse:k.program(23,w,e),fn:k.program(21,v,e),data:e});if(g||g===0)f+=g;f+='</td>\n<td class="visitor">',g=c["if"].call(b,b.ban,{hash:{},inverse:k.program(27,y,e),fn:k.program(25,x,e),data:e});if(g||g===0)f+=g;return f+="</td>",f})})();/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,e){a.Regions={};a.Popup={};a.Popup.open=function(b,a,g){b=window.open(b,a,g);b.focus();b.opener=window};a.Utils.updateTimers=function(a,f){a.find(f).each(function(){var a=e(this).data("timestamp");if(a){var c=Math.round((new Date).getTime()/1E3)-a,a=c%60,b=Math.floor(c/60)%60,c=Math.floor(c/3600),d=[];0<c&&d.push(c);d.push(10>b?"0"+b:b);d.push(10>a?"0"+a:a);e(this).html(d.join(":"))}})}})(Mibew,jQuery);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
MibewAPIUsersInteraction=function(){this.obligatoryArguments={"*":{agentId:null,"return":{},references:{}},result:{errorCode:0}};this.reservedFunctionNames=["result"]};MibewAPIUsersInteraction.prototype=new MibewAPIInteraction;
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b){a.Models.Agent=a.Models.User.extend({defaults:b.extend({},a.Models.User.prototype.defaults,{id:null,isAgent:!0,away:!1}),away:function(){this.setAvailability(!1)},available:function(){this.setAvailability(!0)},setAvailability:function(c){var b=this;a.Objects.server.callFunctions([{"function":c?"available":"away",arguments:{agentId:this.id,references:{},"return":{}}}],function(a){0==a.errorCode&&b.set({away:!c})},!0)}})})(Mibew,_);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,c){var b=[],f=a.Models.QueuedThread=a.Models.Thread.extend({defaults:c.extend({},a.Models.Thread.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",agentName:"",canOpen:!1,canView:!1,canBan:!1,ban:!1,totalTime:0,waitingTime:0,firstMessage:null}),initialize:function(){for(var e=[],b=f.getControls(),d=0,c=b.length;d<c;d++)e.push(new b[d]({thread:this}));this.set({controls:new a.Collections.Controls(e)})}},{addControl:function(a){b.push(a)},getControls:function(){return b}})})(Mibew,
_);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b){b.Models.StatusPanel=b.Models.Base.extend({defaults:{message:""},setStatus:function(a){this.set({message:a})},changeAgentStatus:function(){var a=b.Objects.Models.agent;a.get("away")?a.available():a.away()}})})(Mibew);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,c){var b=[],f=a.Models.Visitor=a.Models.User.extend({defaults:c.extend({},a.Models.User.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",firstTime:0,lastTime:0,invitations:0,chats:0,invitationInfo:!1}),initialize:function(){for(var e=[],b=f.getControls(),d=0,c=b.length;d<c;d++)e.push(new b[d]({visitor:this}));this.set({controls:new a.Collections.Controls(e)})}},{addControl:function(a){b.push(a)},getControls:function(){return b}})})(Mibew,_);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,c,d){b.Collections.Agents=c.Collection.extend({model:b.Models.Agent,comparator:function(a){return a.get("name")},initialize:function(){var a=b.Objects.Models.agent;b.Objects.server.callFunctionsPeriodically(function(){return[{"function":"updateOperators",arguments:{agentId:a.id,"return":{operators:"operators"},references:{}}}]},d.bind(this.updateOperators,this))},updateOperators:function(a){this.update(a.operators)}})})(Mibew,Backbone,_);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(c,e,f){c.Collections.Threads=e.Collection.extend({model:c.Models.QueuedThread,initialize:function(){this.revision=0;var a=this,b=c.Objects.Models.agent;c.Objects.server.callFunctionsPeriodically(function(){return[{"function":"currentTime",arguments:{agentId:b.id,"return":{time:"currentTime"},references:{}}},{"function":"updateThreads",arguments:{agentId:b.id,revision:a.revision,"return":{threads:"threads",lastRevision:"lastRevision"},references:{}}}]},f.bind(this.updateThreads,this))},comparator:function(a){var b=
{field:a.get("waitingTime").toString()};this.trigger("sort:field",a,b);return b.field},updateThreads:function(a){if(0==a.errorCode){if(0<a.threads.length){var b;b=a.currentTime?Math.round((new Date).getTime()/1E3)-a.currentTime:0;for(var d=0,e=a.threads.length;d<e;d++)a.threads[d].totalTime=parseInt(a.threads[d].totalTime)+b,a.threads[d].waitingTime=parseInt(a.threads[d].waitingTime)+b;this.trigger("before:update:threads",a.threads);var f=c.Models.Thread.prototype.STATE_CLOSED,g=c.Models.Thread.prototype.STATE_LEFT;
b=[];this.update(a.threads,{remove:!1,sort:!1});b=this.filter(function(a){return a.get("state")==f||a.get("state")==g});0<b.length&&this.remove(b);this.sort();this.trigger("after:update:threads")}this.revision=a.lastRevision}}})})(Mibew,Backbone,_);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,e,f){b.Collections.Visitors=e.Collection.extend({model:b.Models.Visitor,initialize:function(){var a=b.Objects.Models.agent;b.Objects.server.callFunctionsPeriodically(function(){return[{"function":"currentTime",arguments:{agentId:a.id,"return":{time:"currentTime"},references:{}}},{"function":"updateVisitors",arguments:{agentId:a.id,"return":{visitors:"visitors"},references:{}}}]},f.bind(this.updateVisitors,this))},comparator:function(a){var c={field:a.get("firstTime").toString()};this.trigger("sort:field",
a,c);return c.field},updateVisitors:function(a){if(0==a.errorCode){var c;c=a.currentTime?Math.round((new Date).getTime()/1E3)-a.currentTime:0;for(var d=0,b=a.visitors.length;d<b;d++)a.visitors[d].lastTime=parseInt(a.visitors[d].lastTime)+c,a.visitors[d].firstTime=parseInt(a.visitors[d].firstTime)+c;this.trigger("before:update:visitors",a.visitors);this.update(a.visitors);this.trigger("after:update:visitors")}}})})(Mibew,Backbone,_);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,c,d){b.Views.Agent=c.Marionette.ItemView.extend({template:d.templates.agent,tagName:"span",className:"agent",modelEvents:{change:"render"},initialize:function(){this.isModelLast=this.isModelFirst=!1},serializeData:function(){var a=this.model.toJSON();a.isFirst=this.isModelFirst;a.isLast=this.isModelLast;return a}})})(Mibew,Backbone,Handlebars);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c){a.Views.NoThreads=b.Marionette.ItemView.extend({template:c.templates.no_threads,initialize:function(a){this.tagName=a.tagName}})})(Mibew,Backbone,Handlebars);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c){a.Views.NoVisitors=b.Marionette.ItemView.extend({template:c.templates.no_visitors,initialize:function(a){this.tagName=a.tagName}})})(Mibew,Backbone,Handlebars);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(d,e){d.Views.QueuedThread=d.Views.CompositeBase.extend({template:e.templates.queued_thread,itemView:d.Views.Control,itemViewContainer:".thread-controls",className:"thread",modelEvents:{change:"render"},events:{"click .open-dialog":"openDialog","click .view-control":"viewDialog","click .track-control":"showTrack","click .ban-control":"showBan","click .geo-link":"showGeoInfo","click .first-message a":"showFirstMessage"},initialize:function(){this.lastStyles=[]},serializeData:function(){var a=
this.model,b=d.Objects.Models.page,c=a.toJSON();c.stateDesc=this.stateToDesc(a.get("state"));c.chatting=a.get("state")==a.STATE_CHATTING;c.tracked=b.get("showVisitors");c.firstMessage&&(c.firstMessagePreview=30<c.firstMessage.length?c.firstMessage.substring(0,30)+"...":c.firstMessage);return c},stateToDesc:function(a){var b=d.Localization;return a==this.model.STATE_QUEUE?b.get("chat.thread.state_wait"):a==this.model.STATE_WAITING?b.get("chat.thread.state_wait_for_another_agent"):a==this.model.STATE_CHATTING?
b.get("chat.thread.state_chatting_with_agent"):a==this.model.STATE_CLOSED?b.get("chat.thread.state_closed"):a==this.model.STATE_LOADING?b.get("chat.thread.state_loading"):""},showGeoInfo:function(){var a=this.model.get("userIp");if(a){var b=d.Objects.Models.page,c=b.get("geoLink").replace("{ip}",a);d.Popup.open(c,"ip"+a,b.get("geoWindowParams"))}},openDialog:function(){var a=this.model,a=a.get("state")==a.STATE_CHATTING&&a.get("canView");this.showDialogWindow(a)},viewDialog:function(){this.showDialogWindow(!0)},
showDialogWindow:function(a){var b=this.model.id,c=d.Objects.Models.page;d.Popup.open(c.get("agentLink")+"?thread="+b+(a?"&viewonly=true":""),"ImCenter"+b,c.get("chatWindowParams"))},showTrack:function(){var a=this.model.id,b=d.Objects.Models.page;d.Popup.open(b.get("trackedLink")+"?thread="+a,"ImTracked"+a,b.get("trackedUserWindowParams"))},showBan:function(){var a=this.model,b=a.get("ban"),c=d.Objects.Models.page;d.Popup.open(c.get("banLink")+"?"+(!1!==b?"id="+b.id:"thread="+a.id),"ImBan"+b.id,
c.get("banWindowParams"))},showFirstMessage:function(){var a=this.model.get("firstMessage");a&&alert(a)}})})(Mibew,Handlebars);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,c,d){a.Views.StatusPanel=c.Marionette.ItemView.extend({template:d.templates.status_panel,modelEvents:{change:"render"},ui:{changeStatus:"#change-status"},events:{"click #change-status":"changeAgentStatus"},initialize:function(){a.Objects.Models.agent.on("change",this.render,this)},changeAgentStatus:function(){this.model.changeAgentStatus()},serializeData:function(){var b=this.model.toJSON();b.agent=a.Objects.Models.agent.toJSON();return b}})})(Mibew,Backbone,Handlebars);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,d){a.Views.Visitor=a.Views.CompositeBase.extend({template:d.templates.visitor,itemView:a.Views.Control,itemViewContainer:".visitor-controls",className:"visitor",modelEvents:{change:"render"},events:{"click .invite-link":"inviteUser","click .geo-link":"showGeoInfo","click .track-control":"showTrack"},inviteUser:function(){if(!this.model.get("invitationInfo")){var b=this.model.id,c=a.Objects.Models.page;a.Popup.open(c.get("inviteLink")+"?visitor="+b,"ImCenter"+b,c.get("inviteWindowParams"))}},
showTrack:function(){var b=this.model.id,c=a.Objects.Models.page;a.Popup.open(c.get("trackedLink")+"?visitor="+b,"ImTracked"+b,c.get("trackedVisitorWindowParams"))},showGeoInfo:function(){var b=this.model.get("userIp");if(b){var c=a.Objects.Models.page,d=c.get("geoLink").replace("{ip}",b);a.Popup.open(d,"ip"+b,c.get("geoWindowParams"))}}})})(Mibew,Handlebars);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a){a.Views.AgentsCollection=a.Views.CollectionBase.extend({itemView:a.Views.Agent,className:"agents-collection",collectionEvents:{"sort add remove reset":"render"},initialize:function(){this.on("itemview:before:render",this.updateIndexes,this)},updateIndexes:function(a){var b=this.collection,c=a.model;c&&(a.isModelFirst=0==b.indexOf(c),a.isModelLast=b.indexOf(c)==b.length-1)}})})(Mibew);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(d,h,j,k){d.Views.ThreadsCollection=h.Marionette.CompositeView.extend({template:j.templates.threads_collection,itemView:d.Views.QueuedThread,itemViewContainer:"#threads-container",emptyView:d.Views.NoThreads,className:"threads-collection",collectionEvents:{sort:"renderCollection","sort:field":"createSortField",add:"threadAdded"},itemViewOptions:function(a){return{tagName:d.Objects.Models.page.get("threadTag"),collection:a.get("controls")}},initialize:function(){window.setInterval(k.bind(this.updateTimers,
this),2E3);this.on("itemview:before:render",this.updateStyles,this);this.on("render",this.updateTimers,this)},updateStyles:function(a){var b=this.collection,c=a.model,d=this;if(c.id){var e=this.getQueueCode(c),f=!1,g=!1,b=b.filter(function(a){return d.getQueueCode(a)==e});0<b.length&&(g=b[0].id==c.id,f=b[b.length-1].id==c.id);if(0<a.lastStyles.length){c=0;for(b=a.lastStyles.length;c<b;c++)a.$el.removeClass(a.lastStyles[c]);a.lastStyles=[]}c=(e!=this.QUEUE_BAN?"in":"")+this.queueCodeToString(e);a.lastStyles.push(c);
g&&a.lastStyles.push(c+"-first");f&&a.lastStyles.push(c+"-last");c=0;for(b=a.lastStyles.length;c<b;c++)a.$el.addClass(a.lastStyles[c])}},updateTimers:function(){d.Utils.updateTimers(this.$el,".timesince")},createSortField:function(a,b){var c=this.getQueueCode(a)||"Z";b.field=c.toString()+"_"+a.get("waitingTime").toString()},threadAdded:function(){var a=d.Objects.Models.page.get("webimRoot");a&&d.Objects.Models.sound.play(a+"/sounds/new_user.wav");if(d.Objects.Models.page.get("showPopup"))this.once("render",
function(){alert(d.Localization.get("pending.popup_notification"))})},getQueueCode:function(a){var b=a.get("state");return!1!=a.get("ban")&&b!=a.STATE_CHATTING?this.QUEUE_BAN:b==a.STATE_QUEUE||b==a.STATE_LOADING?this.QUEUE_WAITING:b==a.STATE_CLOSED||b==a.STATE_LEFT?this.QUEUE_CLOSED:b==a.STATE_WAITING?this.QUEUE_PRIO:b==a.STATE_CHATTING?this.QUEUE_CHATTING:!1},queueCodeToString:function(a){return a==this.QUEUE_PRIO?"prio":a==this.QUEUE_WAITING?"wait":a==this.QUEUE_CHATTING?"chat":a==this.QUEUE_BAN?
"ban":a==this.QUEUE_CLOSED?"closed":""},QUEUE_PRIO:1,QUEUE_WAITING:2,QUEUE_CHATTING:3,QUEUE_BAN:4,QUEUE_CLOSED:5})})(Mibew,Backbone,Handlebars,_);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c,d){a.Views.VisitorsCollection=b.Marionette.CompositeView.extend({template:c.templates.visitors_collection,itemView:a.Views.Visitor,itemViewContainer:"#visitors-container",emptyView:a.Views.NoVisitors,className:"visitors-collection",collectionEvents:{sort:"renderCollection"},itemViewOptions:function(b){return{tagName:a.Objects.Models.page.get("visitorTag"),collection:b.get("controls")}},initialize:function(){window.setInterval(d.bind(this.updateTimers,this),2E3);this.on("render",this.updateTimers,
this)},updateTimers:function(){a.Utils.updateTimers(this.$el,".timesince")}})})(Mibew,Backbone,Handlebars,_);
/*
 This file is part of Mibew Messenger project.
 http://mibew.org
 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,h,j){var d=0,g=function(){d++;10==d&&(alert(a.Localization.get("pending.errors.network")),d=0)},b=new h.Marionette.Application;b.addRegions({agentsRegion:"#agents-region",statusPanelRegion:"#status-panel-region",threadsRegion:"#threads-region",visitorsRegion:"#visitors-region",soundRegion:"#sound-region"});b.addInitializer(function(f){var d=a.Objects,c=a.Objects.Models,e=a.Objects.Collections;d.server=new a.Server(j.extend({interactionType:MibewAPIUsersInteraction,onTimeout:g,onTransportError:g},
f.server));c.page=new a.Models.Page(f.page);c.agent=new a.Models.Agent(f.agent);e.threads=new a.Collections.Threads;b.threadsRegion.show(new a.Views.ThreadsCollection({collection:e.threads}));f.page.showOnlineOperators&&(e.visitors=new a.Collections.Visitors,b.visitorsRegion.show(new a.Views.VisitorsCollection({collection:e.visitors})));c.statusPanel=new a.Models.StatusPanel;b.statusPanelRegion.show(new a.Views.StatusPanel({model:c.statusPanel}));f.page.showOnlineOperators&&(e.agents=new a.Collections.Agents,
b.agentsRegion.show(new a.Views.AgentsCollection({collection:e.agents})));c.sound=new a.Models.Sound;b.soundRegion.show(new a.Views.Sound({model:c.sound}));d.server.callFunctionsPeriodically(function(){return[{"function":"update",arguments:{"return":{},references:{},agentId:c.agent.id}}]},function(){})});b.on("start",function(){a.Objects.server.runUpdater()});a.Application=b})(Mibew,Backbone,_);
