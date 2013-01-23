/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,d,c){b.Collections.Messages=d.Collection.extend({model:b.Models.Message,initialize:function(){b.Objects.server.callFunctionsPeriodically(c.bind(this.updateFunctionBuilder,this),c.bind(this.updateChatState,this));b.Objects.server.registerFunction("updateMessages",c.bind(this.apiUpdateMessages,this))},apiUpdateMessages:function(a){a.lastId&&b.Objects.Models.thread.set({lastId:a.lastId});for(var e=[],c=0,d=a.messages.length;c<d;c++)e.push(new b.Models.Message(a.messages[c]));0<e.length&&
this.add(e)},updateFunctionBuilder:function(){var a=b.Objects.Models.thread,c=b.Objects.Models.user;return[{"function":"update",arguments:{"return":{typing:"typing",canPost:"canPost"},references:{},threadId:a.get("id"),token:a.get("token"),lastId:a.get("lastId"),typed:c.get("typing"),user:!c.get("isAgent")}}]},updateChatState:function(a){a.errorCode?b.Objects.Models.Status.message.setMessage(a.errorMessage||"refresh failed"):(a.typing&&b.Objects.Models.Status.typing.show(),b.Objects.Models.user.set({canPost:a.canPost||
!1}))},add:function(){var a=Array.prototype.slice.apply(arguments),a=d.Collection.prototype.add.apply(this,a);this.trigger("multiple:add");return a}})})(Mibew,Backbone,_);
