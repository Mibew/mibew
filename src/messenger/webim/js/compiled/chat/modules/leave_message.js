/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a){var e=a.Application,f=e.module("LeaveMessage",{startWithParent:!1});f.addInitializer(function(d){var b=a.Objects,c=a.Objects.Models;d.page&&c.page.set(d.page);b.leaveMessageLayout=new a.Layouts.LeaveMessage;e.mainRegion.show(b.leaveMessageLayout);c.leaveMessageForm=new a.Models.LeaveMessageForm(d.leaveMessageForm);b.leaveMessageLayout.leaveMessageFormRegion.show(new a.Views.LeaveMessageForm({model:c.leaveMessageForm}));b.leaveMessageLayout.descriptionRegion.show(new a.Views.LeaveMessageDescription);
c.leaveMessageForm.on("submit:complete",function(){b.leaveMessageLayout.leaveMessageFormRegion.close();b.leaveMessageLayout.descriptionRegion.close();b.leaveMessageLayout.descriptionRegion.show(new a.Views.LeaveMessageSentDescription)})});f.addFinalizer(function(){a.Objects.leaveMessageLayout.close();delete a.Objects.Models.leaveMessageForm})})(Mibew);
