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

// Test "ifEven" Handlebars helper
test('ifEven', function() {
    var template = '{{#ifEven foo}}true{{else}}false{{/ifEven}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({foo: 0}),
        'true',
        'Test even value'
    );

    equal(
        compiledTemplate({foo: 1}),
        'false',
        'Test odd value'
    );
});

// Test "ifOdd" Handlebars helper
test('ifOdd', function() {
    var template = '{{#ifOdd foo}}true{{else}}false{{/ifOdd}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({foo: 0}),
        'false',
        'Test even value'
    );

    equal(
        compiledTemplate({foo: 1}),
        'true',
        'Test odd value'
    );
});

// Test "ifAny" Handlebars helper
test('ifAny', function() {
    var template = '{{#ifAny foo bar baz}}true{{else}}false{{/ifAny}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({}),
        'false',
        'Test only falsy values'
    );

    equal(
        compiledTemplate({baz: true}),
        'true',
        'Test only one true value'
    );

    equal(
        compiledTemplate({foo: true, bar: 1}),
        'true',
        'Test more than one true values'
    );
});

// Test "ifEqual" Handlebars helper
test('ifEqual', function() {
    var template = '{{#ifEqual left right}}true{{else}}false{{/ifEqual}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({left: 12, right: "foo"}),
        'false',
        'Test different values'
    );

    equal(
        compiledTemplate({left: "10", right: 10}),
        'true',
        'Test equal values with different types'
    );

    equal(
        compiledTemplate({left: "Bar", right: "Bar"}),
        'true',
        'Test equal values'
    );
});

// Test "repeat" Handlebars helper
test('repeat', function() {
    var template = '{{#repeat times}}{{foo}}{{/repeat}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({foo: 'Foo.', times: 3}),
        'Foo.Foo.Foo.',
        'Test repeating'
    );
});
