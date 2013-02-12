/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(c,a,e){a=new a.Marionette.Application;a.addInitializer(function(a){var d=new c.Server(e.extend({interactionType:MibewAPIInviteInteraction},a.server));d.callFunctionsPeriodically(function(){return[{"function":"invitationState",arguments:{"return":{invited:"invited",threadId:"threadId"},references:{},visitorId:a.visitorId}}]},function(b){0==b.errorCode&&(b.invited||window.close(),b.threadId&&(window.name="ImCenter"+b.threadId,window.location=a.chatLink+"?thread="+b.threadId))});d.runUpdater();
c.Objects.server=d});c.Application=a})(Mibew,Backbone,_);
