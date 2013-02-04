/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
        App.messagesRegion.show(new Mibew.Views.MessagesCollection({
            collection: new Mibew.Collections.Messages(options.messages)
        }));
    });

    Mibew.Application = App;
})(Mibew, Backbone);