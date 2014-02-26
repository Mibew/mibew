/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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