/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
            template: Handlebars.templates.redirect_control,

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