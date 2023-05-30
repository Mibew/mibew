/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2023 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
            template: Handlebars.templates['users/visitor'],

            /**
             * Default item view constructor.
             * @type Function
             */
            childView: Mibew.Views.Control,

            /**
             * DOM element for collection items
             * @type String
             */
            childViewContainer: '.visitor-controls',

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

                    // Check whether operator could actually chat
                    // (see libs/operator.php, has_online_operators function for details)
                    if (page.get('operatorCouldNotInvite')) {
                        Mibew.Utils.alert(Mibew.Localization.trans('Unable to invite user: groups are enabled, and you don\'t belong to any of them.'));
                    }
                    else {

                        // Open invite window
                        Mibew.Popup.open(
                            page.get('inviteLink')
                                + '?visitor='
                                + visitorId,
                            'ImCenter' + visitorId,
                            Mibew.Utils.buildWindowParams(page.get('inviteWindowParams'))
                        );
                    }
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
                    Mibew.Utils.buildWindowParams(page.get('trackedVisitorWindowParams'))
                );
            },

            /**
             * Open window with a tip for geolocation
             */
            showGeoInfo: function() {
                var ip = this.model.get('userIp');
                if (ip) {
                    Mibew.Utils.alert(Mibew.Localization.trans('No geolocation data available. We recommend you to install Mibew:GeoIp and Mibew:OpenStreetMap (or Mibew:GoogleMaps) plugins.'));
                }
            }
        }
    );

})(Mibew, Handlebars);