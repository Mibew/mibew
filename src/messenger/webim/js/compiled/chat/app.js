/*
 Copyright 2005-2013 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,d){var c=a.Application;c.addRegions({mainRegion:"#main-region"});c.addInitializer(function(b){a.PluginOptions=b.plugins||{};a.Objects.server=new a.Server(d.extend({interactionType:MibewAPIChatInteraction},b.server));a.Objects.Models.page=new a.Models.Page(b.page);switch(b.startFrom){case "chat":c.Chat.start(b.chatOptions);break;case "survey":c.Survey.start(b.surveyOptions);break;case "leaveMessage":c.LeaveMessage.start(b.leaveMessageOptions);break;default:throw Error("Dont know how to start!");
}});c.on("start",function(){a.Objects.server.runUpdater()})})(Mibew,_);
