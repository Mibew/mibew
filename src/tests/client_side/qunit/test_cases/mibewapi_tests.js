// Testing MibewAPIInteraction class
module('MibewAPIInteraction');

/**
 * Represents test interaction type
 *
 * @constructor
 */
function MibewAPITestInteraction() {

    this.mandatoryArguments = function() {
        return {
            '*': {
                'return': {},
                'references': {}
            },
            'foo': {
                'bar': 127
            }
        };
    }

    this.getReservedFunctionsNames = function() {
        return [
            'result'
        ];
    }

}
MibewAPITestInteraction.prototype = new MibewAPIInteraction();

// Tests for the getMandatoryArguments method
test('getMandatoryArguments', function(){
    var interaction = new MibewAPITestInteraction();
    // Arguments for all function
    deepEqual(
        interaction.getMandatoryArguments('some_function'),
        ['return', 'references'],
        'Test with arguments for all functions'
    );

    // Arguments for specific function
    deepEqual(
        interaction.getMandatoryArguments('foo'),
        ['return', 'references', 'bar'],
        'Test with arguments for specific function'
    );
});

// Tests for the getMandatoryArgumentsDefaults method
test('getMandatoryArgumentsDefaults', function(){
    var interaction = new MibewAPITestInteraction();
    // Default values for arguments for all function
    deepEqual(
        interaction.getMandatoryArgumentsDefaults('some_function'),
        {
            'return': {},
            'references': {}
        },
        'Test with default values for arguments for all functions'
    );

    // Default values for arguments for specific function
    deepEqual(
        interaction.getMandatoryArgumentsDefaults('foo'),
        {
            'return': {},
            'references': {},
            'bar': 127
        },
        'Test with default values for arguments for specific function'
    );
});

// Testing MibewAPI class
module("MibewAPI");

// Tests for the class constructor
test("constructor", function(){
    // Incorrect initialization
    try {
        new MibewAPI({});
    } catch (e) {
        equal(
            e.message,
            "Wrong interaction type",
            "Test with wrong constructor argument"
        );
    }

    // Correct Initialization
    new MibewAPI(new MibewAPITestInteraction());
    ok(true, "Correct initialization");
});

// Tests for the checkFunction method
test("checkFunction", function(){
    var mibewAPI = new MibewAPI(new MibewAPITestInteraction());

    // Try to check empty function object
    try {
        mibewAPI.checkFunction({});
    } catch (e) {
        equal(
            e.message,
            "Cannot call for function with no name",
            "Test with empty function object"
        );
    }

    // Try to check function with no name
    try {
        mibewAPI.checkFunction({"function": ""});
    } catch (e) {
        equal(
            e.message,
            "Cannot call for function with no name",
            "Test with a function with no name"
        );
    }

    // Try to check function with reserved name and filterReservedFunctions
    // argument equals to true
    try {
        mibewAPI.checkFunction({"function" : "result"}, true);
    } catch (e) {
        equal(
            e.message,
            "'result' is reserved function name",
            "Test with reserved function's name and filterReservedFunctions " +
            "arguments set to true"
        );
    }

    // Try to check function with reserved name and filterReservedFunctions
    // argument equals to false. Arguments list is undefined
    try {
        mibewAPI.checkFunction({"function": "result"}, false);
    } catch (e) {
        equal(
            e.message,
            "There are no arguments in 'result' function",
            "Test with reserved function's name and filterReservedFunctions " +
            "arguments set to false. Arguments list is undefined"
        );
    }

    // Try to check function with not all obligatory arguments
    try {
        mibewAPI.checkFunction({
            "function" : "test",
            "arguments" : {"return" : []}
        });
    } catch (e) {
        equal(
            e.message,
            "Not all mandatory arguments are set in 'test' function",
            "Test with not all mandatory arguments"
        );
    }

    // Try to check correct function
    mibewAPI.checkFunction({
        "function" : "test",
        "arguments" : {
            "return" : [],
            "references" : [],
            "testArgument" : "testValue"
        }
    });
    ok(true, "Test correct function");
});

