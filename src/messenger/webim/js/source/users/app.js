/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
        visitorsRegion: '#visitors-region',
        soundRegion: '#sound-region'
    });

    // Initialize application
    App.addInitializer(function(options){

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
        if (options.page.showOnlineOperators) {
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

        // Initialize sounds
        models.sound = new Mibew.Models.Sound();
        App.soundRegion.show(new Mibew.Views.Sound({
            model: models.sound
        }));

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
