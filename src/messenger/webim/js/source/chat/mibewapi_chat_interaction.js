/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

/**
 * Represents Chat Window to core interaction type
 *
 * @constructor
 */
MibewAPIChatInteraction = function() {
    this.obligatoryArguments = {
        '*': {
            'threadId': null,
            'token': null,
            'return': {},
            'references': {}
        },
        'result': {
            'errorCode': 0
        }
    };

    this.reservedFunctionNames = [
        'result'
    ];
}
MibewAPIChatInteraction.prototype = new MibewAPIInteraction();