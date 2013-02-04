/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,c){var a=new c.Marionette.Application;a.addRegions({messagesRegion:"#messages-region"});a.addInitializer(function(c){a.messagesRegion.show(new b.Views.MessagesCollection({collection:new b.Collections.Messages(c.messages)}))});b.Application=a})(Mibew,Backbone);
