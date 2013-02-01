/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone, Handlebars) {

    /**
     * @class Represents empty visitor view.
     */
    Mibew.Views.NoVisitors = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.NoVisitors.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.no_visitors,

            /**
             * View initializer
             * @param {Object} options Options object passed from
             * {@link Mibew.Views.VisitorsCollection.prototype.itemViewOptions}
             */
            initialize: function(options) {
                this.tagName = options.tagName;
            }
        }
    );

})(Mibew, Backbone, Handlebars);