// Test for the checkRequest method
test("checkRequest", function() {
    var mibewAPI = new MibewAPI(new MibewAPITestInteraction());
    var correctFunction = {
        "function" : "test",
        "arguments" : {
            "return" : [],
            "references" : [],
            "testArgument" : "testValue"
        }
    }

    // Try to check request without token
    try {
        mibewAPI.checkRequest({});
    } catch (e) {
        equal(
            e.message,
            "Empty token",
            "Test with empty token"
        );
    }

    // Try to check request with wrong token type
    try {
        mibewAPI.checkRequest({"token" : false});
    } catch (e) {
        equal(
            e.message,
            "Wrong token type",
            "Test with wrong token type"
        );
    }

    // Try to check request with empty token string
    try {
        mibewAPI.checkRequest({"token" : ""});
    } catch (e) {
        equal(
            e.message,
            "Empty token",
            "Test with empty token string"
        );
    }

    // Try to check request with no functions list
    try {
        mibewAPI.checkRequest({"token" : "123"});
    } catch (e) {
        equal(
            e.message,
            "Empty functions set",
            "Test with no functions list"
        );
    }

    // Try to check request with functions list of the wrong type
    try {
        mibewAPI.checkRequest({"token" : "123", "functions" : {}});
    } catch (e) {
        equal(
            e.message,
            "Empty functions set",
            "Test with wrong type of the functions list"
        );
    }

    // Try to check request with empty functions list
    try {
        mibewAPI.checkRequest({"token" : "123", "functions" : []});
    } catch (e) {
        equal(
            e.message,
            "Empty functions set",
            "Test with empty functions list"
        );
    }

    // Try to check correct request
    mibewAPI.checkRequest({
        "token" : "123",
        "functions" : [
            correctFunction,
            correctFunction
        ]
    });
    ok(true, "Test with correct request");
});

// Test for the checkPackage method
test("checkPackage", function() {
    var mibewAPI = new MibewAPI(new MibewAPITestInteraction());
    var correctRequest = {
        "token" : "123",
        "functions" : [
            {
                "function" : "test",
                "arguments" : {
                    "return" : [],
                    "references" : []
                }
            }
        ]
    }

    // Try to check package with no signature
    try {
        mibewAPI.checkPackage({});
    } catch (e) {
        equal(
            e.message,
            "Missed package signature",
            "Test package with no signature"
        );
    }

    // Try to check package with no protocol version
    try {
        mibewAPI.checkPackage({"signature" : "test_signature"});
    } catch (e) {
        equal(
            e.message,
            "Missed protocol version",
            "Test package with no protocol"
        );
    }

    // Try to check package with wrong protocol version
    try {
        mibewAPI.checkPackage({
            "signature" : "test_signature",
            "proto" : -12
        });
    } catch (e) {
        equal(
            e.message,
            "Wrong protocol version",
            "Test pacakge with wrong protocol version"
        );
    }

    // Try to check package with no 'async' flag value
    try {
        mibewAPI.checkPackage({
            "signature" : "test_signature",
            "proto" : mibewAPI.protocolVersion
        });
    } catch (e) {
        equal(
            e.message,
            "'async' flag is missed",
            "Test with no 'async' flag"
        );
    }

    // Try to check package with wrong 'async' flag value
    try {
        mibewAPI.checkPackage({
            "signature" : "test_signature",
            "proto" : mibewAPI.protocolVersion,
            "async" : "wrong_async_flag"
        });
    } catch (e) {
        equal(
            e.message,
            "Wrong 'async' flag value",
            "Test with wrong 'async' flag value"
        );
    }

    // Try to check package with no requests list
    try {
        mibewAPI.checkPackage({
            "signature" : "test_signature",
            "proto" : mibewAPI.protocolVersion,
            "async" : true
        });
    } catch (e) {
        equal(
            e.message,
            "Empty requests set",
            "Test with no requests list"
        );
    }

    // Try to check package with wrong requests list type
    try {
        mibewAPI.checkPackage({
            "signature" : "test_signature",
            "proto" : mibewAPI.protocolVersion,
            "async" : true,
            "requests" : {}
        });
    } catch (e) {
        equal(
            e.message,
            "Empty requests set",
            "Test with wrong requests list type"
        );
    }

    // Try to check package with empty requests list
    try {
        mibewAPI.checkPackage({
            "signature" : "test_signature",
            "proto" : mibewAPI.protocolVersion,
            "async" : true,
            "requests" : []
        });
    } catch (e) {
        equal(
            e.message,
            "Empty requests set",
            "Test with empty requests list"
        );
    }

    // Try to check correct package
    mibewAPI.checkPackage({
        "signature" : "test_signature",
        "proto" : mibewAPI.protocolVersion,
        "async" : true,
        'requests' : [
            correctRequest,
            correctRequest
        ]
    });
    ok(true, "Test with correct package");
});

