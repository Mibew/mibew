/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a){var d=[],e=a.Application,b=e.module("Invitation",{startWithParent:!1});b.addInitializer(function(f){var c=a.Objects,b=a.Objects.Models;b.thread=new a.Models.Thread(f.thread);b.user=new a.Models.ChatUser(f.user);c.invitationLayout=new a.Layouts.Invitation;e.mainRegion.show(c.invitationLayout);c.Collections.messages=new a.Collections.Messages;c.invitationLayout.messagesRegion.show(new a.Views.MessagesCollection({collection:c.Collections.messages}));d.push(c.server.callFunctionsPeriodically(function(){var b=
a.Objects.Models.thread;return[{"function":"update",arguments:{"return":{},references:{},threadId:b.get("id"),token:b.get("token"),lastId:b.get("lastId"),typed:!1,user:!0}}]},function(){}))});b.on("start",function(){a.Objects.server.restartUpdater()});b.addFinalizer(function(){a.Objects.invitationLayout.close();for(var b=0;b<d.length;b++)a.Objects.server.stopCallFunctionsPeriodically(d[b]);a.Objects.Collections.messages.finalize();delete a.Objects.invitationLayout;delete a.Objects.Models.thread;delete a.Objects.Models.user;
delete a.Objects.Collections.messages})})(Mibew);
