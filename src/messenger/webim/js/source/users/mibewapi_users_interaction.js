/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

/**
 * Represents User list Window to core interaction type
 *
 * @constructor
 */
MibewAPIUsersInteraction = function() {
    this.obligatoryArguments = {
        '*': {
            'agentId': null,
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
MibewAPIUsersInteraction.prototype = new MibewAPIInteraction();