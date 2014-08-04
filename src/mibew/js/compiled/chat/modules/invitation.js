/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e){var t=[],s=e.Application,n=s.module("Invitation",{startWithParent:!1});n.addInitializer(function(n){var o=e.Objects,i=e.Objects.Models;i.thread=new e.Models.Thread(n.thread),i.user=new e.Models.ChatUser(n.user),o.invitationLayout=new e.Layouts.Invitation,s.mainRegion.show(o.invitationLayout),o.Collections.messages=new e.Collections.Messages,o.invitationLayout.messagesRegion.show(new e.Views.MessagesCollection({collection:o.Collections.messages})),t.push(o.server.callFunctionsPeriodically(function(){var t=e.Objects.Models.thread;return[{"function":"update",arguments:{"return":{},references:{},threadId:t.get("id"),token:t.get("token"),lastId:t.get("lastId"),typed:!1,user:!0}}]},function(){}))}),n.on("start",function(){e.Objects.server.restartUpdater()}),n.addFinalizer(function(){e.Objects.invitationLayout.close();for(var s=0;s<t.length;s++)e.Objects.server.stopCallFunctionsPeriodically(t[s]);e.Objects.Collections.messages.finalize(),delete e.Objects.invitationLayout,delete e.Objects.Models.thread,delete e.Objects.Models.user,delete e.Objects.Collections.messages})}(Mibew);