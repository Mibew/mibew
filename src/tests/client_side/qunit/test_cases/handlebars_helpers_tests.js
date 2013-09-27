// Testing Handlebars helpers
module('Handlebars helpers');

// Register test emphasis helper
Handlebars.registerHelper('emTestHelper', function(text) {
    return new Handlebars.SafeString('<em>' + text + '</em>');
});

// Register test strong helper
Handlebars.registerHelper('strongTestHelper', function(text) {
    return new Handlebars.SafeString('<strong>' + text + '</strong>');
});

// Test 'apply' Handlebars helper
test('apply', function() {
    // Test application of two valid helpers to text.
    // There are no spaces before or after comma.
    var template = '{{apply text "emTestHelper,strongTestHelper"}}';
    var compiledTemplate = Handlebars.compile(template);
    var output = compiledTemplate({text: "some_text"});
    equal(
        output,
        '<strong><em>some_text</em></strong>',
        'Test two valid helpers'
    );

    // Test application of two valid helpers in reverse order to text.
    // There are no spaces before or after comma.
    template = '{{apply text "strongTestHelper,emTestHelper"}}';
    compiledTemplate = Handlebars.compile(template);
    output = compiledTemplate({text: "some_text"});
    equal(
        output,
        '<em><strong>some_text</strong></em>',
        'Test two valid helpers in reverse order'
    );

    // Test application of two valid helpers to text.
    // There are some spaces before and after comma.
    template = '{{apply text "emTestHelper ,   strongTestHelper"}}';
    compiledTemplate = Handlebars.compile(template);
    output = compiledTemplate({text: "some_text"});
    equal(
        output,
        '<strong><em>some_text</em></strong>',
        'Test two valid helpers with some spaces before and after comma'
    );

    // Test application of one valid helper and one with wrong name to text.
    // There are no spaces before or after comma.
    template = '{{apply text "emTestHelper,$strongTestHelper"}}';
    compiledTemplate = Handlebars.compile(template);
    output = compiledTemplate({text: "some_text"});
    equal(
        output,
        '<em>some_text</em>',
        'Test one valid helper and one with wrong name'
    );

    // Test application of one valid helper and one unregistered helper to text.
    // There are no spaces before or after comma.
    template = '{{apply text "emTestHelper,unregisteredTestHelper"}}';
    compiledTemplate = Handlebars.compile(template);
    try {
        output = compiledTemplate({text: "some_text"});
    } catch(e) {
        equal(
            e.message,
            "Unregistered helper 'unregisteredTestHelper'!",
            'Test one valid helper and one unregistered helper'
        );
    }
});