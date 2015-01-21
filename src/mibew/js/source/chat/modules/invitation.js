/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

(function(Mibew){

    /**
     * Contains ids of periodically called functions
     * @type Array
     */
    var periodicallyCalled = [];


    // Create shortcut for Application object
    var app = Mibew.Application;

    // Create Invite module
    var invitation = app.module('Invitation', {startWithParent: false});

    // Add module initializer
    invitation.addInitializer(function(options) {
        // Create some shortcuts
        var objs = Mibew.Objects;
        var models = Mibew.Objects.Models;

        // Initialize Thread and User
        models.thread = new Mibew.Models.Thread(options.thread);
        models.user = new Mibew.Models.ChatUser(options.user);

        // Create instance of the Invitation layout
        objs.invitationLayout = new Mibew.Layouts.Invitation();

        // Show layout at page
        app.mainRegion.show(objs.invitationLayout);

        // Initialize Messages area
        // Create messages collection and store it
        objs.Collections.messages = new Mibew.Collections.Messages();

        // Display messages
        objs.invitationLayout.messagesRegion.show(new Mibew.Views.MessagesCollection({
            collection: objs.Collections.messages
        }));


        // Periodically call update function at the server side
        periodicallyCalled.push(
            objs.server.callFunctionsPeriodically(
                function() {
                    // Get thread object
                    var thread = Mibew.Objects.Models.thread;

                    // Build functions list
                    return [
                        {
                            "function": "update",
                            "arguments": {
                                "return": {},
                                "references": {},
                                "threadId": thread.get('id'),
                                "token": thread.get('token'),
                                "lastId": thread.get('lastId'),
                                "typed": false,
                                "user": true
                            }
                        }
                    ]
                },
                function() {}
            )
        );

    });

    invitation.on('start', function() {
        // Restart server updater because module just started and there are no
        // reasons to wait refresh time to get results of previous request
        Mibew.Objects.server.restartUpdater();
    })

    // Add module finalizer
    invitation.addFinalizer(function() {
        // Close layout
        Mibew.Objects.invitationLayout.destroy();


        // Stop call functions periodically
        for (var i = 0; i < periodicallyCalled.length; i++) {
            Mibew.Objects.server.stopCallFunctionsPeriodically(
                periodicallyCalled[i]
            );
        }

        // Run collections finalizers
        // Finalize messages collection
        Mibew.Objects.Collections.messages.finalize();

        // Clean up memory
        // Delete layout
        delete Mibew.Objects.invitationLayout;

        // Delete models
        delete Mibew.Objects.Models.thread;
        delete Mibew.Objects.Models.user;

        // Delete collections
        delete Mibew.Objects.Collections.messages;
    });

})(Mibew);