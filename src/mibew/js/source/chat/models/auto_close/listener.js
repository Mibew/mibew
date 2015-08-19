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

(function (Mibew, _) {

    Mibew.Models.AutoChatCloser = Backbone.Model.extend({
        defaults: {
            timeout: 60 * 10,
            last_activity_time: new Date().getTime() / 1000,
            activity_timer: null
        },

        initialize: function () {
            this.updateLastActivityTime();
            Mibew.Objects.Collections.messages.on('multiple:add', this.updateLastActivityTime, this);
        },

        updateLastActivityTime: function () {
            this.set({last_activity_time: new Date().getTime() / 1000});
            var timer = this.get('activity_timer');
            if (timer != null) {
                clearTimeout(timer);
            }
            this.setIdleTimer(this.get('timeout'));
        },

        close_chat_window: function () {
            var idleSeconds = (new Date().getTime() / 1000) - this.get('last_activity_time');
            if (idleSeconds >= this.get('timeout')) {
                Mibew.Utils.closeChatPopup()
            } else {
                this.setIdleTimer(this.get('timeout') - idleSeconds);
            }
        },

        setIdleTimer: function (timeoutInSec) {
            var timer = setTimeout(_.bind(this.close_chat_window, this), timeoutInSec * 1000);
            this.set({activity_timer: timer});
        }
    });
})(Mibew, _);