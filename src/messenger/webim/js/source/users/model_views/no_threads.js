/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone, Handlebars) {

    /**
     * @class Represents empty thread view.
     */
    Mibew.Views.NoThreads = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.NoThreads.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.no_threads,

            /**
             * View initializer
             * @param {Object} options Options object passed from
             * {@link Mibew.Views.ThreadsCollection.prototype.itemViewOptions}
             */
            initialize: function(options) {
                this.tagName = options.tagName;
            }
        }
    );

})(Mibew, Backbone, Handlebars);