/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,h,j){var c=0,g=function(){c++;10==c&&(alert(a.Localization.get("pending.errors.network")),c=0)},b=new h.Marionette.Application;b.addRegions({agentsRegion:"#agents-region",statusPanelRegion:"#status-panel-region",threadsRegion:"#threads-region",visitorsRegion:"#visitors-region"});b.addInitializer(function(e){var c=a.Objects,f=a.Objects.Models,d=a.Objects.Collections;c.server=new a.Server(j.extend({interactionType:MibewAPIUsersInteraction,onTimeout:g,onTransportError:g},e.server));f.page=
new a.Models.Page(e.page);f.agent=new a.Models.Agent(e.agent);d.threads=new a.Collections.Threads;b.threadsRegion.show(new a.Views.ThreadsCollection({collection:d.threads}));e.page.showOnlineOperators&&(d.visitors=new a.Collections.Visitors,b.visitorsRegion.show(new a.Views.VisitorsCollection({collection:d.visitors})));f.statusPanel=new a.Models.StatusPanel;b.statusPanelRegion.show(new a.Views.StatusPanel({model:f.statusPanel}));e.page.showOnlineOperators&&(d.agents=new a.Collections.Agents,b.agentsRegion.show(new a.Views.AgentsCollection({collection:d.agents})));
c.server.callFunctionsPeriodically(function(){return[{"function":"update",arguments:{"return":{},references:{},agentId:f.agent.id}}]},function(){})});b.on("start",function(){a.Objects.server.runUpdater()});a.Application=b})(Mibew,Backbone,_);
