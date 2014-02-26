/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,d,j){b.Collections.Messages=d.Collection.extend({model:b.Models.Message,initialize:function(){this.periodicallyCalled=[];this.periodicallyCalled.push(b.Objects.server.callFunctionsPeriodically(j.bind(this.updateMessagesFunctionBuilder,this),j.bind(this.updateMessages,this)))},finalize:function(){for(var a=0;a<this.periodicallyCalled.length;a++)b.Objects.server.stopCallFunctionsPeriodically(this.periodicallyCalled[a])},updateMessages:function(a){a.lastId&&b.Objects.Models.thread.set({lastId:a.lastId});
for(var k=b.Models.Message.prototype.KIND_PLUGIN,g=[],c,f,e,h=0,d=a.messages.length;h<d;h++)c=a.messages[h],c.kind!=k?g.push(new b.Models.Message(c)):"object"!=typeof c.data||null===c.data||(f=c.plugin||!1,f="process:"+(!1!==f?f+":":"")+"plugin:message",e={messageData:c,model:!1},this.trigger(f,e),e.model&&(e.model.get("id")||e.model.set({id:c.id}),g.push(e.model)));0<g.length&&this.add(g)},updateMessagesFunctionBuilder:function(){var a=b.Objects.Models.thread,d=b.Objects.Models.user;return[{"function":"updateMessages",
arguments:{"return":{messages:"messages",lastId:"lastId"},references:{},threadId:a.get("id"),token:a.get("token"),lastId:a.get("lastId"),user:!d.get("isAgent")}}]},add:function(){var a=Array.prototype.slice.apply(arguments),a=d.Collection.prototype.add.apply(this,a);this.trigger("multiple:add");return a}})})(Mibew,Backbone,_);
