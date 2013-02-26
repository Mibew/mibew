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
        // Initialize Server
        Mibew.Objects.server = new Mibew.Server(_.extend(
            {'interactionType': MibewAPIChatInteraction},
            options.server
        ));

        // Initialize Page
        Mibew.Objects.Models.page = new Mibew.Models.Page(options.page);

        switch (options.startFrom) {
            case 'chat':
                app.Chat.start(options.chatOptions);
                break;
            case 'survey':
                app.Survey.start(options.surveyOptions);
                break;
            case 'leaveMessage':
                app.LeaveMessage.start(options.leaveMessageOptions);
                break;
            default:
                throw new Error('Dont know how to start!');
                break;
        }
    });

    app.on('start', function() {
        // Run Server updater
        Mibew.Objects.server.runUpdater();
    });

})(Mibew, _);