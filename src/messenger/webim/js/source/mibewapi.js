/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

/**
 * Mibew API class constructor
 *
 * @constructor
 * @param {MibewAPIInteraction} interaction An object that represents
 * interaction type
 */
function MibewAPI(interaction) {

    /**
     * Version of the MIBEW API protocol implemented by the object
     */
    this.protocolVersion = "1.0";

    // Check interaction type
    if (typeof interaction != "object" ||
            !(interaction instanceof MibewAPIInteraction)) {
        throw new Error("Wrong interaction type");
    }

    /**
     * An object that encapsulates type of the interaction
     * @private
     * @type MibewAPIInteraction
     */
    this.interaction = interaction;
}

/**
 * Validate function
 *
 * Throws an Error object if function is not valid.
 *
 * @param {Object} functionObject The function. See Mibew API for details.
 * @param {Boolean} [filterReservedFunctions=false]. Determine if function
 * name must not be in reserved list
 * @thows Error
 */
MibewAPI.prototype.checkFunction = function(functionObject, filterReservedFunctions) {
    filterReservedFunctions = filterReservedFunctions || false;

    // Check function name
    if (typeof functionObject["function"] == "undefined" ||
        functionObject["function"] == "") {
        throw new Error("Cannot call for function with no name");
    }
    if (filterReservedFunctions) {
        for (var i = 0; i < this.interaction.reservedFunctionNames.length; i++) {
            if (functionObject["function"] ==
                    this.interaction.reservedFunctionNames[i]) {
                throw new Error(
                    "'" + functionObject["function"] +
                    "' is reserved function name"
                );
            }
        }
    }

    // Check function's arguments
    if (typeof functionObject.arguments != "object") {
        throw new Error(
            "There are no arguments in '" + functionObject["function"] +
            "' function"
        );
    }
    var obligatoryArgumentsCount = 0;
    var obligatoryArgumentsList = this.interaction.getObligatoryArguments(
        functionObject['function']
    );
    argumentsLoop:
    for (var argName in functionObject.arguments){
        for (var i = 0; i < obligatoryArgumentsList.length; i++) {
            if (argName == obligatoryArgumentsList[i]) {
                obligatoryArgumentsCount++;
                continue argumentsLoop;
            }
        }
    }
    if (obligatoryArgumentsCount != obligatoryArgumentsList.length) {
        throw new Error(
            "Not all obligatory arguments are set in '" +
            functionObject["function"] + "' function"
        );
    }
}

/**
 * Validate request
 *
 * Throws an Error object if request is not valid.
 *
 * @param {Object} requestObject The Request. See Mibew API for details.
 * @thows Error
 */
MibewAPI.prototype.checkRequest = function(requestObject) {
    // Check token
    if (typeof requestObject.token != "string") {
        if (typeof requestObject.token == "undefined") {
            throw new Error("Empty token");
        } else {
            throw new Error("Wrong token type");
        }
    }
    if (requestObject.token == "") {
        throw new Error("Empty token");
    }

    // Request must have at least one function
    if (typeof requestObject.functions != "object" ||
            !(requestObject.functions instanceof Array) ||
            requestObject.functions.length == 0) {
        throw new Error("Empty functions set");
    }

    // Check function
    for (var i = 0; i < requestObject.functions.length; i++) {
        this.checkFunction(requestObject.functions[i]);
    }
}

/**
 * Validate package.
 *
 * Throws an Error object if package is not valid.
 *
 * @param {Object} packageObject The package. See Mibew API for details.
 * @thows Error
 */
MibewAPI.prototype.checkPackage = function (packageObject) {
    // Check signature
    if (typeof packageObject.signature == "undefined") {
        throw new Error("Missed package signature");
    }

    // Check protocol
    if (typeof packageObject.proto == "undefined") {
        throw new Error("Missed protocol version");
    }
    if (packageObject.proto != this.protocolVersion) {
        throw new Error("Wrong protocol version");
    }

    // Check async flag
    if (typeof packageObject.async == "undefined") {
        throw new Error("'async' flag is missed");
    }
    if (typeof packageObject.async != "boolean") {
        throw new Error("Wrong 'async' flag value");
    }

    // Package must have at least one request
    if (typeof packageObject.requests != "object" ||
            !(packageObject.requests instanceof Array) ||
            packageObject.requests.length == 0) {
        throw new Error("Empty requests set");
    }

    // Check requests in package
    for (var i = 0; i < packageObject.requests.length; i++) {
        this.checkRequest(packageObject.requests[i]);
    }
}

