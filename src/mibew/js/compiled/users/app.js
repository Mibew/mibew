/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,h,j){var c=0,g=function(){c++;10==c&&(alert(a.Localization.get("pending.errors.network")),c=0)},b=new h.Marionette.Application;b.addRegions({agentsRegion:"#agents-region",statusPanelRegion:"#status-panel-region",threadsRegion:"#threads-region",visitorsRegion:"#visitors-region"});b.addInitializer(function(d){a.PluginOptions=d.plugins||{};var c=a.Objects,f=a.Objects.Models,e=a.Objects.Collections;c.server=new a.Server(j.extend({interactionType:MibewAPIUsersInteraction,onTimeout:g,onTransportError:g},
d.server));f.page=new a.Models.Page(d.page);f.agent=new a.Models.Agent(d.agent);e.threads=new a.Collections.Threads;b.threadsRegion.show(new a.Views.ThreadsCollection({collection:e.threads}));d.page.showVisitors&&(e.visitors=new a.Collections.Visitors,b.visitorsRegion.show(new a.Views.VisitorsCollection({collection:e.visitors})));f.statusPanel=new a.Models.StatusPanel;b.statusPanelRegion.show(new a.Views.StatusPanel({model:f.statusPanel}));d.page.showOnlineOperators&&(e.agents=new a.Collections.Agents,
b.agentsRegion.show(new a.Views.AgentsCollection({collection:e.agents})));c.server.callFunctionsPeriodically(function(){return[{"function":"update",arguments:{"return":{},references:{},agentId:f.agent.id}}]},function(){})});b.on("start",function(){a.Objects.server.runUpdater()});a.Application=b})(Mibew,Backbone,_);
