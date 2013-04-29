/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(c,d,h){c.Collections.Messages=d.Collection.extend({model:c.Models.Message,initialize:function(){this.periodicallyCalled=[];this.periodicallyCalled.push(c.Objects.server.callFunctionsPeriodically(h.bind(this.updateMessagesFunctionBuilder,this),h.bind(this.updateMessages,this)))},finalize:function(){for(var a=0;a<this.periodicallyCalled.length;a++)c.Objects.server.stopCallFunctionsPeriodically(this.periodicallyCalled[a])},updateMessages:function(a){a.lastId&&c.Objects.Models.thread.set({lastId:a.lastId});
for(var j=c.Models.Message.prototype.KIND_PLUGIN,f=[],b,e,g=0,d=a.messages.length;g<d;g++)b=a.messages[g],b.kind!=j?f.push(new c.Models.Message(b)):"object"!=typeof b.message||null===b.message||(e=b.message.plugin||!1,e="process:"+(!1!==e?e+":":"")+"plugin:message",b={messageData:b,model:!1},this.trigger(e,b),b.model&&f.push(b.model));0<f.length&&this.add(f)},updateMessagesFunctionBuilder:function(){var a=c.Objects.Models.thread,d=c.Objects.Models.user;return[{"function":"updateMessages",arguments:{"return":{messages:"messages",
lastId:"lastId"},references:{},threadId:a.get("id"),token:a.get("token"),lastId:a.get("lastId"),user:!d.get("isAgent")}}]},add:function(){var a=Array.prototype.slice.apply(arguments),a=d.Collection.prototype.add.apply(this,a);this.trigger("multiple:add");return a}})})(Mibew,Backbone,_);
