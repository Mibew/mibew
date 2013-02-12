/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone, _){

    // Create application instance
    var App = new Backbone.Marionette.Application();

    // Initialize application
    App.addInitializer(function(options){

        // Initialize Server
        var server =  new Mibew.Server(_.extend(
            {
                'interactionType': MibewAPIInviteInteraction
            },
            options.server
        ));

        // Periodically call update function at the server side
        server.callFunctionsPeriodically(
            function() {
                // Build functions list
                return [
                    {
                        "function": "invitationState",
                        "arguments": {
                            "return": {
                                'invited': 'invited',
                                'threadId': 'threadId'
                            },
                            "references": {},
                            "visitorId": options.visitorId
                        }
                    }
                ];
            },
            function(args) {
                if (args.errorCode == 0) {
                    if (!args.invited) {
                        // Visitor not invited any more.
                        // Invitation vindow should be closed.
                        window.close();
                    }
                    if (args.threadId) {
                        // Invitation accepted.
                        // Redirect agent to chat page
                        window.name = 'ImCenter' + args.threadId;
                        window.location= options.chatLink
                            + '?thread=' + args.threadId;
                    }
                }
            }
        );

        server.runUpdater();
        Mibew.Objects.server = server;
    });

    Mibew.Application = App;

})(Mibew, Backbone, _);
