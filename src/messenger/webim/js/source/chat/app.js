/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function (Mibew, _) {

    // Create shortcut for application
    var app = Mibew.Application;

    // Define regions
    app.addRegions({
        mainRegion: '#main-region'
    });

    // Initialize application
    app.addInitializer(function(options){
        // Initialize Server, Thread and User
        Mibew.Objects.server = new Mibew.Server(_.extend(
            {
                'interactionType': MibewAPIChatInteraction
            },
            options.server
        ));
        app.Chat.start(options);
    });

    app.on('start', function() {
        // Run Server updater
        Mibew.Objects.server.runUpdater();
    });

})(Mibew, _);