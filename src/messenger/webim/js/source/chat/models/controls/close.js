/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew){

    /**
     * @class Close control model
     */
    Mibew.Models.CloseControl = Mibew.Models.Control.extend(
        /** @lends Mibew.Models.CloseControl.prototype */
        {
            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'CloseControl';
            },

            /**
             * Close chat thread at the server
             *
             * If something went wrong update status message otherwise close
             * chat window
             *
             * @todo May be move to Mibew.Thread class
             */
            closeThread: function() {
                // Get thread and user objects
                var thread = Mibew.Objects.thread;
                var user = Mibew.Objects.Models.user;
                // Send request to the server
                Mibew.Objects.server.callFunctions(
                    [{
                        "function": "close",
                        "arguments": {
                            "references": {},
                            "return": {"closed": "closed"},
                            "threadId": thread.threadId,
                            "token": thread.token,
                            "lastId": thread.lastId,
                            "user": (! user.get('isAgent'))
                        }
                    }],
                    function(args){
                        if (args.closed) {
                            window.close();
                        } else {
                            // Something went wrong. Display error message
                            Mibew.Objects.Models.Status.message.setMessage(
                                args.errorMessage || 'Cannot close'
                            );
                        }
                    },
                    true
                );
            }
        }
    );

})(Mibew);