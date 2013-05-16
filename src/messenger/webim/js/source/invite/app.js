/**
 * @preserve Copyright 2005-2013 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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
