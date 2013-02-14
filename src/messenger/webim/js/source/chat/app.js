/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function (Mibew, _) {

    // Create shortcut for application
    var App = Mibew.Application;

    // Define regions
    App.addRegions({
        controlsRegion: '#controls-region',
        avatarRegion: '#avatar-region',
        messagesRegion: Mibew.Regions.Messages,
        statusRegion: '#status-region',
        messageFormRegion: '#message-form-region',
        soundRegion: '#sound-region'
    });

    // Initialize application
    App.addInitializer(function(options){
        // Create some shortcuts
        var objs = Mibew.Objects;
        var models = Mibew.Objects.Models;
        var controls = Mibew.Objects.Models.Controls;
        var status = Mibew.Objects.Models.Status;


        // Initialize Server, Thread and User
        objs.server = new Mibew.Server(_.extend(
            {
                'interactionType': MibewAPIChatInteraction
            },
            options.server
        ));
        models.thread = new Mibew.Models.Thread(options.thread);
        models.user = new Mibew.Models.ChatUser(options.user);


        // Initialize Page
        models.page = new Mibew.Models.Page(options.page);


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
                link: options.links.mailLink
            });
            ctrlsCollection.add(controls.sendMail);
        }

        // Create controls only for agent
        if (models.user.get('isAgent')) {
            controls.redirect = new Mibew.Models.RedirectControl({
                weight: 200,
                link: options.links.redirectLink
            });
            ctrlsCollection.add(controls.redirect);

            controls.history = new Mibew.Models.HistoryControl({
                weight: 180,
                link: options.links.historyLink
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

        if (options.links.sslLink) {
            controls.secureMode = new Mibew.Models.SecureModeControl({
                weight: 120,
                link: options.links.sslLink
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
        App.controlsRegion.show(new Mibew.Views.ControlsCollection({
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
        App.statusRegion.show(new Mibew.Views.StatusCollection({
            collection: objs.Collections.status
        }));


        // Initialize avatar only for user
        if (! models.user.get('isAgent')) {
            models.avatar = new Mibew.Models.Avatar();
            App.avatarRegion.show(new Mibew.Views.Avatar({
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
        App.messageFormRegion.show(new Mibew.Views.MessageForm({
            model: models.messageForm
        }));

        // Display messages
        App.messagesRegion.show(new Mibew.Views.MessagesCollection({
            collection: objs.Collections.messages
        }));


        // Initialize sounds
        models.sound = new Mibew.Models.Sound();
        App.soundRegion.show(new Mibew.Views.Sound({
            model: models.sound
        }));


        // TODO: May be move it somewhere else
        // Periodically call update function at the server side
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
        );
    });

    App.on('start', function() {
        // Run Server updater
        Mibew.Objects.server.runUpdater();
    });

})(Mibew, _);