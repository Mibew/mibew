/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,d){var b=a.Application;b.addRegions({mainRegion:"#main-region"});b.addInitializer(function(c){a.Objects.server=new a.Server(d.extend({interactionType:MibewAPIChatInteraction},c.server));b.Chat.start(c)});b.on("start",function(){a.Objects.server.runUpdater()})})(Mibew,_);
