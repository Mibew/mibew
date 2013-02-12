/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

/**
 * Represents Invitation waiting Window to core interaction type
 *
 * @constructor
 */
MibewAPIInviteInteraction = function() {
    this.obligatoryArguments = {
        '*': {
            'return': {},
            'references': {},
            'visitorId': null
        },
        'result': {
            'errorCode': 0
        }
    };

    this.reservedFunctionNames = [
        'result'
    ];
}
MibewAPIInviteInteraction.prototype = new MibewAPIInteraction();