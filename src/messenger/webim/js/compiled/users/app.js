/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,h,j){var d=0,g=function(){d++;10==d&&(alert(a.Localization.get("pending.errors.network")),d=0)},b=new h.Marionette.Application;b.addRegions({agentsRegion:"#agents-region",statusPanelRegion:"#status-panel-region",threadsRegion:"#threads-region",visitorsRegion:"#visitors-region",soundRegion:"#sound-region"});b.addInitializer(function(f){var d=a.Objects,c=a.Objects.Models,e=a.Objects.Collections;d.server=new a.Server(j.extend({interactionType:MibewAPIUsersInteraction,onTimeout:g,onTransportError:g},
f.server));c.page=new a.Models.Page(f.page);c.agent=new a.Models.Agent(f.agent);e.threads=new a.Collections.Threads;b.threadsRegion.show(new a.Views.ThreadsCollection({collection:e.threads}));f.page.showOnlineOperators&&(e.visitors=new a.Collections.Visitors,b.visitorsRegion.show(new a.Views.VisitorsCollection({collection:e.visitors})));c.statusPanel=new a.Models.StatusPanel;b.statusPanelRegion.show(new a.Views.StatusPanel({model:c.statusPanel}));f.page.showOnlineOperators&&(e.agents=new a.Collections.Agents,
b.agentsRegion.show(new a.Views.AgentsCollection({collection:e.agents})));c.sound=new a.Models.Sound;b.soundRegion.show(new a.Views.Sound({model:c.sound}));d.server.callFunctionsPeriodically(function(){return[{"function":"update",arguments:{"return":{},references:{},agentId:c.agent.id}}]},function(){})});b.on("start",function(){a.Objects.server.runUpdater()});a.Application=b})(Mibew,Backbone,_);
