/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e){var s=e.Application,a=s.module("LeaveMessage",{startWithParent:!1});a.addInitializer(function(a){var o=e.Objects,i=e.Objects.Models;a.page&&i.page.set(a.page),o.leaveMessageLayout=new e.Layouts.LeaveMessage,s.mainRegion.show(o.leaveMessageLayout),i.leaveMessageForm=new e.Models.LeaveMessageForm(a.leaveMessageForm),o.leaveMessageLayout.leaveMessageFormRegion.show(new e.Views.LeaveMessageForm({model:i.leaveMessageForm})),o.leaveMessageLayout.descriptionRegion.show(new e.Views.LeaveMessageDescription),i.leaveMessageForm.on("submit:complete",function(){o.leaveMessageLayout.leaveMessageFormRegion.close(),o.leaveMessageLayout.descriptionRegion.close(),o.leaveMessageLayout.descriptionRegion.show(new e.Views.LeaveMessageSentDescription)})}),a.addFinalizer(function(){e.Objects.leaveMessageLayout.close(),delete e.Objects.Models.leaveMessageForm})}(Mibew);