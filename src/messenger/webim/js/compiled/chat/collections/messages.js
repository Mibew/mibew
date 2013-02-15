/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,d,f){b.Collections.Messages=d.Collection.extend({model:b.Models.Message,initialize:function(){this.periodicallyCalled=[];this.periodicallyCalled.push(b.Objects.server.callFunctionsPeriodically(f.bind(this.updateMessagesFunctionBuilder,this),f.bind(this.updateMessages,this)))},finalize:function(){for(var a=0;a<this.periodicallyCalled.length;a++)b.Objects.server.stopCallFunctionsPeriodically(this.periodicallyCalled[a])},updateMessages:function(a){a.lastId&&b.Objects.Models.thread.set({lastId:a.lastId});
for(var c=[],e=0,d=a.messages.length;e<d;e++)c.push(new b.Models.Message(a.messages[e]));0<c.length&&this.add(c)},updateMessagesFunctionBuilder:function(){var a=b.Objects.Models.thread,c=b.Objects.Models.user;return[{"function":"updateMessages",arguments:{"return":{messages:"messages",lastId:"lastId"},references:{},threadId:a.get("id"),token:a.get("token"),lastId:a.get("lastId"),user:!c.get("isAgent")}}]},add:function(){var a=Array.prototype.slice.apply(arguments),a=d.Collection.prototype.add.apply(this,
a);this.trigger("multiple:add");return a}})})(Mibew,Backbone,_);
