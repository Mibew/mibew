/*
 Copyright 2005-2013 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,d){var b=a.Application;b.addRegions({mainRegion:"#main-region"});b.addInitializer(function(c){a.Objects.server=new a.Server(d.extend({interactionType:MibewAPIChatInteraction},c.server));a.Objects.Models.page=new a.Models.Page(c.page);switch(c.startFrom){case "chat":b.Chat.start(c.chatOptions);break;case "survey":b.Survey.start(c.surveyOptions);break;case "leaveMessage":b.LeaveMessage.start(c.leaveMessageOptions);break;default:throw Error("Dont know how to start!");}});b.on("start",function(){a.Objects.server.runUpdater()})})(Mibew,
_);
