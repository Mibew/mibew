// Testing Handlebars helpers
module('Handlebars helpers');

// Test "urlReplace" Handlebars helper
test('urlReplace', function() {
    var template = '{{urlReplace foo}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({foo: 'http://example.com'}),
        '<a href="http://example.com" target="_blank">http://example.com</a>',
        'Test HTTP URL'
    );

    equal(
        compiledTemplate({foo: 'https://example.com'}),
        '<a href="https://example.com" target="_blank">https://example.com</a>',
        'Test HTTPS URL'
    );

    equal(
        compiledTemplate({foo: 'ftp://example.com'}),
        '<a href="ftp://example.com" target="_blank">ftp://example.com</a>',
        'Test FTP URL'
    );

    equal(
        compiledTemplate({foo: 'plain text'}),
        'plain text',
        'Test not a URL'
    );

    equal(
        compiledTemplate({foo: 456}),
        '456',
        'Test number argument'
    );
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

// Test "replace" Handlebars helper
test('replace', function() {
    var template = '{{#replace search replacement}}{{source}}{{/replace}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({
            source: 'test source',
            search: 'source',
            replacement: 'target'
        }),
        'test target',
        'Test single replace'
    );

    equal(
        compiledTemplate({
            source: 'Hello\ncruel\nworld!\n',
            search: '\n',
            replacement: '<br/>'
        }),
        'Hello<br/>cruel<br/>world!<br/>',
        'Test multiple replace'
    );

    equal(
        compiledTemplate({
            source: '{{!-- comment',
            search: '{{!--',
            replacement: '<!--'
        }),
        '<!-- comment',
        'Test special regexp characters'
    );

    equal(
        compiledTemplate({
            source: '010203040506070809',
            search: 0,
            replacement: ''
        }),
        '123456789',
        'Test a number as the search value'
    );
});

// Test "cutString" Handlebars helper
test('cutString', function() {
    var template = '{{#cutString length}}{{str}}{{/cutString}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({str: 'Hello world!', length: 40}),
        'Hello world!',
        'Test cutting of a string that is shorter than specified length'
    );

    equal(
        compiledTemplate({str: 'Hello world!', length: 5}),
        'Hello',
        'Test cutting of a string that is longer than specified length'
    );
});

// Test "l10n" Handlebars helper
test('l10n', function() {
    // Add some localization strings that are needed helper testing
    Mibew.Localization.set({
        'one': 'uno',
        'Hello {0}!': '¡Hola {0}!'
    });

    equal(
        Handlebars.compile('{{l10n "one"}}')({}),
        'uno',
        'Test simple string'
    );

    equal(
        Handlebars.compile('{{l10n "Hello {0}!" "world"}}')({}),
        '¡Hola world!',
        'Test string with placeholder'
    );
});

// Test "block", "extends" and "override" Handlebars helpers.
test('inheritance', function() {
    // Test inheritance
    Handlebars.templates = {
        parent: Handlebars.compile(
            'Test {{#block "first"}}1{{/block}} {{#block "second"}}2{{/block}}'
        ),
        child: Handlebars.compile(
            '{{#extends "parent"}}{{#override "first"}}0{{/override}}{{/extends}}'
        ),
        grandChild: Handlebars.compile(
            '{{#extends "child"}}{{#override "first"}}one{{/override}}{{#override "second"}}two{{/override}}{{/extends}}'
        )
    };

    equal(
        Handlebars.templates['parent']({}),
        'Test 1 2',
        'Test default block content'
    );

    equal(
        Handlebars.templates['child']({}),
        'Test 0 2',
        'Test inheritance'
    );

    equal(
        Handlebars.templates['grandChild']({}),
        'Test one two',
        'Test nested inheritance'
    );

    // Test nested blocks
    Handlebars.templates = {
        parent: Handlebars.compile(
            'Test {{#block "first"}}1 {{#block "second"}}2{{/block}}{{/block}}'
        ),
        childInnerBlock: Handlebars.compile(
            '{{#extends "parent"}}{{#override "second"}}two{{/override}}{{/extends}}'
        ),
        childOuterBlock: Handlebars.compile(
            '{{#extends "parent"}}{{#override "first"}}foo{{/override}}{{/extends}}'
        )
    }

    equal(
        Handlebars.templates['childInnerBlock']({}),
        'Test 1 two',
        'Test overriding of the inner block'
    );

    equal(
        Handlebars.templates['childOuterBlock']({}),
        'Test foo',
        'Test overriding of the outer block'
    );

    // Clean up environment
    delete Handlebars.templates;
});

// Test "ifOverridden" Handlebars helper.
test('ifOverridden', function() {
    // Test inheritance
    Handlebars.templates = {
        parent: Handlebars.compile(
            '{{#ifOverridden "foo"}}Child{{else}}Parent{{/ifOverridden}}{{#block "foo"}}{{/block}}'
        ),
        child: Handlebars.compile(
            '{{#extends "parent"}}{{/extends}}'
        ),
        childOverridden: Handlebars.compile(
            '{{#extends "parent"}}{{#override "foo"}}{{/override}}{{/extends}}'
        )
    };

    equal(
        Handlebars.templates['childOverridden']({}),
        'Child',
        'Test overridden block'
    );

    equal(
        Handlebars.templates['child']({}),
        'Parent',
        'Test not overridden block'
    );

    equal(
        Handlebars.templates['parent']({}),
        'Parent',
        'Test with no inheritance'
    );

    // Clean up environment
    delete Handlebars.templates;
});

// Test "unlessOverridden" Handlebars helper.
test('unlessOverridden', function() {
    // Test inheritance
    Handlebars.templates = {
        parent: Handlebars.compile(
            '{{#unlessOverridden "foo"}}Parent{{else}}Child{{/unlessOverridden}}{{#block "foo"}}{{/block}}'
        ),
        child: Handlebars.compile(
            '{{#extends "parent"}}{{/extends}}'
        ),
        childOverridden: Handlebars.compile(
            '{{#extends "parent"}}{{#override "foo"}}{{/override}}{{/extends}}'
        )
    };

    equal(
        Handlebars.templates['childOverridden']({}),
        'Child',
        'Test overridden block'
    );

    equal(
        Handlebars.templates['child']({}),
        'Parent',
        'Test not overridden block'
    );

    equal(
        Handlebars.templates['parent']({}),
        'Parent',
        'Test with no inheritance'
    );

    // Clean up environment
    delete Handlebars.templates;
});
