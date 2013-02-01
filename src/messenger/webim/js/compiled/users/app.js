/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,g,h){var b=new g.Marionette.Application;b.addRegions({agentsRegion:"#agents-region",statusPanelRegion:"#status-panel-region",threadsRegion:"#threads-region",visitorsRegion:"#visitors-region",soundRegion:"#sound-region"});b.addInitializer(function(e){var f=a.Objects,c=a.Objects.Models,d=a.Objects.Collections;f.server=new a.Server(h.extend({interactionType:MibewAPIUsersInteraction},e.server));c.page=new a.Models.Page(e.page);c.agent=new a.Models.Agent(e.agent);d.threads=new a.Collections.Threads;
b.threadsRegion.show(new a.Views.ThreadsCollection({collection:d.threads}));e.page.showOnlineOperators&&(d.visitors=new a.Collections.Visitors,b.visitorsRegion.show(new a.Views.VisitorsCollection({collection:d.visitors})));c.statusPanel=new a.Models.StatusPanel;b.statusPanelRegion.show(new a.Views.StatusPanel({model:c.statusPanel}));e.page.showOnlineOperators&&(d.agents=new a.Collections.Agents,b.agentsRegion.show(new a.Views.AgentsCollection({collection:d.agents})));c.sound=new a.Models.Sound;b.soundRegion.show(new a.Views.Sound({model:c.sound}));
f.server.callFunctionsPeriodically(function(){return[{"function":"update",arguments:{"return":{},references:{},agentId:c.agent.id}}]},function(){})});b.on("start",function(){a.Objects.server.runUpdater()});a.Application=b})(Mibew,Backbone,_);