// Test for the getResultFunction method
test("getResult", function() {
    var mibewAPI = new MibewAPI(new MibewAPITestInteraction);
    var resultFunction = {
        "function" : "result",
        "test_field" : "test_value"
    }

    // Try to get result function from function list, but it contains more than
    // one 'result' function
    try {
        mibewAPI.getResultFunction([
                {"function" : "result"},
                {"function" : "result"},
            ]);
    } catch (e) {
        equal(
            e.message,
            "Function 'result' already exists in functions list",
            "Test more than one 'result' functions in functions list"
        )
    }

    // Try to get 'result' function from functions list that have no 'result'
    // function. 'existance' argument set to true
    try {
        mibewAPI.getResultFunction({}, true);
    } catch (e) {
        equal(
            e.message,
            "There is no 'result' function in functions list",
            "Test with no 'result' function in functions list"
        );
    }

    // Try to get 'result' function from functions list that have no 'result'
    // function. 'existance' argument set to false
    equal(
        mibewAPI.getResultFunction({}, false),
        null,
        "Test with no 'result' function in functions list, 'existance' " +
        "argument equals to false and null as the result functionon returned"
    );

    // Try to get 'result' function from functions list that have no 'result'
    // function. 'existance' argument set to null
    equal(
        mibewAPI.getResultFunction({}, null),
        null,
        "Test with no 'result' function in functions list, 'existance' " +
        "argument equals to null and null as the result functionon returned"
    );

    // Try to get 'result' function from functions list that have 'result'
    // function. 'existance' argument set to false
    try {
        mibewAPI.getResultFunction([
            {"function" : "result"}
        ], false);
    } catch (e) {
        equal(
            e.message,
            "There is 'result' function in functions list",
            "Test with 'result' function in functions list"
        );
    }

    // Try to get result function
    deepEqual(
        mibewAPI.getResultFunction([resultFunction]),
        resultFunction,
        "Test of get 'result' function"
    );
});

// Test for the builResult method
test("buildResult", function() {
    var mibewAPIInteraction = new MibewAPITestInteraction;
    var mibewAPI = new MibewAPI(mibewAPIInteraction);
    var testArguments = {
        "first" : "test_value",
        "second" : "test_value"
    }
    var token = "some token";
    var testPackage = {
        "token" : token,
        "functions" : [
            {
                "function" : "result",
                "arguments" : {
                    "first" : "test_value",
                    "second" : "test_value",
                    "return" : {},
                    "references" : {}
                }
            }
        ]
    }

    // Compare result package
    deepEqual(
        mibewAPI.buildResult(testArguments, token),
        testPackage,
        "Test returned package"
    );
});

// Test for the encodePackage Method
test("encodePackage", function() {
    var mibewAPI = new MibewAPI(new MibewAPITestInteraction);
    var testRequest = mibewAPI.buildResult({}, "some_token");
    var testPackage = {
        "signature" : "",
        "proto" : mibewAPI.protocolVersion,
        "async" : true,
        "requests" : [testRequest]
    }

    // Compare encoded packages
    equal(
        mibewAPI.encodePackage([testRequest]),
        encodeURIComponent(JSON.stringify(testPackage)),
        "Test encoded package"
    );
});

// Test for the decodePackage method
test("decodePackage", function() {
    var mibewAPI = new MibewAPI(new MibewAPITestInteraction);
    var testRequest = mibewAPI.buildResult({}, "some_token");
    var testPackage = {
        "signature" : "",
        "proto" : mibewAPI.protocolVersion,
        "async" : true,
        "requests" : [testRequest]
    }
    var encodedPackage = mibewAPI.encodePackage([testRequest]);

    // Try to decode broken package
    try {
        mibewAPI.decodePackage(encodedPackage.substr(
            Math.floor(encodedPackage.length / 2)
        ));
    } catch(e) {
        ok(e.name = "SyntaxError","Test broken package");
    }

    // Compare decoded packages
    deepEqual(
        mibewAPI.decodePackage(encodedPackage),
        testPackage,
        "Test decoded package"
    )
});

// Testing MibewAPIExecutionContext class
module("MibewAPIExecutionContext");

