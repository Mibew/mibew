/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function (Mibew, Backbone) {

    // Create application instance
    var App = new Backbone.Marionette.Application();

    // Define regions
    App.addRegions({
        messagesRegion: '#messages-region'
    });

    // Initialize application
    App.addInitializer(function(options){
        // Create new empty messages collection and store it
        var messages = new Mibew.Collections.Messages();
        Mibew.Objects.Collections.messages = messages;

        // Update messages in the collection
        messages.updateMessages(options.messages);

        // Dispaly collection
        App.messagesRegion.show(new Mibew.Views.MessagesCollection({
            collection: messages
        }));
    });

    Mibew.Application = App;
})(Mibew, Backbone);