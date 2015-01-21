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

(function(Mibew, _) {

    /**
     * @class Represents Helper model which manage all sounds in chat module.
     * An instance of this class must be initialized only after all system
     * models.
     */
    Mibew.Models.ChatSoundManager = Mibew.Models.BaseSoundManager.extend(
        /** @lends Mibew.Models.ChatSoundManager.prototype */
        {
            /**
             * A list of default model values.
             * Inherits values from Mibew.Models.BaseSoundManager
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.BaseSoundManager.prototype.defaults,
                {
                    /**
                     * Indicates next sound about new message should be skipped
                     * @type Boolean
                     */
                    skipNextMessageSound: false
                }
            ),
            /**
             * Model initializer. It links all system elements and play sound
             * only when it need.
             */
            initialize: function() {
                // Create some shortcuts
                var objs = Mibew.Objects;
                var self = this;

                objs.Collections.messages.on(
                    'multiple:add',
                    this.playNewMessageSound,
                    this
                );
                objs.Models.messageForm.on(
                    'before:post',
                    function () {
                        self.set({skipNextMessageSound: true});
                    }
                );
            },

            /**
             * Play sound about new message
             */
            playNewMessageSound: function() {
                if (! this.get('skipNextMessageSound')) {
                    // Build sound path
                    var path = Mibew.Objects.Models.page.get('mibewBasePath');
                    if (typeof path !== 'undefined') {
                        path += '/sounds/new_message';
                        // Play sound
                        this.play(path);
                    }
                }
                this.set({skipNextMessageSound: false});
            }
        }
    );

})(Mibew, _);