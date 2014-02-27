/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,d){var c=b.Application;c.addRegions({mainRegion:"#main-region"});c.addInitializer(function(a){b.PluginOptions=a.plugins||{};b.Objects.server=new b.Server(d.extend({interactionType:MibewAPIChatInteraction},a.server));b.Objects.Models.page=new b.Models.Page(a.page);switch(a.startFrom){case "chat":c.Chat.start(a.chatOptions);break;case "survey":c.Survey.start(a.surveyOptions);break;case "leaveMessage":c.LeaveMessage.start(a.leaveMessageOptions);break;case "invitation":c.Invitation.start(a.invitationOptions);
break;default:throw Error("Dont know how to start!");}});c.on("start",function(){b.Objects.server.runUpdater()})})(Mibew,_);
