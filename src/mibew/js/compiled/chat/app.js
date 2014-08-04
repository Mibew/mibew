/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t){var a=e.Application;a.addRegions({mainRegion:"#main-region"}),a.addInitializer(function(n){switch(e.PluginOptions=n.plugins||{},e.Objects.server=new e.Server(t.extend({interactionType:MibewAPIChatInteraction},n.server)),e.Objects.Models.page=new e.Models.Page(n.page),n.startFrom){case"chat":a.Chat.start(n.chatOptions);break;case"survey":a.Survey.start(n.surveyOptions);break;case"leaveMessage":a.LeaveMessage.start(n.leaveMessageOptions);break;case"invitation":a.Invitation.start(n.invitationOptions);break;default:throw new Error("Dont know how to start!")}}),a.on("start",function(){e.Objects.server.runUpdater()})}(Mibew,_);