// Test for the storeFunctionResults method
test("storeFunctionResults", function() {
    var context = new MibewAPIExecutionContext();

    // Try to store result for function with inconsistent 'return' argument and
    // function's result
    try {
        context.storeFunctionResults(
            {
                "function" : "test",
                "arguments" : {
                    "return" : {"test_field" : "alias_for_test_field"}
                }
            },
            {"another_test_field" : "another_test_value"}
        );
    } catch (e) {
        equal(
            e.message,
            "Variable with name 'test_field' is undefined in " +
            "the results of the 'test' function",
            "Test with inconsistent 'return' argument and function results"
        );
    }

    // Try to store correct results
    context.storeFunctionResults(
        {
            "function" : "test",
            "arguments" : {
                "return" : {
                    "test_field" : "alias_for_test_field",
                    "another_test_field" : "another_alias"
                }
            }
        },
        {
            "test_field" : "test_value",
            "another_test_field" : "another_test_value"
        }
    );
    deepEqual(
        context.returnValues,
        {
            "alias_for_test_field" : "test_value",
            "another_alias" : "another_test_value"
        },
        "Test returnValues after storing correct results"
    );
    deepEqual(
        context.functionsResults,
        [{
            "test_field" : "test_value",
            "another_test_field" : "another_test_value"
        }],
        "Test functionsResults after storing correct results"
    );
});

// Test for the getArgumentsList method
test("getArgumentsList", function(){
    var context = new MibewAPIExecutionContext();

    // Try to reference to undefined function number in execution context
    try {
        context.getArgumentsList({
            "function" : "test",
            "arguments" : {
                "return" : {},
                "references" : {"test" : 1},
                "test" : "test_value"
            }
        });
    } catch (e) {
        equal(
            e.message,
            "Wrong reference in 'test' function. Function #1 does not call yet.",
            "Test with reference to function that does not called function"
        );
    }

    // Store some test results in context
    context.storeFunctionResults(
        {
            "function" : "test",
            "arguments" : {
                "return" : {
                    "test_field" : "alias_for_test_field",
                    "another_test_field" : "another_alias"
                }
            }
        },
        {
            "test_field" : "test_value",
            "another_test_field" : "another_test_value"
        }
    );

    // Check undefined target name
    try {
        context.getArgumentsList({
            "function" : "test",
            "arguments" : {
                "return" : {},
                "references" : {"test" : 1}
            }
        });
    } catch (e) {
        equal(
            e.message,
            "Wrong reference in 'test' function. Empty 'test' argument.",
            "Test with undefined reference argument"
        );
    }

    // Check empty target name
    try {
        context.getArgumentsList({
            "function" : "test",
            "arguments" : {
                "return" : {},
                "references" : {"test" : 1},
                "test" : ""
            }
        });
    } catch (e) {
        equal(
            e.message,
            "Wrong reference in 'test' function. Empty 'test' argument.",
            "Test with empty reference argument"
        );
    }

    // Check undefined target value
    try {
        context.getArgumentsList({
            "function" : "test",
            "arguments" : {
                "return" : {},
                "references" : {"test" : 1},
                "test" : "undefined_target_value"
            }
        });
    } catch (e) {
        equal(
            e.message,
            "Wrong reference in 'test' function. There is no " +
            "'undefined_target_value' argument in #1 function results",
            "Test with undefined target value"
        );
    }

    // Check correct references
    deepEqual(
        context.getArgumentsList({
            "function" : "test",
            "arguments" : {
                "return" : {},
                "references" : {"test" : 1},
                "test" : "test_field"
            }
        }),
        {
            "return" : {},
            "references" : {"test" : 1},
            "test" : "test_value"
        },
        "Test returned arguments list"
    );
});

// Test for the getResults method
test("getResults", function() {
    var context = new MibewAPIExecutionContext();

    // Store some test results in context
    context.storeFunctionResults(
        {
            "function" : "test",
            "arguments" : {
                "return" : {
                    "test_field" : "alias_for_test_field",
                    "another_test_field" : "another_alias"
                }
            }
        },
        {
            "test_field" : "test_value",
            "another_test_field" : "another_test_value"
        }
    );

    // Check returned values
    deepEqual(
        context.getResults(),
        {
            "alias_for_test_field" : "test_value",
            "another_alias" : "another_test_value"
        },
        "Test returned values"
    );
});