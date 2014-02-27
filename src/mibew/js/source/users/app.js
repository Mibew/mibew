/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone, _){

    /**
     * Represent count of bad AJAX request
     * @type Number
     */
    var badRequestsCount = 0;

    /**
     * Increase badRequestsCount and show reconnect message if need.
     * Calls on every bad request.
     */
    var requestErrorHandler = function() {
        // Increase bad requests count
        badRequestsCount++;
        // Check if there is
        if (badRequestsCount == 10) {
            alert(Mibew.Localization.get('pending.errors.network'));
            badRequestsCount = 0;
        }
    }

    // Create application instance
    var App = new Backbone.Marionette.Application();

    // Define regions
    App.addRegions({
        agentsRegion: '#agents-region',
        statusPanelRegion: '#status-panel-region',
        threadsRegion: '#threads-region',
        visitorsRegion: '#visitors-region'
    });

    // Initialize application
    App.addInitializer(function(options){
        // Store plugin options
        Mibew.PluginOptions = options.plugins || {};

        // Create some shortcuts
        var objs = Mibew.Objects;
        var models = Mibew.Objects.Models;
        var colls = Mibew.Objects.Collections;

        // Initialize Server, Thread and User
        objs.server = new Mibew.Server(_.extend(
            {
                'interactionType': MibewAPIUsersInteraction,
                onTimeout: requestErrorHandler,
                onTransportError: requestErrorHandler
            },
            options.server
        ));

        // Initialize Page
        models.page = new Mibew.Models.Page(options.page);

        // Initialize Agent
        models.agent = new Mibew.Models.Agent(options.agent);

        // Initialize threads collection
        colls.threads = new Mibew.Collections.Threads();
        App.threadsRegion.show(new Mibew.Views.ThreadsCollection({
            collection: colls.threads
        }));

        // Initialize visitors collection
        if (options.page.showVisitors) {
            colls.visitors = new Mibew.Collections.Visitors();
            App.visitorsRegion.show(new Mibew.Views.VisitorsCollection({
                collection: colls.visitors
            }));
        }

        // Initialize status panel
        models.statusPanel = new Mibew.Models.StatusPanel();
        App.statusPanelRegion.show(new Mibew.Views.StatusPanel({
            model: models.statusPanel
        }));

        // Initialize agents collection and show it
        if (options.page.showOnlineOperators) {
            colls.agents = new Mibew.Collections.Agents();
            App.agentsRegion.show(new Mibew.Views.AgentsCollection({
                collection: colls.agents
            }));
        }

        // Periodically call update function at the server side
        objs.server.callFunctionsPeriodically(
            function() {
                // Build functions list
                return [
                    {
                        "function": "update",
                        "arguments": {
                            "return": {},
                            "references": {},
                            "agentId": models.agent.id
                        }
                    }
                ];
            },
            function(args) {}
        );

    });

    App.on('start', function() {
        // Run Server updater
        Mibew.Objects.server.runUpdater();
    });

    Mibew.Application = App;

})(Mibew, Backbone, _);
