/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Handlebars) {

    /**
     * @class Represents visitor view.
     */
    Mibew.Views.Visitor = Mibew.Views.CompositeBase.extend(
        /** @lends Mibew.Views.Visitor.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.visitor,

            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Mibew.Views.Control,

            /**
             * DOM element for collection items
             * @type String
             */
            itemViewContainer: '.visitor-controls',

            /**
             * CSS class name for view's DOM element
             * @type String
             */
            className: 'visitor',

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'change': 'render'
            },

            /**
             * UI events hash.
             * Map UI events on the view methods.
             * @type Object
             */
            events: {
                'click .invite-link': 'inviteUser',
                'click .geo-link': 'showGeoInfo',
                'click .track-control': 'showTrack'
            },

            /**
             * Invite user to chat
             */
            inviteUser: function() {
                if (! this.model.get('invitationInfo')) {
                    // Create some shortcuts
                    var visitorId = this.model.id;
                    var page = Mibew.Objects.Models.page;

                    // Open invite window
                    Mibew.Popup.open(
                        page.get('inviteLink')
                            + '?visitor='
                            + visitorId,
                        'ImCenter' + visitorId,
                        page.get('inviteWindowParams')
                    );
                }
            },

            /**
             * Open tracked window
             */
            showTrack: function() {
                // Create some shortcuts
                var visitorId = this.model.id;
                var page = Mibew.Objects.Models.page;

                // Open tracked window
                Mibew.Popup.open(
                    page.get('trackedLink')
                        + '?visitor='
                        + visitorId,
                    'ImTracked' + visitorId,
                    page.get('trackedVisitorWindowParams')
                );
            },

            /**
             * Open window with geo information
             */
            showGeoInfo: function() {
                var ip = this.model.get('userIp');
                if (ip) {
                    var page = Mibew.Objects.Models.page;
                    var geoLink = page.get('geoLink')
                        .replace("{ip}", ip);
                    Mibew.Popup.open(
                        geoLink,
                        'ip' + ip,
                        page.get('geoWindowParams')
                    );
                }
            }
        }
    );

})(Mibew, Handlebars);