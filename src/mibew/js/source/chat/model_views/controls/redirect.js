/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents Redirect control view
     */
    Mibew.Views.RedirectControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.RedirectControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.chat_controls_redirect,

            /**
             * Map ui events to view methods
             * The view inherits events from
             * {@link Mibew.Views.Control.prototype.events}.
             * @type Object
             */
            events: _.extend(
                {},
                Mibew.Views.Control.prototype.events,
                {
                    'click': 'redirect'
                }
            ),

            /**
             * View initializer.
             */
            initialize: function() {
                Mibew.Objects.Models.user.on('change', this.render, this);
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             * @returns {Object} Template data
             */
            serializeData: function() {
                var data = this.model.toJSON();
                data.user = Mibew.Objects.Models.user.toJSON();
                return data;
            },

            /**
             * Display user redirection window
             */
            redirect: function() {
                var user = Mibew.Objects.Models.user;
                if (user.get('isAgent') && user.get('canPost')) {
                    var link = this.model.get('link');
                    if (link) {
                        // Redirect browser to user redirection page
                        var style = Mibew.Objects.Models.page.get('style');
                        window.location.href = link.replace(/\&amp\;/g, '&')
                            + (style ? ('&style=' + style) : '');
                    }
                }
            }
        }
    );

})(Mibew, Handlebars, _);