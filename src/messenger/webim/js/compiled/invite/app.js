/*
 Copyright 2005-2013 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(c,a,e){a=new a.Marionette.Application;a.addInitializer(function(a){var d=new c.Server(e.extend({interactionType:MibewAPIInviteInteraction},a.server));d.callFunctionsPeriodically(function(){return[{"function":"invitationState",arguments:{"return":{invited:"invited",threadId:"threadId"},references:{},visitorId:a.visitorId}}]},function(b){0==b.errorCode&&(b.invited||window.close(),b.threadId&&(window.name="ImCenter"+b.threadId,window.location=a.chatLink+"?thread="+b.threadId))});d.runUpdater();
c.Objects.server=d});c.Application=a})(Mibew,Backbone,_);
