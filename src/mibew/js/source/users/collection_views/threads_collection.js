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

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents threads list
     */
    Mibew.Views.ThreadsCollection = Mibew.Views.CompositeBase.extend(
        /** @lends Mibew.Views.ThreadsCollection.prototype */
        {
            template: Handlebars.templates['users/threads_collection'],

            /**
             * DOM element for collection items
             * @type String
             */
            childViewContainer: '#threads-container',

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'threads-collection',

            /**
             * Map collection events to the view methods
             * @type Object
             */
            collectionEvents: {
                'sort': 'render',
                'sort:field': 'createSortField',
                'add': 'threadAdded'
            },

            /**
             * Returns default child view constructor.
             *
             * The function is used instead of "childView" property to provide
             * an ability to override child view constructor without this class
             * overriding.
             *
             * @param {Backbone.Model} model The model the view created for.
             * @returns {Backbone.Marionette.ItemView}
             */
            getChildView: function(model) {
                return Mibew.Views.QueuedThread;
            },

            /**
             * Returns empty view constructor.
             *
             * The function is used instead of "emptyView" property to provide
             * an ability to override empty view constructor without this class
             * overriding.
             *
             * @returns {Backbone.Marionette.ItemView}
             */
            getEmptyView: function() {
                return Mibew.Views.NoThreads;
            },

            /**
             * Pass some options to item view
             * @returns {Object} Options object
             */
            childViewOptions: function(model) {
                var page = Mibew.Objects.Models.page;
                return {
                    tagName: page.get('threadTag'),
                    collection: model.get('controls')
                }
            },

            /**
             * View initializer.
             */
            initialize: function() {
                // Update time in timers
                window.setInterval(_.bind(this.updateTimers, this), 2 * 1000);
                // Register events
                this.on('childview:before:render', this.updateStyles, this);
                this.on('render:collection', this.updateTimers, this);
            },

            /**
             * Update thread DOM element classes depending on thread params.
             * @param {Mibew.Views.QueuedThread} childView View instance for
             * thread in the queue
             */
            updateStyles: function(childView) {
                // Create some shortcuts
                var collection = this.collection;
                var thread = childView.model;
                var self = this;

                if (thread.id) {
                    var queueCode = this.getQueueCode(thread);
                    var isLast = false, isFirst = false;

                    // Filter collection by queue type
                    var filteredThreads = collection.filter(function(model) {
                        return self.getQueueCode(model) == queueCode;
                    });

                    // Get isFirst and isLast flags
                    if (filteredThreads.length > 0) {
                        isFirst = (filteredThreads[0].id == thread.id);
                        isLast = (
                            filteredThreads[filteredThreads.length-1].id == thread.id
                        );
                    }

                    // Remove all old styles
                    if (childView.lastStyles.length > 0) {
                        for(var i = 0, l = childView.lastStyles.length; i < l; i++) {
                            childView.$el.removeClass(childView.lastStyles[i]);
                        }
                        childView.lastStyles = [];
                    }

                    // Create new style name
                    var style = ((queueCode != this.QUEUE_BAN)?'in-':'')
                        + this.queueCodeToString(queueCode);

                    // Store new styles
                    childView.lastStyles.push(style);
                    if (isFirst) {
                        childView.lastStyles.push(style + "-first");
                    }
                    if (isLast) {
                        childView.lastStyles.push(style + "-last");
                    }

                    // Add styles names to DOM element
                    for(var i = 0, l = childView.lastStyles.length; i < l; i++) {
                        childView.$el.addClass(childView.lastStyles[i]);
                    }
                }
            },

            /**
             * Updates time in timers
             */
            updateTimers: function() {
                Mibew.Utils.updateTimers(this.$el, '.timesince');
            },

            /**
             * This is the 'sort:field' event handler.
             * Make threads sort by queue code and waiting time.
             * @param {Mibew.Models.QueuedThread} thread Thread model
             * @param {Object} sort Sorting object that contains property
             * 'field' - a string by which threads will be sorted
             */
            createSortField: function(thread, sort) {
                var queueCode = this.getQueueCode(thread) || 'Z';
                sort.field = queueCode.toString()
                        + '_'
                        + thread.get('waitingTime').toString()
            },

            /**
             * Play sound when new thread add to collection.
             * @param {Mibew.Models.QueuedThread} thread The thread model that
             * have been added.
             */
            threadAdded: function(thread) {
                // Do nothing for threads that do not need to be processed by
                // the operator.
                var queueCode = this.getQueueCode(thread);
                if (queueCode !== this.QUEUE_WAITING && queueCode !== this.QUEUE_PRIO) {
                    return;
                }

                // Build sound path
                var path = Mibew.Objects.Models.page.get('mibewBasePath');
                if (typeof path !== 'undefined') {
                    path += '/sounds/new_user';
                    // Play sound
                    Mibew.Utils.playSound(path);
                }

                // Show popup notification if need
                if (Mibew.Objects.Models.page.get('showPopup')) {
                    this.once('render', function() {
                        Mibew.Utils.alert(
                            Mibew.Localization.trans('A new visitor is waiting for an answer.')
                        );
                    })
                }
            },

            /**
             * Calculate queue code for thread
             * @returns {Boolean|Number} Queue code or false if code is unknown
             */
            getQueueCode: function(thread) {
                var state = thread.get('state');
                if (thread.get('ban') != false
                    && state != thread.STATE_CHATTING) {
                    return this.QUEUE_BAN;
                }
                if (state == thread.STATE_QUEUE
                    || state == thread.STATE_LOADING) {
                    return this.QUEUE_WAITING;
                }
                if (state == thread.STATE_CLOSED
                    || state == thread.STATE_LEFT) {
                    return this.QUEUE_CLOSED;
                }
                if (state == thread.STATE_WAITING) {
                    return this.QUEUE_PRIO;
                }
                if (state == thread.STATE_CHATTING) {
                    return this.QUEUE_CHATTING;
                }

                return false;
            },

            /**
             * Convert numeric queue code to string one
             * @returns {String}
             */
            queueCodeToString: function(code) {
                if (code == this.QUEUE_PRIO) {
                    return "priority-queue";
                }
                if (code == this.QUEUE_WAITING) {
                    return "waiting";
                }
                if (code == this.QUEUE_CHATTING) {
                    return "chat";
                }
                if (code == this.QUEUE_BAN) {
                    return "banned";
                }
                if (code == this.QUEUE_CLOSED) {
                    return "closed";
                }
                return "";
            },

            /** Queues codes */

            /**
             * Priority queue. Includes threads with STATE_WAITING state
             */
            QUEUE_PRIO: 1,

            /**
             * Waiting queue. Includes threads with STATE_LOADING and
             * STATE_WAITING states.
             */
            QUEUE_WAITING: 2,

            /**
             * Chatting queue. Includes threads with STATE_CHATTING state
             */
            QUEUE_CHATTING: 3,

            /**
             * Ban queue. Includes all blocked threads.
             */
            QUEUE_BAN: 4,

            /**
             * Closed queue. Includes all threads with STATE_CLOSED and
             * STATE_LEFT states
             */
            QUEUE_CLOSED: 5

            /** End of queues codes */

        }
    );

})(Mibew, Handlebars, _);