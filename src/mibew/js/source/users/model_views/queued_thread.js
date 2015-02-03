/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
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
     * @class Represents thread view.
     */
    Mibew.Views.QueuedThread = Mibew.Views.CompositeBase.extend(
        /** @lends Mibew.Views.QueuedThread.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['users/queued_thread'],

            /**
             * Default item view constructor.
             * @type Function
             */
            childView: Mibew.Views.Control,

            /**
             * DOM element for collection items
             * @type String
             */
            childViewContainer: '.thread-controls',

            /**
             * CSS class name for view's DOM element
             * @type String
             */
            className: 'thread',

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
                'click .open-dialog': 'openDialog',
                'click .view-control': 'viewDialog',
                'click .track-control': 'showTrack',
                'click .ban-control': 'showBan',
                'click .geo-link': 'showGeoInfo',
                'click .first-message a': 'showFirstMessage'
            },

            /**
             * View initializer
             */
            initialize: function() {
                // Initialize fields and methods of the instance

                /**
                 * Contain list of last styles added to the thread DOM element.
                 * Used by {@link Mibew.Views.ThreadsCollection} view.
                 * @type Array
                 * @fieldOf Mibew.Views.Thread
                 */
                this.lastStyles = [];
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             * Following additional values available in template:
             *  - 'stateDesc': thread state description
             *  - 'chatting': indicates if thread have STATE_CHATTING
             *  - 'tracked': indicates if tracked system is enabled
             *  - 'firstMessagePreview': first message limited by 30 characters
             * @returns {Object} Template data
             */
            serializeData: function() {
                var thread = this.model
                var page = Mibew.Objects.Models.page;
                var data = thread.toJSON();
                data.stateDesc = this.stateToDesc(thread.get('state'));
                data.chatting = (thread.get('state') == thread.STATE_CHATTING);
                data.tracked = page.get('showVisitors');
                if (data.firstMessage) {
                    data.firstMessagePreview = data.firstMessage.length > 30
                        ? data.firstMessage.substring(0,30) + '...'
                        : data.firstMessage
                }
                return data;
            },

            /**
             * Convert numeric thread state code to string description of a
             * state
             * @param {Number} state Thread state code
             * @returns {String} Description of the thread state
             */
            stateToDesc: function(state) {
                var l = Mibew.Localization;
                if (state == this.model.STATE_QUEUE) {
                    return l.trans('In queue');
                }
                if (state == this.model.STATE_WAITING) {
                    return l.trans('Waiting for operator');
                }
                if (state == this.model.STATE_CHATTING) {
                    return l.trans('In chat');
                }
                if (state == this.model.STATE_CLOSED) {
                    return l.trans('Closed');
                }
                if (state == this.model.STATE_LOADING) {
                    return l.trans('Loading');
                }
                return "";
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
            },

            /**
             * Open chat window in dialog mode
             */
            openDialog: function() {
                // Create some shortcuts
                var thread = this.model;
                if (!thread.get('canOpen') && !thread.get('canView')) {
                    // We can neither open dialog nor view it. Do nothing.
                    return;
                }
                var viewOnly = !thread.get('canOpen');

                // Show dialog window
                this.showDialogWindow(viewOnly);
            },

            /**
             * Open chat window in view mode
             */
            viewDialog: function() {
                this.showDialogWindow(true);
            },

            /**
             * Open chat window
             * @param {Boolean} viewOnly Indicates if chat window should be open
             * in view mode
             */
            showDialogWindow: function(viewOnly) {
                // Create some shortcuts
                var thread = this.model;
                var threadId = thread.id;
                var page = Mibew.Objects.Models.page;

                // Open chat window
                Mibew.Popup.open(
                    page.get('agentLink')
                        + '/' + threadId
                        + (viewOnly ? '?viewonly=true': ''),
                    'ImCenter' + threadId,
                    Mibew.Utils.buildWindowParams(page.get('chatWindowParams'))
                );
            },

            /**
             * Open tracked window
             */
            showTrack: function() {
                // Create some shortcuts
                var threadId = this.model.id;
                var page = Mibew.Objects.Models.page;

                // Open tracked window
                Mibew.Popup.open(
                    page.get('trackedLink')
                        + '?thread='
                        + threadId,
                    'ImTracked' + threadId,
                    Mibew.Utils.buildWindowParams(page.get('trackedUserWindowParams'))
                );
            },

            /**
             * Open ban window
             */
            showBan: function() {
                // Create some shortcuts
                var thread = this.model;
                var ban = thread.get('ban');
                var page = Mibew.Objects.Models.page;

                // Open ban window
                Mibew.Popup.open(
                    page.get('banLink')
                        + '/'
                        + (ban !== false
                            ? ban.id + '/edit'
                            : 'add?thread='+ thread.id),
                    'ImBan' + ban.id,
                    Mibew.Utils.buildWindowParams(page.get('banWindowParams'))
                );
            },

            /**
             * Show first message from user to agent
             */
            showFirstMessage: function() {
                var message = this.model.get('firstMessage');
                if (message) {
                    Mibew.Utils.alert(message);
                }
            }

        }
    );

})(Mibew, Handlebars);