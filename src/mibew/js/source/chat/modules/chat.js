/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew){

    /**
     * @namespace Holds instances of control models
     */
    Mibew.Objects.Models.Controls = {};

    /**
     * @namespace Holds instances of status models
     */
    Mibew.Objects.Models.Status = {};

    /**
     * Contains ids of periodically called functions
     * @type Array
     */
    var periodicallyCalled = [];


    // Create shortcut for Application object
    var app = Mibew.Application;

    // Create Chat module
    var chat = app.module('Chat', {startWithParent: false});

    // Add module initializer
    chat.addInitializer(function(options) {
        // Create some shortcuts
        var objs = Mibew.Objects;
        var models = Mibew.Objects.Models;
        var controls = Mibew.Objects.Models.Controls;
        var status = Mibew.Objects.Models.Status;

        // Update page options to change logo block
        if (options.page) {
            models.page.set(options.page);
        }

        // Initialize Thread and User
        models.thread = new Mibew.Models.Thread(options.thread);
        models.user = new Mibew.Models.ChatUser(options.user);

        // Create instance of the chat layout
        var layout = new Mibew.Layouts.Chat();
        objs.chatLayout = layout;

        // Show layout at page
        app.mainRegion.show(layout);


        // Initialize controls
        // Create controls collection
        var ctrlsCollection = new Mibew.Collections.Controls();

        // Create controls only for user
        if (! models.user.get('isAgent')) {
            // Create user name control
            controls.userName = new Mibew.Models.UserNameControl({
                weight: 220
            });
            ctrlsCollection.add(controls.userName);

            // Create mail control
            controls.sendMail = new Mibew.Models.SendMailControl({
                weight: 200,
                link: options.links.mail,
                windowParams: options.windowsParams.mail
            });
            ctrlsCollection.add(controls.sendMail);
        }

        // Create controls only for agent
        if (models.user.get('isAgent')) {
            controls.redirect = new Mibew.Models.RedirectControl({
                weight: 200,
                link: options.links.redirect
            });
            ctrlsCollection.add(controls.redirect);

            controls.history = new Mibew.Models.HistoryControl({
                weight: 180,
                link: options.links.history,
                windowParams: options.windowsParams.history
            });
            ctrlsCollection.add(controls.history);
        }

        // Create toggle sound button
        controls.sound = new Mibew.Models.SoundControl({
            weight: 160
        });
        ctrlsCollection.add(controls.sound);

        // Create refresh button
        controls.refresh = new Mibew.Models.RefreshControl({
            weight: 140
        });
        ctrlsCollection.add(controls.refresh);

        if (options.links.ssl) {
            controls.secureMode = new Mibew.Models.SecureModeControl({
                weight: 120,
                link: options.links.ssl
            });
            ctrlsCollection.add(controls.secureMode);
        }

        // Create close button
        controls.close = new Mibew.Models.CloseControl({
            weight: 100
        });
        ctrlsCollection.add(controls.close);

        objs.Collections.controls = ctrlsCollection;

        // Display controls
        layout.controlsRegion.show(new Mibew.Views.ControlsCollection({
            collection: ctrlsCollection
        }));


        // Iniitialize status bar
        // Create status message model
        status.message = new Mibew.Models.StatusMessage({hideTimeout: 5000});

        // Create typing status model
        status.typing = new Mibew.Models.StatusTyping({hideTimeout: 5000});

        // Create status collection
        objs.Collections.status = new Mibew.Collections.Status([
            status.message,
            status.typing
        ]);

        // Display status bar
        layout.statusRegion.show(new Mibew.Views.StatusCollection({
            collection: objs.Collections.status
        }));


        // Initialize avatar only for user
        if (! models.user.get('isAgent')) {
            models.avatar = new Mibew.Models.Avatar();
            layout.avatarRegion.show(new Mibew.Views.Avatar({
                model: models.avatar
            }));
        }


        // Initialize chat window
        // Create messages collection and store it
        objs.Collections.messages = new Mibew.Collections.Messages();

        // Create message processor model
        models.messageForm = new Mibew.Models.MessageForm(
            options.messageForm
        );

        // Display message processor
        layout.messageFormRegion.show(new Mibew.Views.MessageForm({
            model: models.messageForm
        }));

        // Display messages
        layout.messagesRegion.show(new Mibew.Views.MessagesCollection({
            collection: objs.Collections.messages
        }));

        models.soundManager = new Mibew.Models.ChatSoundManager();

        // TODO: May be move it somewhere else
        // Periodically call update function at the server side
        periodicallyCalled.push(
            objs.server.callFunctionsPeriodically(
                function() {
                    // Get thread and user objects
                    var thread = Mibew.Objects.Models.thread;
                    var user = Mibew.Objects.Models.user;

                    // Build functions list
                    return [
                        {
                            "function": "update",
                            "arguments": {
                                "return": {
                                    'typing': 'typing',
                                    'canPost': 'canPost'
                                },
                                "references": {},
                                "threadId": thread.get('id'),
                                "token": thread.get('token'),
                                "lastId": thread.get('lastId'),
                                "typed": user.get('typing'),
                                "user": (! user.get('isAgent'))
                            }
                        }
                    ]
                },
                function(args) {
                    // Check if there was an error
                    if (args.errorCode) {
                        Mibew.Objects.Models.Status.message.setMessage(
                            args.errorMessage || 'refresh failed'
                        );
                        return;
                    }
                    // Update typing status
                    if (args.typing) {
                        Mibew.Objects.Models.Status.typing.show();
                    }
                    // Update user
                    Mibew.Objects.Models.user.set({
                        canPost: args.canPost || false
                    });
                }
            )
        );

    });

    chat.on('start', function() {
        // Restart server updater because module just started and there are no
        // reasons to wait refresh time to get results of previous request
        Mibew.Objects.server.restartUpdater();
    })

    // Add module finalizer
    chat.addFinalizer(function() {
        // Close layout
        Mibew.Objects.chatLayout.close();


        // Stop call functions periodically
        for (var i = 0; i < periodicallyCalled.length; i++) {
            Mibew.Objects.server.stopCallFunctionsPeriodically(
                periodicallyCalled[i]
            );
        }

        // Run models finalizers
        // TODO: may be automate this process
        // Finalize avatar model
        if (typeof Mibew.Objects.Models.avatar != 'undefined') {
            Mibew.Objects.Models.avatar.finalize();
        }

        // Run collections finalizers
        // Finalize messages collection
        Mibew.Objects.Collections.messages.finalize();

        // Clean up memory
        // Delete layout
        delete Mibew.Objects.chatLayout;

        // Delete models
        delete Mibew.Objects.Models.thread;
        delete Mibew.Objects.Models.user;
        delete Mibew.Objects.Models.page;
        delete Mibew.Objects.Models.avatar;
        delete Mibew.Objects.Models.messageForm;
        delete Mibew.Objects.Models.Controls;
        delete Mibew.Objects.Models.Status;

        // Delete collections
        delete Mibew.Objects.Collections.messages;
        delete Mibew.Objects.Collections.controls;
        delete Mibew.Objects.Collections.status;
    });

})(Mibew);