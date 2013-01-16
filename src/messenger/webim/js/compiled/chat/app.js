/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,j,k){var f=new j.Marionette.Application;f.addRegions({controlsRegion:"#controls-region",avatarRegion:"#avatar-region",messagesRegion:a.Regions.Messages,statusRegion:"#status-region",messageFormRegion:"#message-form-region",soundRegion:"#sound-region"});f.addInitializer(function(d){var g=a.Objects,c=a.Objects.Models,b=a.Objects.Models.Controls,h=a.Objects.Models.Status;g.server=new a.Server(k.extend({interactionType:MibewAPIChatInteraction},d.server));g.thread=new a.Thread(d.thread);c.user=
new a.Models.User(d.user);c.page=new a.Models.Page(d.page);var e=new a.Collections.Controls;c.user.get("isAgent")||(b.userName=new a.Models.UserNameControl({weight:220}),e.push(b.userName),b.sendMail=new a.Models.SendMailControl({weight:200,link:d.links.mailLink}),e.push(b.sendMail));c.user.get("isAgent")&&(b.redirect=new a.Models.RedirectControl({weight:200,link:d.links.redirectLink}),e.push(b.redirect),b.history=new a.Models.HistoryControl({weight:180,link:d.links.historyLink}),e.push(b.history));
b.sound=new a.Models.SoundControl({weight:160});e.push(b.sound);b.refresh=new a.Models.RefreshControl({weight:140});e.push(b.refresh);d.links.sslLink&&(b.secureMode=new a.Models.SecureModeControl({weight:120,link:d.links.sslLink}),e.push(b.secureMode));b.close=new a.Models.CloseControl({weight:100});e.push(b.close);g.Collections.controls=e;f.controlsRegion.show(new a.Views.ControlsCollection({collection:e}));h.message=new a.Models.StatusMessage({hideTimeout:5E3});h.typing=new a.Models.StatusTyping({hideTimeout:5E3});
g.Collections.status=new a.Collections.Status([h.message,h.typing]);f.statusRegion.show(new a.Views.StatusCollection({collection:g.Collections.status}));c.user.get("isAgent")||(c.avatar=new a.Models.Avatar,f.avatarRegion.show(new a.Views.Avatar({model:c.avatar})));g.Collections.messages=new a.Collections.Messages;c.messageForm=new a.Models.MessageForm(d.messageForm);f.messageFormRegion.show(new a.Views.MessageForm({model:c.messageForm}));f.messagesRegion.show(new a.Views.MessagesCollection({collection:g.Collections.messages}));
c.sound=new a.Models.Sound;f.soundRegion.show(new a.Views.Sound({model:c.sound}));g.server.runUpdater()});a.Application=f})(Mibew,Backbone,_);
