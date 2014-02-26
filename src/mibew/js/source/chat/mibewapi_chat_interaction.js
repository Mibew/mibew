/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Represents Chat Window to core interaction type
 *
 * @constructor
 */
MibewAPIChatInteraction = function() {
    this.mandatoryArguments = function() {
        return {
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
    };

    this.getReservedFunctionsNames = function() {
        return [
            'result'
        ];
    }
}
MibewAPIChatInteraction.prototype = new MibewAPIInteraction();