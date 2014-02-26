/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a){var e=a.Application,f=e.module("LeaveMessage",{startWithParent:!1});f.addInitializer(function(d){var b=a.Objects,c=a.Objects.Models;d.page&&c.page.set(d.page);b.leaveMessageLayout=new a.Layouts.LeaveMessage;e.mainRegion.show(b.leaveMessageLayout);c.leaveMessageForm=new a.Models.LeaveMessageForm(d.leaveMessageForm);b.leaveMessageLayout.leaveMessageFormRegion.show(new a.Views.LeaveMessageForm({model:c.leaveMessageForm}));b.leaveMessageLayout.descriptionRegion.show(new a.Views.LeaveMessageDescription);
c.leaveMessageForm.on("submit:complete",function(){b.leaveMessageLayout.leaveMessageFormRegion.close();b.leaveMessageLayout.descriptionRegion.close();b.leaveMessageLayout.descriptionRegion.show(new a.Views.LeaveMessageSentDescription)})});f.addFinalizer(function(){a.Objects.leaveMessageLayout.close();delete a.Objects.Models.leaveMessageForm})})(Mibew);