/**
 * Search 'result' function in functionsList. If request contains more than
 * one result functions throws an Error
 *
 * @param {Object[]} functionsList Array of functions. See MibewAPI for
 * function structure details
 * @param {Boolean|null} [existance="null"] (optional) Control existance of
 * the 'result' function in request. Use boolean true if 'result' function
 * must exists in request, boolean false if must not and null if it doesn't
 * matter.
 * @returns {Object|null} Function object if 'result' function found and
 * null otherwise
 * @throws Error
 */
MibewAPI.prototype.getResultFunction = function(functionsList, existance){
    if (typeof existance == "undefined") {
        existance = null;
    }
    var resultFunction = null;
    // Try to find result function
    for (var i in functionsList) {
        if (! functionsList.hasOwnProperty(i)) {
            continue;
        }
        if (functionsList[i]["function"] == 'result') {
            if (resultFunction !== null) {
                // Another result function found
                throw new Error(
                    "Function 'result' already exists in functions list"
                );
            }
            // First 'result' function found
            resultFunction = functionsList[i];
        }
    }
    if (existance === true && resultFunction === null) {
        // 'result' function must present in request
        throw new Error("There is no 'result' function in functions list");
    }
    if (existance === false && resultFunction !== null) {
        throw new Error("There is 'result' function in functions list");
    }
    return resultFunction;
}

/**
 * Builds result package
 *
 * @param {Object} resultArguments Arguments of the result function
 * @param {String} token Token of the result package
 * @returns {Object} Result package
 */
MibewAPI.prototype.buildResult = function(resultArguments, token) {
    var mergedArguments = resultArguments;
    var defaultArguments = this.interaction.getObligatoryArgumentsDefaults('result');
    for (var argName in defaultArguments) {
        if (! defaultArguments.hasOwnProperty(argName)) {
            continue;
        }
        mergedArguments[argName] = defaultArguments[argName];
    }
    return {
        'token': token,
        'functions': [
            {
                'function' : 'result',
                'arguments' : mergedArguments
            }
        ]
    }
}

/**
 * Encodes package
 *
 * @param {Object[]} requests Array of the Requests. See Mibew API for
 * details.
 * @returns {String} Ready for transfer encoded package
 */
MibewAPI.prototype.encodePackage =  function(requests) {
    var packageObject = {};
    packageObject.signature = "";
    packageObject.proto = this.protocolVersion;
    packageObject.async = true;
    packageObject.requests = requests;
    return encodeURIComponent(JSON.stringify(packageObject)).replace(/\%20/gi, '+');
}

/**
 * Decodes package and validate package structure
 *
 * Throws an Error object if package cannot be decoded or is not valid
 *
 * @param {String} encodedPackage Encoded package
 * @returns {Object} The Decoded package. See Mibew API for details.
 * @throws Error
 */
MibewAPI.prototype.decodePackage = function(encodedPackage){
    var decodedPackage = JSON.parse(decodeURIComponent(encodedPackage.replace(/\+/gi, ' ')));
    this.checkPackage(decodedPackage);
    return decodedPackage;
}
/**
 * End of MibewAPI Class
 */


/**
 * Represents interaction type
 *
 * @constructor
 */
function MibewAPIInteraction(){
    /**
     * Defines obligatory arguments and default values for them
     *
     * Keys of the array are function names ('*' for all functions). Values are
     * arrays of obligatory arguments with key for name of an argument and value
     * for default value.
     *
     * For example:
     * <code>
     * this.obligatoryArguments = {
     *      '*': {
     *          'return': {},
     *          'references': {}
     *      },
     *      'result': {
     *          'errorCode': 0
     *      }
     * }
     * </code>
     * @type Object
     * @private
     */
    this.obligatoryArguments = {};

    /**
     * Reserved function's names
     *
     * Defines reserved(system) function's names described in the Mibew API.
     * @type Array
     */
    this.reservedFunctionNames =  [];
}

/**
 * Returns obligatory arguments for the functionName function
 *
 * @param {String} functionName Function name
 * @returns {Array} An array of obligatory arguments
 */
MibewAPIInteraction.prototype.getObligatoryArguments = function(functionName) {
    var obligatoryArguments = [];
    // Add obligatory for all functions arguments
    if (typeof this.obligatoryArguments['*'] == 'object') {
        for (var arg in this.obligatoryArguments['*']) {
            if (! this.obligatoryArguments['*'].hasOwnProperty(arg)) {
                continue;
            }
            obligatoryArguments.push(arg);
        }
    }
    // Add obligatory arguments for given function
    if (typeof this.obligatoryArguments[functionName] == 'object') {
        for (var arg in this.obligatoryArguments[functionName]) {
            if (! this.obligatoryArguments[functionName].hasOwnProperty(arg)) {
                continue;
            }
            obligatoryArguments.push(arg);
        }
    }
    return obligatoryArguments;
}

