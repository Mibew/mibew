/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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