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

(function(Mibew){

    /**
     * @namespace Holds utility functions
     */
    Mibew.Utils = Mibew.Utils || {};

    /**
     * Closes chat window.
     *
     * The method helps to close chat popup no mater how it's implemented
     * (iframe or window).
     */
    Mibew.Utils.closeChatPopup = function() {
        if (window.parent && (window.parent !== window) && window.parent.postMessage) {
            // It seems that the chat is working in an iframe. Tell the parent
            // window that the chat is stoped so it could close the iframe.
            window.parent.postMessage('mibew-chat-closed:' + window.name, '*');
        } else {
            // Just close the window.
            window.close();
        }
    }

})(Mibew);