/**
 * Returns default values of obligatory arguments for the functionName function
 *
 * @param {String} functionName Function name
 * @returns {Object} An object fields names are obligatory arguments and
 * values are default values of them
 */
MibewAPIInteraction.prototype.getObligatoryArgumentsDefaults = function(functionName) {
    var obligatoryArguments = {};
    // Add obligatory for all functions arguments
    if (typeof this.obligatoryArguments['*'] == 'object') {
        for (var arg in this.obligatoryArguments['*']) {
            if (! this.obligatoryArguments['*'].hasOwnProperty(arg)) {
                continue;
            }
            obligatoryArguments[arg] = this.obligatoryArguments['*'][arg];
        }
    }
    // Add obligatory arguments for given function
    if (typeof this.obligatoryArguments[functionName] == 'object') {
        for (var arg in this.obligatoryArguments[functionName]) {
            if (! this.obligatoryArguments[functionName].hasOwnProperty(arg)) {
                continue;
            }
            obligatoryArguments[arg] = this.obligatoryArguments[functionName][arg];
        }
    }
    return obligatoryArguments;
}
/**
 * End of MibewAPIInteraction class
 */

/**
 * Represents Window to core interaction type
 *
 * @constructor
 * @todo Think about real values!
 */
function MibewAPICoreInteraction() {
    this.obligatoryArguments = {
        '*': {
            'return': {},
            'references': {}
        }
    };

    this.reservedFunctionNames = [
        'result'
    ];
}
MibewAPICoreInteraction.prototype = new MibewAPIInteraction();

/**
 * Implements functions execution context
 *
 * @constructor
 */
function MibewAPIExecutionContext() {
    /**
     * Values which returns after execution of all functions in request
     * @private
     */
    this.returnValues = {};

    /**
     * Results of execution of all function in request
     * @private
     */
    this.functionsResults = [];
}

/**
 * Build arguments list by replace all references by values of execution
 * context
 *
 * @param {Object} functionObject The Function. See MibewAPI for details.
 * @returns {Array} Arguments list
 * @throws Error
 */
MibewAPIExecutionContext.prototype.getArgumentsList = function(functionObject) {
    var argumentsList = functionObject.arguments;
    var references = functionObject.arguments.references;
    var referenceTo, funcNum;
    for (var variableName in references) {
        if (! references.hasOwnProperty(variableName)) {
            continue;
        }
        referenceTo = null;
        funcNum = references[variableName];
        // Check target function in context
        if (typeof this.functionsResults[funcNum - 1] == "undefined") {
            // Wrong function number
            throw new Error("Wrong reference in '" +
                functionObject['function'] + "' function. Function #" +
                funcNum + " does not call yet."
            );
        }

        // Check reference
        if (typeof argumentsList[variableName] == "undefined" ||
                argumentsList[variableName] == "") {
            // Empty argument that should contains reference
            throw new Error("Wrong reference in '" +
                    functionObject['function'] + "' function. " +
                    "Empty '" + variableName + "' argument."
            );
        }
        referenceTo = argumentsList[variableName];

        // Check target value
        if (typeof this.functionsResults[funcNum - 1][referenceTo] ==
                "undefined") {
                throw new Error(
                    "Wrong reference in '" + functionObject['function'] +
                    "' function. There is no '" + referenceTo +
                    "' argument in #" + funcNum + " function results"
            );
        }

        // Replace reference by target value
        argumentsList[variableName] = this.functionsResults[funcNum - 1][referenceTo];
    }
    return argumentsList;
}

/**
 * Returns requets results
 *
 * @returns {Object}
 */
MibewAPIExecutionContext.prototype.getResults = function(){
    return this.returnValues;
}

/**
 * Stores functions results in execution context and add values to request
 * result
 *
 * @param {Object} functionObject The Function. See MibewAPI for details.
 * @param {Object} results Object of the function results.
 * @throws Error
 */
MibewAPIExecutionContext.prototype.storeFunctionResults = function(functionObject, results) {
    var alias;
    // Add value to request results
    for (var argName in functionObject.arguments["return"]) {
        if (! functionObject.arguments["return"].hasOwnProperty(argName)) {
            continue;
        }
        alias = functionObject.arguments["return"][argName];
        if (typeof results[argName] == "undefined") {
            throw new Error(
                "Variable with name '" + argName + "' is undefined in " +
                "the results of the '" + functionObject['function'] +
                "' function"
            );
        }
        this.returnValues[alias] = results[argName];
    }
    // Store function results in execution context
    this.functionsResults.push(results);
}
/**
 * End of MibewAPIExecutionContext class
 */
