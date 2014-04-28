/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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
            template: Handlebars.templates.queued_thread,

            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Mibew.Views.Control,

            /**
             * DOM element for collection items
             * @type String
             */
            itemViewContainer: '.thread-controls',

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
                    return l.get('chat.thread.state_wait');
                }
                if (state == this.model.STATE_WAITING) {
                    return l.get('chat.thread.state_wait_for_another_agent');
                }
                if (state == this.model.STATE_CHATTING) {
                    return l.get('chat.thread.state_chatting_with_agent');
                }
                if (state == this.model.STATE_CLOSED) {
                    return l.get('chat.thread.state_closed');
                }
                if (state == this.model.STATE_LOADING) {
                    return l.get('chat.thread.state_loading');
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
                        + '?thread='
                        + threadId
                        + (viewOnly ? '&viewonly=true': ''),
                    'ImCenter' + threadId,
                    page.get('chatWindowParams')
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
                    page.get('trackedUserWindowParams')
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
                        + '?'
                        + (ban !== false
                            ? 'id='+ban.id
                            : 'thread='+ thread.id),
                    'ImBan' + ban.id,
                    page.get('banWindowParams')
                );
            },

            /**
             * Show first message from user to agent
             */
            showFirstMessage: function() {
                var message = this.model.get('firstMessage');
                if (message) {
                    alert(message);
                }
            }

        }
    );

})(Mibew, Handlebars);